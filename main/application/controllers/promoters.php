<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for all promoter related pacges. Displays summary list of promoters and individual
 * promoter pages.
 */
class Promoters extends MY_Controller {
	
	// Base path of views unique to this ccontroller
	private $view_dir = 'front/promoters/';
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
	
	/**
	 * /promoters/$arg0/$arg1/$arg2/$arg3/$arg4/
	 * Control point, chooses private method to handle request based on URL
	 * Example: 
	 * 		This details what url segments correspond to arguments for this index function
	 * 		www.vibecompass.com/promoters/fede_wild_child/all_guest_lists/
	 * 			- $arg0 = 'fede_wild_child'
	 * 			- $arg1 = 'all_guest_lists' <-- this argument correlates to af method. All contests share same methods.
	 * 			- $arg2 = (optional, not specified in example but available if later required)
	 * 			- $arg3 = Limiter check, throws 404 for non-existant urls
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 */
	public function index($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){

		/* --------------------- Load promoter library ------------------------ */
		//load the promoter library if a promoter public identifier is available
		if($arg0 != '' && $arg1 != ''){
			$this->load->library('library_promoters');
			$this->library_promoters->initialize(array('promoter_public_identifier' => $arg1), false, $arg0);
		}
		/* --------------------- End Load promoter library ------------------------ */

		/*--------------------- AJAX Request Bypass Handler ---------------------*/
		//The idea here is to avoid loading the static assets, header, body, etc if
		//this is a valid ajax request to this controller. Simply go directly to the
		//'_ajax' function if it exists.
		
		//Note: ocupload is the name of the plugin used for one-click image uploading
			//it creates a hidden iframe which is used to submit an image without a page refresh
		if(($this->input->is_ajax_request() && $this->input->post('ajaxify') === false ) || $this->input->post('ocupload')){
			
			//SPECIAL CASES:
			//we want these methods to fire but we don't want to force users to supply extra url segment
			if($arg1 == ''){
				$method = 'home';
			}elseif($arg2 == ''){
				$method = 'primary';
			}
						
			if(!isset($method)){
				$method = 'guest_lists';
			}			
						
			//check to see if method exists, throw error if false
			if(!method_exists($this, '_' . $method)){
			//	log_message('error', 'undefined method called via ajax: promoters->' . $arg1);
				die(json_encode(array('success' => false)));
			}
			call_user_func(array($this, '_ajax_' . $method), $arg0, $arg1, $arg2, $arg3);
			return;
		}
		/*--------------------- End AJAX Request Bypass Handler ---------------------*/
			
			
		/* ------------------------------- Prepare static asset urls ------------------------------- */	
		//Set in 'CONTROLLER METHOD ROUTING,' passed to the header view. Loads additional
		//javascript/css files+properties that are unique to specific pages loaded from this
		//controller
//		$header_data['additional_karma_javascripts'] = array();
//		$header_data['additional_karma_css'] = array();
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
		 * /promoters/
		 * Examples: 
		 * 		/promoters/
		 * 
		 * 		$arg0 = ''
		 * 		$arg1 = ''
		 * 		$arg2 = ''
		 * 		$arg3 = ''
		 * */
		if($arg0 == ''){
			//no promoter is specified, showcase all promoters in system
			
			//display preview of all promoters
			$method = 'home';
			$header_data['additional_front_css'] = array(
//				'locations.css'
			);
			
		}
		/*
		 * /promoters/[identifier: showcase a specific city]/ --multiple
		 * Examples:
		 * 		/promoters/boston/
		 * 
		 * 		$arg0 = 'boston'
		 * 		$arg1 = ''
		 * 		$arg2 = ''
		 * 		$arg3 = ''
		 * */
		elseif($arg0 != '' && $arg1 == ''){
			
			//showcase all promoters in system for a given city
			$method = 'home';
			$header_data['additional_front_css'] = array(
//				'locations.css'
			);
					
		}
		/**
		 * /promoters/boston/federico/
		 * 
		 * 		arg0 = 'boston'
		 * 		arg1 = 'federico'
		 * 		arg2 = ''
		 */
		elseif($arg0 != '' && $arg1 != '' && $arg2 == ''){
			//showcase a specific promoter
			
			
			$method = 'primary';
			//This method will cover what was formerly 'primary' && 'all_guest_lists' && 'events'
			
			$header_data['additional_front_css'] = array(
//				'promoter.css',
//				'venue.css'
			);
	/*		
			$header_data['additional_global_javascripts'] = array(
														'charts/highcharts.js',
														'charts/themes/gray.js'
														);
			$header_data['additional_global_css'] = array(
														);
	*/
			
		}
		/*
		 * /promoters/[identifier: showcase a specific promoter]/[function: guestlist, reviews, pictures, etc] --multiple
		 * Examples:
		 * 		/promoters/boston/fede/{* ---- Guest List Name ---- *}/
		 * 		
		 * 		$arg0 = 'boston'
		 * 		$arg1 = 'fede'
		 * 		$arg2 = 'guest_lists' || 'events'
		 * 		$arg3 = ''
		 */
		elseif($arg0 != '' && $arg1 != '' && $arg2 != '' && $arg3 == ''){
			
			
			//showcase all events and guest lists for promoter
			switch($arg2){
				case 'guest_lists':
					$method = 'guest_lists';
					$header_data['additional_front_css'] = array(
//						'promoter.css',
//						'venue.css'
					);
					
					break;
				case 'events':
					$method = 'events';
					$header_data['additional_front_css'] = array(
//						'promoter.css',
//						'venue.css'
					);
					
					break;
				default:
					show_error('invalid url');
					break;
			}
			
			
		}
		/*
		 * /promoters/city/[identifier: showcase a specific promoter]/[function: guestlist, reviews, pictures, etc] --multiple
		 * Examples:
		 * 		/promoters/boston/fede/{* ---- Guest List Name ---- *}/
		 * 		
		 * 		$arg0 = 'boston'
		 * 		$arg1 = 'fede'
		 * 		$arg2 = 'guest_lists' || 'events'
		 * 		$arg3 = 'wild_thursdays'
		 */
		elseif($arg0 != '' && $arg1 != '' && $arg2 != '' && $arg3 != ''){
			
			
			//showcase specific event or guest list for promoter
			switch($arg2){
				case 'guest_lists':
					$method = 'guest_lists';
					$header_data['additional_front_css'] = array(
//						'promoter.css',
//						'venue.css'
					);
					
					$header_data['additional_front_javascripts'] = array(
	//					'page/promoter.js'
					);
					
					$header_data['additional_global_javascripts'] = array(
	//					'jquery.maskedinput-1.3.min.js'
					);
					
					break;
				case 'events':
					$method = 'events';
					$header_data['additional_front_css'] = array(
//						'promoter.css'
					);
					
					break;
				default:
					show_error('invalid url');
					break;
			}
			
		}
		/**
		* Limiter check
		*/
		elseif($arg0 != '' && $arg1 != '' && $arg2 != '' && $arg3 != '' && $arg4 != ''){
			show_error('Invalid url', 404);
		}
		# ----------------------------------------------------------------------------------- #
		#	END CONTROLLER METHOD ROUTING													  #
		# ----------------------------------------------------------------------------------- #	
		
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

		$this->lang->load('promoters', $this->config->item('current_lang'));
		
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
	
		$this->load->model('model_users_promoters', 'users_promoters', true);
	
		//Verify valid city
		if($arg0 != ''){
			//city specified
			
			$this->load->model('model_app_data', 'app_data', true);
			if(!$city = $this->app_data->retrieve_valid_city($arg0)){
				show_error('unknown city'); //prob better just a reg 404
				die();
			}
		
			
			//retrieve all promoters for this city
			$data['promoters'] = $this->users_promoters->retrieve_multiple_promoters($arg0);
			
			$header_custom = new stdClass;
			$header_custom->url = base_url() . 'promoters/' . $arg0 . '/';
			$header_custom->title_prefix = lang_key($this->lang->line('ad-promoters_home_title_city'), array('location' => $city->name . ', ' . $city->state)) . ' | ';
			$header_custom->page_description = lang_key($this->lang->line('ad-promoters_home_desc_city'), array('location' => $city->name . ', ' . $city->state));
			$this->load->vars('header_custom', $header_custom);
			
		}else{
			
			$this->load->model('model_app_data', 'app_data', true);
			$data['all_cities'] = $this->app_data->retrieve_all_cities();
			
			$this->load->model('model_users_promoters', 'users_promoters', true);
			foreach($data['all_cities'] as &$vc_city){
				//retrieve all promoters for this city
				$vc_city->promoters = $this->users_promoters->retrieve_multiple_promoters($vc_city->url_identifier);
			}
			
			$header_custom = new stdClass;
			$header_custom->url = base_url() . 'promoters/';
			$header_custom->title_prefix = $this->lang->line('ad-promoters_home_title') . ' | ';
			$header_custom->page_description = $this->lang->line('ad-promoters_home_desc');			
			$this->load->vars('header_custom', $header_custom);
			
		}
			
			
		$data['city'] = (isset($city)) ? $city : false;
				
		$this->body_html = $this->load->view('front/_common/view_front_invite', '', true);
		$this->body_html .= $this->load->view($this->view_dir . 'view_front_promoters_home', $data, true);
		
	}
	
