<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This gearman job returns all facebook data for a given user
 * 
 */
class Net_Gearman_Job_promoter_guest_list_retrieve_members extends Net_Gearman_Job_Common{
	
	
    public function run($args){
    	    	
		//assemple everything we're going to need
		$created_guest_lists = $args['created_guest_lists'];
		//somehow it's being recieved as an object instead of an array??
		$created_guest_lists = (array)json_decode($created_guest_lists);
		$CI =& get_instance();
		$CI->load->library('library_memcached', '', 'memcached');
		$handle = $this->handle;
		
		//get the FBIDs of all the members on each upcoming guest list
		$CI->load->model('model_guest_lists', 'guest_lists', true);
		foreach($created_guest_lists as &$guest_list){
			$guest_list = $CI->guest_lists->retrieve_promoter_guest_list_members($guest_list);
			
			//convert from array of object to array of strings
			foreach($guest_list as &$member){
				$member = $member->users_oauth_uid;
			}
			
		}
			
		//facebook users that don't exist in memcache, after loop is executed these users will be queried
		//from facebook and cached then added to the data to be returned
		$facebook_user_cache_list = array();
		
		//for each FBID -- get the facebook user data and replace
		foreach($created_guest_lists as &$guest_list){
			//each $guest_list == array of FBIDs
			
			foreach($guest_list as &$user){
				//each $user == FBID
				
				//grab user from cache if we know them, add to query + cache list if we don't
				if($temp = $CI->memcached->get('facebook_user_0_' . $user)){
					//replace string w/ user object
					$temp = json_decode($temp);
					
					//WE ONLY WANT TO SEND PIC_SQUARE BACK TO USER FROM THIS METHOD!
					$user = $temp->pic_square;
					
				}else{
					$facebook_user_cache_list[] = $user;
				}
				
			}
						
		}
		
		$facebook_user_cache_list = array_unique($facebook_user_cache_list);
		$facebook_user_cache_list = array_values($facebook_user_cache_list); //resets keys 0,1,2...
			
		//if this is not empty we need to query facebook
		if($facebook_user_cache_list){
			
			$CI->load->library('library_facebook', '', 'facebook');
			
			$fql = "SELECT uid, first_name, last_name, pic_square
					FROM user
					WHERE ";
				
			foreach($facebook_user_cache_list as $key => $user){
				if($key == (count($facebook_user_cache_list) - 1)){
					$fql .= "uid = $user";
				}else{
					$fql .= "uid = $user OR ";
				}
			}
			
			$results = $CI->facebook->fb_fql_query($fql);
						
	//		$CI->load->helper('facebook_uid_converter');
			
			//cache each facebook user data for 1 week
			foreach($results as &$user){
								
				//convert float to int for memcache key
	//			$user['uid'] = facebook_uid_converter($user['uid']);
				
				$key = 'facebook_user_0_' . $user['uid'];
				
				$CI->memcached->add($key, json_encode($user), 604800); //one week
				
			}		
		
		
			//for each FBID -- get the facebook user data and replace
			foreach($created_guest_lists as &$guest_list){
				//each $guest_list == array of FBIDs
				
				foreach($guest_list as &$user){
					//each $user == FBID
					
					if(is_string($user)){
						//wasn't in cache, update with query results
							
						//find result object in set...
						foreach($results as $res){
							
							//convert float to int-string...
	//						$uid = facebook_uid_converter($res['uid']);
							
							if($user == $uid){
								$user = $res['pic_square'];
								break;
							}
							
						}
						
					}
					
				}
							
			}
		}
		
		$data = json_encode(array('success' => true,
									'message' => $created_guest_lists));
																	
		//send result to memcached
		$CI->memcached->add($handle, 
								$data,
								120);

		$response = 'retrieved users for guest lists ';
		foreach($created_guest_lists as $key => $cgl)
			$response .= "| $key";
		
		echo 'Guest List Retrieve Members: ' . $response . ' - uncached users: ' . count($facebook_user_cache_list) . PHP_EOL;
		
		return;
	}
			
}