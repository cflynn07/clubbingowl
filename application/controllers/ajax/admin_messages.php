<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_messages extends MY_Controller {
	
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
		$this->session->keep_flashdata('manage_image');
		
		$vc_user = json_decode($this->session->userdata('vc_user'));
						
		//don't want users accessing this controller unless it's via an AJAX call
		//checks to see if current user is logged in and is a manager, promoter or host
		if(!$vc_user){
			header('', true, 403);
			die(json_encode(array('success' => false,
									'message' => 'invalid access attempt')));
		}
		
		$admin_panel = $this->input->post('admin_panel');				
		switch($admin_panel){
			case 'managers':
				
				if(isset($vc_user->manager)){
					
					$this->teams_fan_page_id = $vc_user->manager->team_fan_page_id;
					
				}else{
					header('', true, 403);
					die('Not authorized');
				}
				
				break;
			case 'promoters':
				
				if(isset($vc_user->promoter)){		
					
					$this->teams_fan_page_id = $vc_user->promoter->t_fan_page_id;
					
				}else{
					header('', true, 403);
					die('Not authorized');
				}
				
				break;
			case 'hosts':
				
				if(isset($vc_user->host)){
					
					$this->teams_fan_page_id = $vc_user->host->th_teams_fan_page_id;
					
				}else{
					header('', true, 403);
					die('Not authorized');
				}
				
				break;
			default:
				
				header('', true, 403);
				die('Not authorized');
				
				break;
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
		
		if($this->input->post('socket_id') && $this->input->post('channel_name')){
			//this is an authentication request
			$this->_pusher_auth_request();
			return;
		}
		
		if($this->input->post('vc_method') === FALSE)
			die(json_encode(array('success' => false)));
		
		switch($this->input->post('vc_method')){
			case 'new':
				$this->_new_message();
				break;
			case 'read':
				$this->_read_init();
				break;
			case 'alert_active':
				$this->_alert_active();
				break;
			case 'alert_inactive':
				$this->_alert_inactive();
				break;
			case 'chat_activity':
				$this->_alert_chat_activity();
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'unknown method: ' . $this->input->post('vc_method'))));
		}
	}

	/**
	 * Notifies other users in team chat that a user has begun to type a message
	 * 
	 * @return 	null
	 */
	private function _alert_chat_activity(){
		
		$chat_activity = $this->input->post('chat_activity');
		$this->pusher->trigger('presence-' . $this->teams_fan_page_id, 'user_chat_activity', array('oauth_uid' => $this->users_oauth_uid,
																									'chat_activity' => $chat_activity));
													
		die(json_encode(array('success' => true)));
		
	}

	/**
	 * Creates a new message to team from a team member
	 * 
	 * @return	null
	 */
	private function _new_message(){
		
		$message = $this->input->post('message');
		if(!$message)
			die(json_encode(array('success' => false)));
		
		$message_id = $this->team_messaging->create_message(array('users_oauth_uid' => $this->users_oauth_uid, 
													'teams_fan_page_id' => $this->teams_fan_page_id,
													'message_content' => $message));
													
		$this->pusher->trigger('presence-' . $this->teams_fan_page_id, 'new', array('oauth_uid' => $this->users_oauth_uid,
																					'message' => $message,
																					'm_id' => $message_id));
													
		die(json_encode(array('success' => true)));
		
	}
	
	/**
	 * Retrieve last 100 promoter messages on initial load of chat feature
	 * 
	 * @return	null
	 */
	private function _read_init(){
		
		$result = $this->team_messaging->retrieve_init(array('teams_fan_page_id' => $this->teams_fan_page_id,
																'users_oauth_uid' => $this->users_oauth_uid));
		
		die(json_encode($result));
		
	}
	
	/**
	 * Pusher authentication requests
	 * 
	 * @return	null
	 */
	private function _pusher_auth_request(){
		$socket_id = $this->input->post('socket_id');
		$channel_name = $this->input->post('channel_name');
		
		$admin_panel = $this->input->post('admin_panel');
		$vc_user = json_decode($this->session->userdata('vc_user'));
				
		switch($admin_panel){
			case 'managers':
				
				if(isset($vc_user->manager)){
					if('presence-' . $vc_user->manager->team_fan_page_id !== $channel_name){
						header('', true, 403);
						die('Not authorized');
					}
				}else{
					header('', true, 403);
					die('Not authorized');
				}
				
				break;
			case 'promoters':
				
				if(isset($vc_user->promoter)){		
					if('presence-' . $vc_user->promoter->t_fan_page_id !== $channel_name){
						header('', true, 403);
						die('Not authorized');
					}
				}else{
					header('', true, 403);
					die('Not authorized');
				}
				
				break;
			case 'hosts':
				
				if(isset($vc_user->host)){
					if('presence-' . $vc_user->host->th_teams_fan_page_id !== $channel_name){
						header('', true, 403);
						die('Not authorized');
					}
				}else{
					header('', true, 403);
					die('Not authorized');
				}
				
				break;
			default:
				
				header('', true, 403);
				die('Not authorized');
				
				break;
		}
		
		$this->team_messaging->delete_inactive($this->users_oauth_uid, $this->teams_fan_page_id);
		$this->pusher->trigger('presence-' . $this->teams_fan_page_id, 'member_active', array('id' => $this->users_oauth_uid));
		
		$result = $this->pusher->presence_auth($channel_name, $socket_id, $vc_user->oauth_uid, null);		
		die($result);
	}
	
	/**
	 * Pusher notification that user is active
	 * 
	 * @return	null
	 */
	private function _alert_active(){
		
		$this->team_messaging->delete_inactive($this->users_oauth_uid, $this->teams_fan_page_id);
		
		$this->pusher->trigger('presence-' . $this->teams_fan_page_id, 'member_active', array('id' => $this->users_oauth_uid));
		
		die(json_encode(array('success' => true)));
		
	}

	/**
	 * Pusher notification that user is inactive
	 * 
	 * @return	null
	 */
	private function _alert_inactive(){
		
		$this->team_messaging->create_inactive($this->users_oauth_uid, $this->teams_fan_page_id);
		
		$this->pusher->trigger('presence-' . $this->teams_fan_page_id, 'member_inactive', array('id' => $this->users_oauth_uid));
		
		die(json_encode(array('success' => true)));
		
	}
	
}

/* End of file admin_messages.php */
/* Location: ./application/controllers/ajax/admin_messages.php */