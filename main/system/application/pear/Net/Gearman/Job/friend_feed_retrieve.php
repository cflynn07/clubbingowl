<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Retrieves list of NEW user's friends that are VibeCompass users
 */
class Net_Gearman_Job_friend_feed_retrieve extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->load->library('library_memcached', '', 'memcached');
		$CI->load->library('library_facebook', '', 'facebook');
		$CI->load->model('model_users', 'users', true);
		$handle = $this->handle;
		
		
		$user_oauth_uid 	= $args['user_oauth_uid'];
		$access_token 		= $args['access_token'];
		$iterator_position 	= $args['iterator_position'];
		
		
		if(!$vc_user_friend_feed = $CI->memcached->get('vc_user_friend_feed-' . $user_oauth_uid)){
			
			$fql = "SELECT uid, name, pic, pic_square, is_app_user, third_party_id FROM user
				WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = $user_oauth_uid)
				ORDER BY is_app_user DESC";
			$vc_user_friend_feed = $CI->facebook->fb_fql_query($fql, $access_token);	
			$CI->memcached->add('vc_user_friend_feed-' . $user_oauth_uid, json_encode($vc_user_friend_feed), 60 * 60); //1 hour
			
		}else{
			$vc_user_friend_feed = json_decode($vc_user_friend_feed);
		}
				
		$iterator_position = (!$iterator_position) ? 0 : $iterator_position;
		
		
		/*
		$fql = "SELECT uid, name, pic, pic_square, is_app_user, third_party_id FROM user
				WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = $user_oauth_uid)
				ORDER BY is_app_user DESC
				LIMIT $iterator_position, 18";
		$friends = $CI->facebook->fb_fql_query($fql, $access_token);		
		*/
		
		
		if(isset($vc_user_friend_feed['error_code'])){
			var_dump($vc_user_friend_feed);
			$CI->memcached->delete('cache_user_friends_' . $user_oauth_uid);
			$CI->memcached->add($handle, 
								json_encode(array('success' => false)),
								60);			
			return;
		}
 
 		$friends = array_slice($vc_user_friend_feed, $iterator_position, 18);
				
		$data = json_encode(array('success' => true,
									'message' => $friends));
											
		//send result to memcached
		$CI->memcached->add($handle, 
								$data,
								60);
		
		echo "Retrieved vc_user $user_oauth_uid friends feed @ iterator position: " . (($iterator_position === false) ? "false" : $iterator_position) . " - friends: " . count($friends) . PHP_EOL;
    }
}