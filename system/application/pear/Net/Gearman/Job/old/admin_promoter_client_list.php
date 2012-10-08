<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This gearman job returns all facebook data for a given user
 * 
 */
class Net_Gearman_Job_admin_promoter_client_list extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
    	$promoter_id = $args['promoter_id'];
		$CI =& get_instance();
		$CI->load->library('library_memcached', '', 'memcached');
		$handle = $this->handle;
		
		//retrieve list of promoter's client facebook ids
		$CI->load->model('model_users_promoters', 'users_promoters', true);
		$promoter_clients = $CI->users_promoters->retrieve_promoter_clients_list($promoter_id, array('cache' => true));
		
		//facebook users that don't exist in memcache, after loop is executed these users will be queried
		//from facebook and cached then added to the data to be returned
		$facebook_user_cache_list = array();
		
		//for each user, retrieve facebook data from memcache if exists
		foreach($promoter_clients as &$client){
			
			//grab user from cache if we know them, add to query + cache list if we don't
			if($user = $CI->memcached->get('facebook_user_0_' . $client->pglr_user_oauth_uid)){
				//replace string w/ user object
				$client = (array)json_decode($user);
			}else{
				$facebook_user_cache_list[] = $client->pglr_user_oauth_uid;
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

			foreach($results as &$user){
											
				$key = 'facebook_user_0_' . $user['uid'];
				$CI->memcached->add($key, json_encode($user), 604800); //one week
				
			}		
		
			//add newly recieved results to $promoter_clients
			foreach($promoter_clients as &$client){
					
				if(is_object($client)){
					//wasn't in cache, update with query results
					
					//find result object in set...
					foreach($results as $result){
						
						if($client->pglr_user_oauth_uid == $result['uid']){
							$client = (array)$result;
							break;
						}
						
					}
				}
				
			}
			
		}
		

		$data = json_encode(array('success' => true,
									'message' => $promoter_clients));
		
											
		//send result to memcached
		$CI->memcached->add($handle, 
								$data,
								120);
		
		//Possibly remove for production, kind of cool to look at tho.
		echo 'Retrieved promoter ' . $promoter_id . ' client lists. Uncached users: ' . count($facebook_user_cache_list) . PHP_EOL;
		return;
		
    }
}