	/**
	 * showcase a specific promoter
	 * This is a promoter's home page
	 * 
	 * @param	url segment 1 (promoter public identifier)
	 * @param	url segment 2
	 * @param	url segment 3
	 * @param	url segment 4
	 * @return	null
	 */
	private function _primary($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){
		
		
		
		
		
		
		
		
		$this->_helper_send_pusher_presence();
		
		
		




		//additional promoter information specific to this page
		if($vc_user = $this->session->userdata('vc_user')){
			$vc_user = json_decode($vc_user);
						
			//assemble venue ids for gearman job
			$promoter_venues_ids = array();
			foreach($this->library_promoters->promoter->promoter_team_venues as $ptv){
				$promoter_venues_ids[] = $ptv->tv_id;
			}
			
			$this->load->helper('run_gearman_job');
			$arguments = array('user_oauth_uid' => $vc_user->oauth_uid,
								'access_token' => $vc_user->access_token,
								'promoter_team_fan_page_id' => $this->library_promoters->promoter->team->t_fan_page_id,
								'promoter_oauth_uid' => $this->library_promoters->promoter->up_users_oauth_uid,
								'promoter_id' => $this->library_promoters->promoter->up_id,
								'promoter_venues_ids' => $promoter_venues_ids);
			run_gearman_job('gearman_individual_promoter_friend_activity', $arguments);
		}

		$this->body_html .= $this->load->view('front/_common/view_front_invite', '', true);		
		$this->body_html .= $this->load->view($this->view_dir . 'promoters_menu/view_promoters_menu_header', '', true);
		
		$this->body_html .= $this->load->view($this->view_dir . 'promoters_menu/view_promoters_menu_options', '', true);				
		$this->body_html .= $this->load->view($this->view_dir . 'view_front_promoters_profile_body_profile', '', true);	
		
		$this->body_html .= $this->load->view($this->view_dir . 'promoters_menu/view_promoters_menu_footer', '', true);
		
		
		$header_custom = new stdClass;
		$header_custom->url = base_url() . 'promoters/' . $arg0 . '/' . $this->library_promoters->promoter->up_public_identifier . '/';
		$header_custom->title_prefix = $this->library_promoters->promoter->u_full_name .  ' | ' . $this->lang->line('ad-promoters_home_title') . ' | ';
		$header_custom->page_image = $this->config->item('s3_uploaded_images_base_url')	
										. 'profile-pics/' 
										. $this->library_promoters->promoter->up_profile_image
										. '_p.jpg';
										
										
		$header_custom->page_description = lang_key($this->lang->line('ad-promoters_home_profile_desc'), array('promoter_u_full_name' => $this->library_promoters->promoter->u_full_name)) . ' | ' . $this->library_promoters->promoter->up_biography;
		/*
		$this->library_promoters->promoter->u_full_name 
												. '\'s VibeCompass Promoter Profile | ' 
												. $this->library_promoters->promoter->up_biography;	
		*/									
		$this->load->vars('header_custom', $header_custom);
		
		$this->_helper_record_view();
	}
	
