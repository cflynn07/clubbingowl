<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Library that abstracts all of the Facebook fan-pages for the controller
 * 
 */
class Library_facebook_application{
	
	//CodeIgniter superobject
	private $CI;
	
	//decoded data from the facebook signed request
	public $signed_request_data = array();
	
	//tracks whether or not the visitor is a page admin, this is set to the session
		//during the initial page visit. The first visit to a facebook application in
		//an iframe is the only request that contains the signed request that specifies
		//if a user is admin or not
	public $page_admin = false;
	
	//data retrieved from database for a given page, false if page not known
	public $page_data = false;
	
	/**
	 * Class constructor
	 * 
	 * @return	library_venue
	 */
	public function __construct(){
		$this->CI =& get_instance();
		
	}
	
	/**
	 * Initializes library based on page id
	 * 
	 * @param	string (promoter public identifier)
	 * @return	null
	 */
	public function initialize(){
		
		
		
		
		
		
		
		//parse POST fields and retrieve data / verify with private key
		if($signed_request = $this->CI->input->post('signed_request')){
			
			list($encoded_sig, $payload) = explode('.', $signed_request);
			
			$sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
			$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
			
			if(strtoupper($data['algorithm']) !== 'HMAC-SHA256'){
				die('Unknown algorithm');
			}
			
			$expected_sig = hash_hmac('sha256', $payload, $this->CI->config->item('facebook_api_secret'), $raw = true);
			if($sig !== $expected_sig){
		    	die('Signature error');
		  	}
			
			$this->signed_request_data = $data;
			
			if(!isset($data['page']))
				return; //some sort of error has occured...
			
			//if this visitor is a page admin, set session data for subsequent ajax requests
			if($data['page']['admin']){
				
				if($page_admin = $this->CI->session->userdata('page_admin')){
					//if this user already has page-admin data, add this page to list of pages
					//that user is admin of
					
					$page_admin = json_decode($page_admin);
					$page_admin->pages[] = $data['page']['id'];
					$page_admin->pages = array_unique($page_admin->pages);
					
				}else{
					//user has no existing page admin data
					
					$page_admin = new stdClass;
					$pages = array(
									$data['page']['id']
									);
					$page_admin->pages = $pages;
					
				}

				//add data to session...
				$this->CI->session->set_userdata('page_admin', json_encode($page_admin));
				$this->page_admin = $page_admin;
			}
			
			
			
			//retrieve data on file for this team
			$this->CI->load->model('model_teams', 'teams', true);
			$this->page_data = $this->CI->teams->retrieve_team($data['page']['id'], array('completed_setup' => true));




			if(isset($this->page_data->team->team_piwik_id_site)){
				
				//TODO: FIX	
				//load the piwik site id of this promoter on this page for tracking purposes
				$this->CI->load->vars('additional_sites_ids', array($this->page_data->team->team_piwik_id_site));
		
			}
			
			
			
			
			
		}else{
			
			//bypass for ajax requests...
			if(!$this->CI->input->is_ajax_request()){
				//improper post request
				//echo 'improper request, no signed data.';
				echo '<script type="text/javascript">window.top.location = "https://www.' . SITE . '.' . TLD . '";</script>';
				die();
			}
			
		}
		
		
		
		//set page_admin from session data if ajax request
		if($this->CI->input->is_ajax_request()){
			if($page_admin = $this->CI->session->userdata('page_admin')){
				$this->page_admin = json_decode($page_admin);
			}
			
			//throw an error if the fan_page_id that was submitted does not match this user's session fan_pages
			$fan_page_id = $this->CI->input->post('fan_page_id');
			if($fan_page_id && $this->page_admin && (!in_array($fan_page_id, $this->page_admin->pages))){
				die(json_encode(array('success' => false,
										'message' => 'invalid fan page id')));
			}
			
			//TODO: Check to see if submitted $fan_page_id matches signed_request_data
			
			if($fan_page_id){
				//retrieve data on file for this team
				$this->CI->load->model('model_teams', 'teams', true);
				$this->page_data = $this->CI->teams->retrieve_team($fan_page_id);
			}
		}
		
		
		
		
		
		
		
		
		
		return true;
	}

	
	/**
	 * Initialize except not from facebook, from plugin on website
	 * 
	 */
	function initialize_web_plugin($tfpid){
		
		//retrieve data on file for this team
		$this->CI->load->model('model_teams', 'teams', true);
		$this->page_data = $this->CI->teams->retrieve_team($tfpid, array('completed_setup' => true));

		if(isset($this->page_data->team->team_piwik_id_site)){
			
			//TODO: FIX	
			//load the piwik site id of this promoter on this page for tracking purposes
			$this->CI->load->vars('additional_sites_ids', array($this->page_data->team->team_piwik_id_site));
	
		}
		
		return true;
		
	}
	

