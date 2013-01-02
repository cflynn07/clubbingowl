<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for facebook/vibecompass 'friends' activity stream
 * 
 */
class Friends extends MY_Controller {
	
	// Base path of views unique to this controller
	private $view_dir = 'front/friends/';	
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
	 * /friends/$arg0/$arg1/
	 * Control point, chooses private method to handle request based on URL
	 * Example: 
	 * 		This details what url segments correspond to arguments for this index function
	 * 		www.vibecompass.com/friends/
	 * 			- $arg0 = ''
	 * 			- $arg1 = ''
	 * 			
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @return	null
	 * */
	public function index($arg0 = '', $arg1 = ''){

		/*--------------------- AJAX Request Bypass Handler ---------------------*/
		//The idea here is to avoid loading the static assets, header, body, etc if
		//this is a valid ajax request to this controller. Simply go directly to the
		//'_ajax' function if it exists.
		
		//Note: ocupload is the name of the plugin used for one-click image uploading
			//it creates a hidden iframe which is used to submit an image without a page refresh
		if(($this->input->is_ajax_request() && $this->input->post('ajaxify') === false ) || $this->input->post('ocupload')){
						
			//SPECIAL CASES:
			//anything can go through to primary
			$arg1 = $arg0;
			$arg0 = 'primary';
			
			//check to see if method exists, throw error if false
			if(!method_exists($this, '_' . $arg0)){
				log_message('error', 'undefined method called via ajax: friends->' . $arg0);
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
	//	$header_data['additional_karma_javascripts'] = array();
	//	$header_data['additional_karma_css'] = array();
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
		 * /friends/
		 * Examples: 
		 * 		/friends/
		 * 
		 * 		$arg0 = ''
		 * 		$arg1 = ''
		 * */
		if($arg0 == ''){
			
			$method = 'primary';
		//	$header_data['additional_front_css'] = array(
		//		'friends.css'
		//	);

		}
		/*
		 * /friends/[some yet to be determined subsection]
		 * 
		 * 		$arg0 = '[some yet to be determined subsection]'
		 * 		$arg1 = ''
		 * 
		 * */
		elseif($arg0 != '' && $arg1 == ''){
			
			//anything can go through to primary
			$arg1 = $arg0;
			$method = 'primary';
		//	$header_data['additional_front_css'] = array(
		//		'profile.css'
		//	);
			
			
		}
		/*
		 * /promoters/[some yet to be determined subsection]/{SOMETHING FAKE}
		 * 
		 * 		$arg0 = 'messages'
		 * 		$arg1 = '{SOMETHING FAKE}'
		 * 
		 * */
		elseif($arg0 != '' && $arg1 != '' && $arg2 == ''){
			
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
		$this->load->vars('header_data', $header_data);
		
		//loads all active cities for venues and promoters
		determine_active_cities();
		
		# ---------------- LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		
		setlocale(LC_ALL, $this->config->item('current_lang_locale'));
		$language = $this->config->item('current_lang');
		$this->lang->load('friends',	$language);
		
		
		//call 'body' function and include all arguments/url-segments
		call_user_func(array($this, '_' . $method), $arg0, $arg1);
		
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
	 * Showcase vibecompass friends
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @return	null
	 */
	private function _primary($arg0 = '', $arg1 = ''){
		
		$vc_user = json_decode($this->session->userdata('vc_user'));
		
		if($arg1 == ''){
			//overview of all vibecompass/facebook friends
						
			if($vc_user){
				
				
				
				/*
				$friend_feed_retrieve = $this->session->userdata('friend_feed_retrieve');
				if($friend_feed_retrieve !== false)
					$friend_feed_retrieve = json_decode($friend_feed_retrieve);
				
				if(!$friend_feed_retrieve || ($friend_feed_retrieve->req_time < (time() - 10))){
					
					//start gearman job for retrieving guest lists
					$this->load->library('pearloader');
					$gearman_client = $this->pearloader->load('Net', 'Gearman', 'Client');
					
					# ------------------------------------------------------------- #
					#	Send guest list request to gearman as a background job		#
					# ------------------------------------------------------------- #				
					//add job to a task
					$gearman_task = $this->pearloader->load('Net', 'Gearman', 'Task', array('func' => 'friend_feed_retrieve',
																							'arg'  => array('user_oauth_uid' => $vc_user->oauth_uid,
																											'access_token' => $vc_user->access_token,
																											'iterator_position' => false)));
					$gearman_task->type = Net_Gearman_Task::JOB_BACKGROUND;
					
					//add test to a set
					$gearman_set = $this->pearloader->load('Net', 'Gearman', 'Set');
					$gearman_set->addTask($gearman_task);
					 
					//launch that shit
					$gearman_client->runSet($gearman_set);
					# ------------------------------------------------------------- #
					#	END Send guest list request to gearman as a background job	#
					# ------------------------------------------------------------- #
				
					//Save background handle and server to user's session
					$friend_feed_retrieve = new stdClass;
					$friend_feed_retrieve->handle = $gearman_task->handle;
					$friend_feed_retrieve->server = $gearman_task->server;
					$friend_feed_retrieve->attempt = 0;
					$friend_feed_retrieve->req_time = time();
					
					$this->session->set_userdata('friend_feed_retrieve', json_encode($friend_feed_retrieve));
					
				}
				
				 * */
				 
				$this->body_html .= $this->load->view('front/_common/view_front_invite', '', true);		
			}
			
			$this->body_html .= $this->load->view($this->view_dir . 'view_front_friends', '', true);
			
			$header_custom = new stdClass;
			$header_custom->url = base_url() . 'friends/';
			$header_custom->title_prefix = $this->lang->line('ad-friends') . ' | ';
			$this->load->vars('header_custom', $header_custom);
			
		}else{
			//specific friend requested
			
			//determine if user exists in vibecompass, if no: HTTP 404
			$this->load->model('model_users', 'users', true);
			
			//EXTREMELY IMPORTANT -- 2nd paramter == true, removes sensitive data such as access_token and prevents that from getting sent to client
			$user = $this->users->retrieve_user_by_third_party_id($arg1, true);
			
			if($user){
				//user exists and is in vibecompass
				
				if($vc_user){
					
					//is user trying to access friend page of themselves? If so redirect to profile
					if($user->users_oauth_uid == $vc_user->oauth_uid){
					//	redirect('/profile/');
					//	die();
						
						$this->body_html = '<script type="text/javascript">window.top.location = "/profile";</script>';
						return;
					}
					
				}
				
				if($user->users_promoter == 1){
					
					//promoters do not have profile pages, redirect to promoter profile
					$this->load->model('model_users_promoters', 'users_promoters', true);
					$promoter = $this->users_promoters->retrieve_promoter(array('users_oauth_uid' => $user->users_oauth_uid));
					
					if($promoter->up_completed_setup == 1){
					//	redirect('/promoters/' . $promoter->team->c_url_identifier . '/' . $promoter->up_public_identifier . '/', 301);
					//	die();
					
					
					
					
						if($this->input->post('ajaxify')){
							$this->body_html = '<a class="ajaxify" id="redirect_link" style="opacity:0;display:none;" href="/promoters/' . $promoter->up_public_identifier . '/">Promoter</a><script type="text/javascript">jQuery("#redirect_link").trigger("click");</script>';
						}else{
							$this->body_html = '<script type="text/javascript">window.top.location = "/promoters/' . $promoter->up_public_identifier . '/";</script>';	
						}



						
						return;
					}
					
				}
				
				if($vc_user){
					
					$friend_retrieve = $this->session->userdata('friend_retrieve');
					if($friend_retrieve !== false)
						$friend_retrieve = json_decode($friend_retrieve);
					
					if(!$friend_retrieve || ($friend_retrieve->req_time < (time() - 10))){
					
						//user exists, launch background job to determine if user is friends with current user
						//and retrieve user's data
						$this->load->library('pearloader');
						$gearman_client = $this->pearloader->load('Net', 'Gearman', 'Client');
						
						# ------------------------------------------------------------- #
						#	Send guest list request to gearman as a background job		#
						# ------------------------------------------------------------- #				
						//add job to a task
						$gearman_task = $this->pearloader->load('Net', 'Gearman', 'Task', array('func' => 'friend_retrieve',
																								'arg'  => array('user_oauth_uid' => $vc_user->oauth_uid,
																												'access_token' => $vc_user->access_token,
																												'friend' => json_encode($user))));
						$gearman_task->type = Net_Gearman_Task::JOB_BACKGROUND;
						
						//add test to a set
						$gearman_set = $this->pearloader->load('Net', 'Gearman', 'Set');
						$gearman_set->addTask($gearman_task);
						 
						//launch that shit
						$gearman_client->runSet($gearman_set);
						# ------------------------------------------------------------- #
						#	END Send guest list request to gearman as a background job	#
						# ------------------------------------------------------------- #
					
						//Save background handle and server to user's session
						$friend_retrieve = new stdClass;
						$friend_retrieve->handle = $gearman_task->handle;
						$friend_retrieve->server = $gearman_task->server;
						$friend_retrieve->attempt = 0;
						$friend_retrieve->req_time = time();
						$this->session->set_userdata('friend_retrieve', json_encode($friend_retrieve));
					}
					
					$this->body_html .= $this->load->view('front/_common/view_front_invite', '', true);		

				}
				
				$data['friend'] = $user;
				
				$header_custom = new stdClass;
				$header_custom->url = base_url() . 'friends/' . $arg1 . '/';
				$header_custom->title_prefix = $user->users_full_name . ' | ';
				$header_custom->page_description = lang_key($this->lang->line('ad-friends_desc'), array(
															'friend_name' => $user->users_full_name
															)) . ' | ' . $this->lang->line('ad-description');
				$this->load->vars('header_custom', $header_custom);
				
				$this->body_html .= $this->load->view($this->view_dir . 'view_front_individual_friend', $data, true);
				
			}else{
				//user is not in vibecompass
				
				show_404('User not found');
				die();
				
			//	header("HTTP/1.0 404 Not Found");
			//	$this->load->view($this->view_dir . 'view_friend_404');
				
			}
			
		}	
		
	}
	
	/*******************************************************************************************************************
	 * 	END CONTROLLER VIEW DISPLAY FUNCTIONS
	 * 		Below functions are called via AJAX and helpers
	/ ******************************************************************************************************************/
	
	/**
	 * AJAX helper, retrieves news feed
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @return	null
	 */
	private function _ajax_primary($arg0 = '', $arg1 = ''){
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'Invalid access attempt')));
		}
		
		switch($vc_method){
			case 'friend_feed_retrieve':
				
				if($this->input->post('status_check')){
					//check to see if job complete
					
					
					$this->load->helper('check_gearman_job_complete');
					check_gearman_job_complete('friend_feed_retrieve');	
					
					
					/*
					
					
					if(!$friend_feed_retrieve = $this->session->userdata('friend_feed_retrieve'))
						die(json_encode(array('success' => false,
												'message' => 'No retrieve request found')));	
													
					$friend_feed_retrieve = json_decode($friend_feed_retrieve);
					$friend_feed_retrieve->attempt += 1;
					
					//check job status to see if it's completed
					$this->load->library('library_memcached', '', 'memcached');
					if($friend_feed = $this->memcached->get($friend_feed_retrieve->handle)){
						//free memory from memcached
						$this->memcached->delete($friend_feed_retrieve->handle);
						$this->session->unset_userdata('friend_feed_retrieve');
						
						$temp = json_decode($friend_feed);
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
						
						die($friend_feed); //<-- already json in memcache					 
					 
					}else{
						
						if($friend_feed_retrieve->attempt > 4)
							$this->session->unset_userdata('friend_feed_retrieve');
						else 
							$this->session->set_userdata('friend_feed_retrieve', json_encode($friend_feed_retrieve));
						
						die(json_encode(array('success' => false)));
					}
					
					*/
					
					
					
					
				}else{
					//create new job
					
					if($vc_user = $this->session->userdata('vc_user')){
						
						/*							
						$vc_user = json_decode($vc_user);
						
						$friend_feed_retrieve = $this->session->userdata('friend_feed_retrieve');
						if($friend_feed_retrieve !== false)
							$friend_feed_retrieve = json_decode($friend_feed_retrieve);
						
						if(!$friend_feed_retrieve || ($friend_feed_retrieve->req_time < (time() - 10))){
							
							//start gearman job for retrieving guest lists
							$this->load->library('pearloader');
							$gearman_client = $this->pearloader->load('Net', 'Gearman', 'Client');
							
							# ------------------------------------------------------------- #
							#	Send guest list request to gearman as a background job		#
							# ------------------------------------------------------------- #				
							//add job to a task
							$gearman_task = $this->pearloader->load('Net', 'Gearman', 'Task', array('func' => 'friend_feed_retrieve',
																									'arg'  => array('user_oauth_uid' => $vc_user->oauth_uid,
																													'access_token' => $vc_user->access_token,
																													'iterator_position' => $this->input->post('iterator'))));
							$gearman_task->type = Net_Gearman_Task::JOB_BACKGROUND;
							
							//add test to a set
							$gearman_set = $this->pearloader->load('Net', 'Gearman', 'Set');
							$gearman_set->addTask($gearman_task);
							 
							//launch that shit
							$gearman_client->runSet($gearman_set);
							# ------------------------------------------------------------- #
							#	END Send guest list request to gearman as a background job	#
							# ------------------------------------------------------------- #
						
							//Save background handle and server to user's session
							$friend_feed_retrieve = new stdClass;
							$friend_feed_retrieve->handle = $gearman_task->handle;
							$friend_feed_retrieve->server = $gearman_task->server;
							$friend_feed_retrieve->attempt = 0;
							$friend_feed_retrieve->req_time = time();
							$this->session->set_userdata('friend_feed_retrieve', json_encode($friend_feed_retrieve));
						}

						die(json_encode(array('success' => true)));
						*/
						
					}else{
						
						die(json_encode(array('success' => false, 
												'message' => 'User not authenticated.')));
						
					}
					
				}
				break;
			case 'friend_retrieve':
				
				
				$this->load->helper('check_gearman_job_complete');
				check_gearman_job_complete('friend_retrieve');	
					
					
					
					
				
				/*
				if(!$friend_retrieve = $this->session->userdata('friend_retrieve'))
					die(json_encode(array('success' => false,
											'message' => 'No retrieve request found')));	
												
				$friend_retrieve = json_decode($friend_retrieve);
				$friend_retrieve->attempt += 1;
				
				//check job status to see if it's completed
				$this->load->library('library_memcached', '', 'memcached');
				if($friend = $this->memcached->get($friend_retrieve->handle)){
					//free memory from memcached
					$this->memcached->delete($friend_retrieve->handle);
					$this->session->unset_userdata('friend_retrieve');
					die($friend); //<-- already json in memcache
				}else{
					
					if($friend_retrieve->attempt > 4) 
						$this->session->unset_userdata('friend_retrieve');
					else 
						$this->session->set_userdata('friend_retrieve', json_encode($friend_retrieve));
					
					die(json_encode(array('success' => false)));
				}
				
				*/
				
				
				
			
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'Unknown vc_method.')));
				break;				
				
		}
		
	}
}

/* End of file friends.php */
/* Location: ./application/controllers/friends.php */