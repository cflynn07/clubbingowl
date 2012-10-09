<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Retrieves list of NEW user's friends that are VibeCompass users
 */
class Net_Gearman_Job_friend_retrieve extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->load->library('library_memcached', '', 'memcached');
		$CI->load->library('library_facebook', '', 'facebook');
		$handle = $this->handle;
		
		$user_oauth_uid = $args['user_oauth_uid'];
		$access_token = $args['access_token'];
		$friend = json_decode($args['friend']);
		
		//find out if current user and friend are friends
		$fql = "SELECT uid, name, pic, pic_big, pic_square, sex FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = $user_oauth_uid AND uid2 = $friend->users_oauth_uid)";
		$result = $CI->facebook->fb_fql_query($fql, $access_token);
		
		if(!$result){
			//users are NOT friends
			
			//only perform two queries if users are somehow not friends
			$fql = "SELECT uid, name, pic, pic_big, pic_square, sex FROM user WHERE uid = $friend->users_oauth_uid";
			$result = $CI->facebook->fb_fql_query($fql);
			
			$friend->vc_facebook = (isset($result[0])) ? $result[0] : $result;
			
			$friend->vc_friend = false;
			
			$data = json_encode(array('success' => true,
									'message' => $friend));
			
		}else{
			//users are friends
			
			//attach result of FQL query
			$friend->vc_facebook = (isset($result[0])) ? $result[0] : $result;
			
			//find associated data w/ this friend
			$CI->load->model('model_users', 'users', true);
			$friend->vc_friend = $CI->users->retrieve_friend_data($friend->users_oauth_uid);
			
			
			
			
			
			
			
			if($friend->vc_friend->vc_mates){
				//FQL lookup of vc_friends this user goes clubbing w/
				$fql = "SELECT uid, name, pic, pic_big, pic_square, sex, third_party_id, is_app_user FROM user WHERE ";
				foreach($friend->vc_friend->vc_mates as $m8){
					$fql .= "uid = $m8->oauth_uid OR ";
				}
				$fql = rtrim($fql, ' OR ');
				$fql .= " ORDER BY is_app_user DESC";
				//$friend->vc_friend->vc_mates = $CI->facebook->fb_fql_query($fql);
				$friends = $CI->facebook->fb_fql_query($fql);
				
								
				###### MISSING FRIEND (DATABASE) FILTERING -------------------
				
				//Grab all the friends that are app users
				$app_user_friends = array();
				foreach($friends as $fr){
					if($fr['is_app_user']){
						$app_user_friends[] = $fr['uid'];
					}
				}
		
				//if there are friends that are app users that must be filtered...
				if($app_user_friends){
					
					/*
					
					//find the subset (or full set) of these users that are in our database
					$known_app_users = $CI->users->retrieve_app_users($app_user_friends);
					//convert array of objects to array of strings
					foreach($known_app_users as &$val){
						$val = $val->oauth_uid;
					}
					unset($val);
				
					//loop over the array of friends and remove the 'is_app_user' status for users that are claimed to be app_users, but are unknown to our database
					foreach($friends as $key => $fr){
						
						if($fr['is_app_user']){
							
							if(!in_array($fr['uid'], $known_app_users)){
								$frs[$key]['is_app_user'] = false;
							}
							
						}
						
					}
					
					*/
					
					//reorder array
					$friends_temp = array();
					foreach($friends as $fr){
						//add all 'app users' in order
						if($fr['is_app_user'])
							$friends_temp[] = $fr;
					}
					foreach($friends as $fr){
						//add all 'app users' in order
						if(!$fr['is_app_user'])
							$friends_temp[] = $fr;
					}
					$friends = $friends_temp;
				
				}





				$friend->vc_friend->vc_mates = $friends;
				###################################### ----------------------
								
			}
			
			$data = json_encode(array('success' => true,
									'message' => $friend));
			
		}
		
		$CI->memcached->add($handle, 
								$data,
								60);
		
		echo "Retrieved friend_page of $friend->users_full_name for $user_oauth_uid. Users friends: " . (($result) ? "true" : "false") . PHP_EOL;
    }
}