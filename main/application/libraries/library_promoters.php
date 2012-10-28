<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Library of helper functions for use with the promoters controller. Intended to be 
 * loaded from every url of the promoters controller that showcases a specific promoter.
 * 
 */
class library_promoters{
	
	private $CI;
	public $promoter;
	
	/**
	 * Class constructor
	 * 
	 * @return	library_promoters
	 */
	public function __construct(){
		$this->CI =& get_instance();
	}
	
	/**
	 * Loads all data common to any page that showcases a specific promoter and makes it available
	 * globally for all views.
	 * 
	 * @param	string (promoter public identifier)
	 * @param	bool (is this being initalized from the admin panel)
	 * @return	null
	 */
	public function initialize($options, $admin_panel = false, $city = false){
		
		//remove requirement to have completed setup if this is being loaded via the admin panel
		if($admin_panel)
			$data = array('completed_setup' => ''); // 'banned' => '', 'quit' => '', up_banned => ''
		else
			$data = array('city' => $city);
		
		//Look up promoter information based on public identifier, if promoter does not exist throw 404
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		if(!$promoter = $this->CI->users_promoters->retrieve_promoter($options, $data)){
			
			if(!$admin_panel){
				//promoter doesn't exist
				show_404('Promoter does not exist');
				die();
			}
			
		}
			
	//	$promoter = $this->CI->users_promoters->retrieve_promoter($options, array());
		
		//add additional information to promoter object
		$promoter->profile_image_complete_url = $this->CI->config->item('s3_promoter_picture_base_url') 
													. $promoter->up_profile_image
													. '_p' 
													. $this->CI->config->item('img_file_ext');
	
	//dumping anything here breaks a lot of stuff btw												
	//	Kint::dump($promoter);
		
		//add promoter object as property of this object and make globally available to all views
		$this->promoter = $promoter;
		$this->CI->load->vars('promoter', $promoter);
					
		//TODO: FIX	
		//load the piwik site id of this promoter on this page for tracking purposes
		$this->CI->load->vars('additional_sites_ids', array($promoter->up_piwik_id_site));
	}

	/**
	 * Creates a new promoter-event
	 * 
	 * @return 	array
	 */
	function create_new_event(){
				
		$this->CI->load->library('form_validation');
		
		$this->CI->form_validation->set_rules('venue', '', 'required');
		$this->CI->form_validation->set_rules('event_name', '', 'trim|required');
		$this->CI->form_validation->set_rules('event_date', '', 'trim|required');
		$this->CI->form_validation->set_rules('event_description', '', 'trim|required');
		$this->CI->form_validation->set_rules('auto_approve', '', 'required');
		$this->CI->form_validation->set_rules('guest_list_override', '', 'required');
		
		//guest_list_auto_approve
		
		if($this->CI->form_validation->run() == false){
			return array('success' => false,
							'message' => 'Please fill out all fields.');
		}
		
		$venue = $this->CI->input->post('venue');
		$event_name = $this->CI->input->post('event_name');
		$event_date = $this->CI->input->post('event_date');
		$event_description = $this->CI->input->post('event_description');
		$auto_approve = $this->CI->input->post('auto_approve');
		$guest_list_override = $this->CI->input->post('guest_list_override');
		
		$guest_list_override = ($guest_list_override == 'true') ? 1 : 0;
		$auto_approve = ($auto_approve == 'true') ? 1 : 0;
		
		//make sure gl_name is > 5 characters && < 30 && no special characters
		$event_name = trim(preg_replace('/\s\s+/', ' ', $event_name));
		$event_description = trim(preg_replace('/\s\s+/', ' ', $event_description));
		
		if(preg_match('~[^a-z0-9 ]~i', $event_name))
			return array('success' => false,
							'message' => 'Event names must contain only alphanumeric characters');	
						
		if(strlen($event_name) < 5)
			return array('success' => false,
							'message' => 'Event name must be more than 5 characters');
		
		if(strlen($event_name) > 30)
			return array('success' => false,
							'message' => 'Event name must not exceed 30 characters');

		if(strlen($event_description) > 2000)
			return array('success' => false,
							'message' => 'Event description must not exceed 2000 characters');
		
		//convert date to ISO standard
		$event_date = date('Y-m-d', strtotime($event_date));
		
		//validate date is in range [tomorrow - 6months]
		if(strtotime($event_date) < strtotime(date('Y-m-d', time())))
			return array('success' => false, //Date before
							'message' => 'Events can not occur in the past');

		if(strtotime($event_date) > strtotime(date('Y-m-d', strtotime('+6 month'))))
			return array('success' => false, //Date before
							'message' => 'Events can not be created more than six months in advance');
							
		$image_name = null;
		
		//IMAGE Handling
		if($manage_image = $this->CI->session->flashdata('manage_image')){			
			$manage_image = json_decode($manage_image);
			
			if(isset($manage_image->image_data)){
				//This user has uploaded an image
				
				$this->CI->load->library('library_image_upload', '', 'image_upload');
				$image_name = $this->CI->image_upload->make_image_live($manage_image->type, $manage_image->image_data->image);
				
			}
			
		}
	
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		return $this->CI->users_promoters->create_promoter_event($this->promoter->up_id, $venue, $event_name, $event_date, $event_description, $auto_approve, $guest_list_override);
		
		
	}

