<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Venues controller
 */
class Venues extends MY_Controller {
	
	// Base path of views unique to this controller
	private $view_dir = 'front/venues/';
	private $header_html = '';
	private $body_html = '';
	private $footer_html = '';
	
	/**
	 * Controller constructor. Perform any universal operations here.
	 * 
	 * @return	null
	 * */
	function __construct(){
		parent::__construct();
	}
	
	/**2
	 * /primary/$arg0/$arg1/$arg2/$arg3/$arg4/
	 * Control point, chooses private method to handle request based on URL
	 * Example: 
	 * 		This details what url segments correspond to arguments for this index function
	 * 		www.vibecompass.com/venues/boston/estate/guest_lists/
	 * 			- $arg0 = 'venues'
	 * 			- $arg1 = 'boston' 
	 * 			- $arg2 = 'estate'
	 * 			- $arg3 = 'guest_lists'
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 * */
	public function index($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){


		/* --------------------- Load Venue library ------------------------ */
		//load the promoter library if a promoter public identifier is available
		if($arg0 != '' && $arg1 != ''){
			$this->load->library('library_venues');
			$this->library_venues->initialize($arg1, $arg0);
		}
		/* --------------------- End Load Venue library ------------------------ */
			
			
		/* ------------------------------- Prepare static asset urls ------------------------------- */	
		//Set in 'CONTROLLER METHOD ROUTING,' passed to the header view. Loads additional
		//javascript/css files+properties that are unique to specific pages loaded from this
		//controller
		$header_data['additional_front_javascripts'] = array();
		$header_data['additional_front_css'] = array();
		$header_data['additional_global_javascripts'] = array();
		$header_data['additional_global_css'] = array();
		//additional_js_properties are javascript variables defined in the global namespace, making them
			//available to code in included js files
		$header_data['additional_js_properties'] = array();
		/* ------------------------------- End prepare static asset urls ------------------------------- */

		
		# ----------------------------------------------------------------------------------- #
		#	BEGIN CONTROLLER METHOD ROUTING													  #
		# ----------------------------------------------------------------------------------- #	
		/*
		 * /venues/
		 * Examples: 
		 * 		/promoters/
		 * 
		 * 		$arg0 = ''
		 * 		$arg1 = ''
		 * 		$arg2 = ''
		 * 		$arg3 = ''
		 * */
		if($arg0 == ''){
			//no venues is specified, showcase all venues in system
			
			$header_data['additional_front_css'] = array(
//				'venues.css',
//				'locations.css'
			);
			
			//display preview of all venues
			$method = 'home';
			
		}
		/*
		 * /venues/[identifier: showcase a specific city]/ --multiple
		 * Examples:
		 * 		/venues/boston/
		 * 
		 * 		$arg0 = 'boston'
		 * 		$arg1 = ''
		 * 		$arg2 = ''
		 * 		$arg3 = ''
		 * */
		elseif($arg0 != '' && $arg1 == ''){
			
			//showcase all venues in system for a given city
			$method = 'home';
			$header_data['additional_front_css'] = array(
//				'venues.css',
//				'locations.css'
			);
					
		}
		/**
		 * /venues/boston/estate/
		 * 
		 * 		arg0 = 'boston'
		 * 		arg1 = 'estate'
		 * 		arg2 = ''
		 */
		elseif($arg0 != '' && $arg1 != '' && $arg2 == ''){
			//showcase a specific venue
			
			
			$method = 'primary';
			//This method will cover what was formerly 'primary' && 'all_guest_lists' && 'events'
			
			$header_data['additional_front_css'] = array(
//				'venue.css'
			);
			$header_data['additional_global_javascripts'] = array(
										//				'charts/highcharts.js'
										//				'charts/themes/gray.js'
														);
	
			
		}
		/*
		 * /venues/[identifier: showcase a specific city]/VENUE_NAME/[function: guestlist, reviews, pictures, etc] --multiple
		 * Examples:
		 * 		/promoters/boston/fede/{* ---- Guest List Name ---- *}/
		 * 		
		 * 		$arg0 = 'boston'
		 * 		$arg1 = 'estate'
		 * 		$arg2 = 'guest_lists' || 'events' || 'promoters'
		 * 		$arg3 = ''
		 */
		elseif($arg0 != '' && $arg1 != '' && $arg2 != '' && $arg3 == ''){
			
			//showcase all events and guest lists for promoter
			switch($arg2){
				case 'guest_lists':
					$method = 'guest_lists';
					
					$header_data['additional_front_css'] = array(
		//				'venue.css'
					);
					
					break;
				case 'events':
					$method = 'events';
					
					$header_data['additional_front_css'] = array(
		//				'venue.css'
					);
					
					break;
				default:
					show_404('invalid url');
					break;
			}
			
		}
		/*
		 * /venues/[identifier: showcase a specific city]/VENUE_NAME/[function: guestlist, reviews, pictures, etc] --multiple
		 * Examples:
		 * 		/promoters/boston/fede/{* ---- Guest List Name ---- *}/
		 * 		
		 * 		$arg0 = 'boston'
		 * 		$arg1 = 'estate'
		 * 		$arg2 = 'guest_lists' || 'events'
		 * 		$arg3 = 'wild_thursdays'
		 */
		elseif($arg0 != '' && $arg1 != '' && $arg2 != '' && $arg3 != ''){
			
			//showcase specific event or guest list for promoter
			switch($arg2){
				case 'guest_lists':
					$method = 'guest_lists';
					
					$header_data['additional_front_css'] = array(
			//			'venue.css',
			//			'promoter.css'
					);
					
					$header_data['additional_front_javascripts'] = array(
			//			'page/promoter.js'
					);
					
					$header_data['additional_global_javascripts'] = array(
			//			'jquery.maskedinput-1.3.min.js'
					);
					
					break;
				case 'events':
					$method = 'events';
					
					$header_data['additional_front_css'] = array(
			//			'venue.css'
					);
					
					break;
				default:
					show_404('invalid url');
					break;
			}
			
		}
		/**
		* Limiter check
		*/
		elseif($arg0 != '' && $arg1 != '' && $arg2 != '' && $arg3 != '' && $arg4 != ''){
			show_404('Invalid url');
		}
		# ----------------------------------------------------------------------------------- #
		#	END CONTROLLER METHOD ROUTING													  #
		# ----------------------------------------------------------------------------------- #	
		
		

		/*--------------------- AJAX Request Bypass Handler ---------------------*/
		//The idea here is to avoid loading the static assets, header, body, etc if
		//this is a valid ajax request to this controller. Simply go directly to the
		//'_ajax' function if it exists.
		
		//Note: ocupload is the name of the plugin used for one-click image uploading
			//it creates a hidden iframe which is used to submit an image without a page refresh
		if(($this->input->is_ajax_request() && $this->input->post('ajaxify') === false ) || $this->input->post('ocupload')){
			
			//check to see if method exists, throw error if false
			if(!method_exists($this, '_' . $method)){
				die(json_encode(array('success' => false)));
			}
			call_user_func(array($this, '_ajax_' . $method), $arg0, $arg1, $arg2, $arg3, $arg4);
			return;
		}
		/*--------------------- End AJAX Request Bypass Handler ---------------------*/
		
		
		/**
		 * 'header_data' is made globally available to all views so that the 'view_common_header'
		 * view can be included from the header files of all themes to properly include
		 * external css/js files and define global-namespace js variables
		 */
		$this->load->vars('header_data', $header_data);
		
		$vc_user = $this->session->userdata('vc_user');
		if($vc_user !== false){
			$this->load->vars('vc_user', json_decode($vc_user));
		}else{
			$this->load->vars('vc_user', false);
		}
				
		//loads all active cities for venues and promoters
		determine_active_cities();
		
		$this->lang->load('venues', $this->config->item('current_lang'));

		# ---------------- LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		
		//call 'body' function and include all arguments/url-segments
		call_user_func(array($this, '_' . $method), $arg0, $arg1, $arg2, $arg3, $arg4);
		
		if($this->input->post('ajaxify') === false){
			//display header view
			$this->header_html = $this->load->view('front/view_front_header', '', true);
			
			//Display the footer view after the header/body views have been displayed
			$this->footer_html = $this->load->view('front/view_front_footer', '', true);
		}else{
			
			$this->body_html = $this->load->view('front/_common/view_common_title_ajaxify', '', true) . $this->body_html;
			
		}
		
		# ---------------- END LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		
		//Construct final output for browser
		$this->output->set_output($this->header_html . $this->body_html . $this->footer_html);
		
	}
	