	/**
	 * showcase an individual guest list for a promoter
	 * 
	 * @param	url segment 1 city_identifier
	 * @param	url segment 2 (promoter public identifier)
	 * @param	url segment 3 (sub-page of promoter public profile) (guest_lists)
	 * @param	url segment 4 (guest list id)
	 * @param	url segment 5
	 * @return	null
	 */
	private function _guest_lists($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){	
		
		setlocale(LC_ALL, $this->config->item('current_lang_locale'));
		
		$this->body_html .= $this->load->view('front/_common/view_front_invite', '', true);
		$this->body_html .= $this->load->view($this->view_dir . 'promoters_menu/view_promoters_menu_header', '', true);
		$this->body_html .= $this->load->view($this->view_dir . 'promoters_menu/view_promoters_menu_options', '', true);				
		
		if($arg3 === ''){
			
			//All Guest Lists
			
			//get promoter's guest lists for this day
			$data['all_guest_lists'] = $this->library_promoters->retrieve_all_guest_lists();
			$this->body_html .= $this->load->view($this->view_dir . 'guest_lists/view_front_promoters_profile_body_guest_lists', $data, true);				
			
			Kint::dump($data);
			
			$header_custom = new stdClass;
			$header_custom->url = base_url() . 'promoters/' . $arg0 . '/' . $this->library_promoters->promoter->up_public_identifier . '/guest_lists/';
			
			
			$header_custom->page_image = $this->config->item('s3_uploaded_images_base_url')	
										. 'profile-pics/' 
										. $this->library_promoters->promoter->up_profile_image
										. '_p.jpg';
										
										
			$header_custom->title_prefix = lang_key($this->lang->line('ad-promoters_all_guest_lists_title'), array(
				'promoter_u_full_name' => $this->library_promoters->promoter->u_full_name
			)) . ' | ' . $this->lang->line('ad-promoters_home_title') . ' | ';
			
			/*
			$this->library_promoters->promoter->u_full_name .  's Guest Lists and Tables | Promoter | ';
			 * */
			
			$header_custom->page_description = lang_key($this->lang->line('ad-promoters_all_guest_lists_desc'), array(
				'promoter_u_full_name' => $this->library_promoters->promoter->u_full_name
			)) . ' | ' . $this->library_promoters->promoter->up_biography;
			
		
			/*
			$this->library_promoters->promoter->u_full_name 
												. '\'s VibeCompass Promoter Guest Lists | ' 
												. $this->library_promoters->promoter->up_biography;	
			
			*/
			
					
			$this->load->vars('header_custom', $header_custom);
			
		}else{
			
			//Specific List
			
			$this->_helper_record_reference_code();
			//get this guest list, if guest list isn't found - throw 404
			
			$data['guest_list'] = $this->library_promoters->retrieve_promoter_guest_list($arg3);
			
			
			
			
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
			
			
			$this->body_html .= $this->load->view($this->view_dir . 'guest_lists/view_front_promoters_profile_body_guest_lists_individual', $data, true);				
			
			
			
			
			$header_custom = new stdClass;
			$header_custom->url = base_url() . 'promoters/' . $arg0 . '/' . $this->library_promoters->promoter->up_public_identifier . '/guest_lists/' . $arg3 . '/';
			$header_custom->page_image = $this->config->item('s3_uploaded_images_base_url')	
										. 'guest_lists/' 
										. $data['guest_list']->pgla_image
										. '_p.jpg';
										
										
			$header_custom->title_prefix = lang_key($this->lang->line('ad-promoters_spec_gl_title'), array(
				'promoter_u_full_name' 	=> $this->library_promoters->promoter->u_full_name,
				'pgla_name'				=> $data['guest_list']->pgla_name,
				'tv_name'				=> $data['guest_list']->tv_name
			)) . ' | ' . $this->lang->line('ad-promoters_home_title') . ' | ';
			
			
			
			/*
			$this->library_promoters->promoter->u_full_name .  's Guest List ' . $data['guest_list']->pgla_name . ' @ ' . $data['guest_list']->tv_name . ' | Promoter | ';
			 * */
			$header_custom->page_description = lang_key($this->lang->line('ad-promoters_spec_gl_desc'), array(
				'promoter_u_full_name' 	=> $this->library_promoters->promoter->u_full_name,
				'pgla_name'				=> $data['guest_list']->pgla_name,
				'tv_name'				=> $data['guest_list']->tv_name
			)) . ' | ' . $data['guest_list']->pgla_description;
			
			/*
			$this->library_promoters->promoter->u_full_name 
												. '\'s Guest List '
												. $data['guest_list']->pgla_name
												. ' @ '
												. $data['guest_list']->tv_name
												. ' | ' 
												. $data['guest_list']->pgla_description;	
			*/
			
			
			$this->load->vars('header_custom', $header_custom);
			
		}
		
		$this->body_html .= $this->load->view($this->view_dir . 'promoters_menu/view_promoters_menu_footer', '', true);
		$this->_helper_record_view();
	}

	
	/**
	 * Showcase a promoter's special events
	 * 
	 * @param	url segment 1 (promoter public identifier)
	 * @param	url segment 2 (sub-page of promoter public profile)
	 * @param	url segment 3 (catagory of guest lists)
	 * @param	url segment 4
	 * @return	null
	 */
	private function _events($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){

//		$this->body_html  = $this->load->view($this->view_dir . 'view_front_promoter_pusher_presence', '', true);
		$this->body_html .= $this->load->view('front/_common/view_front_invite', '', true);
		$this->body_html .= $this->load->view($this->view_dir . 'promoters_menu/view_promoters_menu_header', '', true);
		
		$this->body_html .= $this->load->view($this->view_dir . 'promoters_menu/view_promoters_menu_options', '', true);					
		
		if($arg3 === ''){
			
			//All Events
			
			
		}else{
			
			//Specific Event
			
			$this->_helper_record_reference_code();
			
			
		}
		
		
		
		$this->body_html .= $this->load->view($this->view_dir . 'events/view_front_promoters_profile_body_events', '', true);
		$this->body_html .= $this->load->view($this->view_dir . 'promoters_menu/view_promoters_menu_footer', '', true);		
		
		
		$header_custom = new stdClass;
		$header_custom->url = base_url() . 'promoters/' . $arg0 . '/' . $this->library_promoters->promoter->up_public_identifier . '/events/';
		$header_custom->title_prefix = $this->library_promoters->promoter->u_full_name .  's Events | Promoter | ';
		$this->load->vars('header_custom', $header_custom);
		
		
		$this->_helper_record_view();
	}

	
	/*******************************************************************************************************************
	 * 	END CONTROLLER VIEW DISPLAY FUNCTIONS
	 * 		Below functions are called via AJAX and helpers
	/ ******************************************************************************************************************/
	