	/**
	 * Creates a new promoter_guest_list_authorization for a request from the promoter admin panel
	 * 
	 * @return 	array
	 */
	function create_promoter_guest_list_authorization(){
		
		$this->CI->load->library('form_validation');
		
		$this->CI->form_validation->set_rules('venue', 				'', 'required');
		$this->CI->form_validation->set_rules('auto_approve', 		'', 'required');
		$this->CI->form_validation->set_rules('auto_promote', 		'', 'required');
		$this->CI->form_validation->set_rules('gl_name', 			'', 'required');
		$this->CI->form_validation->set_rules('weekday', 			'', 'trim|required');
		$this->CI->form_validation->set_rules('gl_description', 	'', 'trim|required');
		
		
		
		$this->CI->form_validation->set_rules('min_age', 			'', 'trim|required');
		$this->CI->form_validation->set_rules('regular_cover', 		'', 'trim|required');
		$this->CI->form_validation->set_rules('gl_cover', 			'', 'trim|required');
		$this->CI->form_validation->set_rules('door_opens', 		'', 'trim|required');
		$this->CI->form_validation->set_rules('door_closes', 		'', 'trim|required');
		$this->CI->form_validation->set_rules('auto_promote', 		'', 'trim|required');
		$this->CI->form_validation->set_rules('additional_info_1', 	'', 'trim');
		$this->CI->form_validation->set_rules('additional_info_2', 	'', 'trim');
		$this->CI->form_validation->set_rules('additional_info_3', 	'', 'trim');
		
		
		
		
		//guest_list_auto_approve
		
		if($this->CI->form_validation->run() == false){
			return array('success' => false,
							'message' => 'Please fill out all fields.');
		}
		
		//Force all guest lists to have images
		//IMAGE Handling
		if($manage_image = $this->CI->session->flashdata('manage_image')){			
			$manage_image = json_decode($manage_image);
			
			if(!isset($manage_image->image_data) || !$manage_image->image_data){
				//This user has NOT uploaded an image
				
				return array('success' => false,
								'message' => 'You must upload an image.');
				
			}
			
		}
		
		$team_venue_id 		= $this->CI->input->post('venue');
		$auto_approve 		= $this->CI->input->post('auto_approve');
		$gl_name 			= $this->CI->input->post('gl_name');
		$gl_description 	= $this->CI->input->post('gl_description');
		$weekday 			= $this->CI->input->post('weekday');
		$auto_promote 		= $this->CI->input->post('auto_promote');
		
		
		$gl_cover			= $this->CI->input->post('gl_cover');
		$regular_cover		= $this->CI->input->post('regular_cover');
		$door_opens			= $this->CI->input->post('door_opens');
		$door_closes		= $this->CI->input->post('door_closes');
		$min_age			= $this->CI->input->post('min_age');
		$additional_info_1	= strip_tags($this->CI->input->post('additional_info_1'));
		$additional_info_2	= strip_tags($this->CI->input->post('additional_info_2'));
		$additional_info_3	= strip_tags($this->CI->input->post('additional_info_3'));
		
		switch($weekday){
			case 0:
				$weekday = 'mondays';
				break;
			case 1:
				$weekday = 'tuesdays';
				break;
			case 2:
				$weekday = 'wednesdays';
				break;
			case 3:
				$weekday = 'thursdays';
				break;
			case 4:
				$weekday = 'fridays';
				break;
			case 5:
				$weekday = 'saturdays';
				break;
			case 6:
				$weekday = 'sundays';
				break;
			default:
				return array('success' => false,
							'message' => 'Invalid weekday');	
		}
		
		//make sure gl_name is > 5 characters && < 30 && no special characters
		$gl_name = trim(preg_replace('/\s\s+/', ' ', $gl_name));
		
		if(preg_match('~[^a-z0-9 ]~i', $gl_name))
			return array('success' => false,
							'message' => 'List name must contain only alphanumeric characters');	
						
		if(strlen($gl_name) < 5)
			return array('success' => false,
							'message' => 'List name must be more than 5 characters');
		
		if(strlen($gl_name) > 30)
			return array('success' => false,
							'message' => 'List name must not exceed 30 characters');

		if(strlen($gl_description) > 2000)
			return array('success' => false,
							'message' => 'List description must not exceed 2000 characters');
			
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		return $this->CI->users_promoters->create_promoter_guest_list_authorization($this->promoter->up_id, $team_venue_id, $weekday, $gl_name, $gl_description, $auto_approve, $gl_cover, $regular_cover, $door_opens, $door_closes, $min_age, $additional_info_1, $additional_info_2, $additional_info_3, $auto_promote);
		
	}

