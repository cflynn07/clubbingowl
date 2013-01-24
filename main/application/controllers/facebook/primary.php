<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for facebook tab application
 */
class Primary extends MY_Controller {
	
	// Base path of views unique to this controller
	private $view_dir 		= 'front/facebook/';
	private $header_html 	= '';
	private $body_html 		= '';
	private $footer_html 	= '';
	
	/**
	 * Controller constructor. Perform any universal operations here.
	 * 
	 * @return	null
	 * */
	function __construct(){
		parent::__construct();
		
	}
	
	/**
	 * /$arg0/$arg1/
	 * Control point, chooses private method to handle request based on URL
	 * Example: 
	 * 		
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @return	null
	 * */
	public function index($arg0 = '', $arg1 = ''){
		
		//initialize library with data stored for current page
		$this->load->library('library_facebook_application', '', 'facebook_application');
		$this->facebook_application->initialize();
		
		
		
		
		
		
		
		$vc_user = $this->session->userdata('vc_user');
		if($vc_user !== false){
			$this->load->vars('vc_user', json_decode($vc_user));
		}else{
			$this->load->vars('vc_user', false);
		}
		
		
		
		
		
		
			
			
			
			
			
		/*--------------------- AJAX Request Bypass Handler ---------------------*/
		//The idea here is to avoid loading the static assets, header, body, etc if
		//this is a valid ajax request to this controller. Simply go directly to the
		//'_ajax' function if it exists.
		
		//Note: ocupload is the name of the plugin used for one-click image uploading
			//it creates a hidden iframe which is used to submit an image without a page refresh
		if($this->input->is_ajax_request() || $this->input->post('ocupload')){
			
			//check to see if method exists, throw error if false
			if(!method_exists($this, '_' . $arg0)){
				log_message('error', 'undefined method called via ajax: facebook/primary->' . $arg0);
				die(json_encode(array('success' => false)));
			}
			call_user_func(array($this, '_ajax_' . $arg0), $arg0, $arg1);
			return;
		}
		/*--------------------- End AJAX Request Bypass Handler ---------------------*/
		
			
		/* ------------------------------- Prepare static asset urls ------------------------------- */	
		//Set in 'CONTROLLER METHOD ROUTING,' passed to the header view. Loads additional
		//javascript/css files+properties that are unique to specific pages loaded from this
		//controller
		$header_data['additional_facebook_javascripts'] = array();
		$header_data['additional_facebook_css'] = array();
		
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
		 * /[facebook-page]/
		 * Examples: 
		 * 		/app/
		 * 		/page/
		 * 
		 * 		$arg0 = ''
		 * 		$arg1 = ''
		 * */
		if($arg0 == ''){
			
			//this shouldn't happen for this controller
			
		}
		/*
		 * /[facebook-page]/
		 * Examples: 
		 * 		/app/
		 * 		/page/
		 * 
		 * 		$arg0 = 'app' || 'page'
		 * 		$arg1 = ''
		 * */
		elseif($arg0 != '' && $arg1 == ''){
			
			switch($arg0){
				case 'page':
				//	$header_data['additional_front_css'] = array(
				//		'facebook.css',
				//		'locations.css',
				//		'venues.css',
				//		'promoter.css'
				//	);
					
				//	$header_data['additional_front_javascripts'] = array(
				//		'page/promoter.js'
				//	);
					
				//	$header_data['additional_global_javascripts'] = array(
				//		'jquery.maskedinput-1.3.min.js'
				//	);
					
					$method = 'page';
					
					break;
				case 'app':
					
					$method = 'app';
					
					break;
			}
			
		}
		/*
		 * /page/{SOMETHING FAKE}
		 * 
		 * 		$arg0 = 'page'
		 * 		$arg1 = '{SOMETHING FAKE}'
		 * 
		 * */
		elseif($arg0 != '' && $arg1 != ''){
			
			show_404();
			
		}
		
		# ----------------------------------------------------------------------------------- #
		#	END CONTROLLER METHOD ROUTING													  #
		# ----------------------------------------------------------------------------------- #	
		
		/**
		 * 'header_data' is made globally available to all views so that the 'view_common_header'
		 * view can be included from the header files of all themes to properly include
		 * external css/js files and define global-namespace js variables
		 */
		
		//make data globally available to all views
		$this->load->vars(array(
			'page_data' 			=> $this->facebook_application->page_data,
			'signed_request_data' 	=> $this->facebook_application->signed_request_data
		));
				
		$this->load->vars('header_data', $header_data);
		
		
		
		
		//loads all active cities for venues and promoters
		
		# ---------------- LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		//call 'body' function and include all arguments/url-segments
		call_user_func(array($this, '_' . $method), $arg0, $arg1);
		
		if($method != 'app'){
			//display header view
			$this->header_html = $this->load->view('front/facebook/view_front_facebook_header', '', true);
			
			//Display the footer view after the header/body views have been displayed
			$this->footer_html = $this->load->view('front/facebook/view_front_facebook_footer', '', true);
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
	 * Displays information when page invoked from within
	 * fan page tab
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @return	null
	 */
	private function _page($arg0 = '', $arg1 = ''){
										
							
							
		if(!$this->facebook_application->page_data){
			
			
			//check if this is a team-venue facebook page
			if(isset($this->facebook_application->signed_request_data['page']['id'])){
				
				$page_id = $this->facebook_application->signed_request_data['page']['id'];
				
				//find if this page ID is linked with a TEAM_VENUE
				
				$this->db->select("*")
					->from('facebook_team_venues')
					->where(array(
						'fan_page_id' => $page_id
					));
				$query = $this->db->get();
				$result = $query->row();
				
								
				if($result){
					//YES this is a team_venue facebook fan page
					
					
					
					$this->load->library('library_venues', '', 'library_venues');
					$this->library_venues->initialize_tv_id($result->team_venue_id);
					
					
					
					$data['guest_lists'] = $this->library_venues->retrieve_all_guest_lists();
					$data['team_venues'] = array($this->library_venues->venue);
					
					
					
					
					//retrieve page authorized guest lists and display?
				//	$data['guest_lists'] = $this->facebook_application->retrieve_page_guest_lists();
				//	$data['team_venues'] = 
					
					
					
								
								
					$vc_user = $this->session->userdata('vc_user');
					if($vc_user)
						$vc_user = json_decode($vc_user);
					
					//if user signed in, retrieve friend-venue activity for all venues listed
					if($vc_user){
									
						//assemble venue ids for gearman job
						$team_venue_ids = array();
						foreach($data['team_venues'] as $tv){
							$team_venue_ids[] = $tv->tv_id;
						}
						
						$this->load->helper('run_gearman_job');
						$arguments = array('user_oauth_uid' => $vc_user->oauth_uid,
											'access_token' => $vc_user->access_token,
											'team_venue_ids' => $team_venue_ids);
						run_gearman_job('gearman_retrieve_friend_venues_activity', $arguments);
						
					}
					
											
					$this->body_html = $this->load->view($this->view_dir . 'page/view_page_facebook_page_guest_lists', $data, true);
				
					return;
					
					
				}
				
				
			}
		}
							
							
					
					
					
					
					
					
					
							
							
							
		if($this->facebook_application->page_data){
			//we know this page
			
			
			
			if(isset($this->facebook_application->signed_request_data['user']['locale'])){
				
				$lang = $this->facebook_application->signed_request_data['user']['locale'];
				$lang = strtolower(substr($lang, 0, 2));
				
				//is this a language we support?
				if(!in_array($lang, $this->config->item('supported_lang_codes'))){
					$lang = 'en';
				}
				
			}else{
				
				$lang = 'en';
				
			}
			
			
			$data['lang'] 		= $lang;
		//	setlocale(LC_ALL, $lang . '_' . strtoupper($lang) . ((DEPLOYMENT_ENV == 'cloudcontrol') ? '.utf8' : ''));
			
			
			//retrieve page authorized guest lists and display?
			$data['guest_lists'] = $this->facebook_application->retrieve_page_guest_lists();
			$data['team_venues'] = $this->facebook_application->retrieve_team_venues();
						
						
			$vc_user = $this->session->userdata('vc_user');
			if($vc_user)
				$vc_user = json_decode($vc_user);
			
			//if user signed in, retrieve friend-venue activity for all venues listed
			if($vc_user){
							
				//assemble venue ids for gearman job
				$team_venue_ids = array();
				foreach($data['team_venues'] as $tv){
					$team_venue_ids[] = $tv->tv_id;
				}
				
				$this->load->helper('run_gearman_job');
				$arguments = array('user_oauth_uid' => $vc_user->oauth_uid,
									'access_token' => $vc_user->access_token,
									'team_venue_ids' => $team_venue_ids);
				run_gearman_job('gearman_retrieve_friend_venues_activity', $arguments);
				
			}
			
									
			$this->body_html = $this->load->view($this->view_dir . 'page/view_page_facebook_page_guest_lists', $data, true);			
			
		}else{
			//page unknown - probably just added
			
			
			
			// ---------- We're abandoning all of this ------------
			
			
			if($this->facebook_application->signed_request_data['page']['admin']){
				//visitor is admin -- show setup
				$this->body_html = $this->load->view($this->view_dir . 'page/view_page_facebook_admin_setup', '', true);
			
			}else{
				//visitor is NOT admin -- show 'awaiting setup' message to users
				$this->body_html = $this->load->view($this->view_dir . 'page/view_page_facebook_awaiting_setup', '', true);
			
			}
			
			
			
			
		}
		
	}
	
	/**
	 * Displays information when page invoked from 
	 * application page (720px)
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @return	null
	 */
	private function _app($arg0 = '', $arg1 = ''){
		
		/*
		echo '<pre>';
		var_dump($this->facebook_application->page_data);
		var_dump($this->facebook_application->signed_request_data); 
		die();
		*/
		
		if($request_ids = $this->input->get('request_ids')){
			$this->load->helper('run_gearman_job');
			$arguments = array('request_ids' => $request_ids);
			run_gearman_job('retrieve_facebook_app_requests', $arguments);
		}
		
		
		
		if(isset($this->facebook_application->signed_request_data['user']['locale'])){
			
			$lang_code = $this->facebook_application->signed_request_data['user']['locale'];
			$lang_code = strtolower(substr($lang_code, 0, 2));
			
			//is this a language we support?
			if(!in_array($lang_code, $this->config->item('supported_lang_codes'))){
				$lang_code = 'www';
			}

			if($lang_code == 'en'){
				$lang_code = 'www';
			}
			
		}else{
			
			$lang_code = 'www';
			
		}
		
		
		$data['lang_code'] = $lang_code;
		//user is then redirected via javascript
		$this->body_html = $this->load->view($this->view_dir . 'app/view_app_facebook_dashboard', $data, true);

	}
	
	/*******************************************************************************************************************
	 * 	END CONTROLLER VIEW DISPLAY FUNCTIONS
	 * 		Below functions are called via AJAX and helpers
	/ ******************************************************************************************************************/
	
	/**
	 * Handler for page-based ajax requests
	 * 
	 * @param	first url segment
	 * @param	second url segment
	 * @return	null
	 */
	private function _ajax_page($arg0 = '', $arg1 = ''){
		
		$vc_method = $this->input->post('vc_method');
		
		$vc_user = $this->session->userdata('vc_user');
		if($vc_user !== false){
			$vc_user = json_decode($vc_user);
		}
		
		
		
		$retrieve_global_method = function($CI){
			
	//		$CI =& get_instance();
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
				
			
				$team_fan_page_id = $CI->input->post('team_fan_page_id');
				$team_venues = $CI->facebook_application->retrieve_team_venues($team_fan_page_id);
			
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
		
		
		
		
		
		switch($vc_method){
			case 'setup_next_step': //requests next step in signup process
				
				$response = $this->facebook_application->setup_next_step();
				die(json_encode($response));
				
				break;
			case 'step_submit': //submits the data for the current setup step
				
				$response = $this->facebook_application->step_submit();
				die(json_encode($response));
				
				break;
			case 'team_guest_list_join_request':
				
				
				
				
				$response = $this->facebook_application->team_guest_list_join_request();			
				die(json_encode($response));
				
				
				
				
				break;
			case 'friend_venue_activity_retrieve':
				
				$retrieve_global_method($this);
			
				break;
			case 'tgla_html_retrieve':
								
								
					
								
								
								
				$tgla_id 	= $this->input->post('tgla_id');
				$tv_id 		= $this->input->post('tv_id');
				
				
		//		var_dump($this->input->post()); die();
				
				
				$this->load->library('library_venues');
				$this->library_venues->initialize_tv_id($tv_id);






				$this->load->model('model_team_guest_lists', 'team_guest_lists', true);
				$data['guest_list'] = $this->team_guest_lists->retrieve_individual_guest_list_for_plugin($tgla_id);
				
				
				if(!$data['guest_list']){
					show_404('Guest List Not Found');
				}
				
				
				
				
				$vc_user = $this->session->userdata('vc_user');
				$vc_user = json_decode($vc_user);
				if($vc_user){
					$this->load->model('model_teams', 'teams', true);
					$this->teams->create_team_profile_view($vc_user->oauth_uid, $data['guest_list']->tv_team_fan_page_id);
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

				$guest_list_html = $this->load->view('front/venues/guest_lists/view_front_venues_profile_body_guest_lists_individual', $data, true);
				
				die($guest_list_html);
				
				
				
				
				break;
	/*		case 'team_guest_list_join_request':

				//This functionality was originall built into the facebook plugin, so we copy it here
				$this->load->library('library_facebook_application', '', 'facebook_application');
				$response = $this->facebook_application->team_guest_list_join_request();
				die(json_encode($response));
				
				break;
	*/		default:
				die(json_encode(array('success' => false,
										'message' => 'invalid access attempt')));
				break;
		}
		
		die(json_encode(array('success' => false)));
		
	}
	
	/**
	 * Handler for AJAX requests from app page
	 * 
	 * @param	first url segment
	 * @param	second url segment
	 * @return 	null
	 */
	private function _ajax_app($arg0 = '', $arg1 = ''){

		die(json_encode(array('success' => false)));		

	}
}

/* End of file profile.php */
/* Location: ./application/controllers/profile.php */