<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Primary controller of website.
 * Loads home page.
 */
class Primary extends MY_Controller {
	
	// Base path of views unique to this controller
//	private $view_dir = 'karma/primary/';
	private $view_dir = 'front/primary/';
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
	 * /primary/$arg0/$arg1/$arg2/$arg3/$arg4/
	 * Control point, chooses private method to handle request based on URL
	 * Example: 
	 * 		This details what url segments correspond to arguments for this index function
	 * 		www.vibecompass.com/promoters/fede_wild_child/all_guest_lists/
	 * 			- $arg0 = 'fede_wild_child'
	 * 			- $arg1 = 'all_guest_lists' <-- this argument correlates to a method. All contests share same methods.
	 * 			- $arg2 = (optional, not specified in example but available if later required)
	 * 			- $arg3 = Limiter check, throws 404 for non-existant urls
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 * */
	public function index($arg0 = 'primary'){
		
		/*--------------------- AJAX Request Bypass Handler ---------------------*/
		//The idea here is to avoid loading the static assets, header, body, etc if
		//this is a valid ajax request to this controller. Simply go directly to the
		//'_ajax' function if it exists.
		
		//Note: ocupload is the name of the plugin used for one-click image uploading
			//it creates a hidden iframe which is used to submit an image without a page refresh
		if(($this->input->is_ajax_request() && $this->input->post('ajaxify') === false ) || $this->input->post('ocupload')){
			
			//check to see if method exists, throw error if false
			if(!method_exists($this, '_' . $arg0)){
				log_message('error', 'undefined method called via ajax: primary->' . $arg0);
				die(json_encode(array('success' => false)));
			}
			call_user_func(array($this, '_ajax_' . $arg0), $arg0);
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
		$header_data['additional_front_css'] = array(
//			'home.css',
//			'landing.css'
		);
		$header_data['additional_global_javascripts'] = array();
		$header_data['additional_global_css'] = array();
		//additional_js_properties are javascript variables defined in the global namespace, making them
			//available to code in included js files
		$header_data['additional_js_properties'] = array();
		/* ------------------------------- End prepare static asset urls ------------------------------- */

		
		# ----------------------------------------------------------------------------------- #
		#	BEGIN CONTROLLER METHOD ROUTING													  #
		# ----------------------------------------------------------------------------------- #	


		
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
		
		//call 'body' function and include all arguments/url-segments
		call_user_func(array($this, '_' . $arg0), $arg0);
		
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
	 * Display home page
	 * 
	 * @param 	first url segment
	 * @param	second url segment
	 * @return 	null
	 */
	private function _primary($arg0 = ''){
		
							
		if($vc_user = $this->session->userdata('vc_user')){
			$vc_user = json_decode($vc_user);
						
			$this->load->helper('run_gearman_job');
			$arguments = array('user_oauth_uid' 	=> $vc_user->oauth_uid,
								'access_token' 		=> $vc_user->access_token,
								'iterator_position' => false,
								'lang_locale'		=> $this->config->item('current_lang_locale'));
			run_gearman_job('news_feed_retrieve', $arguments);
		}

		$this->lang->load('home_auth', $this->config->item('current_lang'));
		$this->lang->load('home_unauth', $this->config->item('current_lang'));

		$this->body_html = $this->load->view($this->view_dir . 'view_front_primary_home', '', true);
		
	}
	
	
	/*******************************************************************************************************************
	 * 	END CONTROLLER VIEW DISPLAY FUNCTIONS
	 * 		Below functions are called via AJAX and helpers
	/ ******************************************************************************************************************/
	
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
			case 'news_feed_retrieve':
				
				
				
				
				
				
				if($this->input->post('status_check')){
					//check to see if job complete
					
					
						
					$this->load->helper('check_gearman_job_complete');
					check_gearman_job_complete('news_feed_retrieve');	
					
					/*
					
					
					if(!$news_feed_retrieve = $this->session->userdata('news_feed_retrieve'))
						die(json_encode(array('success' => false,
												'message' => 'No retrieve request found')));
													
					$news_feed_retrieve = json_decode($news_feed_retrieve);
					$news_feed_retrieve->attempt += 1;
					
					//check job status to see if it's completed
					$this->load->library('library_memcached', '', 'memcached');
					if($news_feed = $this->memcached->get($news_feed_retrieve->handle)){
						//free memory from memcached
						$this->memcached->delete($news_feed_retrieve->handle);
						$this->session->unset_userdata('news_feed_retrieve');
						
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
						
						
						if($news_feed_retrieve->attempt > 4)
							$this->session->unset_userdata('news_feed_retrieve'); //didn't finish in time
						else 
							$this->session->set_userdata('news_feed_retrieve', json_encode($news_feed_retrieve));
							
						die(json_encode(array('success' => false)));
					}
					
					 
					*/ 
			
			
				}else{
					//create new job
					
			
					if($vc_user = $this->session->userdata('vc_user')){
						
						$vc_user = json_decode($vc_user);
						$this->load->helper('run_gearman_job');
						$arguments = array('user_oauth_uid' => $vc_user->oauth_uid,
											'access_token' => $vc_user->access_token,
											'iterator_position' => $this->input->post('iterator'),
											'lang_locale'		=> $this->config->item('current_lang_locale'));
						
						run_gearman_job('news_feed_retrieve', $arguments);
						
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
}

/* End of file primary.php */
/* Location: ./application/controllers/primary.php */