	/**
	 * 
	 */
	function edit_promoter_guest_list_authorization(){
		
		
		$this->CI->load->library('form_validation');
			
		
		
		$this->CI->form_validation->set_rules('gl_description', 	'', 'trim|required');		
		$this->CI->form_validation->set_rules('min_age', 			'', 'trim|required');
		$this->CI->form_validation->set_rules('regular_cover', 		'', 'trim|required');
		$this->CI->form_validation->set_rules('gl_cover', 			'', 'trim|required');
		$this->CI->form_validation->set_rules('door_opens', 		'', 'trim|required');
		$this->CI->form_validation->set_rules('door_closes', 		'', 'trim|required');
		$this->CI->form_validation->set_rules('auto_promote', 		'', 'trim|required');
		$this->CI->form_validation->set_rules('auto_approve', 		'', 'required');
		$this->CI->form_validation->set_rules('auto_promote', 		'', 'required');
		$this->CI->form_validation->set_rules('additional_info_1', 	'', 'trim');
		$this->CI->form_validation->set_rules('additional_info_2', 	'', 'trim');
		$this->CI->form_validation->set_rules('additional_info_3', 	'', 'trim');
		
		
		
		
		//guest_list_auto_approve
		
		if($this->CI->form_validation->run() == false){
			return array('success' => false,
							'message' => 'Please fill out all fields.');
		}
		
		/*
		
		//Force all guest lists to have images
		//IMAGE Handling
		if($manage_image = $this->CI->session->flashdata('manage_image')){			
			$manage_image = json_decode($manage_image);
			
			if(!isset($manage_image->image_data) || !$manage_image->image_data){
				//This user has NOT uploaded an image
				
				return array('success' => false,
								'message' => 'You must upload an image.');
				
			}
			
		}
		
		 * 
		 * */
		
		$auto_approve 		= $this->CI->input->post('auto_approve');
		$auto_promote		= $this->CI->input->post('auto_promote');
		
		$gl_description 	= $this->CI->input->post('gl_description');		
		$gl_cover			= $this->CI->input->post('gl_cover');
		$regular_cover		= $this->CI->input->post('regular_cover');
		$door_opens			= $this->CI->input->post('door_opens');
		$door_closes		= $this->CI->input->post('door_closes');
		$min_age			= $this->CI->input->post('min_age');
		$additional_info_1	= strip_tags($this->CI->input->post('additional_info_1'));
		$additional_info_2	= strip_tags($this->CI->input->post('additional_info_2'));
		$additional_info_3	= strip_tags($this->CI->input->post('additional_info_3'));
		$pgla_id			= $this->CI->input->post('pgla_id');
		
		
		if($auto_approve == 'true')
			$auto_approve = 1;
		else 
			$auto_approve = 0;
		
		
		if($auto_promote == 'true')
			$auto_promote = 1;
		else 
			$auto_promote = 0;
		
		
		if(strlen($gl_description) > 2000)
			return array('success' => false,
							'message' => 'List description must not exceed 2000 characters');
			
			
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		$this->CI->users_promoters->update_pgla($pgla_id, $this->promoter->up_id, array(
			'min_age'			=> $min_age,
			'door_open'			=> $door_opens,
			'door_close'		=> $door_closes,
			'regular_cover'		=> $regular_cover,
			'gl_cover'			=> $gl_cover,
			'additional_info_1' => $additional_info_1,
			'additional_info_2' => $additional_info_2,
			'additional_info_3' => $additional_info_3,
			'auto_approve' 		=> $auto_approve,
			'description' 		=> $gl_description,
			'auto_promote'		=> $auto_promote
		));
		
		return array('success' => true);
		
	}

