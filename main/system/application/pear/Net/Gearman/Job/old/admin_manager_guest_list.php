<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Returns all clients for a manager's guest lists
 * 
 */
class Net_Gearman_Job_admin_manager_guest_list extends Net_Gearman_Job_Common{
	
    public function run($args){

    	$manager_oauth_uid = $args['manager_oauth_uid'];
		$CI =& get_instance();
		$CI->load->library('library_memcached', '', 'memcached');
		$handle = $this->handle;
		
		
		//retrieve all guest list authorizations for this particular manager
		$CI->load->model('model_users_managers', 'users_managers', true);
		$manager_gl_authorizations = $CI->users_managers->retrieve_all_manager_team_guest_list_reservations($manager_oauth_uid);
		
		
		//for each authorized guest list, find this weeks guest list if it exists, and members (heads + entourages)
		foreach($manager_gl_authorizations as &$gla){
			
			$current_list = $CI->users_managers->retrieve_teams_guest_list_authorizations_current_guest_list($gla->tgla_id);
			if($current_list){
				//if this week has a guest list, attach it plus all members

				$current_list->groups = $CI->users_managers->retrieve_teams_guest_list_members($current_list->tgl_id);
				
			}
			
			$gla->current_list = ($current_list) ? $current_list : false; //want false if empty, not empty array
			
		}
								
		//facebook users that don't exist in memcache, after loop is executed these users will be queried
		//from facebook and cached then added to the data to be returned
		$facebook_user_cache_list = array();
		
		//for each guest list, find all groups associated with it
		foreach($manager_gl_authorizations as &$gla){			
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
			if($gla->current_list){
				
				foreach($gla->current_list->groups as &$group){
				
					//grab head user from cache if we know them, add to query + cache list if we don't
					if($tglr_user_oauth_uid = $CI->memcached->get('facebook_user_0_' . $group->tglr_user_oauth_uid)){
						//replace string w/ user object
						$group->tglr_user_oauth_uid = json_decode($tglr_user_oauth_uid);
					}else{
						$facebook_user_cache_list[] = $group->tglr_user_oauth_uid;
					}
					
					//for each entourage user, see if we have the in memcache
					foreach($group->entourage as &$user){
	
						if($tglre_oauth_uid = $CI->memcached->get('facebook_user_0_' . $user->tglre_oauth_uid)){
							$user = json_decode($tglre_oauth_uid);
						}else{
							$facebook_user_cache_list[] = $user->tglre_oauth_uid;
						}	
						
					}	
					
				}
				
			}
	
		}

		//Now we have a list of all the users that didn't exist in memcache. Facebook query those
		//users, add each of them to memcache, and update $weekly_guest_lists with new data
				
		//filter out duplicates
		$facebook_user_cache_list = array_unique($facebook_user_cache_list);
		$facebook_user_cache_list = array_values($facebook_user_cache_list); //resets keys 0,1,2...
				
		//if this is not empty we need to query facebook
		if($facebook_user_cache_list){
			
			$CI->load->library('library_facebook', '', 'facebook');
			
			$fql = "SELECT uid, first_name, last_name, pic_square
					FROM user
					WHERE ";
				
			foreach($facebook_user_cache_list as $key => $oauth_uid){
				if($key == (count($facebook_user_cache_list) - 1)){
					$fql .= "uid = $oauth_uid";
				}else{
					$fql .= "uid = $oauth_uid OR ";
				}
			}
			
			$results = $CI->facebook->fb_fql_query($fql);
						
			//cache each facebook user data for 1 week
			foreach($results as $facebook_user){
								
				$key = 'facebook_user_0_' . $facebook_user['uid'];
				$CI->memcached->add($key, json_encode($facebook_user), 604800); //one week
				
			}
			
			//add newly recieved results to $weekly_guest_lists
			foreach($manager_gl_authorizations as &$gla){
				
				if($gla->current_list){
						
					foreach($gla->current_list->groups as &$group){
						
						if(isset($group->tglr_user_oauth_uid)){
							//wasn't in cache, update with query results
							
							//find result object in set...
							foreach($results as $result){
																
								if($group->tglr_user_oauth_uid == $result['uid']){
									$group->tglr_user_oauth_uid = $result;
									break;
								}	
								
							}
						}
						
						foreach($group->entourage as &$user){
							
							if(isset($user->tglre_oauth_uid)){
								//wasn't in cache, update with query results
								
								//find result object in set...
								foreach($results as $result){
														
									if($user->tglre_oauth_uid == $result['uid']){
										$user = $result;
										break;
									}	
									
								}
							}
							
						}
					}
					
				}
				
			}
		}
		
		$data = json_encode(array('success' => true,
									'message' => $manager_gl_authorizations));
																	
		//send result to memcached
		$CI->memcached->add($handle, 
								$data,
								120);
		
		//Possibly remove for production, kind of cool to look at tho.
		echo "Retrieved manager $manager_oauth_uid guest lists. Uncached users: " . count($facebook_user_cache_list) . PHP_EOL;
   }
}