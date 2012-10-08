<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This gearman job returns all facebook data for a given user
 * 
 */
class Net_Gearman_Job_admin_promoter_guest_list extends Net_Gearman_Job_Common{
	
    public function run($args){
    	
    	$promoter_id = $args['promoter_id'];
		$CI =& get_instance();
		$CI->load->library('library_memcached', '', 'memcached');
		$handle = $this->handle;
		
		//retrieve a list of all the guest lists a promoter has set up
		$CI->load->model('model_users_promoters', 'users_promoters', true);
		$weekly_guest_lists = $CI->users_promoters->retrieve_promoter_guest_list_authorizations($promoter_id);
		
		$CI->load->model('model_guest_lists', 'guest_lists', true);
		
		//facebook users that don't exist in memcache, after loop is executed these users will be queried
		//from facebook and cached then added to the data to be returned
		$facebook_user_cache_list = array();
		
		//for each guest list, find all groups associated with it
		foreach($weekly_guest_lists as &$gla){
			$gla->groups = $CI->guest_lists->retrieve_single_guest_list_and_guest_list_members($gla->pgla_id);
			
			/**
			 * Strategy:
			 * 		For each facebook user on a promoter's guest list - check to see
			 * if we have data cached on that particular user in memcache. If not,
			 * add that user's id to a list of users that we don't have any data for
			 * -- we will query facbeook once for all the data of these users. Each
			 * user's data will be cached in memcache for a week.
			 * 
			 * 		This way, anytime a single user's data is needed anywhere in the
			 * application, if it's already been loaded we don't need to queyr facebook
			 * again.
			 */
			
			
			/*
			foreach($gla->groups as &$group){
	
				//grab head user from cache if we know them, add to query + cache list if we don't
				if($head_user = $CI->memcached->get('facebook_user_0_' . $group->head_user)){
					//replace string w/ user object
					$group->head_user = json_decode($head_user);
				}else{
					$facebook_user_cache_list[] = $group->head_user;
				}
				
				//for each entourage user, see if we have the in memcache
				foreach($group->entourage_users as &$user){

					if($entourage_user = $CI->memcached->get('facebook_user_0_' . $user)){
						$user = json_decode($entourage_user);
					}else{
						$facebook_user_cache_list[] = $user;
					}	
					
				}	
				
			}
			*/
		}

		//Now we have a list of all the users that didn't exist in memcache. Facebook query those
		//users, add each of them to memcache, and update $weekly_guest_lists with new data
				
		//filter out duplicates
	//	$facebook_user_cache_list = array_unique($facebook_user_cache_list);
	//	$facebook_user_cache_list = array_values($facebook_user_cache_list); //resets keys 0,1,2...
	/*			
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
			
			//cache each facebook user data for 1 week
			foreach($results as &$user){
								
				$key = 'facebook_user_0_' . $user['uid'];
				$CI->memcached->add($key, json_encode($user), 604800); //one week
				
			}
			
			//add newly recieved results to $weekly_guest_lists
			foreach($weekly_guest_lists as &$gla){
				foreach($gla->groups as &$group){
					
					if(is_string($group->head_user)){
						//wasn't in cache, update with query results
						
						//find result object in set...
						foreach($results as $result){
								
							
							if($group->head_user == $result['uid']){
								$group->head_user = $result;
								break;
							}	
							
						}
					}
					
					foreach($group->entourage_users as &$user){
						
						if(is_string($user)){
							//wasn't in cache, update with query results
							
							//find result object in set...
							foreach($results as $result){
															
								if($user == $result['uid']){
									$user = $result;
									break;
								}	
								
							}
						}
					}
				}
			}
		}
		*/		
		$data = json_encode(array('success' => true,
									'message' => $weekly_guest_lists));
																	
		//send result to memcached
		$CI->memcached->add($handle, 
								$data,
								120);
		
		//Possibly remove for production, kind of cool to look at tho.
		echo 'Retrieved promoter ' . $promoter_id . ' guest lists. Uncached users: ' . count($facebook_user_cache_list) . PHP_EOL;
		return;
		
    }
}