	/*******************************************************************************************************************
	 * 	END CONTROLLER ENTRY POINT ROUTING FUNCTIONS
	 * 		Below functions called based on arguments to index function. They are responsible for rendering page content
	/ ******************************************************************************************************************/
	
	/**
	 * Showcase all promoters
	 * Showcases globally and by city
	 * 
	 * @param 	first url segment
	 * @param	second url segment
	 * 
	 * @return 	null
	 */
	private function _home($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){
	
		$this->load->model('model_app_data', 'app_data', true);
	
		$vc_user = $this->session->userdata('vc_user');
		if($vc_user !== false){
			$vc_user = json_decode($vc_user);
		}
	
		//Verify valid city
		if($arg0 != ''){
			//city specified
			
			if(!$city = $this->app_data->retrieve_valid_city($arg0)){
				show_404('unknown city'); //prob better just a reg 404
				die();
			}
			
			//retrieve all venues for this city
			$data['venues'] = $this->app_data->retrieve_all_venues($arg0);
						
			//if user signed in, retrieve friend-venue activity for all venues listed
			if($vc_user){
							
				//assemble venue ids for gearman job
				$team_venue_ids = array();
				foreach($data['venues'] as $tv){
					$team_venue_ids[] = $tv->tv_id;
				}
				
				$this->load->helper('run_gearman_job');
				$arguments = array('user_oauth_uid' => $vc_user->oauth_uid,
									'access_token' => $vc_user->access_token,
									'team_venue_ids' => $team_venue_ids);
				run_gearman_job('gearman_retrieve_friend_venues_activity', $arguments);
			}
			
			
			
			
			$header_custom = new stdClass;
			$header_custom->url = base_url() . 'venues/' . $arg0 . '/';
			$header_custom->title_prefix = lang_key($this->lang->line('ad-venues_home_title_city'), 
														array(
															'location' => $city->name . ', ' . $city->state
														))
											. ' | ';
			$header_custom->page_description = lang_key($this->lang->line('ad-venues_home_desc_city'), array('location' => $city->name . ', ' . $city->state));
			$this->load->vars('header_custom', $header_custom);
			
		}else{
			//all cities
			
			$this->load->model('model_app_data', 'app_data', true);
			$data['all_cities'] = $this->app_data->retrieve_all_cities();
			
			$this->load->model('model_users_promoters', 'users_promoters', true);
			
			$team_venues = array();
			foreach($data['all_cities'] as &$vc_city){
				//retrieve all venues for this city
				$vc_city->venues = $this->app_data->retrieve_all_venues($vc_city->url_identifier);
				$team_venues = array_merge($team_venues, $vc_city->venues);
			}
			
			
			//if user signed in, retrieve friend-venue activity for all venues listed
			if($vc_user){
							
				//assemble venue ids for gearman job
				$team_venue_ids = array();
				foreach($team_venues as $tv){
					$team_venue_ids[] = $tv->tv_id;
				}
				
				$this->load->helper('run_gearman_job');
				$arguments = array('user_oauth_uid' => $vc_user->oauth_uid,
									'access_token' => $vc_user->access_token,
									'team_venue_ids' => $team_venue_ids);
				run_gearman_job('gearman_retrieve_friend_venues_activity', $arguments);
			}
			
			
			$header_custom = new stdClass;
			$header_custom->url = base_url() . 'venues/';
			$header_custom->title_prefix = $this->lang->line('ad-venues_home_title') . ' | ';
			$header_custom->page_description = $this->lang->line('ad-venues_home_desc');			
			$this->load->vars('header_custom', $header_custom);
			
		}
			
		$data['city'] = (isset($city)) ? $city : false;
					
		$this->body_html = $this->load->view('front/_common/view_front_invite', '', true);
		$this->body_html .= $this->load->view($this->view_dir . 'view_front_venues_home', $data, true);

	}

