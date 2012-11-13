<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Retrieves list of NEW user's friends that are VibeCompass users and sends all of them a notification
 * that a new user has joined VibeCompass
 * 
 */
class Net_Gearman_Job_gearman_individual_promoter_friend_reviews extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->load->library('Redis', '', 'redis');
		$CI->load->library('library_facebook', '', 'facebook');
		$handle = $this->handle;
		
		$user_oauth_uid 	= $args['user_oauth_uid'];
		$access_token 		= $args['access_token'];
		$promoter_id 		= $args['promoter_id'];
		
			
		
		//first get list of user's friends that are vc users
		$CI->load->helper('retrieve_vc_user_friends');
		$result = retrieve_vc_user_friends($user_oauth_uid, $access_token);
			
			
		if(isset($result['error_code'])){
			var_dump($result);
			echo 'sending error code' . PHP_EOL;
			
			$CI->redis->del('cache_user_friends_' . $user_oauth_uid);
			$CI->redis->set($handle, 
								json_encode(array('success' => false)));	
			$CI->redis->expire($handle, 60);		
			return;
		}
		
		
		
		
			
		//convert from array of objects to array of integers
		$user_friends = array();
		$user_friends_ids = array();
		
		foreach($result as $key => $uf){
			
			$uf = (array)$uf;
			
			$user_friends[$uf['uid']] = $uf; //$uf['pic_square'];
			$user_friends_ids[] = $uf['uid'];
		}


		$CI->load->model('model_users', 'users', true);
		$reviews = $CI->users->retrieve_user_promoter_friend_reviews(array(
			'user_friends_oauth_uids'	=>  $user_friends_ids,
			'users_promoters_id'		=> 	$promoter_id
		));


		$key = 'up_pop-' . $user_oauth_uid . '_' . $promoter_id;
		$CI->redis->set($key, json_encode($reviews));
		$CI->redis->expire($key, 60 * 10); //live for 10 minutes


		
		$data = json_encode(array('success' => true,
									'message' => $reviews));
		
		//send result to memcached
		$CI->redis->set($handle, 
								$data);
		$CI->redis->expire($handle, 120);	
			
			
		echo "Retrieved individual promoter $promoter_id FRIEND REVIEWS for $user_oauth_uid." . PHP_EOL;

	}
}