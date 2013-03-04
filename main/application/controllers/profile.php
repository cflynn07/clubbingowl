<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for users profile page.
 * 
 * Show guest lists (join + accept status)
 * Messages
 */
class Profile extends MY_Controller {
	
	// Base path of views unique to this controller
	private $view_dir = 'front/profile/';
	
	//vc_user object
	private $vc_user;
	
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
		
		//check if user is authenticated
		if(!$vc_user = $this->session->userdata('vc_user')){
			//if user is not authenticated, make sure they're only able to visit /profile/
			redirect('/');
			die();
		}
		
		$this->vc_user = json_decode($vc_user);
		
	}
	
	/**
	 * /profile/$arg0/$arg1/
	 * Control point, chooses private method to handle request based on URL
	 * Example: 
	 * 		This details what url segments correspond to arguments for this index function
	 * 		www.vibecompass.com/profile/
	 * 			- $arg0 = ''
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
			//we want these methods to fire but we don't want to force users to supply extra url segment
			if($arg0 == ''){
				$arg0 = 'primary';
			}
			
			//check to see if method exists, throw error if false
			if(!method_exists($this, '_' . $arg0)){
				log_message('error', 'undefined method called via ajax: profile->' . $arg0);
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
		 * /profile/
		 * Examples: 
		 * 		/profile/
		 * 
		 * 		$arg0 = ''
		 * 		$arg1 = ''
		 * */
		if($arg0 == ''){
			//no promoter is specified, showcase all promoters in system
			
			$header_data['additional_front_css'] = array(
	//			'history.css'
			);
			
			//display preview of all promoters
			$method = 'primary';
			
		}
		/*
		 * /profile/messages/
		 * 
		 * 		$arg0 = 'messages'
		 * 		$arg1 = ''
		 * 
		 * */
		elseif($arg0 != '' && $arg1 == ''){
			
			switch($arg0){
				default:
					show_404();
					break;
			}
			
		}
		/*
		 * /promoters/messages/{SOMETHING FAKE}
		 * 
		 * 		$arg0 = 'messages'
		 * 		$arg1 = '{SOMETHING FAKE}'
		 * 
		 * */
		elseif($arg0 != '' && $arg1 != '' && $arg2 == ''){
			
			show_404('invalid url');
			
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
	 * Showcase user profile home page
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @return	null
	 */
	private function _primary($arg0 = '', $arg1 = ''){
		
		$this->load->model('model_users', 'users', true);
		$data['user'] 					= $this->users->retrieve_user($this->vc_user->oauth_uid);
		$data['reservation_requests'] 	= $this->users->retrieve_user_reservation_requests($this->vc_user->oauth_uid);		
		$data['favorite_promoters'] 	= $this->users->retrieve_users_favorite_promoters($this->vc_user->oauth_uid);

		$cmp_function = function($a, $b){
			
			if(isset($a->tgl_date)){
				$a_date = $a->tgl_date;
			}elseif(isset($a->pgl_date)){
				$a_date = $a->pgl_date;
			}
			
			
			if(isset($b->tgl_date)){
				$b_date = $b->tgl_date;
			}elseif(isset($b->pgl_date)){
				$b_date = $b->pgl_date;
			}
			
			return strtotime($b_date) - strtotime($a_date);
		//	return strtotime($a_date) - strtotime($b_date);
		};
		
		$data['reservation_requests'] = array_merge($data['reservation_requests']->team_result, $data['reservation_requests']->promoter_result);

		usort($data['reservation_requests'], $cmp_function);

		$this->body_html = $this->load->view('front/_common/view_front_invite', '', true);	
		$this->body_html .= $this->load->view($this->view_dir . 'view_front_profile_home', $data, true);
		
		$header_custom = new stdClass;
		$header_custom->title_prefix = lang_key($this->lang->line('ad-vc_user_profile'), array(
			'user_name' => $data['user']->users_full_name
		)) . ' | ';
		$this->load->vars('header_custom', $header_custom);
	}
	
	/**
	 * 
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @return	null
	 */
	private function _settings($arg0 = '', $arg1 = ''){
		
		$this->load->model('model_users', 'users', true);
		
		$data['user'] = $this->users->retrieve_user($this->vc_user->oauth_uid);
		
		$this->load->view($this->view_dir . 'view_profile_settings', $data);
				
	}
	
	
	/*******************************************************************************************************************
	 * 	END CONTROLLER VIEW DISPLAY FUNCTIONS
	 * 		Below functions are called via AJAX and helpers
	/ ******************************************************************************************************************/
	
	/**
	 * AJAX functionality for primary controller
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @return	null
	 */
	private function _ajax_primary($arg0 = '', $arg1 = ''){
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'Invalid request')));
		}
		
		switch($vc_method){
			case 'update_settings':
				
				$opt_out_email = ($this->input->post('opt_out_email') == 'true') ? 1 : 0;
			//	$opt_out_search = ($this->input->post('opt_out_search') == 'true') ? 1 : 0;
				
				
				$this->db->where(array(
					'oauth_uid' => $this->vc_user->oauth_uid
				))->update('users', array(
					'opt_out_email' => $opt_out_email
				));
				
				
			//	$this->load->model('model_users', 'users', true);
			//	$this->users->update_user($this->vc_user->oauth_uid, array('opt_out_email' => $opt_out_email));
				
				
				
				die(json_encode(array('success' => true,
										'message' => '')));
				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'Unknown vc_method')));
				break;
		}
		
	}
	
	/**
	 * AJAX functionality for settings controller
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @return	null
	 */
	private function _ajax_settings($arg0 = '', $arg1 = ''){
		

		
	}
	
}

/* End of file profile.php */
/* Location: ./application/controllers/profile.php */