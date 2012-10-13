<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Worker extends CI_Controller {
	
    public function __construct() {
    	parent::__construct();
		//force only access from command line
        if(php_sapi_name() !== 'cli') {
            show_404();
        }
		
	//	error_reporting(E_ALL);
	//	ini_set('display_errors', '1');
		
    }
	
	
	
	
	/**
	 * Email a user's vibecompass friends when they join VibeCompass or join a guest-list
	 * 
	 */
	public function worker_email_vc_user_friends($oauth_uid = "504405294"){
		
		
		$this->db->where(array('oauth_uid' => $oauth_uid));
		$query = $this->db->get('users');
		$result = $query->row();
		
		
		
		
		/*	
		$this->load->helper('run_gearman_job');
		$arguments = array(
			'user_oauth_uid'	=> $result->oauth_uid,
			'access_token'		=> $result->access_token
		);
		
		run_gearman_job('gearman_email_friends_new_user', $arguments, false);
		
		*/
		
		
		
		
		
		//user just logged in, notify friends
		$this->load->helper('run_gearman_job');
		$arguments = array('access_token' => $result->access_token,
								'user_oauth_uid' => $result->oauth_uid);
		run_gearman_job('gearman_vc_user_notify_friends_online', $arguments, false);
		
		return;
				
	}
	
	
	
	
	public function test(){
	
		echo 'brazin test' . PHP_EOL;
	
		
		
		return;
	
		$this->load->library('Cloudcontrol', '', 'cloudcontrol');
	    $email = 'cflynn@ccs.neu.edu';
	    $password = 'w3smsnicq34';
	    $this->cloudcontrol->auth($email, $password);
	    $this->cloudcontrol->worker_create('vibecompass', 'default', 'index.php', 'worker all');
		
		echo 'done!' . PHP_EOL;		
				
	}
	
	
	
	
	/**
	 * Worker that performs all tasks, mostly used for development/simplicity
	 * 
	 */
	public function all($arg1 = ''){
		
		try{
 			
			echo 'all worker starting' . PHP_EOL;
		
			$this->load->library('pearloader');
			$gearman_worker = $this->pearloader->load('Net', 'Gearman', 'Worker');	
						
			$gearman_worker->addAbility('facebook_user_authenticate');
						
			$gearman_worker->addAbility('guest_list_text_message');
			$gearman_worker->addAbility('guest_list_share_facebook');
			$gearman_worker->addAbility('retrieve_facebook_app_requests');
		//	$gearman_worker->addAbility('admin_promoter_piwik_stats');
			$gearman_worker->addAbility('admin_manager_piwik_stats');
			$gearman_worker->addAbility('news_feed_retrieve');
			$gearman_worker->addAbility('friend_feed_retrieve');
			$gearman_worker->addAbility('friend_retrieve');
			$gearman_worker->addAbility('gearman_individual_promoter_friend_activity');			
			$gearman_worker->addAbility('gearman_admin_manager_promoter_piwik_stats');			
			$gearman_worker->addAbility('gearman_promoter_manual_add');			
			$gearman_worker->addAbility('gearman_manager_manual_add');			
			$gearman_worker->addAbility('gearman_vc_user_notify_friends_online');		
			$gearman_worker->addAbility('gearman_email_friends_new_user');				
			$gearman_worker->addAbility('gearman_retrieve_friend_venues_activity');	
			$gearman_worker->addAbility('gearman_individual_venue_friend_activity');	
			$gearman_worker->addAbility('gearman_send_emails');	
			$gearman_worker->addAbility('gearman_send_sms_notification');	
					
			
			if($arg1 == 'special_emails'){
				$gearman_worker->addAbility('gearman_email_friends_new_user');	
				$gearman_worker->addAbility('gearman_send_sms_mass_text_team_announcements');	
				$gearman_worker->addAbility('gearman_email_friends_gl_join');
			}
			
			
			
			
		    $gearman_worker->beginWork();
			
		}catch(Net_Gearman_Exception $e){
		   	
		//	var_dump($e); //die();
			echo 'Exception...';
			
		}
	}
}

/* End of file worker.php */
/* Location: ./application/controllers/worker.php */