	/**
	 * showcase a specific venue
	 * This is a venue's home page
	 * 
	 * @param	url segment 1 (promoter public identifier)
	 * @param	url segment 2
	 * @param	url segment 3
	 * @param	url segment 4
	 * @return	null
	 */
	private function _primary($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){

//		$this->_helper_record_view();	TODO: Implement for venues?
		$this->lang->load('venues', $this->config->item('current_lang'));
		$this->lang->load('home_auth', $this->config->item('current_lang'));
		
	//	Kint::dump($this->library_venues);
		
		//additional promoter information specific to this page		
		if($vc_user = $this->session->userdata('vc_user')){
			$vc_user = json_decode($vc_user);
						
			
			$this->load->helper('run_gearman_job');
			$arguments = array('user_oauth_uid' => $vc_user->oauth_uid,
								'access_token' => $vc_user->access_token,
								'venue_id' => array($this->library_venues->venue->tv_id));
			run_gearman_job('gearman_individual_venue_friend_activity', $arguments); 
						
		}
				
		//TODO: Add presence
		$this->body_html = $this->load->view('front/_common/view_front_invite', '', true);
		$this->body_html .= $this->load->view($this->view_dir . 'venues_menu/view_venues_menu_header', '', true);
		
		$this->body_html .= $this->load->view($this->view_dir . 'venues_menu/view_venues_menu_options', '', true);
		$this->body_html .= $this->load->view($this->view_dir . 'view_front_venues_profile_body_profile', '', true);
	
		$this->body_html .= $this->load->view($this->view_dir . 'venues_menu/view_venues_menu_footer', '', true);
				
				
		$header_custom = new stdClass;
		$header_custom->url = base_url() . 'venues/' . $arg0 . '/' . str_replace(' ', '_', $this->library_venues->venue->tv_name) . '/';
		$header_custom->title_prefix = $this->library_venues->venue->tv_name 
										.  ' - ' 
										. $this->library_venues->venue->c_name 
										. ', ' 
										. $this->library_venues->venue->c_state 
										. ' | ' 
										. $this->lang->line('ad-venues_home_title') 
										. ' | ';
		$header_custom->page_image = $this->config->item('s3_uploaded_images_base_url')	
										. 'venues/banners/' 
										. $this->library_venues->venue->tv_image
										. '_p.jpg';
		$header_custom->page_description = lang_key($this->lang->line('ad-venues_home_profile_desc'), 
												array('venue_name' => $this->library_venues->venue->tv_name)) 
												. ' | ' 
												. $this->library_venues->venue->tv_description;
		
		$this->load->vars('header_custom', $header_custom);
		
	}

