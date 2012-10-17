<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Retrieves list of NEW user's friends that are VibeCompass users and sends all of them a notification
 * that a new user has joined VibeCompass
 * 
 */
class Net_Gearman_Job_gearman_individual_promoter_friend_activity extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->load->library('Redis', '', 'redis');
		$CI->load->library('library_facebook', '', 'facebook');
		$handle = $this->handle;
		
		$user_oauth_uid = $args['user_oauth_uid'];
		$access_token = $args['access_token'];
		$promoter_oauth_uid = $args['promoter_oauth_uid'];
		$promoter_id = $args['promoter_id'];
		$promoter_venues_ids = $args['promoter_venues_ids'];
		$promoter_team_fan_page_id = $args['promoter_team_fan_page_id'];
		
		//CACHE SHARED WITH news_feed_retrieve gearman job ------ 
		
		//First get list of user's friends that are vibecompass users						
	//	$fields = array('uid', 'pic_square', 'first_name', 'last_name', 'name', 'third_party_id');
	///	if(!$result = $CI->memcached->get('cache_user_friends_' . $user_oauth_uid)){
	//		$result = $CI->facebook->retrieve_user_facebook_friends($access_token, $fields);
	//		$CI->memcached->add('cache_user_friends_' . $user_oauth_uid, $result, (60 * 15));
	//		echo 'IPFA cached user '  . $user_oauth_uid . ' friends' . PHP_EOL;
	//	}
		
		
		//first get list of user's friends that are vc users
		$CI->load->helper('retrieve_vc_user_friends');
		$result = retrieve_vc_user_friends($user_oauth_uid, $access_token);
			
			
		if(isset($result['error_code'])){
			var_dump($result);
			echo 'sending error code' . PHP_EOL;
			
			
			$CI->redis->delete('cache_user_friends_' . $user_oauth_uid);
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






		//Retrieve friends that have been to this promoter's venues
		$CI->load->helper('friends_venues_correlate');
		$promoter_venues_user_friends = friends_venues_correlate($user_friends_ids, $promoter_venues_ids);
		
		
		
		
		
		$CI->load->model('model_users_promoters', 'users_promoters', true);
		$promoter_notifications = $CI->users_promoters->retrieve_promoter_client_newsfeed($promoter_oauth_uid, $user_friends_ids);
		$weeks_popularity = $CI->users_promoters->retrieve_promoter_client_trend_activity($promoter_id, $user_friends_ids);	
		
		$result = new stdClass;
		$result->promoter_notifications = $promoter_notifications;
		$result->promoter_popularity_trend = $weeks_popularity;
		$result->user_friends = $user_friends;
		$result->user_friends_ids = $user_friends_ids;
		$result->promoter_venues_user_friends = $promoter_venues_user_friends;
		
		$data = json_encode(array('success' => true,
									'message' => $result));
		
					
		//send result to memcached
		$CI->redis->set($handle, 
								$data);
		$CI->redis->expire($handle, 120);	
			
			
		echo "Retrieved individual promoter $promoter_oauth_uid popularity for user $user_oauth_uid." . PHP_EOL;

	}
}