	/**
	 * AJAX calls to promoter's home page, supplies data about promoter's popularity w/ friends
	 * 
	 * @param	url segment
	 * @param	url segment
	 * @param	url segment
	 * @param	url segment
	 * @return	null
	 */
	private function _ajax_primary($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){
				
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'Invalid access attempt')));
		}
		
		switch($vc_method){
			case 'feed_retrieve':
				
				if($this->input->post('status_check')){
					//check to see if job complete
					
					$this->load->helper('check_gearman_job_complete');
					check_gearman_job_complete('gearman_individual_promoter_friend_activity');	
					
					
					/*
					
					
					if(!$gearman_individual_promoter_friend_activity = $this->session->userdata('gearman_individual_promoter_friend_activity'))
						die(json_encode(array('success' => false,
												'message' => 'No retrieve request found')));	
													
					$gearman_individual_promoter_friend_activity = json_decode($gearman_individual_promoter_friend_activity);
					$gearman_individual_promoter_friend_activity->attempt += 1;
					
					//check job status to see if it's completed
					$this->load->library('library_memcached', '', 'memcached');
					if($promoter_popularity = $this->memcached->get($gearman_individual_promoter_friend_activity->handle)){
						//free memory from memcached
						$this->memcached->delete($gearman_individual_promoter_friend_activity->handle);
						$this->session->unset_userdata('gearman_individual_promoter_friend_activity');
						
						$temp = json_decode($promoter_popularity);
						
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
						
						die($promoter_popularity); //<-- already json in memcache
					}else{
						
						if($gearman_individual_promoter_friend_activity->attempt > 4)
							$this->session->unset_userdata('gearman_individual_promoter_friend_activity');
						else 
							$this->session->set_userdata('gearman_individual_promoter_friend_activity', json_encode($gearman_individual_promoter_friend_activity));
												
						die(json_encode(array('success' => false)));
						
					}
					
					
					 */ 
				

				}else{
					//create new job
					
					if($vc_user = $this->session->userdata('vc_user')){
						
						$vc_user = json_decode($vc_user);
						
						$this->load->helper('run_gearman_job');
						
						//assemble venue ids for gearman job
						$promoter_venues_ids = array();
						foreach($this->library_promoters->promoter->promoter_team_venues as $ptv){
							$promoter_venues_ids[] = $ptv->tv_id;
						}
						
						$arguments = array('user_oauth_uid' => $vc_user->oauth_uid,
											'access_token' => $vc_user->access_token,
											'promoter_team_fan_page_id' => $this->library_promoters->promoter->team->t_fan_page_id,
											'promoter_oauth_uid' => $this->library_promoters->promoter->up_users_oauth_uid,
											'promoter_id' => $this->library_promoters->promoter->up_id,
											'promoter_venues_ids' => $promoter_venues_ids);
						run_gearman_job('gearman_individual_promoter_friend_activity', $arguments);

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
	 * handles javascript ajax calls for users to join a guest list
	 * 
	 * Checks to see if user is a member of a guest list for a given night.
	 * Allows user to sign up for a guest list if they have not already 
	 * signed up for a different list on that night.
	 * 
	 * @param	url segment
	 * @param	url segment
	 * @param	url segment
	 * @param	url segment
	 * @return	null
	 * */	
	private function _ajax_guest_lists($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){
		
		$vc_method = $this->input->post('vc_method');
		
		//route execution to appropriate helper function based on what operation the user
		//is trying to perform
		switch($vc_method){
			case 'promoter_guest_list_join_request': //second step
				$result = $this->library_promoters->_ajax_guest_list_submit_helper();
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'undefined vc_method')));
				break;
		}
		
		die(json_encode($result));
	}	

	/**
	 * AJAX requests made from guest list overview page
	 * 
	 * @param	url segment
	 * @param	url segment
	 * @param	url segment
	 * @param	url segment
	 * @return	null
	 * 
	private function _ajax_all_guest_lists($arg0 = '', $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = ''){
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'invalid access attempt')));
		}
		
		switch($vc_method){
			case 'guest_list_members_images':
				
				if(!$promoter_guest_list_retrieve_members = $this->session->userdata('promoter_guest_list_retrieve_members'))
					die(json_encode(array('success' => false,
											'message' => 'No guest list retrieve request found')));	
													
				$promoter_guest_list_retrieve_members = json_decode($promoter_guest_list_retrieve_members);
				
				//check job status to see if it's completed
				$this->load->library('library_memcached', '', 'memcached');
				if($guest_list_members = $this->memcached->get($promoter_guest_list_retrieve_members->handle)){
					//free memory from memcached
					$this->memcached->delete($promoter_guest_list_retrieve_members->handle);
					$this->session->unset_userdata('promoter_guest_list_retrieve_members');
					die($guest_list_members); //<-- already json in memcache
				}else{
					die(json_encode(array('success' => false)));
				}
				
			
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'invalid access attempt')));
				break;
				
		}
	}	
*/

	/**
	 * AJAX requests made from events overview page
	 * 
	 * @param	url segment
	 * @param	url segment
	 * @param	url segment
	 * @param	url segment
	 * @return	null
	 */
	private function _ajax_events(){
		
		die(json_encode(array('success' => true, 'message' => '')));
		
	}


	/**
	 * Helper function to record a view of a profile page every time an authenticated user visits this profile page
	 * 
	 * @return	null
	 */
	private function _helper_record_view(){
		
		if($vc_user = $this->session->userdata('vc_user')){
			$vc_user = json_decode($vc_user);
			
			$this->load->model('model_users_promoters', 'users_promoters', true);
			$this->users_promoters->create_profile_view($vc_user->oauth_uid, $this->library_promoters->promoter->pt_id);
		}
		
	}
	
	/**
	 * Helper function to record when a user is referred to this guest list by another user
	 * 
	 * @return 	null
	 */
	private function _helper_record_reference_code(){
		// --------------------------- record reference code -------------------------- //
		if($ref = $this->input->get('ref')){
			
			if($vc_user = $this->session->userdata('vc_user')){
				$vc_user = json_decode($vc_user);
			}
			
			//promoter_name + gl_name + reference_third_party_id
			$gl_id = $arg0 . $arg2 . $ref;
			
			//check to see if this session has performed a ref on this gl in ~5min
			if($session_references = $this->session->userdata('session_references')){
				$session_references = json_decode($session_references);
				
				
				if(isset($session_references->$gl_id)){
					//session has added a ref to this gl
					
					//has it been > 5 minutes since it happened?
					if($session_references->$gl_id > (time() - (60 * 5)) ){
						//no
						
						
					}else{
						//yes
						
						$session_references->$gl_id = time();
						$this->session->set_userdata('session_references', json_encode($session_references));
						
						//record reference
						$this->load->model('model_users_promoters', 'users_promoters', true);
						$this->users_promoters->create_facebook_post_reference($vc_user, $ref, $data['guest_list']->pgla_id);
						
					}
					
				}else{
					//session has NOT added a ref to this gl
					
					$session_references->$gl_id = time();
					$this->session->set_userdata('session_references', json_encode($session_references));
					
					//record reference
					$this->load->model('model_users_promoters', 'users_promoters', true);
					$this->users_promoters->create_facebook_post_reference($vc_user, $ref, $data['guest_list']->pgla_id);
						
				}
				
				
			}else{
				//first reference performed via this session
				
				$session_references = new stdClass;
				$session_references->$gl_id = time();
				$this->session->set_userdata('session_references', json_encode($session_references));
				
				//record reference
				$this->load->model('model_users_promoters', 'users_promoters', true);
				$this->users_promoters->create_facebook_post_reference($vc_user, $ref, $data['guest_list']->pgla_id);
				
			}
			
		}	
		// --------------------------- end record reference code -------------------------- //
	}


	/**
	 * Sends a cookie that triggers a pusher presence
	 * 
	 */
	private function _helper_send_pusher_presence(){
		return;
		$test = array(
			'test' => time()
		);
		
		$cookie_data = array('name' 		=> 'k',
								'value' 	=> json_encode($test),
								'domain'	=> '.vibecompass.' . TLD,
								'expire'	=> '10',
								'path' 		=> '/',
								'prefix'	=> 'vc_',
								'secure'	=> false
		);
		$this->input->set_cookie($cookie_data);
		
	}
}

/* End of file promoters.php */
/* Location: ./application/controllers/promoters.php */