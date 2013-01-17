<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Development extends MY_Controller {
	
	// Base path of views unique to this controller
	private $view_dir 		= 'emails/';
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
		
		if(MODE != 'local')
			show_404();
		
	}
	
	/*
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
	public function index($arg0 = 'primary', $arg1 = '', $arg2 = ''){
		
		
	//	Kint::dump($arg0);
		$key = $arg0;
			
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
			
			
		
		$arg0 = 'primary';
		
		# ----------------------------------------------------------------------------------- #
		#	END CONTROLLER METHOD ROUTING													  #
		# ----------------------------------------------------------------------------------- #	
		
		
		$this->_primary($arg0, $arg1);
		
		
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
	 *
	 * 
	 * @param 	first url segment
	 * @param	second url segment
	 * @return 	null
	 */
	private function _primary($arg0 = '', $arg1 = ''){
		
		
		$email_data = new stdClass;
		$email_data->to_user = new stdClass;
		$email_data->to_user->email_opts_hash 	= '9999';
		$email_data->to_user->u_first_name 		= 'Casey';
		$email_data->to_user->u_last_name 		= 'Flynn';
		$email_data->to_user->u_full_name 		= 'Casey Flynn';
		

		$this->body_html = $this->load->view($this->view_dir . 'view_email_new_promoter_status', array('email_data' => $email_data), true);
		
	}	
}

/* End of file development.php */
/* Location: ./application/controllers/development.php */