	/**
	 * Retrieves the current user's setup step for this fan_page
	 * 
	 * @return 	int
	 */
	private function _get_setup_step($fan_page_id){
		//attach object as property to page_admin for every page that this user admins
		//if it's not already set
		if(!isset($this->page_admin->$fan_page_id)){
			//increment the step
			$this->page_admin->$fan_page_id->setup_step = 0;
		}
		
		return $this->page_admin->$fan_page_id->setup_step;
		
	}
	
	/**
	 * Increments the user's current setup step
	 * 
	 * @return 	null
	 */
	private function _increment_setup_step($fan_page_id){
		//attach object as property to page_admin for every page that this user admins
		//if it's not already set
		if(!isset($this->page_admin->$fan_page_id)){
			//increment the step
			$this->page_admin->$fan_page_id->setup_step = 0;
		}
		
		if($this->page_admin->$fan_page_id->setup_step < 3)
			$this->page_admin->$fan_page_id->setup_step++;
		else
			$this->page_admin->$fan_page_id->setup_step = 0;
		
		$this->CI->session->set_userdata('page_admin', json_encode($this->page_admin));
	}

	/**
	 * Checks the user's status as a page_admin and their current setup step
	 * 
	 * @return	array
	 */
	public function setup_next_step(){
		
		//check to see if user is authenticated with VibeCompass
		if(!$vc_user = $this->CI->session->userdata('vc_user'))
			return array('success' => false,
							'message' => 'user not authenticated');
			
		$vc_user = json_decode($vc_user);
		
		//check to make sure fan_page_id set properly
		if(!$fan_page_id = $this->CI->input->post('fan_page_id'))
			return array('success' => false,
							'message' => 'no fan_page_id specified');
							
		//check to make sure user is an admin of this fan_page
		if(!($this->page_admin && in_array($fan_page_id, $this->page_admin->pages)))
			return array('success' => false,
							'message' => 'user not authorized to manage this page');
		
		/* -------------------- Perform operation for current setup step ----------------- */
		$step = $this->_get_setup_step($fan_page_id);
		
		switch($step){
			case 0:
				
				$this->CI->load->model('model_teams', 'teams', true);
				$data['cities'] = $this->CI->teams->retrieve_team_cities();
				$view_html = $this->CI->load->view('facebook/page/setup_steps/view_setup_step_0', $data, true);
				
				break;
			case 1:
				
				$data['page_data'] = $this->page_data;
				$view_html = $this->CI->load->view('facebook/page/setup_steps/view_setup_step_1', $data, true);
				
				break;
			case 2:
				
				$data['page_data'] = $this->page_data;
				$view_html = $this->CI->load->view('facebook/page/setup_steps/view_setup_step_2', $data, true);
				
				break;
			default:
				
				return array('success' => false,
								'message' => 'an unknown error has occured');
								
				break;
		}
		
		$response = array('success' => true,
							'message' => $view_html);
		/* -------------------- End perform operation for current setup step ----------------- */
		
		//increment the step & save to session
//		$this->_increment_setup_step($fan_page_id);
		
		return $response;
		
	}

