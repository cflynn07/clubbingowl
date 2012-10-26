<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error extends MY_Controller {
	
	
	/**
	 * Controller constructor. Perform any universal operations here.
	 * 
	 * @return	null
	 * */
	function __construct(){
		parent::__construct();
				
	}

	/**
	 * Error controller
	 */
	function index(){
		
		show_404('ERROR->INDEX');
		
	}
}

/* End of file error.php */
/* Location: ./application/controllers/error.php */