<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Verifies that a VC user is a friend of the promoter that is attempting to add them
 * to their guest list
 */
class Net_Gearman_Job_gearman_promoter_manual_add extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->load->library('library_memcached', '', 'memcached');
		$CI->load->library('library_facebook', '', 'facebook');
		$handle = $this->handle;
		
		$user_oauth_uid = $args['user_oauth_uid'];
		$promoter_id	= $args['promoter_id'];
		$access_token = $args['access_token'];
		$oauth_uids = json_decode($args['oauth_uids']);
		$pgla_id = $args['pgla_id'];
				
		//find out if current user and friend are friends		
		$fql = "SELECT uid2 FROM friend WHERE uid1 = $user_oauth_uid AND (";
		
		foreach($oauth_uids as $key => $uid){
			
			if($key == (count($oauth_uids) - 1)){
				
				$fql .= "uid2 = $uid)";
				
			}else{
				
				$fql .= "uid2 = $uid OR ";
				
			}

		}
				
		$result = $CI->facebook->fb_fql_query($fql, $access_token);
				
		if(count($result) != count($oauth_uids)){
			//users are NOT friends
			
			$data = json_encode(array('success' => true,
									'message' => false));
			
			$CI->memcached->add($handle, 
								$data,
								60);
			return;
			
		}
		
		
		
		$CI->load->model('model_guest_lists', 'guest_lists', true);
		
		$message = new stdClass;
		foreach($oauth_uids as $uid){
			
			$result = $CI->guest_lists->create_new_promoter_guest_list_reservation($pgla_id,
																					$uid,
																					array(),
																					$promoter_id,
																					false,
																					false,
																					'',
																					false,
																					'',
																					false,
																					0,			//table_min_spend
																					true,
																					true);
			
			$message->$uid->pglr_id = $result[1];
			
		}
		
		
	
		//users are friends
		
		$data = json_encode(array('success' => true,
								'message' => $message));
		
		
		$CI->memcached->add($handle, 
								$data,
								60);
		
		echo "Promoter guest list manual add. PGLA_ID:" . $pgla_id . " User friend: " . (($result) ? "true" : "false") . PHP_EOL;
    }
}