	/**
	 * Retrieves the guest lists + registered users for a given promoter and a given
	 * weekday or venue
	 * 
	 * @param	$weekday || venue
	 */
	function retrieve_day_guest_lists($weekday){
		
		
		//Do any given operation for a specific weekday...
		switch($weekday){
			case 'mondays':
				break;
			case 'tuesdays':
				break;
			case 'wednesdays':
				break;
			case 'thursdays':
				break;
			case 'fridays':
				break;
			case 'saturdays':
				break;
			case 'sundays':
				break;
			default:
				show_404('Invalid url');
				break;
		}
		
		$this->CI->load->model('model_guest_lists', 'guest_lists', true);
		return $this->CI->guest_lists->retrieve_day_guest_lists($this->promoter->up_id, $weekday);
		
	}

	/**
	 * Retrieves the guest lists + registered users for a given promoter and a given
	 * weekday or venue
	 * 
	 * @param	$weekday || venue
	 */
	function retrieve_all_guest_lists(){
		
		$this->CI->load->model('model_guest_lists', 'guest_lists', true);
		return $this->CI->guest_lists->retrieve_day_guest_lists($this->promoter->up_id);
		
	}
	
	/**
	 * Retrieves a specific guest list for a specific promoter. If the guest list doesn't
	 * exist this method will throw a 404.
	 * 
	 * @param	string (guest list identifier)
	 * @return	object (guest_list)
	 */
	public function retrieve_promoter_guest_list($guest_list_name){
		
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		if(!$guest_list = $this->CI->users_promoters->retrieve_promoter_guest_list($this->promoter->up_id, 
																					$guest_list_name)){
			show_404('Guest list not found'); //this guest list doesn't exist for this promoter
		}
		
		return $guest_list;
		
	}
	
	/**
	 * Retrieves all of the team_venues for all of the teams that this promoter is associated with
	 * 
	 * @return	
	 */
	function retrieve_promoter_team_venues(){
		
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		return $this->CI->users_promoters->retrieve_promoter_team_venues($this->promoter->up_id);
		
	}
	
