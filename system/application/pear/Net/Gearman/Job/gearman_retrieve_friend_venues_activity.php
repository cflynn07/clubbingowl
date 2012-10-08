<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Net_Gearman_Job_gearman_retrieve_friend_venues_activity extends Net_Gearman_Job_Common{
	
	
	private $CI;
	private $settings;	
	
    public function run($args){
    	
		// Get Codeigniter instance, and config.
		$this->CI = get_instance();
		$this->CI->load->library('library_memcached', '', 'memcached');
		$this->CI->load->library('library_facebook', '', 'facebook');
		$handle = $this->handle;
		
		
		$this->CI->benchmark->mark('code_start');
		
		$user_oauth_uid 	= $args['user_oauth_uid'];
		$access_token 		= $args['access_token'];
		$team_venue_ids		= $args['team_venue_ids'];
		
		
		//first get list of user's friends that are vc users
		$this->CI->load->helper('retrieve_vc_user_friends');
		$result = retrieve_vc_user_friends($user_oauth_uid, $access_token);
		
		if(isset($result['error_code'])){
			var_dump($result);
			echo 'sending error code' . PHP_EOL;
			$this->CI->memcached->delete('cache_user_friends_' . $user_oauth_uid);
			$this->CI->memcached->add($handle, 
								json_encode(array('success' => false)),
								60);			
			return;
		}
			
		//convert from array of objects to array of integers
		$user_friends = array();
		$user_friends_ids = array();
		
		foreach($result as $key => $uf){
			$user_friends[$uf['uid']] = $uf; //$uf['pic_square'];
			$user_friends_ids[] = $uf['uid'];
		}
		
		
		//Retrieve friends that have been to this promoter's venues
		$this->CI->load->helper('friends_venues_correlate');
		$team_venues_user_friends = friends_venues_correlate($user_friends_ids, $team_venue_ids);
		
		$response_obj = new stdClass;
		$response_obj->tv_friends_pop = $team_venues_user_friends;
		$response_obj->user_friends = $user_friends;
		
		$data = json_encode(array('success' => true,
									'message' => $response_obj));
		
					
		//send result to memcached
		$this->CI->memcached->add($handle, 
								$data,
								60);
		
		$this->CI->benchmark->mark('code_end');
		
		
		echo 'Retrieved TV user activity for ' . $user_oauth_uid . ', elapsed time: ' . $this->CI->benchmark->elapsed_time('code_start', 'code_end') . PHP_EOL;
		
    }
}