	/**
	 * Display all guest lists that are available for a venue
	 * 
	 * @param	url segment 1 (promoter public identifier)
	 * @param	url segment 2
	 * @param	url segment 3
	 * @param	url segment 4
	 * @return	null
	 */
	private function _guest_lists($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){
		
		setlocale(LC_ALL, $this->config->item('current_lang_locale'));
				
		//TODO: Add presence
		$this->body_html = $this->load->view('front/_common/view_front_invite', '', true);
		$this->body_html .= $this->load->view($this->view_dir . 'venues_menu/view_venues_menu_header', '', true);
		
		$this->body_html .= $this->load->view($this->view_dir . 'venues_menu/view_venues_menu_options', '', true);
		
		
		if($arg3 === ''){
			//all guest lists

			$data['all_guest_lists'] = $this->library_venues->retrieve_all_guest_lists();
			
			$this->body_html .= $this->load->view($this->view_dir . 'guest_lists/view_front_venues_profile_body_guest_lists', $data, true);
			
			$header_custom = new stdClass;
		
			$header_custom->url = base_url() . 'venues/' . $arg0 . '/' . str_replace(' ', '_', $this->library_venues->venue->tv_name) . '/guest_lists/';
		 
		 	$header_custom->title_prefix = lang_key($this->lang->line('ad-venues_all_guest_lists_title'),
													array(
														'venue_name' => $this->library_venues->venue->tv_name,
														'location'	=> $this->library_venues->venue->c_name . ', ' . $this->library_venues->venue->c_state 
													)) 
											. ' | ';
			$header_custom->page_image = $this->config->item('s3_uploaded_images_base_url')	
											. 'venues/banners/' 
											. $this->library_venues->venue->tv_image
											. '_p.jpg';
			$header_custom->page_description = lang_key($this->lang->line('ad-venues_all_guest_lists_desc'),
													array(
														'venue_name' => $this->library_venues->venue->tv_name,
														'location'	=> $this->library_venues->venue->c_name . ', ' . $this->library_venues->venue->c_state 
													)) 
											. ' | '
											. $this->library_venues->venue->tv_description;
			$this->load->vars('header_custom', $header_custom);
			
		}else{
			//specific guest list
			
			$data['guest_list'] = $this->library_venues->retrieve_individual_guest_list($arg3);
			
			
		
			if(!$data['guest_list']){

				if(!$this->input->post('ajaxify'))
					header('HTTP/1.0 404 Not Found');
								
				$this->body_html .= $this->load->view($this->view_dir . 'guest_lists/view_front_venues_profile_body_guest_lists_individual_not_found', $data, true);				
				$this->body_html .= $this->load->view($this->view_dir . 'venues_menu/view_venues_menu_footer', '', true);
				return;
				
			}
			
			
			$vc_user = $this->session->userdata('vc_user');
			$vc_user = json_decode($vc_user);
			if($vc_user){
				$this->load->model('model_teams', 'teams', true);
				$this->teams->create_team_profile_view($vc_user->oauth_uid, $data['guest_list']->tgla_team_fan_page_id);
			}
			
			
			
			// --------- retrieve floorplans for venue
			$this->load->model('model_teams', 'teams', true);
			$venue_floorplan = $this->teams->retrieve_venue_floorplan($data['guest_list']->tv_id, $data['guest_list']->tv_team_fan_page_id);
			$venue_floors = new stdClass;
			
			//iterate over all items to extract floors
			foreach($venue_floorplan as $key => $vlf){
				if(!isset($vlf->vlf_id))
					continue;
				
				if($vlf->vlf_deleted == 1)
					continue;
				
				if(!array_key_exists($vlf->vlf_id, $venue_floors)){
					
					$floor_object = new stdClass;
					$floor_object->items = array();
					$floor_object->name = $vlf->vlf_floor_name;
					
					$floor_id = $vlf->vlf_id;
					$venue_floors->$floor_id = $floor_object;
					
				}
			}
			
			//for each floor, extract the items
			foreach($venue_floors as $key => &$vf){
							
				foreach($venue_floorplan as $vlf){
					if($key == $vlf->vlf_id){
						//item is on THIS floor
						
						if($vlf->vlfi_id == NULL)
							continue;
						
						if($vlf->vlfi_deleted == 1)
							continue;
											
						$vf->items[] = $vlf;
						
					}
				}
				
			}
			
			$data['venue_floorplan'] = $venue_floors;
			// -------------------------
			
			$this->body_html .= $this->load->view($this->view_dir . 'guest_lists/view_front_venues_profile_body_guest_lists_individual', $data, true);
			
			$header_custom = new stdClass;
			$header_custom->url = base_url() . 'venues/' . $arg0 . '/' . str_replace(' ', '_', $this->library_venues->venue->tv_name) . '/guest_lists/' . str_replace(' ', '_', $data['guest_list']->tgla_name) . '/';
			$header_custom->title_prefix = lang_key($this->lang->line('ad-venues_spec_gl_title'),
													array(
														'venue_name' => $this->library_venues->venue->tv_name,
														'tgla_name'	 => strtoupper($data['guest_list']->tgla_name)
													)) 
											. ' | ';
			$header_custom->page_image = $this->config->item('s3_uploaded_images_base_url')	
											. 'guest_lists/' 
											. $data['guest_list']->tgla_image
											. '_p.jpg';
											
			$header_custom->page_description = lang_key($this->lang->line('ad-venues_spec_gl_title'),
													array(
														'venue_name' => $this->library_venues->venue->tv_name,
														'tgla_name'	 => $data['guest_list']->tgla_name
													)) 
											. ' | '
											. $data['guest_list']->tgla_description;
			$this->load->vars('header_custom', $header_custom);
			
		}
	
		$this->body_html .= $this->load->view($this->view_dir . 'venues_menu/view_venues_menu_footer', '', true);


		$this->load->vars('header_custom', $header_custom);

	}
	
