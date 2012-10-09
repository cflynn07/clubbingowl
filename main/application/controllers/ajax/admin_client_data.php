<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_client_data extends MY_Controller {
	
	private $users_oauth_uid;
	private $teams_fan_page_id;
	
	/**
	 * constructor for controller, checks to see if controller methods were called via ajax
	 * 
	 * @return	null
	 */
	function __construct(){
		parent::__construct();
		
		$this->load->library('session');
		
		$vc_user = json_decode($this->session->userdata('vc_user'));
						
		//don't want users accessing this controller unless it's via an AJAX call
		//checks to see if current user is logged in and is a manager, promoter or host
		if(!$vc_user){
			header('', true, 403);
			die(json_encode(array('success' => false,
									'message' => 'not authenticated')));
		}
		
		//make sure this user is a promoter or a manager
		if(!isset($vc_user->promoter)){
			if(!isset($vc_user->manager)){
				header('', true, 403);
				die(json_encode(array('success' => false,
										'message' => 'not authorized')));
			}
		}
		
		if(isset($vc_user->promoter->t_fan_page_id)){
			$this->teams_fan_page_id = $vc_user->promoter->t_fan_page_id;
		}elseif(isset($vc_user->manager->team_fan_page_id)){
			$this->teams_fan_page_id = $vc_user->manager->team_fan_page_id;
		}
		
		$this->users_oauth_uid = $vc_user->oauth_uid;
		
		$this->load->library('pusher');
		$this->load->model('model_team_messaging', 'team_messaging', true);
		
	}
	
	/**
	 * Routing method of controller, decides what private method to call based 
	 * on 'method' parameter of post request
	 * 
	 * @param	?
	 * @return	?
	 * */
	function index(){
		
		if($this->input->post('vc_method') === FALSE)
			die(json_encode(array('success' => false)));
		
		switch($this->input->post('vc_method')){
			case 'retrieve':
				$this->_retrieve();
				break;
			case 'save_notes':
				$this->_save_notes();
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'unknown method')));
		}
	}
	
	
	/**
	 * Retrieve the basic data for this client, confirms requested user is client,
	 * delivers different data based on whether or not requesting user is promoter or manager
	 * 
	 * @return 	null
	 */
	private function _retrieve(){
		
		
		
	}
	
	/**
	 * Save notes on a specific client for a specific promoter or manager
	 * 
	 * @return 	null
	 */
	private function _save_notes(){
		
		
		
	}
}

/* End of file admin_client_data.php */
/* Location: ./application/controllers/ajax/admin_client_data.php */