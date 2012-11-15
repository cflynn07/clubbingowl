<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pusher_presence extends MY_Controller {
	
	private $vc_user;
	
	/**
	 * constructor for controller, checks to see if controller methods were called via ajax
	 * 
	 * @return	null
	 */
	function __construct(){
		parent::__construct();
		
		$this->load->library('session');
		
		$this->vc_user = false;
		if($this->session->userdata('vc_user'))
			$this->vc_user = json_decode($this->session->userdata('vc_user'));
		
		$this->load->library('pusher');
		$this->session->keep_flashdata('manage_image');

	}
	
	/**
	 * Routing method of controller, decides what private method to call based 
	 * on 'method' parameter of post request
	 * 
	 * @param	?
	 * @return	?
	 * */
	function index(){
		
		if($this->input->post('socket_id') && $this->input->post('channel_name')){
			//this is an authentication request
			$this->_pusher_auth_request();
			return;
		}
		
		if($this->input->post('vc_method') === FALSE)
			die(json_encode(array('success' => false)));
		
		switch($this->input->post('vc_method')){
			default:
				die(json_encode(array('success' => false,
										'message' => 'unknown method: ' . $this->input->post('vc_method'))));
		}
	}

	/**
	 * Pusher authentication requests
	 * 
	 * @return	null
	 */
	private function _pusher_auth_request(){
		$socket_id 		= $this->input->post('socket_id');
		$channel_name 	= $this->input->post('channel_name');
		$promoter_id 	= $this->input->post('promoter_id');
		
		$vc_channel 	= $this->input->post('vc_channel');		
		
		if($this->vc_user){
			
	//		if($promoter_id){
				
				$data['promoter_id'] = $promoter_id;
				$result = $this->pusher->presence_auth($channel_name, $socket_id, $this->vc_user->oauth_uid, $data);		
				die($result);
				
	//		}else{
	//			$result = $this->pusher->socket_auth($channel_name, $socket_id);		
	//			die($result);
	//		}
	//			
		}else{
			
			header('', true, 403);
			die('Not authorized');
			
		}
		
	}
	
}

/* End of file pusher_presence.php */
/* Location: ./application/controllers/ajax/pusher_presence.php */