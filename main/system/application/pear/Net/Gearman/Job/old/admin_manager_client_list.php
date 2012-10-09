<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Returns all clients for a given venue to a venue manager
 * 
 */
class Net_Gearman_Job_admin_manager_client_list extends Net_Gearman_Job_Common{
	
    public function run($args){
    	return;		
    	$manager_venues = $args['venues'];
		$CI =& get_instance();
		$CI->load->library('library_memcached', '', 'memcached');
		$handle = $this->handle;
		
		$CI->load->model('model_users_managers', 'users_managers', true);
				
		$venues_clients = array();
		foreach($manager_venues as $venue){
			
			$venues_clients[$venue['venue_name']] = $CI->users_managers->retrieve_venue_clients($venue['venue_id']);
			
		}
		
		//facebook users that don't exist in memcache, after loop is executed these users will be queried
		//from facebook and cached then added to the data to be returned
		$facebook_user_cache_list = array();
		
		foreach($venues_clients as &$venues){
			
			foreach($venues as &$client){
				
				//grab user from cache if we know them, add to query + cache list if we don't
				if($user = $CI->memcached->get('facebook_user_0_' . $client->oauth_uid)){
					//replace string w/ user object
					$client = json_decode($user);
				}else{
					$facebook_user_cache_list[] = $client->oauth_uid;
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
						
		//	$CI->load->helper('facebook_uid_converter');
			//cache each facebook user data for 1 week
			foreach($results as &$user){
								
				//convert float to int for memcache key
			//	$user['uid'] = facebook_uid_converter($user['uid']);
				$key = 'facebook_user_0_' . $user['uid'];
				
				$CI->memcached->add($key, json_encode($user), 604800); //one week
				
			}		
		
			foreach($venues_clients as &$venues){
				
				foreach($venues as &$client){
					
					if(is_string($client->oauth_uid)){
						//wasn't in cache, update with query results
						
						//find result object in set...
						foreach($results as $result){
							
							if($client->oauth_uid == $result['uid']){
								$client = $result;
								break;
							}
							
						}
					}
					
				}
				
			}
		}
		
		$data = json_encode(array('success' => true,
									'message' => $venues_clients));
																	
		//send result to memcached
		$CI->memcached->add($handle, 
								$data,
								120);
		
		//Possibly remove for production, kind of cool to look at tho.
		$response_string = 'retrieved venue | ';
		foreach($manager_venues as $venue){
			$response_string .= $venue['venue_name'] . ' | ';
		}
		$response_string .= 'client lists. Uncached users: ' . count($facebook_user_cache_list) . PHP_EOL;
		
		echo $response_string;
		return;
		
    }
}