	/**
	 * Processes user input for each step in the page setup process
	 * 
	 * @return	array
	 */
	public function step_submit(){
		
		//check to make sure fan_page_id set properly
		if(!$fan_page_id = $this->CI->input->post('fan_page_id'))
			return array('success' => false,
							'message' => 'no fan_page_id specified');
		
		$step = $this->_get_setup_step($fan_page_id);
		
		switch($step){
			case 0:
				//basic information, team-name + venues
				return $this->_step_submit_0();
				break;
			case 1:
				//guest lists for team-venues
				return $this->_step_submit_1();
				break;
			case 2:
				//add promoters to team
				return $this->_step_submit_2();
				break;
			default:
				return array('success' => false,
								'message' => 'unknown error has occured');
				break;
		}
		
	}
	
	/**
	 * First step in the page-admin setup process,
	 * save page information in 'teams' table and update
	 * 	users
	 * 	managers_teams
	 *  teams
	 *  team_venues
	 * 
	 * @return	array
	 */
	private function _step_submit_0(){
		
		// -------------- Validate form submission data ------------------- //
		$fan_page_id = $this->CI->input->post('fan_page_id');
		$venues = $this->CI->input->post('venues');
		
		$this->CI->load->library('form_validation');

		$this->CI->form_validation->set_rules('team_name', 'Team Name', 'required|trim');
		$this->CI->form_validation->set_rules('team_primary_city', 'Team Primary City', 'required');
		$this->CI->form_validation->set_rules('team_description', 'Team Description', 'required|trim');
		$this->CI->form_validation->set_rules('venues', 'Venues', 'required');

		if($this->CI->form_validation->run() == FALSE){
			//missing form fields
			return array('success' => false,
							'message' => 'Please fill out all the form fields');
		}
		
		//limit 20 venues per team for initial setup
		if(count($venues) > 10){
			return array('success' => false,
							'message' => 'Limit of 10 venues');
		}
		
		//validate all venues
		foreach($venues as $key => &$venue){
			
			/*
			city
			description
			name
			state
			street_address
			zip
			 * */
			
	//		if(!isset($venue['name']))
			
	//		if(!isset($venue['description']))
			
			//strip extra whitespace from ends and within string
			$venue['name'] = trim(preg_replace('/\s\s+/', ' ', $venue['name']));			
			$venue['description'] = trim(preg_replace('/\s\s+/', ' ', $venue['description']));
			$venue['street_address'] = trim(preg_replace('/\s\s+/', ' ', $venue['street_address']));			
			$venue['city'] = trim(preg_replace('/\s\s+/', ' ', $venue['city']));
			$venue['state'] = trim(preg_replace('/\s\s+/', ' ', $venue['state']));
			$venue['zip'] = trim(preg_replace('/\s\s+/', ' ', $venue['zip']));
			
			//No alpha-numeric characters
			if(preg_match('~[^a-z0-9 ]~i', $venue['city']) 
				|| preg_match('~[^a-z0-9 ]~i', $venue['state']) 
				|| preg_match('~[^a-z0-9 ]~i', $venue['zip'])){
					
					return array('success' => false,
									'message' => 'City, State and Zip must contain only alphanumeric characters',
									'venue_id' => $key);	
					
			}
				
			//verify lengths of important fields
			if(strlen($venue['name']) == 0)
				return array('success' => false,
								'message' => 'Venue name must not be blank',
								'venue_id' => $key);
			
			if(strlen($venue['name']) > 255)
				return array('success' => false,
								'message' => 'Venue name must not exceed 255 characters',
								'venue_id' => $key);
			
			if(strlen($venue['description']) == 0)
				return array('success' => false,
								'message' => 'Venue description must not be blank',
								'venue_id' => $key);
			
			if(strlen($venue['description']) > 2000)
				return array('success' => false,
								'message' => 'Venue description must not exceed 2000 characters',
								'venue_id' => $key);

			if(strlen($venue['street_address']) == 0)
				return array('success' => false,
								'message' => 'Venue street address must not be blank',
								'venue_id' => $key);
								
			if(strlen($venue['city']) == 0)
				return array('success' => false,
								'message' => 'Venue city must not be blank',
								'venue_id' => $key);
								
			if(strlen($venue['state']) == 0)
				return array('success' => false,
								'message' => 'Venue state must not be blank',
								'venue_id' => $key);
								
			if(strlen($venue['zip']) == 0)
				return array('success' => false,
								'message' => 'Venue zip must not be blank',
								'venue_id' => $key);
								
			if(!array_key_exists($venue['state'], $this->CI->config->item('states')))
				return array('success' => false,
								'message' => 'Invalid state or province',
								'venue_id' => $key);
				
		}
		// -------------- End validate form submission data ------------------- //
		
		//add this fan_page to the 'teams' table along with name and description (completed_setup == 0)
		$this->CI->load->model('model_teams', 'teams', true);
		$this->CI->teams->create_team($this->CI->input->post());
		
		//update the current vc_user's record in 'users' to indicate they are a manager and add record
			//to managers_teams
		$this->CI->load->model('model_users', 'users', true);
		$vc_user = json_decode($this->CI->session->userdata('vc_user'));
		$this->CI->users->update_user($vc_user->oauth_uid, array('manager' => 1));
			//NOTE: unique index present on oauth_uid and fan_page_id -- prevents duplicates on DB lvl
			
			
		$this->CI->teams->create_managers_teams($vc_user->oauth_uid, $fan_page_id);
		//update session to indicate user is now a manager (if not already set to true)
			$vc_user->manager = true;
			$this->CI->session->set_userdata('vc_user', json_encode($vc_user));
		
		foreach($venues as &$venue){
			$venue['image'] = null;
		}
		unset($venue);
		
		//add venues to team_venues
		$this->CI->teams->create_team_venues($fan_page_id, $venues);
		
		//increment setup step
		$this->_increment_setup_step($fan_page_id);
				
		return array('success' => true,
						'message' => 'step 1 completed successfully');
	}

