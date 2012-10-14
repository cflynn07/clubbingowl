<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for events showcase page
 * 
 */
class Corp extends MY_Controller {
	
	// Base path of views unique to this controller
	private $view_dir = 'front/corp/';
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
	 * /events/$arg0/$arg1/
	 * Control point, chooses private method to handle request based on URL
	 * Example: 
	 * 		This details what url segments correspond to arguments for this index function
	 * 		www.vibecompass.com/events/
	 * 			- $arg0 = ''
	 * 			- $arg1 = ''
	 * 			
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @return	null
	 * */
	public function index($arg0 = '', $arg1 = ''){

		/* --------------------- Load promoter library ------------------------ */
		//load the promoter library if a promoter public identifier is available
		if($arg0 != ''){
		//	$this->load->library('library_friends');
		//	$this->library_profile->initialize();
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
			if($arg0 == ''){
				$arg0 = 'primary';
			}
			
			//check to see if method exists, throw error if false
			if(!method_exists($this, '_' . $arg0)){
				log_message('error', 'undefined method called via ajax: events->' . $arg0);
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
		 * /events/
		 * Examples: 
		 * 		/friends/
		 * 
		 * 		$arg0 = ''
		 * 		$arg1 = ''
		 * */
		if($arg0 == ''){
			
			show_404();
			$arg0 = 'primary';
			
		}
		/*
		 * /events/[some yet to be determined subsection]
		 * 
		 * 		$arg0 = '[some yet to be determined subsection]'
		 * 		$arg1 = ''
		 * 
		 * */
		elseif($arg0 != '' && $arg1 == ''){
						
			switch($arg0){
				case 'tos':
					break;
				default:
					show_404();
			}
			
		}
		/*
		 * /events/[some yet to be determined subsection]/{SOMETHING FAKE}
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
		
		//call 'body' function and include all arguments/url-segments
		call_user_func(array($this, '_' . $arg0), $arg0, $arg1);
		
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
	 * Showcase events home page
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @return	null
	 */
	private function _primary($arg0 = '', $arg1 = ''){
		
		$header_custom = new stdClass;
		$header_custom->url = base_url() . 'corp/';
		$header_custom->title_prefix = $this->lang->line('ad-vc_team') . ' | ';
		$this->load->vars('header_custom', $header_custom);

		

		$this->body_html = $this->load->view($this->view_dir . 'view_front_corp_team', '', true);
		
	}
	
	/**
	 * TOS Overview Page
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @return	null
	 */
	private function _tos($arg0 = '', $arg1 = ''){
				
		$header_custom = new stdClass;
		$header_custom->url = base_url() . 'corp/tos/';
		$header_custom->title_prefix = $this->lang->line('ad-tos') . ' | ';
		$this->load->vars('header_custom', $header_custom);

		$this->body_html = $this->load->view($this->view_dir . 'view_front_corp_TOS', '', true);
				
	}
	
	/*******************************************************************************************************************
	 * 	END CONTROLLER VIEW DISPLAY FUNCTIONS
	 * 		Below functions are called via AJAX and helpers
	/ ******************************************************************************************************************/
	
}

/* End of file corp.php */
/* Location: ./application/controllers/corp.php */