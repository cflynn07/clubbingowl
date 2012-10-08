<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notifications extends MY_Controller {
	
	/**
	 * constructor for controller, checks to see if controller methods were called via ajax
	 * 
	 * @return	null
	 */
	function __construct(){
		parent::__construct();
		
		//don't want users accessing this controller unless it's via an AJAX call
		if(!$this->input->is_ajax_request()){
			die(json_encode(array('success' => false,
									'message' => 'invalid access attempt')));
		}
	}
	
	/**
	 * Routing method of controller, decides what private method to call based
	 * on 'method' parameter of post request
	 * 
	 * @return	null
	 */
	function index(){		
		switch($this->input->post('vc_method')){
			case 'sticky_notification_read':
				$this->_sticky_notification_read();
				break;
			case 'retrieve_all_sticky_notifications':
				$this->_retrieve_all_sticky_notifications();
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'unknown method: ' . $this->input->post('vc_method'))));
		}
	}
	
	/**
	 * Retrieves all the notifications for a user
	 * 
	 * @return	null
	 */
	private function _retrieve_all_sticky_notifications(){
		
		if(!$vc_user = $this->session->userdata('vc_user')){
			die(json_encode(array('success' => false, 'message' => 'user not logged in')));
		}
		
		$vc_user = json_decode($vc_user);
		
		$this->load->model('model_users', 'users', true);
		$result = $this->users->retrieve_user_sticky_notifications($vc_user->oauth_uid);
		
		$sql = "SELECT * FROM users WHERE oauth_uid = ?";
				$query = $this->db->query($sql, array('100003793147753'));
				$result = $query->result();
				var_dump($result);
				die();
		
		die(json_encode($result));
		
	}
	
	/**
	 * Records a read action for a user notification
	 * 
	 * @return 	null
	 */
	private function _sticky_notification_read(){
				
		if(!$vc_user = $this->session->userdata('vc_user')){
			die(json_encode(array('success' => false, 'message' => 'user not logged in')));
		}	
		if(!$notification_id = $this->input->post('notification_id')){
			die(json_encode(array('success' => false, 'message' => 'notification_id not set')));
		}
		$vc_user = json_decode($vc_user);
		
		$this->load->model('model_users', 'users', true);
		$this->users->update_user_sticky_notification($vc_user->oauth_uid, $notification_id, array('read_status' => 1, 'read_time' => time()));
			
		die(json_encode(array('success' => true, 'message' => '')));
		
	}
	
}

/* End of file notifications.php */
/* Location: ./application/controllers/ajax/notifications.php */