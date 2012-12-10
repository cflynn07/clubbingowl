<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Retrieves list of NEW user's friends that are VibeCompass users and sends all of them a notification
 * that a new user has joined VibeCompass
 * 
 */
class Net_Gearman_Job_news_feed_retrieve extends Net_Gearman_Job_Common{
	
    public function run($args){
	    try{
			//get all the stuff we're going to need...
			$CI =& get_instance();
			$CI->load->library('Redis', '', 'redis');
			$CI->load->library('library_facebook', '', 'facebook');
			$handle = $this->handle;
			
			$user_oauth_uid 	= $args['user_oauth_uid'];
			$access_token 		= $args['access_token'];
			$iterator_position 	= $args['iterator_position'];
			$lang_locale 		= $args['lang_locale'];
			
			
			//first get list of user's friends that are vc users
			$CI->load->helper('retrieve_vc_user_friends');
			$result = retrieve_vc_user_friends($user_oauth_uid, $access_token);
			
			if(!is_array($result))
				return;
										
			if(isset($result['error_code'])){
				var_dump($result);
				
				$data = json_encode(array('success' => false));
				$CI->redis->del('cache_user_friends_' . $user_oauth_uid);
				$CI->redis->set($handle, 
										$data);
				$CI->redis->expire($handle, 120);							
									
											
				return;
			}
				
			//convert from array of objects to array of integers
			$user_friends_pics = array();
			$user_friends_ids = array();
			
			foreach($result as $key => $uf){
				
				$uf = (array)$uf;
				
				$user_friends_pics[$uf['uid']] = $uf['pic_square'];
				$user_friends_ids[] = $uf['uid'];
			}
			
			
			$CI->load->model('model_users', 'users', true);
					
			$user_friends 						= new stdClass;
			$user_friends->user_friends_pics 	= $user_friends_pics;
			$user_friends->user_friends_ids 	= $user_friends_ids;
							
			$notifications = $CI->users->retrieve_user_notifications($user_friends->user_friends_ids, $iterator_position, array('lang_locale' => $lang_locale), $user_oauth_uid);
			
			if($iterator_position === false || $iterator_position == 'false'){
				//if iterator is false, also retrieve friend promoter, guest-list and venue popularity
					
				$vc_popularity_graph 					= $CI->users->retrieve_user_friend_popular_promoters_venues_guestlists($user_oauth_uid, $user_friends_ids, $lang_locale);
				$notifications->user_friends_vc_obj_pop = $vc_popularity_graph;
				
			}
			
			
			//add pictures to result sent back to browser
			$notifications->user_friends_pics = $user_friends->user_friends_pics;
			
			$data = json_encode(array('success' => true,
										'message' => $notifications));
			
			
	//		$handle = 'test_simple_handle';		
			//send result to memcached
			$CI->redis->set($handle, 
									$data);
			$CI->redis->expire($handle, 120);			
			
			//var_dump($data);
			echo "Retrieved vc_user news feed @ iterator position: " . (($iterator_position === false) ? "false" : $iterator_position) . " - notifications: " . count($notifications->data) . PHP_EOL;
	
		}catch(Exception $e){
			return;
		}
	}
}