<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Rtupdates extends MY_Controller {

	function __construct(){
				
		parent::__construct();
	}
	

	public function index(){
		
		//cf5d812bb1b187b73d709820fbb9f073
		
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			
		    if(array_key_exists('hub_mode', $_GET) 
		        && array_key_exists('hub_challenge', $_GET) 
		        && array_key_exists('hub_verify_token', $_GET)
		        && $_GET['hub_mode'] == 'subscribe' 
		        && $_GET['hub_verify_token'] == 'cf5d812bb1b187b73d709820fbb9f073')
		        	die($_GET['hub_challenge']);
		
		
		}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
		
		    $payload = file_get_contents('php://input');
		    if(!empty($payload) 
		        && array_key_exists($_SERVER, 'HTTP_X_HUB_SIGNATURE')
		        && $_SERVER['HTTP_X_HUB_SIGNATURE'] == 'sha1='
		              .hash_hmac('sha1', $payload, $this->config->item('facebook_api_secret'))){
		              		
							$this->load->library('library_facebook', '', 'facebook');
							$json = json_decode($payload);
							
							
							foreach($json->entry as $entry){
								
								$entry = (object)$entry;
								$fql = "SELECT
											uid, 
											name,
											email,
											first_name,
											last_name,
											third_party_id,
											sex,
											username,
											timezone
										FROM user
										WHERE uid = " . $entry->uid;
								$fb_user_info = $CI->facebook->fb_fql_query($fql);
								if(!isset($fb_user_info[0])){
									return;
								}
								$fb_user_info = $fb_user_info[0];
								//did facebook api return an error?
								if(array_key_exists('error', $fb_user_info)){
									return;
								}
								
								$this->db->where(array('oauth_uid' => $fb_user_info['uid']))
									->update('users', array(
										'email'			=> $fb_user_info['email'],
										'first_name'	=> $fb_user_info['first_name'],
										'last_name'		=> $fb_user_info['last_name'],
										'full_name'		=> $fb_user_info['name']
									));
								
							}
							
							
							
							
							
							
		              }
		}
		
		
		
	}
	
	
}

/* End of file rtupdates.php */
/* Location: ./application/controllers/rtupdates.php */