	/**
	 * Adds generic guest lists to each venue && checks for step completion
	 * 
	 * @return	array
	 */
	private function _step_submit_1(){
		
		//vc_sub_method == 'add_guest_list' || 'complete_step'
		
		switch($this->CI->input->post('vc_sub_method')){
			case 'guest_list_add':
				
				//verify inputs and that team_venues_id submitted belongs to this team
				$list_name = $this->CI->input->post('list_name');
				$list_weekday = $this->CI->input->post('list_weekday');
				$list_auto_approve = $this->CI->input->post('list_auto_approve');
				$list_auto_approve = ($list_auto_approve == 'true') ? 1 : 0;
				$team_venue_id = $this->CI->input->post('team_venue_id');
								
				//verify that $team_venue_id belongs to this team
				$verify_match = false;
				foreach($this->page_data->team_venues as $venue){
					if($team_venue_id == $venue->team_venue_id){
						$verify_match = true;
						break;
					}
				}
				
				if(!$verify_match)
					return array('success' => false,
									'message' => 'Unknown error has occured');
				
				$list_name = trim(preg_replace('/\s\s+/', ' ', $list_name));
			
				if(preg_match('~[^a-z0-9 ]~i', $list_name))
					return array('success' => false,
									'message' => 'List name must contain only alphanumeric characters');	
						
				if(strlen($list_name) == 0)
					return array('success' => false,
									'message' => 'List name must not be blank');
				
				if(strlen($list_name) > 255)
					return array('success' => false,
									'message' => 'List name must not exceed 255 characters');
									
				if($list_weekday === false)
					return array('success' => false,
									'message' => 'List weekday must be set');
				
				if(intval($list_weekday) < 0 || intval($list_weekday) > 6)
					return array('success' => false,
									'message' => 'Invalid list weekday');	
				
				//verify that there isn't already an existing guest list at this venue/day
				switch(intval($list_weekday)){
					case 0:
						$list_weekday = 'mondays';
						break;
					case 1:
						$list_weekday = 'tuesdays';
						break;
					case 2:
						$list_weekday = 'wednesdays';
						break;
					case 3:
						$list_weekday = 'thursdays';
						break;
					case 4:
						$list_weekday = 'fridays';
						break;
					case 5:
						$list_weekday = 'saturdays';
						break;
					case 6:
						$list_weekday = 'sundays';
						break;
				}
				
				$this->CI->load->model('model_team_guest_lists', 'team_guest_lists', true);
				$guest_list = $this->CI->team_guest_lists->retrieve_team_guest_lists_authorizations(array(), array('team_venue_id' => $team_venue_id,
																													'weekday' => $list_weekday));
				if($guest_list)
					return array('success' => false, 'message' => 'You already have a guest list at this venue on ' . $list_weekday);
				
				//add record to teams_guest_list_authorizations
				$result = $this->CI->team_guest_lists->create_team_guest_list_authorization(array(
																								'team_venue_id' => $team_venue_id,
																								'day' => $list_weekday,
																								'name' => $list_name,
																								'create_time' => time(),
																								'auto_approve' => $list_auto_approve
																							//	'hash_id' => hash('')
																								));
								
				
				//return success message
				if($result){
					$success_message = array(
											'list_name' => $list_name,
											'list_weekday' => $list_weekday,
											'auto_approve' => $list_auto_approve
											);
					
					return array('success' => true,
									'message' => $success_message);
				}else{
					return array('success' => false,
									'message' => 'unknown error');
				}
					
				
				break;
			case 'complete_step':
								
				$this->CI->load->model('model_team_guest_lists', 'team_guest_lists', true);
				$team_guest_list_authorizations = $this->CI->team_guest_lists->retrieve_team_guest_lists_authorizations(array(), array('fan_page_id' => $this->page_data->team->team_fan_page_id));
								
				if(!$team_guest_list_authorizations)
					return array('success' => false,
									'message' => 'You must add at least one guest list to all of your team\'s venues.');
				
				//simplify list of guest list venues
				foreach($team_guest_list_authorizations as &$tgla){
					$tgla = $tgla->tgla_team_venue_id;
				}
								
				//simplify list of venues
				$venues_temp = array();
				foreach($this->page_data->team_venues as $venue){
					$venues_temp[] = $venue->team_venue_id;
				}
				
				foreach($venues_temp as $venue){
					
					//does every venue have at least one guest list?
					if(!in_array($venue, $team_guest_list_authorizations)){
						return array('success' => false,
										'message' => 'You must add at least one guest list to all of your team\'s venues.');
					}
					
				}
				
				//This step is now performed explicitly from admin panel				
				//set completed_setup == 1
				$this->CI->load->model('model_teams', 'teams', true);
		//		$this->CI->teams->update_team($this->page_data->team->team_fan_page_id, array('completed_setup' => 1));
				
				$this->CI->teams->update_team_add_piwik($this->page_data->team->team_fan_page_id);
				
				//increment setup step
				$this->_increment_setup_step($this->page_data->team->team_fan_page_id);
								
				//return success
				return array('success' => true);
				
				break;
			default:
				return array('success' => false,
								'message' => 'unknown vc_sub_method');
				break;
		}
		
	}

