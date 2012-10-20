<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class library_admin_managers{
	
	private $CI;
	public $team;
	
	/**
	 * Class constructor
	 * 
	 * @return	library_promoters
	 */
	public function __construct(){
		$this->CI =& get_instance();
	}
	
	/**
	 * Load current team for manager
	 * 
	 * @param	int manager oauth_uid
	 * @return	null
	 */
	public function initialize($user_oauth_uid){
		
		//Look up promoter information based on public identifier, if promoter does not exist throw 404
		$this->CI->load->model('model_users_managers', 'users_managers', true);
		$team = $this->CI->users_managers->retrieve_manager_team($user_oauth_uid);

		//add team object as property of this object and make globally available to all views
		$this->team = $team;
		$this->CI->load->vars('team', $team);
			
	}

	
	###############################################################################################
	#	BEGIN controllers/admin/promoters.php HELPER METHODS									  #
	###############################################################################################	
	
	/**
	 * Helper function, invite hosts to join team
	 * 
	 * @return	array
	 */
	function invitation_host_create(){
		
		if(!$vc_user = $this->CI->session->userdata('vc_user')){
			//this shouldn't happen
			return array('success' => false,
							'message' => 'Unknown error');
		}
		$vc_user = json_decode($vc_user);
		
		if(!$users = $this->CI->input->post('users')){
			return array('success' => false, 'message' => 'No request users');
		}
		
		$this->CI->load->model('model_teams', 'teams', true);
		
		$team_hosts = $this->CI->teams->retrieve_team_hosts($this->team->team_fan_page_id);
		
		foreach($users as $user){
			
			//verify that there isn't already a pending invitation to this user
			if($invitation = $this->CI->teams->retrieve_team_invitation($this->team->team_fan_page_id, $user, 'host')){
				return array('success' => false,
								'message' => 'Invitation already sent to ' . $user);
			}
		
			//check if invited user is already active host for this team
			foreach($team_hosts as $th){
				
				if($th->th_users_oauth_uid == $user && $th->th_quit == '0' && $th->th_banned == '0'){
					return array('success' => false,
									'message' => $user . ' is already an active host with this team');
				}
				
			}
			
		}
		
		//create user invitations
		$this->CI->teams->create_team_invitations($this->team->team_fan_page_id, $users, $vc_user->oauth_uid, 'host');
		
		foreach($users as $user){
			$this->_helper_invitation_user_notify($user);
		}
		
		return array('success' => true,
						'message' => 'Invitations created');
		
	}
	
	/**
	 * Helper function that creates an invitation for a vibecompass user (present or not present in DB)
	 * to be a promoter for a specific team
	 * 
	 * @return 	array
	 */
	function invitation_create($type = 'promoter'){
		
		if(!$vc_user = $this->CI->session->userdata('vc_user')){
			//this shouldn't happen
			return array('success' => false,
							'message' => 'Unknown error');
		}
		$vc_user = json_decode($vc_user);
		
		if(!$users = $this->CI->input->post('users')){
			return array('success' => false, 'message' => 'No request users');
		}
		
		switch($type){
			case 'promoter':
				
				// -----------------------------------------------------------------------------
				$this->CI->load->model('model_teams', 'teams', true);
				$team_promoters = $this->CI->teams->retrieve_team_promoters($this->team->team_fan_page_id);
				
				foreach($users as $user){
					
					//verify that there isn't already a pending invitation to this user
					if($invitation = $this->CI->teams->retrieve_team_invitation($this->team->team_fan_page_id, $user)){
						return array('success' => false,
										'message' => 'Invitation already sent to ' . $user);
					}
				
					//check if invited user is already active promoter for this team
					foreach($team_promoters as $tp){
						
						if($tp->u_oauth_uid == $user){
							return array('success' => false,
											'message' => $user . ' is already an active promoter with this team');
						}
						
					}
					
				}
				
				//create user invitations
				$this->CI->teams->create_team_invitations($this->team->team_fan_page_id, $users, $vc_user->oauth_uid, $type);
				// -----------------------------------------------------------------------------
				
				break;
			case 'manager':
				break;
			case 'host':
				break;
		}
		
		foreach($users as $user){
			$this->_helper_invitation_user_notify($user);
		}
		
		return array('success' => true,
						'message' => 'Invitations created');
		
	}

	/**
	 * Retrieves all the venues that this team has configured
	 * 
	 * @return 	array
	 */
	function retrieve_team_venues(){
		
		$this->CI->load->model('model_users_managers', 'users_managers', true);
		return $this->CI->users_managers->retrieve_team_venues($this->team->team_fan_page_id);
		
	}

	/**
	 * Removes a promoter from a team by setting 'banned' == 1 in promoters_teams
	 * 
	 * @return 	array
	 */
	function promoter_delete(){
		
		if(!$promoter_id = $this->CI->input->post('promoter_id'))
			return array('success' => false,
							'message' => 'promoter_id not specified');
							
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		$this->CI->users_promoters->update_promoter_ban_team($promoter_id, $this->team->team_fan_page_id);
		
		//delete promoter from piwik
		$this->CI->users_promoters->update_promoter_delete_piwik($promoter_id);
		
		return array('success' => true, 
						'message' => '');
		
	}
	
	/**
	 * Removes a promoter from a team by setting 'banned' == 1 in promoters_teams
	 * 
	 * @return 	array
	 */
	function host_delete(){
		
		if(!$host_id = $this->CI->input->post('host_id'))
			return array('success' => false,
							'message' => 'host_id not specified');
							
		$this->CI->load->model('model_users_hosts', 'users_hosts', true);
		$this->CI->users_hosts->update_host_ban_team($host_id, $this->team->team_fan_page_id);
				
		return array('success' => true, 
						'message' => '');
		
	}
	
	
	/**
	 * Invoked via AJAX request to create a new team venue
	 * 
	 * @return 	array
	 */
	function create_new_team_venue(){
		
		//venue_street_address
		//venue_city
		//venue_state
		//venue_zip
		//venue_name
		//venue_description
		
		$this->CI->load->library('form_validation');

		$this->CI->form_validation->set_rules('venue_name', 'Venue Name', 'required|trim');
		$this->CI->form_validation->set_rules('venue_street_address', 'Venue Street Address', 'required|trim');
		$this->CI->form_validation->set_rules('venue_city', 'Venue City', 'required|trim');
		$this->CI->form_validation->set_rules('venue_state', 'Venue State', 'required');
		$this->CI->form_validation->set_rules('venue_zip', 'Venue Zip', 'required|trim');
		$this->CI->form_validation->set_rules('venue_description', 'Venue Description', 'required|trim');

		if($this->CI->form_validation->run() == FALSE){
			//missing form fields
			return array('success' => false,
							'message' => 'Please fill out all the form fields');
		}		
		
		$venue_name = $this->CI->input->post('venue_name');
		$venue_description = $this->CI->input->post('venue_description');
		$venue_street_address = $this->CI->input->post('venue_street_address');
		$venue_city = $this->CI->input->post('venue_city');
		$venue_state = $this->CI->input->post('venue_state');
		$venue_zip = $this->CI->input->post('venue_zip');
		$venue_guest_lists = $this->CI->input->post('venue_guest_lists');
		
				
	//	if(!$venue_guest_lists || count($venue_guest_lists) == 0)
	//		return array('success' => false,
	//						'message' => 'Venue must have at least one guest list');
		
		if(is_array($venue_guest_lists))
		foreach($venue_guest_lists as $key => &$vgl){
			
			$vgl = (object)$vgl;
			
			$vgl->description = strip_tags($vgl->description);
			
			//remove excess spaces inside description
			$vgl->name = trim(preg_replace('/\s\s+/', ' ', strip_tags($vgl->name)));
			
			continue;
			
			if(preg_match('~[^a-z0-9 ]~i', $vgl->name))
				return array('success' => false,
								'message' => 'List name must contain only alphanumeric characters',
								'key' => $key);
			
			if(!isset($vgl->name) || strlen($vgl->name) < 5 || strlen($vgl->name) > 30)
				return array('success' => false, 
								'message' => 'List name must be at least 5 characters and less than 30 characters', 
								'key' => $key);
			
			if(!isset($vgl->description) || strlen($vgl->description) == 0)
				return array('success' => false, 
								'message' => 'Enter a description for all guest lists', 
								'key' => $key);
			
			if(!isset($vgl->weekday) || $vgl->weekday < 0 || $vgl->weekday > 6)
				return array('success' => false,
								'message' => 'Invalid Guest List Weekday',
								'key' => $key);
			
			if(!isset($vgl->auto_approve))
				$vgl->auto_approve = 'false';
			
		}

		unset($vgl);

		$list_weekdays = array();
		
	//	foreach($venue_guest_lists as $vgl){
	//		$list_weekdays[] = $vgl->weekday;
	//	}
		
	//	if(count($list_weekdays) != count(array_unique($list_weekdays)))
	//		return array('success' => false,
	//						'message' => 'You have two guest lists at this venue on the same night.');
				
		//strip extra whitespace from ends and within string
		$venue_name 			= strip_tags(trim(preg_replace('/\s\s+/', ' ', $venue_name)));			
		$venue_description 		= strip_tags(trim(preg_replace('/\s\s+/', ' ', $venue_description)));
		$venue_street_address 	= strip_tags(trim(preg_replace('/\s\s+/', ' ', $venue_street_address)));			
		$venue_city 			= strip_tags(trim(preg_replace('/\s\s+/', ' ', $venue_city)));
		$venue_state 			= strip_tags(trim(preg_replace('/\s\s+/', ' ', $venue_state)));
		
		//No alpha-numeric characters
		if(preg_match('~[^a-z0-9 ]~i', $venue_city) 
			|| preg_match('~[^a-z0-9 ]~i', $venue_state)){
				
			return array('success' => false,
							'message' => 'City, and Zip must contain only alphanumeric characters');	
				
		}
		
		//verify lengths of important fields
		if(strlen($venue_name) == 0)
			return array('success' => false,
							'message' => 'Venue name must not be blank');
		
		if(strlen($venue_name) > 255)
			return array('success' => false,
							'message' => 'Venue name must not exceed 255 characters');
							
		if(strlen($venue_description) == 0)
			return array('success' => false,
							'message' => 'Venue description must not be blank');
		
		if(strlen($venue_description) > 2000)
			return array('success' => false,
							'message' => 'Venue description must not exceed 2000 characters');
		
		if(!array_key_exists($venue_state, $this->CI->config->item('states')))
			return array('success' => false,
							'message' => 'Invalid state or province');
							
		if(strlen($venue_city) == 0)
				return array('success' => false,
								'message' => 'Venue city must not be blank');
		
		if(strlen($venue_zip) == 0)
				return array('success' => false,
								'message' => 'Venue zip must not be blank');
		// -------------- End validate form submission data ------------------- //
		
		
		
		//verify venue name isn't already taken --------
		$sql1 = "SELECT
					
					t.city_id 	as t_city_id
					
				FROM 	team_venues tv
				
				JOIN 	teams t 
				ON 		tv.team_fan_page_id = t.fan_page_id
				
				WHERE 	tv.name = ?";
		$query1 = $this->CI->db->query($sql1, array($venue_name));
		$result1 = $query1->result();
		if($result1){
			foreach($result1 as $res){
				if($this->team->c_id == $res->t_city_id)
					return array('success' => false,
							'message' => 'Venue name is already taken in your city');
			}
		}
		unset($result1);
		unset($query1);
		// -------------------------------------------
		
		
		$image_name = null;
		
		//IMAGE Handling
		if($manage_image = $this->CI->session->userdata('manage_image')){			
			$manage_image = json_decode($manage_image);
			
			if(isset($manage_image->image_data)){
				//This user has uploaded an image
				
				$this->CI->load->library('library_image_upload', '', 'image_upload');
				$image_name = $this->CI->image_upload->make_image_live($manage_image->type, $manage_image->image_data->image);
				
			}
			
		}
		
		$this->CI->load->model('model_teams', 'teams', true);
		
		//add venue to team_venues
		$venues[] = array(
						'name' => $venue_name,
						'description' => $venue_description,
						'street_address' => $venue_street_address,
						'state' => $venue_state,
						'city' => $venue_city,
						'zip' => $venue_zip,
						'image' => $image_name
						);
		
		$team_venue_id = $this->CI->teams->create_team_venues($this->team->team_fan_page_id, $venues);
		
		$this->CI->load->model('model_team_guest_lists', 'team_guest_lists', true);
		
		
		//add guest lists to venue
		if(false)
		foreach($venue_guest_lists as $vgl){
			
			switch($vgl->weekday){
				case 0:
					$vgl->weekday = 'mondays';
					break;
				case 1:
					$vgl->weekday = 'tuesdays';
					break;
				case 2:
					$vgl->weekday = 'wednesdays';
					break;
				case 3:
					$vgl->weekday = 'thursdays';
					break;
				case 4:
					$vgl->weekday = 'fridays';
					break;
				case 5:
					$vgl->weekday = 'saturdays';
					break;
				case 6:
					$vgl->weekday = 'sundays';
					break;
			}
			
			$this->CI->team_guest_lists->create_team_guest_list_authorization(array(
																					'team_venue_id' => $team_venue_id,
																					'day' => $vgl->weekday,
																					'name' => $vgl->name,
																					'description' => $vgl->description,
																					'create_time' => time(),
																					'auto_approve' => ($vgl->auto_approve == 'true') ? 1 : 0
																					));
			
		}
		$this->CI->session->unset_userdata('manage_image');
		return array('success' => true, 'message' => '');
		
	}

	/**
	 * Edit the settings for an existing team venue
	 * 
	 * @param	string (team venue id)
	 * @return	array
	 */
	function edit_team_venue($tv_id){
		
		//venue_street_address
		//venue_city
		//venue_state
		//venue_zip
		//venue_name
		//venue_description
		
		$this->CI->load->library('form_validation');

		$this->CI->form_validation->set_rules('venue_street_address', 'Venue Street Address', 'required|trim');
		$this->CI->form_validation->set_rules('venue_city', 'Venue City', 'required|trim');
		$this->CI->form_validation->set_rules('venue_state', 'Venue State', 'required');
		$this->CI->form_validation->set_rules('venue_zip', 'Venue Zip', 'required|trim');
		$this->CI->form_validation->set_rules('venue_description', 'Venue Description', 'required|trim');

		if($this->CI->form_validation->run() == FALSE){
			//missing form fields
			return array('success' => false,
							'message' => 'Please fill out all the form fields');
		}		
		
		$venue_description 		= $this->CI->input->post('venue_description');
		$venue_street_address 	= $this->CI->input->post('venue_street_address');
		$venue_city 			= $this->CI->input->post('venue_city');
		$venue_state 			= $this->CI->input->post('venue_state');
		$venue_zip 				= $this->CI->input->post('venue_zip');
		$venue_guest_lists 		= $this->CI->input->post('venue_guest_lists');
		
		
	//	if(!$venue_guest_lists || count($venue_guest_lists) == 0)
	//		return array('success' => false,
	//						'message' => 'Venue must have at least one guest list');
		
		if($venue_guest_lists)
		foreach($venue_guest_lists as $key => &$vgl){
			
			$vgl = (object)$vgl;
			
			$vgl->tgla_description = strip_tags($vgl->tgla_description);
			
			if(!isset($vgl->tgla_auto_approve))
				$vgl->tgla_auto_approve = 'false';
				
			
			//remove excess spaces inside description
			$vgl->tgla_name = trim(preg_replace('/\s\s+/', ' ', strip_tags($vgl->tgla_name)));
			
			continue;
			
			if(preg_match('~[^a-z0-9 ]~i', $vgl->tgla_name))
				return array('success' => false,
								'message' => 'List name must contain only alphanumeric characters',
								'key' => $key);
			
			if(!isset($vgl->tgla_name) || strlen($vgl->tgla_name) < 5 || strlen($vgl->tgla_name) > 30)
				return array('success' => false, 
								'message' => 'List name must be at least 5 characters and less than 30 characters', 
								'key' => $key);
			
			if(!isset($vgl->tgla_description) || strlen($vgl->tgla_description) == 0)
				return array('success' => false, 
								'message' => 'Enter a description for all guest lists', 
								'key' => $key);
			
			if(!isset($vgl->tgla_weekday) || $vgl->tgla_weekday < 0 || $vgl->tgla_weekday > 6)
				return array('success' => false,
								'message' => 'Invalid Guest List Weekday',
								'key' => $key);
			
			
			
		}

		unset($vgl);

		$list_weekdays = array();
		
		if($venue_guest_lists)
		foreach($venue_guest_lists as $vgl){
			$list_weekdays[] = $vgl->tgla_day;
		}
		
		//TODO.... possibly remove
	//	if(count($list_weekdays) != count(array_unique($list_weekdays)))
	//		return array('success' => false,
	//						'message' => 'You have two guest lists at this venue on the same night.');
				
		//strip extra whitespace from ends and within string
		$venue_description 		= strip_tags(trim(preg_replace('/\s\s+/', ' ', $venue_description)));
		$venue_street_address 	= strip_tags(trim(preg_replace('/\s\s+/', ' ', $venue_street_address)));			
		$venue_city 			= strip_tags(trim(preg_replace('/\s\s+/', ' ', $venue_city)));
		$venue_state 			= strip_tags(trim(preg_replace('/\s\s+/', ' ', $venue_state)));
		
		//No alpha-numeric characters
		if(preg_match('~[^a-z0-9 ]~i', $venue_city)
			|| preg_match('~[^a-z0-9 ]~i', $venue_state)){
				
			return array('success' => false,
							'message' => 'City, and Zip must contain only alphanumeric characters');	
				
		}
		
		//verify lengths of important fields
	//	if(strlen($venue_name) == 0)
	//		return array('success' => false,
	//						'message' => 'Venue name must not be blank');
		
	//	if(strlen($venue_name) > 255)
	//		return array('success' => false,
	//						'message' => 'Venue name must not exceed 255 characters');
							
		if(strlen($venue_description) == 0)
			return array('success' => false,
							'message' => 'Venue description must not be blank');
		
		if(strlen($venue_description) > 2000)
			return array('success' => false,
							'message' => 'Venue description must not exceed 2000 characters');
		
		if(!array_key_exists($venue_state, $this->CI->config->item('states')))
			return array('success' => false,
							'message' => 'Invalid state or province');
							
		if(strlen($venue_city) == 0)
				return array('success' => false,
								'message' => 'Venue city must not be blank');
		
		if(strlen($venue_zip) == 0)
				return array('success' => false,
								'message' => 'Venue zip must not be blank');
		// -------------- End validate form submission data ------------------- //
		

		
		
		
		$image_name = null;
		
		//IMAGE Handling
		if($manage_image = $this->CI->session->userdata('manage_image')){			
			$manage_image = json_decode($manage_image);
			
			if(isset($manage_image->image_data)){
				//This user has uploaded an image
				
				$this->CI->load->library('library_image_upload', '', 'image_upload');
				$image_name = $this->CI->image_upload->make_image_live($manage_image->type, $manage_image->image_data->image);
				
			}
			
		}
		
		$this->CI->load->model('model_teams', 'teams', true);
		
		$venue = array(
						'description' 		=> $venue_description,
						'street_address' 	=> $venue_street_address,
						'state' 			=> $venue_state,
						'city' 				=> $venue_city,
						'zip' 				=> $venue_zip,
						'image' 			=> $image_name
						);
		$this->CI->teams->edit_team_venue($this->team->team_fan_page_id, $tv_id, $venue);
		
		
		
		//foreach guest list determine if new, or if existing. If existing, edit details.
		
		
		
		
				/*
		$this->CI->load->model('model_team_guest_lists', 'team_guest_lists', true);
		//add guest lists to venue
		if($venue_guest_lists)
		foreach($venue_guest_lists as $vgl){
			
			switch($vgl->tgla_day){
				case 0:
					$vgl->tgla_day = 'mondays';
					break;
				case 1:
					$vgl->tgla_day = 'tuesdays';
					break;
				case 2:
					$vgl->tgla_day = 'wednesdays';
					break;
				case 3:
					$vgl->tgla_day = 'thursdays';
					break;
				case 4:
					$vgl->tgla_day = 'fridays';
					break;
				case 5:
					$vgl->tgla_day = 'saturdays';
					break;
				case 6:
					$vgl->tgla_day = 'sundays';
					break;
			}
			
			$this->CI->team_guest_lists->create_team_guest_list_authorization(array(
																					'team_venue_id' => $team_venue_id,
																					'day' => $vgl->tgla_day,
																					'name' => $vgl->tgla_name,
																					'description' => $vgl->tgla_description,
																					'create_time' => time(),
																					'auto_approve' => ($vgl->tgla_auto_approve == 'true') ? 1 : 0
																					));
			
		}*/
		$this->CI->session->unset_userdata('manage_image');
		return array('success' => true, 'message' => '');
		
	}

	/**
	 * Notify a user when they've been invited to join a team
	 * 
	 * @param	string
	 * @param	array
	 * @return 	null
	 */
	private function _helper_invitation_user_notify($user_oauth_uid){
		
		$this->CI->load->library('pusher');
		$this->CI->load->model('model_users', 'users', true);
		
		$invitations = $this->CI->users->retrieve_user_invitations($user_oauth_uid);
		
		$data = array(
			'notification_type' => 'invitation',
			'all_invitations' => $invitations
		);
					
		$this->CI->pusher->trigger('private-vc-' . $user_oauth_uid, 'notification', $data);
					
	}
	
	###############################################################################################
	#	BEGIN controllers/promoters.php HELPER METHODS											  #
	###############################################################################################

}
/* End of file library_admin_managers.php */
/* Location: ./application/libraries/library_admin_managers.php */