	/**
	 * Retrieves the number of clients that a promoter is associated with
	 * 
	 * @return 	int
	 */
	function retrieve_num_clients(){
		
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		$result = $this->CI->users_promoters->retrieve_promoter_clients_list($this->promoter->up_id, $this->promoter->team->t_fan_page_id, array('count' => true));
		
		return $result[0]->count_clients;
		
	}
	
	/**
	 * Retrieve fbids of promoter's clients
	 * 
	 * @return 	array
	 */
	function retrieve_promoter_clients_list(){
		
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		$clients = $this->CI->users_promoters->retrieve_promoter_clients_list($this->promoter->up_id, $this->promoter->team->t_fan_page_id);
		foreach($clients as &$client){
			$client = $client->pglr_user_oauth_uid;
		}
		return $clients;
	}
	
	/**
	 * Retrieve all promoter guest list authorizations for a given promoter
	 * 
	 * @return 	array
	 */
	function retrieve_promoter_guest_list_authorizations(){
		
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		return $this->CI->users_promoters->retrieve_promoter_guest_list_authorizations($this->promoter->up_id);
		
	}
	
	/**
	 * Retrieves a promoter's guest list authorizations that are 'Events' and not regular recurring guest lists
	 * 
	 * @param	array (options)
	 */
	function retrieve_promoter_guest_list_authorizations_events($options = array()){
		
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		return $this->CI->users_promoters->retrieve_promoter_guest_list_authorizations_events($this->promoter->up_id, $options);
		
	}
	
	/**
	 * Retrieves the total number of guest list reservation requests, or the number of upcoming guest list
	 * reservation requests
	 * 
	 * @param	bool (upcoming only = true)
	 * @return 	int
	 */
	function retrieve_num_guest_list_reservation_requests($upcoming = true){
		
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		return $this->CI->users_promoters->retrieve_num_guest_list_reservation_requests($this->promoter->up_id, array('upcoming' => $upcoming));
		
	}
	
	/**
	 * Retrieves the number of guest list reservation requests for the trailing 12 weeks
	 * 
	 * @return 	array
	 */
	function retrieve_trailing_weekly_guest_list_reservation_requests(){
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		return $this->CI->users_promoters->retrieve_trailing_weekly_guest_list_reservation_requests($this->promoter->up_id);
	}
	
	/**
	 * Updates a guest list for a promoter and sets it to deactivated
	 *
	 * @return	array
	 */
	function update_promoter_guest_list_set_deactivated(){
		
		if(!$pgla_id = $this->CI->input->post('pgla_id')){
			return array('success' => false,
							'message' => 'pgla_id not set');
		}
		
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		return $this->CI->users_promoters->update_promoter_guest_list_set_deactivated($pgla_id, $this->promoter->up_id);
				
	}
	
	/**
	 * Updates a guest list with a new auto_approve value
	 * 
	 * @return 	array
	 */
	function update_promoter_guest_list_set_auto_approve(){
		
		if(!$pgla_id = $this->CI->input->post('pgla_id')){
			return array('success' => false,
							'message' => 'pgla_id not set');
		}
		
		if(!$auto_approve = $this->CI->input->post('auto_approve')){
			return array('success' => false,
							'message' => 'auto_approve not set');
		}
		
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		return $this->CI->users_promoters->update_promoter_guest_list_set_auto_approve($pgla_id, $auto_approve, $this->promoter->up_id);
	}
	
	/**
	 * Update the notes on a guest list reservation for the host/hostess at the door
	 * 
	 * @param	int (pglr id)
	 * @param	string (host message)
	 * @return 	array
	 */
	function update_promoter_reservation_host_notes($pglr_id, $host_message){
		
		$this->CI->load->model('model_guest_lists', 'guest_lists', true);
		return $this->CI->guest_lists->update_promoter_reservation_host_notes($this->promoter->up_id, $pglr_id, $host_message);
		
	}
	
	###############################################################################################
	#	BEGIN controllers/admin/promoters.php HELPER METHODS									  #
	###############################################################################################	
	
	
	###############################################################################################
	#	BEGIN controllers/promoters.php HELPER METHODS											  #
	###############################################################################################
	