	/**
	 * All functions related to adding promoters to a team
	 * 
	 * @return	null
	 */
	private function _step_submit_2(){
		
		switch($this->CI->input->post('vc_sub_method')){
			
			/*
			case 'promoters_add':
				
				$recipients = $this->CI->input->post('recipients');
				$recipients = explode(',', $recipients);
				
				//make each user a promoter for this venue / invite users to be promoters
				$this->CI->load->model('model_users_promoters', 'users_promoters', true);
				foreach($recipients as $recipient){
					$this->CI->users_promoters->create_promoter($recipient, array($this->page_data->team->team_fan_page_id));
				}
				
				return array('success' => true,
								'message' => json_encode($recipients));
				
				break;
			 * */
			
			case 'complete_step':
				
				//no complete_step action?
				
				break;
			default:
				return array('success' => false,
								'message' => 'unknown vc_sub_method');
				break;
		}
		
	}
	
	# ----------------------------------------------------------------------------------- #
	#	END TEAM SETUP METHODS															  #
	#		The following methods are related to user guest list join requests			  #
	# ----------------------------------------------------------------------------------- #
	
	/**
	 * Retrieves all the generic guest lists for this team's facebook page
	 * 
	 * @param	bool (Retrieve current guest list and members?)
	 * @return	array
	 */
	function retrieve_page_guest_lists($retrieve_members = false){
		
		
		
		
		$this->CI->load->model('model_team_guest_lists', 'team_guest_lists', true);
		$team_guest_lists = $this->CI->team_guest_lists->retrieve_team_guest_lists_authorizations(array(), 
																array('fan_page_id' => $this->page_data->team->team_fan_page_id,
																		'deactivated' => true));
		
		//query database for each authorized guest list and see if one exists for this week
		if($retrieve_members){
			
		}
		
		return $team_guest_lists;
		
		
		
		
		
	}
	
	
	/**
	 * Retrieve all the venues that are associated with this team
	 * 
	 * @return	array
	 */
	function retrieve_team_venues($team_fan_page_id = false){
		$this->CI->load->model('model_users_managers', 'users_managers', true);
		if($team_fan_page_id)
			$team_venues = $this->CI->users_managers->retrieve_team_venues($team_fan_page_id);
		else
			$team_venues = $this->CI->users_managers->retrieve_team_venues($this->page_data->team->team_fan_page_id);
		
		
		foreach($team_venues as &$tv){
			
			$sql = "SELECT DISTINCT
						
							up.id					as up_id,
							up.last_login_time		as up_last_login_time,
							up.public_identifier	as up_public_identifier,
							up.biography			as up_biography,
							up.profile_image		as up_profile_image,
							c.name					as c_name,
							c.url_identifier		as c_url_identifier,
							u.full_name				as u_full_name,
							u.first_name			as u_first_name,
							u.last_name				as u_last_name,
							t.name 					as t_name
						
						FROM 	promoters_guest_list_authorizations pgla 
						
						JOIN 	users_promoters up 
						ON 		pgla.user_promoter_id = up.id
						
						JOIN	users u 
						ON 		up.users_oauth_uid = u.oauth_uid
						
						JOIN 	promoters_teams pt
						ON 		pt.promoter_id = up.id
						
						JOIN	teams t 
						ON 		pt.team_fan_page_id = t.fan_page_id
						
						JOIN	cities c 
						ON		t.city_id = c.id
						
						JOIN 	team_venues tv 
						ON 		pgla.team_venue_id = tv.id
						
						WHERE	t.completed_setup = 1
						AND 	pt.approved = 1 
						AND 	pt.banned = 0 
						AND 	pt.quit = 0
						AND 	tv.banned = 0
						AND 	up.completed_setup = 1
						AND 	up.banned = 0
						AND 	pgla.team_venue_id = ?";
			$query = $this->CI->db->query($sql, array($tv->tv_id));
			$tv->venue_promoters = $query->result();
			
		}
		
		return $team_venues;
		
	}
	
