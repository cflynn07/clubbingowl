<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Retrieves list of NEW user's friends that are VibeCompass users and sends all of them a notification
 * that a new user has joined VibeCompass
 * 
 */
class Net_Gearman_Job_gearman_individual_venue_friend_activity extends Net_Gearman_Job_Common{
	
    public function run($args){

		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->load->library('library_memcached', '', 'memcached');
		$CI->load->library('library_facebook', '', 'facebook');
		$handle = $this->handle;

		$user_oauth_uid 	= $args['user_oauth_uid'];
		$access_token 		= $args['access_token'];		
		$venue_id 			= $args['venue_id']; //ARRAY <--
		
		//first get list of user's friends that are vc users
		$CI->load->helper('retrieve_vc_user_friends');
		$result = retrieve_vc_user_friends($user_oauth_uid, $access_token);

		if(isset($result['error_code'])){
			var_dump($result);
			echo 'sending error code' . PHP_EOL;
			$CI->memcached->delete('cache_user_friends_' . $user_oauth_uid);
			$CI->memcached->add($handle, 
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
		$CI->load->helper('friends_venues_correlate');
		$venue_user_friends = friends_venues_correlate($user_friends_ids, $venue_id);
	
		
		$CI->load->model('model_teams', 'teams', true);
		$venue_user_news_feed = $CI->teams->retrieve_venue_news_feed($user_friends_ids, $venue_id[0]);
		
		$result = new stdClass;
		$result->venue_user_friends = $venue_user_friends;
		$result->venue_user_news_feed = $venue_user_news_feed;
		$result->user_friends = $user_friends;
		$result->user_friends_ids = $user_friends_ids;
		
		
		$data = json_encode(array('success' => true,
									'message' => $result));
		
					
		//send result to memcached
		$CI->memcached->add($handle, 
								$data,
								60);
			
		echo "Retrieved venue " . $venue_id[0] . " user friend popularity and news feed for " . $user_oauth_uid . PHP_EOL;
		
	}
}