	/**
	 * helper function to '_ajax_guest_list'
	 * Submits a user's guest list (username + entourage) as a new guest list.
	 * Actual implementation a bit more complicated. Must document more.
	 * 
	 * @return	array
	 */
	public function _ajax_guest_list_submit_helper(){
				
		//remove guest list request data from global post array
		$id 				= $this->CI->input->post('gl_id');
		$entourage 			= $this->CI->input->post('entourage');
		$table_request 		= $this->CI->input->post('table_request');
		$share_facebook 	= $this->CI->input->post('facebook_share');
		$request_message 	= $this->CI->input->post('request_message');
		$text_message 		= $this->CI->input->post('text_message');
		$phone_number 		= $this->CI->input->post('phone_number');
		$phone_carrier 		= $this->CI->input->post('phone_carrier');
		$table_min_spend	= $this->CI->input->post('table_min_spend');
		
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
				$text_message = 'false';
		}
		
		if(!$vc_user = $this->CI->session->userdata('vc_user')){
			return array('success' => false,
							'message' => 'User not authenticated');
		}
		$vc_user = json_decode($vc_user);
		$head = $vc_user->oauth_uid;
	
		//attempt to add guest list join request
		//a lot of checking goes on inside this model method***
		$this->CI->load->model('model_guest_lists', 'guest_lists', true);
		
		
		$result = $this->CI->guest_lists->create_new_promoter_guest_list_reservation($id, 
																						$head, 
																						$entourage,
																						$this->promoter->up_id,
																						$table_request, 
																						$share_facebook,
																						$request_message,
																						$text_message, 
																						$phone_number, 
																						$phone_carrier,
																						$table_min_spend);
		