	/**
	 * Called when vc_user attempts to join a guest list at a venue
	 * 
	 * @return	array
	 */
	function team_guest_list_join_request($guest_list_id 			= false,
											$entourage 				= false,
											$table_request 			= false,
											$text_message 			= false,
											$request_message 		= false,
											$share_facebook 		= false,
											$phone_number 			= false,
											$phone_carrier 			= false,
											$team_fan_page_id 			= false,
											$date_check_override 	= false,
											$approve_override 		= false,
											$vc_user				= false,
											$table_min_spend		= false){
				
		$guest_list_id 		= (!$guest_list_id) 	? $this->CI->input->post('gl_id') 			: $guest_list_id;
		$entourage 			= (!$entourage) 		? $this->CI->input->post('entourage') 		: $entourage;
		$table_request 		= (!$table_request) 	? $this->CI->input->post('table_request') 	: $table_request;
		$text_message 		= (!$text_message) 		? $this->CI->input->post('text_message') 	: $text_message;
		$request_message 	= (!$request_message) 	? $this->CI->input->post('request_message') : $request_message;
		$share_facebook 	= (!$share_facebook) 	? $this->CI->input->post('facebook_share') 	: $share_facebook;
		$phone_number 		= (!$phone_number) 		? $this->CI->input->post('phone_number') 	: $phone_number;
		$phone_carrier 		= (!$phone_carrier) 	? $this->CI->input->post('phone_carrier') 	: $phone_carrier;
		$team_fan_page_id 	= (!$team_fan_page_id) 	? $this->CI->input->post('fan_page_id') 	: $team_venue_id;
		$table_min_spend	= (!$table_min_spend) 	? $this->CI->input->post('table_min_spend') : $table_min_spend;
		
		if(!$table_min_spend)
			$table_min_spend = 0;
		
		$table_request 		= ($table_request	== 'true' || $table_request 	== 1 || $table_request 		== '1') ? 1 : 0;
		$text_message 		= ($text_message 	== 'true' || $text_message 		== 1 || $text_message 		== '1') ? 1 : 0;
		$request_message 	= ($request_message == 'true' || $request_message 	== 1 || $request_message 	== '1') ? 1 : 0;
		$share_facebook 	= ($share_facebook 	== 'true' || $share_facebook 	== 1 || $share_facebook 	== '1') ? 1 : 0;
		
		/*
		 
		 array(9) {
  ["ci_csrf_token"]=>
  string(32) "84ffa18d725441b4a8327282be2d1de9"
  ["vc_method"]=>
  string(28) "team_guest_list_join_request"
  ["gl_id"]=>
  string(2) "87"
  ["table_request"]=>
  string(5) "false"
  ["facebook_share"]=>
  string(4) "true"
  ["request_message"]=>
  string(0) ""
  ["text_message"]=>
  string(5) "false"
  ["phone_number"]=>
  string(0) ""
  ["phone_carrier"]=>
  string(7) "invalid"
} 
		 
		 * */
				
		//sanitize phone_carrier
		switch($phone_carrier){
			case 0:
				$phone_carrier = 'att';
				break;
			case 1:
				$phone_carrier = 'verizon';
				break;
			case 2:
				$phone_carrier = 'tmobile';
				break;
			case 3:
				$phone_carrier = 'sprint';
				break;
			default:
		//		$text_message = false;
		}
		
		//check to make sure user is authenticated
		if(!$vc_user){
			if(!$vc_user = $this->CI->session->userdata('vc_user')){
				return array('success' => false,
							 'message' => 'User not authenticated');
			}
			$vc_user = json_decode($vc_user);
		}
		
		//attempt to add user to guest list
		$this->CI->load->model('model_team_guest_lists', 'team_guest_lists', true);
		return $this->CI->team_guest_lists->create_team_guest_list_reservation($vc_user->oauth_uid,
																				$team_fan_page_id,
																				$entourage, 
																				$guest_list_id, 
																				$table_request,
																				$share_facebook,
																				$text_message,
																				
																				
																				$request_message,
																				
																				
																				$phone_number,
																				$phone_carrier,
																				$date_check_override,
																				$approve_override,
																				$table_min_spend);
		
	}
}
/* End of file library_facebook_application.php */
/* Location: ./application/libraries/library_facebook_application.php */