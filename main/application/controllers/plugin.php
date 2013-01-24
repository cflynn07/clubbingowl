<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for facebook tab application
 * This version is the plugin that goes on a website and it mimicks the facebook tab application
 * 
 * 
 */
class Plugin extends MY_Controller {
	
	// Base path of views unique to this controller
	private $view_dir = 'front/facebook/';
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
	 * /$arg0/$arg1/
	 * Control point, chooses private method to handle request based on URL
	 * Example: 
	 * 		
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @return	null
	 * */
	public function index($arg0 = '', $arg1 = ''){
		
		
		$method = 'page';
		
		//initialize library with data stored for current page
		$this->load->library('library_facebook_application', '', 'facebook_application');
		
		
		
		$this->facebook_application->initialize_web_plugin($arg0);
		
		
		
		
		
		
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
			if(!method_exists($this, '_' . $method)){
				die(json_encode(array('success' => false)));
			}
			call_user_func(array($this, '_ajax_' . $method), $arg0, $arg1);
			return;
		}
		/*--------------------- End AJAX Request Bypass Handler ---------------------*/
		
		
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
			'page_data' => $this->facebook_application->page_data
		//	'signed_request_data' => $this->facebook_application->signed_request_data
		));


		//loads all active cities for venues and promoters
		
		# ---------------- LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		//call 'body' function and include all arguments/url-segments
		call_user_func(array($this, '_' . $method), $arg0, $arg1);
		
		//display header view
		$this->header_html = $this->load->view('front/facebook/view_front_facebook_header', '', true);
		
		//Display the footer view after the header/body views have been displayed
		$this->footer_html = $this->load->view('front/facebook/view_front_facebook_footer', '', true);
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
			if($this->input->get('tv_id') !== false){
				
				$tv_id = $this->input->get('tv_id');
				
				
				
				
				$this->load->library('library_venues', '', 'library_venues');
				$this->library_venues->initialize_tv_id($tv_id);
				
								
								
				if($this->library_venues->venue){
					//YES this is a team_venue facebook fan page
					
					
					
					$data['guest_lists'] = $this->library_venues->retrieve_all_guest_lists();
					$data['team_venues'] = array($this->library_venues->venue);
					
					
					$promoters_ids = array();
					foreach($data['team_venues'] as $tv){
						foreach($tv->venue_promoters as $pro){
							$promoters_ids[] = $pro->up_id;
						}
					}
					$promoters_ids = array_unique($promoters_ids);
		
					//additional promoter information specific to this page
					if($vc_user = $this->session->userdata('vc_user')){
						$vc_user = json_decode($vc_user);
						
						$this->load->helper('run_gearman_job');
						$arguments = array(
							'user_oauth_uid' 	=> $vc_user->oauth_uid,
							'access_token' 		=> $vc_user->access_token,
							'promoters_ids'		=> $promoters_ids
						);
						run_gearman_job('gearman_promoter_friend_activity', $arguments);
						
					}
					
					
								
								
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
			setlocale(LC_ALL, $lang . '_' . strtoupper($lang) . ((DEPLOYMENT_ENV == 'cloudcontrol') ? '.utf8' : ''));
			
			
			//retrieve page authorized guest lists and display?
			$data['guest_lists'] = $this->facebook_application->retrieve_page_guest_lists();
			$data['team_venues'] = $this->facebook_application->retrieve_team_venues();
						
			
			
			$promoters_ids = array();
			foreach($data['team_venues'] as &$tv){
				foreach($tv->venue_promoters as $key => $pro){
					
					if($pro->t_fan_page_id !== $this->facebook_application->page_data->team->team_fan_page_id){
						unset($tv->venue_promoters[$key]);
						continue;
					}
					
					$promoters_ids[] = $pro->up_id;
				}
			}unset($tv);
			$promoters_ids = array_unique($promoters_ids);
			
			
			
			

			//additional promoter information specific to this page
			if($vc_user = $this->session->userdata('vc_user')){
				$vc_user = json_decode($vc_user);
				
				$this->load->helper('run_gearman_job');
				$arguments = array(
					'user_oauth_uid' 	=> $vc_user->oauth_uid,
					'access_token' 		=> $vc_user->access_token,
					'promoters_ids'		=> $promoters_ids
				);
				run_gearman_job('gearman_promoter_friend_activity', $arguments);
				
			}
			
			
				
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
			
			
			$header_custom = new stdClass;
			$header_custom->title_prefix = ' | ClubbingOwl Plugin | ';
			$this->load->vars('header_custom', $header_custom);
			
									
			$this->body_html = $this->load->view($this->view_dir . 'page/view_page_facebook_page_guest_lists', $data, true);			
			
		}else{
			
			//visitor is NOT admin -- show 'awaiting setup' message to users
			$this->body_html = $this->load->view($this->view_dir . 'page/view_page_facebook_awaiting_setup', '', true);
			
		}
		
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
			case 'promoter_friends_retrieve':
				
				
				if(!$vc_user)
					die(json_encode(array('success' => false)));
					
				
				if($this->input->post('status_check')){
					//check to see if job complete
					
					$this->load->helper('check_gearman_job_complete');
					check_gearman_job_complete('gearman_promoter_friend_activity');
					
				}else{
					
					
					
					
					
					
					
					
					
					
					
					
					
					if(!$this->facebook_application->page_data){
			
						
						//check if this is a team-venue facebook page
						if($this->input->get('tv_id') !== false){
							
							$tv_id = $this->input->get('tv_id');						
							
							
							
							$this->load->library('library_venues', '', 'library_venues');
							$this->library_venues->initialize_tv_id($tv_id);
							
											
											
							if($this->library_venues->venue){
								//YES this is a team_venue facebook fan page
								
								
								
								$data['guest_lists'] = $this->library_venues->retrieve_all_guest_lists();
								$data['team_venues'] = array($this->library_venues->venue);
								
								
								$promoters_ids = array();
								foreach($data['team_venues'] as $tv){
									foreach($tv->venue_promoters as $pro){
										$promoters_ids[] = $pro->up_id;
									}
								}
								$promoters_ids = array_unique($promoters_ids);
					
								//additional promoter information specific to this page
								if($vc_user = $this->session->userdata('vc_user')){
									$vc_user = json_decode($vc_user);
									
									$this->load->helper('run_gearman_job');
									$arguments = array(
										'user_oauth_uid' 	=> $vc_user->oauth_uid,
										'access_token' 		=> $vc_user->access_token,
										'promoters_ids'		=> $promoters_ids
									);
									run_gearman_job('gearman_promoter_friend_activity', $arguments);
									
								}
								
								
											
											
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
						
						$lang = 'en';
						$data['lang'] 		= $lang;
						
						
						//retrieve page authorized guest lists and display?
						$data['guest_lists'] = $this->facebook_application->retrieve_page_guest_lists();
						$data['team_venues'] = $this->facebook_application->retrieve_team_venues();
									
									
									
						$promoters_ids = array();
						foreach($data['team_venues'] as $tv){
							foreach($tv->venue_promoters as $pro){
								$promoters_ids[] = $pro->up_id;
							}
						}
						$promoters_ids = array_unique($promoters_ids);
			
						//additional promoter information specific to this page
						if($vc_user = $this->session->userdata('vc_user')){
							$vc_user = json_decode($vc_user);
							
							$this->load->helper('run_gearman_job');
							$arguments = array(
								'user_oauth_uid' 	=> $vc_user->oauth_uid,
								'access_token' 		=> $vc_user->access_token,
								'promoters_ids'		=> $promoters_ids
							);
							run_gearman_job('gearman_promoter_friend_activity', $arguments);
							
						}
						
						die(json_encode(array('success' => true)));
																		
					}








					
			
				}
				
				
				break;
				
				
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
								
								
								
								
								
				$tgla_id = $this->input->post('tgla_id');
				$tv_id = $this->input->post('tv_id');
				
				$this->load->library('library_venues');
				$this->library_venues->initialize_tv_id($tv_id);

				$this->load->model('model_team_guest_lists', 'team_guest_lists', true);
				$data['guest_list'] = $this->team_guest_lists->retrieve_individual_guest_list_for_plugin($tgla_id);
				
				if(!$data['guest_list']){
					show_404('Guest List Not Found');
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
	
}

/* End of file plugin.php */
/* Location: ./application/controllers/plugin.php */