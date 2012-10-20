<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends MY_Controller {
	
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
	
	/**
	 * Routing method of controller, decides what private method to call based
	 * on 'method' parameter of post request
	 * 
	 * @param	?
	 * @return	?
	 */
	function index(){		
		switch($this->input->post('vc_method')){
			case 'session_login':
				$this->_session_login();
				break;
			case 'session_logout':
				$this->_session_logout();
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'unknown method: ' . $this->input->post('vc_method'))));
		}
	}
	
	/**
	 * call facebook api and get user information
	 * - check to see if user is new or already known, record if new
	 * - set session variables
	 * - respond to client
	 * 
	 * @return	null
	 */
	
	private function _session_login(){		
		
		//Is request new job or status check?
		if($this->input->post('status_check')){
			//Status check
			
			
			
			if(!$facebook_user_authenticate = $this->session->userdata('facebook_user_authenticate'))
				die(json_encode(array('success' => false,
										'message' => 'No login request')));	
													
			$facebook_user_authenticate = json_decode($facebook_user_authenticate);
			$facebook_user_authenticate->attempt += 1;
			
			//check job status to see if it's completed
			$this->load->library('Redis', '', 'redis');
			if($login_result = $this->redis->get($facebook_user_authenticate->handle)){
				//job complete
				
				//free memory from memcached
				$this->redis->del($facebook_user_authenticate->handle);
				$this->session->unset_userdata('facebook_user_authenticate');
				
				//unserialize
				$login_result = json_decode($login_result);	
								
				if($login_result->success){
					$message = $login_result->message;
				}else{
					//some kind of error?
					die(json_encode(array('success' => false)));
				}								
				
		//	passed
				
		//		$sql = "SELECT * FROM users WHERE oauth_uid = ?";
		//		$query = $this->db->query($sql, array('100003793147753'));
		//		$result = $query->result();
		//		var_dump($result);
		//		die();
				
				$this->_helper_set_user_session($message);
				return;
				
				
				/*
				
				# ----------------------------------------------------------------- #
				#	Send notify friends online to gearman as a background job		#
				# ----------------------------------------------------------------- #				
				//add job to a task
				$gearman_task = $this->pearloader->load('Net', 'Gearman', 'Task', array('func' => 'gearman_vc_user_notify_friends_online',
																						'arg'  => array('access_token' => $message->users_access_token,
																										'user_oauth_uid' => $message->users_oauth_uid)));
				$gearman_task->type = Net_Gearman_Task::JOB_BACKGROUND;
				
				//add test to a set
				$gearman_set = $this->pearloader->load('Net', 'Gearman', 'Set');
				$gearman_set->addTask($gearman_task);
				 
				//launch that shit
				$gearman_client->runSet($gearman_set);
				# --------------------------------------------------------------------- #
				#	END Send notify friends online to gearman as a background job		#
				# --------------------------------------------------------------------- #			
						
				
				
						
				//set session
				$vc_user = new stdClass;
				$vc_user->user_id = $message->users_id;
				$vc_user->oauth_uid = $message->users_oauth_uid;
				$vc_user->access_token = $message->users_access_token;
				$vc_user->access_token_expiration_time = $message->users_access_token_expiration_time;
				$vc_user->first_name = $message->users_first_name;
				$vc_user->last_name = $message->users_last_name;

				//forms user response
				$response_json['success'] = true;
				$response_json['oauth_uid'] = $vc_user->oauth_uid;
				$response_json['first_name'] = $vc_user->first_name;
				$response_json['last_name'] =  $vc_user->last_name;
				
				//is this user also a promoter, manager, or super_admin?
				if($message->users_promoter == '1'){
									
					$this->load->model('model_users_promoters', 'users_promoters', true);
					$promoter = $this->users_promoters->retrieve_promoter(array('users_oauth_uid' => $message->users_oauth_uid), array('completed_setup' => '', 'id_only' => true));
					
					//only assign this user as a promoter if they have a current team
					if($promoter){
						$vc_user->promoter = $promoter;
						$response_json['promoter'] = true;
					}					
					
				}
				
				if($message->users_manager == '1'){
				
					//assign venue id's that this manager owns to session
					$this->load->model('model_users_managers', 'users_managers', true);
					$vc_user->manager = $this->users_managers->retrieve_manager_team($message->users_oauth_uid);
					
					$response_json['manager'] = true;
					
				}
				
				if($message->users_super_admin == '1'){
					
					$vc_user->super_admin = true; //TODO
					$response_json['super_admin'] = true;
					
				}
				
				if($message->users_host == '1'){
					
					$this->load->model('model_users_hosts', 'users_hosts', true);
					$host = $this->users_hosts->retrieve_team_host($message->users_oauth_uid);
					
					if($host->th_banned == '0' && $host->th_quit == '0'){
						$vc_user->host = $host;
						$response_json['host'] = true;
					}
					
				}
				
				//check to see if user has any invitations
				$this->load->model('model_users', 'users', true);
				if($invitations = $this->users->retrieve_user_invitations($vc_user->oauth_uid)){
				
		//			$vc_user->invitations = $invitations;
					$response_json['invitations'] = $invitations;
										
				}

				$this->session->set_userdata('vc_user', json_encode($vc_user));
				die(json_encode($response_json));
			
				 * 
				 * */
				
				
				
				
				
				
							
			}else{
				
				if($facebook_user_authenticate->attempt > 9)
					$this->session->unset_userdata('facebook_user_authenticate'); //didn't finish in time
				else 
					$this->session->set_userdata('facebook_user_authenticate', json_encode($facebook_user_authenticate));
				
				//job not complete, come again.
				die(json_encode(array('success' => false)));
			}
			
			
			
			
			
			
			
		}else{
			//New job request
												
			//check to make sure ajax request contained an access_token, 
			//required for method to work
			if(!$this->input->post('access_token')){
				die(json_encode(array('success' => false,
										'message' => 'invalid access attempt, access_token not set.')));
			}
			
			
	//		$sql = "SELECT * FROM users WHERE oauth_uid = ?";
	//			$query = $this->db->query($sql, array('100003793147753'));
	//			$result = $query->result();
	//			var_dump($result);
	//			die();

			//------------------------------ QUICK LOGIN --------------------------------------
			$this->load->helper('facebook_signed_request_decode');
			$facebook_app_id = $this->config->item('facebook_app_id');
						
			if($fbsr_cookie = $this->input->cookie('fbsr_' . $facebook_app_id)){
				
				$fbsr_cookie = facebook_signed_request_decode($fbsr_cookie);
				
				if(isset($fbsr_cookie['user_id'])){
					
					//we can do a quick login attempt if this user already exists
					$this->load->model('model_users', 'users', true);
					if($vibecompass_user = $this->users->retrieve_user($fbsr_cookie['user_id'])){
						
						$access_token_expiration_time = intval($vibecompass_user->users_access_token_acquired_time) + intval($vibecompass_user->users_access_token_valid_seconds);
						$vibecompass_user->users_access_token_expiration_time = $access_token_expiration_time;
						
						if($access_token_expiration_time > (time() + 300)){

							//we already know this user and their access token hasn't expired, quick login success
							$this->_helper_set_user_session($vibecompass_user);
							return;

						}
					}
				}
			}
			//------------------------------ END QUICK LOGIN --------------------------------------
			
			
			$this->load->helper('run_gearman_job');
			$arguments = array('access_token' => $this->input->post('access_token'),
								'notify_admins' => true);
			run_gearman_job('facebook_user_authenticate', $arguments);
			
			//Send response to user telling them to come back and check in 1 second if their login
				//job has completed yet.
			die(json_encode(array('success' => true)));
				
		}
	}
	
	/*
	 * unsets session variables
	 * 
	 * @return	null
	 * */
	private function _session_logout(){
		$this->session->unset_userdata('vc_user');
		
		die(json_encode(array('success' => true)));
	}
	
	/**
	 * Sets the user session from a long or short login
	 * 
	 * @param	object (vc_user)
	 * @return	null
	 */
	private function _helper_set_user_session($message){
				
		$this->load->helper('run_gearman_job');
		$arguments = array('access_token' => $message->users_access_token,
								'user_oauth_uid' => $message->users_oauth_uid);
		run_gearman_job('gearman_vc_user_notify_friends_online', $arguments, false);		
				
								
		//passed						
	//	$sql = "SELECT * FROM users WHERE oauth_uid = ?";
	//			$query = $this->db->query($sql, array('100003793147753'));
	//			$result = $query->result();
	//			var_dump($result);
	//			die();						
								
		//set session
		$vc_user = new stdClass;
		$vc_user->user_id = $message->users_id;
		$vc_user->oauth_uid = $message->users_oauth_uid;
		$vc_user->access_token = $message->users_access_token;
		$vc_user->access_token_expiration_time = $message->users_access_token_expiration_time;
		$vc_user->first_name = $message->users_first_name;
		$vc_user->last_name = $message->users_last_name;
		$vc_user->last_activity = time();
				
		//forms user response
		$response_json['success'] = true;
		$response_json['oauth_uid'] = $vc_user->oauth_uid;
		$response_json['first_name'] = $vc_user->first_name;
		$response_json['last_name'] =  $vc_user->last_name;
				
		//is this user also a promoter, manager, or super_admin?
		if($message->users_promoter == '1'){
							
			$this->load->model('model_users_promoters', 'users_promoters', true);
			$promoter = $this->users_promoters->retrieve_promoter(array('users_oauth_uid' => $message->users_oauth_uid), array('completed_setup' => '', 'id_only' => true));
			
			//only assign this user as a promoter if they have a current team
			if($promoter){
				$vc_user->promoter = $promoter;
				$response_json['promoter'] = true;
			}					
			
		}
		
		if($message->users_manager == '1'){
		
			//assign venue id's that this manager owns to session
			$this->load->model('model_users_managers', 'users_managers', true);
			$vc_user->manager = $this->users_managers->retrieve_manager_team($message->users_oauth_uid);
			
			$response_json['manager'] = true;
			
		}
		
	// passed	
	//	echo '2' . PHP_EOL;
	//	$sql = "SELECT * FROM users WHERE oauth_uid = ?";
	//			$query = $this->db->query($sql, array('100003793147753'));
	//			$result = $query->result();
	//			var_dump($result);
	//			die();
		
		if($message->users_super_admin == '1'){
			
			$vc_user->super_admin = true; //TODO
			$response_json['super_admin'] = true;
			
		}
		
		if($message->users_host == '1'){
			
			$this->load->model('model_users_hosts', 'users_hosts', true);
			$host = $this->users_hosts->retrieve_team_host($message->users_oauth_uid);
			
			if($host->th_banned == '0' && $host->th_quit == '0'){
				$vc_user->host = $host;
				$response_json['host'] = true;
			}
			
		}
		
		//check to see if user has any invitations
		$this->load->model('model_users', 'users', true);
		if($invitations = $this->users->retrieve_user_invitations($vc_user->oauth_uid)){
		
//			$vc_user->invitations = $invitations;
			$response_json['invitations'] = $invitations;
								
		}

//echo '3----' . PHP_EOL;
//$sql = "SELECT * FROM users WHERE oauth_uid = ?";
//				$query = $this->db->query($sql, array('100003793147753'));
//				$result = $query->result();
//				var_dump($result);
//				die();


		$this->session->set_userdata('vc_user', json_encode($vc_user));
		die(json_encode($response_json));
		
	}
	
	/*
	 * calls mailchimp api and adds this user to a mailing list
	 * 
	 * @param	array (facebook user info)
	 * @return	null
	 * */
	private function _add_to_mailing_list($fb_user_info){
		$this->load->library('MCAPI');
		
		$listId = 'f2af86af55'; //I had to get this via an annoying api call...
		$email = $fb_user_info['email'];
				
		$merge_vars = array('FNAME'=>$fb_user_info['first_name'], 
							'LNAME'=>$fb_user_info['last_name'], 
							'INTERESTS'=>'');

		//By default this sends a confirmation email 
		$retval = $this->mcapi->listSubscribe($listId, $email, $merge_vars);
		
		//log any errors that occur
		if ($this->mcapi->errorCode)
			log_message('error', 'Unable to add email to mailing list from _add_to_mailing_list(): ' . $email);
	}
}

/* End of file auth.php */
/* Location: ./application/controllers/ajax/auth.php */