	/**
	 * Display all guest lists that are available for a venue
	 * 
	 * @param	url segment 1 (promoter public identifier)
	 * @param	url segment 2
	 * @param	url segment 3
	 * @param	url segment 4
	 * @return	null
	 */
	private function _events($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){
		
		
		//TODO: Add presence
		$this->body_html = $this->load->view('front/_common/view_front_invite', '', true);
		$this->body_html .= $this->load->view($this->view_dir . 'venues_menu/view_venues_menu_header', '', true);
		
		$this->body_html .= $this->load->view($this->view_dir . 'venues_menu/view_venues_menu_options', '', true);
		$this->body_html .= $this->load->view($this->view_dir . 'events/view_front_venues_profile_body_events', '', true);
	
		$this->body_html .= $this->load->view($this->view_dir . 'venues_menu/view_venues_menu_footer', '', true);

		$header_custom = new stdClass;
		$header_custom->title_prefix = 'Events @ ' . $this->library_venues->venue->tv_name .  ' in ' . $this->library_venues->venue->c_name . ', ' . $this->library_venues->venue->c_state . ' | Venue | ';
		$this->load->vars('header_custom', $header_custom);

	}
	
	
	/*******************************************************************************************************************
	 * 	END CONTROLLER VIEW DISPLAY FUNCTIONS
	 * 		Below functions are called via AJAX and helpers
	/ ******************************************************************************************************************/
	
