<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auto_suggest extends MY_Controller {
	
	/*
	 * constructor for controller, checks to see if controller methods were called via ajax
	 * 
	 * @return	null
	 * */
	function __construct(){
		parent::__construct();
		
		//don't want users accessing this controller unless it's via an AJAX call
		if(!$this->input->is_ajax_request()){
			header('', true, 403);
			die(json_encode(array('success' => false,
									'message' => 'invalid access attempt')));
		}
	}
	
	/*
	 * Routing method of controller, decides what private method to call based 
	 * on 'method' parameter of post request
	 * 
	 * @param	?
	 * @return	?
	 * */
	function index(){		
		switch($this->input->post('vc_method')){
			case 'find_completions':
				$this->_find_completions();
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'unknown method: ' . $this->input->post('vc_method'))));
		}
	}

	# ----------------------------------------- #
	
	/*
	 * scans database for pattern matches between input string, promoters, and venues
	 * 
	 * @return	null
	 * */
	private function _find_completions(){

		$this->load->model('model_auto_suggest', 'auto_suggest', true);
		$result = $this->auto_suggest->retrieve_promoter_matches($this->input->post('search_pattern'));
		
		die(json_encode($result));
	}
	
	# ----------------------------------------- #
}

/* End of file auto_complete.php */
/* Location: ./application/controllers/ajax/auto_complete.php */