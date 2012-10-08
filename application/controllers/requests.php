<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller that displays application facebook-app-requests to users who were invited to
 * join a guest list or be a promoter
 * 
 */
class Requests extends MY_Controller {
	
	// Base path of views unique to this controller
	private $view_dir = 'front/requests/';
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
		if($this->input->is_ajax_request() || $this->input->post('ocupload')){
			
			$arg0 = 'primary';
			
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
		
		$header_data['additional_front_css'] = array(
//			'home.css',
//			'landing.css'
		);
		
		$arg0 = 'primary';
		
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
		
		//display header view
		$this->header_html = $this->load->view('front/view_front_header', '', true);
		
		//Display the footer view after the header/body views have been displayed
		$this->footer_html = $this->load->view('front/view_front_footer', '', true);
		
		# ---------------- END LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		
		//Construct final output for browser
		$this->output->set_output($this->header_html . $this->body_html . $this->footer_html);
		
	}
	
	/*******************************************************************************************************************
	 * 	END CONTROLLER ENTRY POINT ROUTING FUNCTIONS
	 * 		Below functions called based on arguments to index function. They are responsible for rendering page content
	/ ******************************************************************************************************************/
	
	/**
	 * Displays returned results from a facebook app-request
	 * Such as:
	 * 		- Guest List / Event entourage requests
	 * 		- Promoter Join Requests
	 * 
	 * @param 	first url segment
	 * @param	second url segment
	 * @return 	null
	 */
	private function _primary($arg0 = ''){
		
		/*
			Kint::dump($this->session->userdata('vc_user')); 
			$retrieve_facebook_app_requests = $this->session->userdata('retrieve_facebook_app_requests');
			Kint::dump($retrieve_facebook_app_requests); die();
		*/
			
		if($retrieve_facebook_app_requests = $this->session->userdata('retrieve_facebook_app_requests')){
			//page is being visited after a retrieve_facebook_app_requests job was started from the application's 'app page'
			
			/*	NOT ENOUGH TIME FROM REDIRECT TO THIS CONTROLLER FOR API CALL TO FINISH, CHECK WITH POLLING
			
			$retrieve_facebook_app_requests = json_decode($retrieve_facebook_app_requests);
			
			//check job status to see if it's completed
			$this->load->library('library_memcached', '', 'memcached');
			if($requests = $this->memcached->get($retrieve_facebook_app_requests->handle)){
				//free memory from memcached
				$this->memcached->delete($retrieve_facebook_app_requests->handle);
				$this->session->unset_userdata('retrieve_facebook_app_requests');
				
				$requests = json_decode($requests);
				
			}
			*/
			
			$data['requests'] = $requests = false;
			
			$header_custom = new stdClass;
			$header_custom->url = base_url();
			$header_custom->title_prefix = 'Friend Requests | ';
			$header_custom->page_description = 'Friend Requests';
			$this->load->vars('header_custom', $header_custom);
			
			$this->body_html = $this->load->view($this->view_dir . 'view_front_primary_app_requests', $data, true);	
			
		}else{
			//page is improperly being visited, redirect to home page
			
			redirect('/');
			
		}
		
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
			case 'request_feed_retrieve':
				if($this->input->post('status_check')){
					//check to see if job complete
					
					/*
					if(!$retrieve_facebook_app_requests = $this->session->userdata('retrieve_facebook_app_requests'))
						die(json_encode(array('success' => false,
												'message' => 'No retrieve request found')));	
													
					$retrieve_facebook_app_requests = json_decode($retrieve_facebook_app_requests);
					
					//check job status to see if it's completed
					$this->load->library('library_memcached', '', 'memcached');
					if($request_feed = $this->memcached->get($retrieve_facebook_app_requests->handle)){
						//free memory from memcached
						$this->memcached->delete($retrieve_facebook_app_requests->handle);
						$this->session->unset_userdata('retrieve_facebook_app_requests');
						die($request_feed); //<-- already json in memcache
					}else{
						die(json_encode(array('success' => false)));
					}
					*/
					
					$this->load->helper('check_gearman_job_complete');
					check_gearman_job_complete('retrieve_facebook_app_requests');
					
				}	
					
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'Unknown vc_method.')));
				break;				
				
		}
		
	}	
}

/* End of file requests.php */
/* Location: ./application/controllers/requests.php */