	/**
	 * AJAX helper for city venues, or global venues page
	 * 
	 * @param	url segment 1
	 * @param	url segment 2
	 * @param	url segment 3
	 * @param	url segment 4
	 * @param	url segment 5
	 * @return	null
	 */
	private function _ajax_home($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'Invalid access attempt')));
		}
		
		$vc_user = $this->session->userdata('vc_user');
		if($vc_user !== false){
			$vc_user = json_decode($vc_user);
		}
	
		// -------------------------------------------------------------
		
		$retrieve_city_method = function($CI, $arg0){
			
	//		$CI =& get_instance
			$vc_user = $CI->session->userdata('vc_user');
			if($vc_user !== false){
				$vc_user = json_decode($vc_user);
			}
			
			if($CI->input->post('status_check')){
				//check if job complete
				
				$CI->load->helper('check_gearman_job_complete');
				check_gearman_job_complete('gearman_retrieve_friend_venues_activity');
				
			}else{
				//start new job
				
				if(!$vc_user){
					die(json_encode(array('success' => false, 'message' => 'user not authenticated')));
				}
				
				$CI->load->model('model_app_data', 'app_data', true);
				if(!$city = $CI->app_data->retrieve_valid_city($arg0)){
					show_404('unknown city'); //prob better just a reg 404
					die();
				}
				
				//retrieve all venues for this city
				$data['venues'] = $CI->app_data->retrieve_all_venues($arg0);
				
				//if user signed in, retrieve friend-venue activity for all venues listed
							
				//assemble venue ids for gearman job
				$team_venue_ids = array();
				foreach($data['venues'] as $tv){
					$team_venue_ids[] = $tv->tv_id;
				}
				
				$CI->load->helper('run_gearman_job');
				$arguments = array('user_oauth_uid' => $vc_user->oauth_uid,
									'access_token' => $vc_user->access_token,
									'team_venue_ids' => $team_venue_ids);
				run_gearman_job('gearman_retrieve_friend_venues_activity', $arguments);
					
				die(json_encode(array('success' => true)));
				
			}
			
		};
		
		// -------------------------------------------------------------
		
		$retrieve_global_method = function($CI){
			
	//		$CI =* get_instance();
			$vc_user = $CI->session->userdata('vc_user');
			if($vc_user !== false){
				$vc_user = json_decode($vc_user);
			}
			
			if($CI->input->post('status_check')){
				//check if job complete
				
				$CI->load->helper('check_gearman_job_complete');
				check_gearman_job_complete('gearman_retrieve_friend_venues_activity');
				
			}else{
				//start new job
				
				if(!$vc_user){
					die(json_encode(array('success' => false, 'message' => 'user not authenticated')));
				}
				
				$CI->load->model('model_app_data', 'app_data', true);
				$data['all_cities'] = $CI->app_data->retrieve_all_cities();
				
											
				$team_venues = array();
				foreach($data['all_cities'] as &$vc_city){
					//retrieve all venues for this city
					$vc_city->venues = $CI->app_data->retrieve_all_venues($vc_city->url_identifier);
					$team_venues = array_merge($team_venues, $vc_city->venues);
				}
			
				//assemble venue ids for gearman job
				$team_venue_ids = array();
				foreach($team_venues as $tv){
					$team_venue_ids[] = $tv->tv_id;
				}
				
				$CI->load->helper('run_gearman_job');
				$arguments = array('user_oauth_uid' => $vc_user->oauth_uid,
									'access_token' => $vc_user->access_token,
									'team_venue_ids' => $team_venue_ids);
				run_gearman_job('gearman_retrieve_friend_venues_activity', $arguments);
				
				die(json_encode(array('success' => true)));
				
			}
			
		};
		
		// -------------------------------------------------------------
	
		switch($vc_method){
			case 'friend_venue_activity_retrieve':
				
				if($arg0 !== ''){
					//city specified
					
					$retrieve_city_method($this, $arg0);
					
				}else{
					//no city specified
					
					$retrieve_global_method($this);
					
				}
			
				break;
			default:
				die(json_encode(array('success' => false, 'message' => 'unknown vc method')));
				break;
		}
		
	}
	
	
	/**
	 * AJAX request handler for home page
	 * 
	 * @param	url segment
	 * @return	null
	 */
	private function _ajax_primary($arg0 = ''){
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'Invalid access attempt')));
		}
		
		switch($vc_method){
			case 'feed_retrieve':
				if($this->input->post('status_check')){
					//check to see if job complete
					
					$this->load->helper('check_gearman_job_complete');
					check_gearman_job_complete('gearman_individual_venue_friend_activity');	
					
					/*
					
					if(!$gearman_individual_venue_friend_activity = $this->session->userdata('gearman_individual_venue_friend_activity'))
						die(json_encode(array('success' => false,
												'message' => 'No retrieve request found')));
													
					$gearman_individual_venue_friend_activity = json_decode($gearman_individual_venue_friend_activity);
					$gearman_individual_venue_friend_activity->attempt += 1;
					
					//check job status to see if it's completed
					$this->load->library('library_memcached', '', 'memcached');
					if($news_feed = $this->memcached->get($gearman_individual_venue_friend_activity->handle)){
						//free memory from memcached
						$this->memcached->delete($gearman_individual_venue_friend_activity->handle);
						$this->session->unset_userdata('gearman_individual_venue_friend_activity');
						
						$temp = json_decode($news_feed);
						if(isset($temp->success) && $temp->success === false){
							//user's facebook session is invalid, delete session
							
							$vc_user = $this->session->userdata('vc_user');
							$vc_user = json_decode($vc_user);
							if(isset($vc_user->oauth_uid)){
								$this->users->update_user($vc_user->oauth_uid, array(
									'access_token_valid_seconds' => 0
								));
								$this->session->unset_userdata('vc_user');
							}
														
						}
						
						die($news_feed); //<-- already json in memcache
					}else{
						
						if($gearman_individual_venue_friend_activity->attempt > 4)
							$this->session->unset_userdata('gearman_individual_venue_friend_activity'); //didn't finish in time
						else 
							$this->session->set_userdata('gearman_individual_venue_friend_activity', json_encode($gearman_individual_venue_friend_activity));
							
						die(json_encode(array('success' => false)));
					}
					  
					  
					*/
					
					
				}else{
					//create new job
					
					if($vc_user = $this->session->userdata('vc_user')){
							
						$vc_user = json_decode($vc_user);
						
						$this->load->helper('run_gearman_job');
						
						if($vc_user = $this->session->userdata('vc_user')){
							$vc_user = json_decode($vc_user);
										
							
							$this->load->helper('run_gearman_job');
							$arguments = array('user_oauth_uid' => $vc_user->oauth_uid,
												'access_token' => $vc_user->access_token,
												'venue_id' => array($this->library_venues->venue->tv_id));
							run_gearman_job('gearman_individual_venue_friend_activity', $arguments); 
										
						}

						die(json_encode(array('success' => true)));
						
					}else{
						
						die(json_encode(array('success' => false, 'message' => 'User not authenticated.')));
						
					}
					
					
				}
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'Unknown vc_method.')));
				break;				
				
		}
		
	}	

	/**
	 * Processes a user request to join a guest list
	 * 
	 * @param	URL SEG 1
	 * @param	URL SEG 2
	 * @param	URL SEG 3
	 * @param	URL SEG 4
	 * @param	URL SEG 5
	 * @return	null
	 */
	private function _ajax_guest_lists($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false)));
		}
		
		switch($vc_method){
			
			case 'team_guest_list_join_request':
				
				//This functionality was originall built into the facebook plugin, so we copy it here
				$this->load->library('library_facebook_application', '', 'facebook_application');
				$response = $this->facebook_application->team_guest_list_join_request();
				die(json_encode($response));
				
				break;
			default:
				die(json_encode(array('success' => false)));
			
		}
		
	}

}

/* End of file venues.php */
/* Location: ./application/controllers/venues.php */