		return array('success' => $result[0],
						'message' => $result[1]);
	}

	/**
	 * Retrieves statistics surrounding a single promoter client
	 * 
	 * @return 	object
	 */
	function retrieve_client_statistics(){
		
	}
	
	/**
	 * Retrieves the guest list from a specific week
	 * 
	 * @return 	object
	 */
	function retrieve_specific_week(){
		
		$week_index = $this->CI->input->post('index');
		if($week_index === false)
			return 	array('success' => false, 'message' => 'Invalid request');
		
		$pgla_id = $this->CI->input->post('pgla_id');
		if($pgla_id === false)
			return 	array('success' => false, 'message' => 'Invalid request');
		
		
		$this->CI->load->model('model_guest_lists', 'guest_lists', true);
		
		//retrieve day of week for pgla_id that guest list is authorized on & make sure pgla_id belongs to this promoter
		if(!$result = $this->CI->guest_lists->retrieve_plga_day_promoter_check($pgla_id, $this->promoter->up_id))
			return array('success' => false, 'message' => 'Unknown error');
		
		$data = $this->CI->guest_lists->retrieve_single_guest_list_and_guest_list_members($pgla_id, $result->pgla_day, $week_index, $result->pgla_create_time);
		
		$response = new stdClass;
		$response->data = $data;
		
		return array('success' => true, 'message' => $response);
		
	}
	
	/**
	 * Retrieve the top profile visitors
	 * 
	 * @return 	array
	 */
	function retrieve_top_profile_visitors(){
		
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		return $this->CI->users_promoters->retrieve_top_profile_visitors($this->promoter->pt_id);
		
	}
	
	/**
	 * Retrieve the most recent VC users to view this promoter's profile
	 * 
	 * @return	array
	 */
	function retrieve_recent_profile_views(){
		
		$this->CI->load->model('model_users_promoters', 'users_promoters', true);
		return $this->CI->users_promoters->retrieve_recent_profile_views($this->promoter->pt_id);
		
	}
	
	/**
	 * Allows promoter to manually add a Facebook friend to his/her guest list. First must execute
	 * Facebook FQL request to verify UID is a friend of the current promoter (and a valid Facebook oauth UID)
	 * 
	 * @return 	object
	 */
	function manual_list_add(){
		
		if($this->CI->input->post('status_check')){
			//check to see if job complete
			
			
			
			$this->CI->load->helper('check_gearman_job_complete');
			check_gearman_job_complete('gearman_promoter_manual_add');	
			
			/*
			
			
			if(!$gearman_promoter_manual_add = $this->CI->session->userdata('gearman_promoter_manual_add'))
				die(json_encode(array('success' => false,
										'message' => 'No request found')));	
			
			$gearman_promoter_manual_add = json_decode($gearman_promoter_manual_add);
			
			//check job status to see if it's completed
			$this->CI->load->library('library_memcached', '', 'memcached');
			if($result = $this->CI->memcached->get($gearman_promoter_manual_add->handle)){
				//free memory from memcached
				$this->CI->memcached->delete($gearman_promoter_manual_add->handle);
				$this->CI->session->unset_userdata('gearman_promoter_manual_add');
				return json_decode($result); //<-- already json in memcache (slight inefficiency here...)
			}else{
				return array('success' => false);
			}
			
			*/
			
			
		}else{
			//create new job
			
			if($vc_user = $this->CI->session->userdata('vc_user')){
				
				//head user uid is required
				if(!$oauth_uids = $this->CI->input->post('oauth_uids'))
					return array('success' => false);
				
				//pgla id is required
				if(!$pgla_id = $this->CI->input->post('pgla_id'))
					return array('success' => false);
				
				$vc_user = json_decode($vc_user);
				//start gearman job for retrieving guest lists
				
				
				/*
				$this->CI->load->library('pearloader');
				$gearman_client = $this->CI->pearloader->load('Net', 'Gearman', 'Client');
				
				# ------------------------------------------------------------- #
				#	Send guest list request to gearman as a background job		#
				# ------------------------------------------------------------- #				
				//add job to a task
				$gearman_task = $this->CI->pearloader->load('Net', 'Gearman', 'Task', array('func' => 'gearman_promoter_manual_add',
																							'arg'  => array('user_oauth_uid' 	=> $vc_user->oauth_uid,
																											'promoter_id'		=> $this->promoter->up_id,
																											'access_token' 		=> $vc_user->access_token,
																											'pgla_id'			=> $pgla_id,
																						'oauth_uids' 		=> json_encode($oauth_uids))));
																						
																						
																						
				$gearman_task->type = Net_Gearman_Task::JOB_BACKGROUND;
				
				//add test to a set
				$gearman_set = $this->CI->pearloader->load('Net', 'Gearman', 'Set');
				$gearman_set->addTask($gearman_task);
				 
				//launch that shit
				$gearman_client->runSet($gearman_set);
				# ------------------------------------------------------------- #
				#	END Send guest list request to gearman as a background job	#
				# ------------------------------------------------------------- #
			
				//Save background handle and server to user's session
				$gearman_promoter_manual_add = new stdClass;
				$gearman_promoter_manual_add->handle = $gearman_task->handle;
				$gearman_promoter_manual_add->server = $gearman_task->server;
				$this->CI->session->set_userdata('gearman_promoter_manual_add', json_encode($gearman_promoter_manual_add));
				
				return array('success' => true);
				*/
				
				$this->CI->load->library('run_gearman_job');
				run_gearman_job('gearman_promoter_manual_add', array('user_oauth_uid' 	=> $vc_user->oauth_uid,
																	'promoter_id'		=> $this->promoter->up_id,
																	'access_token' 		=> $vc_user->access_token,
																	'pgla_id'			=> $pgla_id,
																	'oauth_uids' 		=> json_encode($oauth_uids)));
				
				
				
				
			}else{
				
				return array('success' => false, 'message' => 'User not authenticated.');
				
			}
			
			
			
			
			
		}

	}
}
/* End of file library_promoters.php */
/* Location: ./application/libraries/library_promoters.php */