<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Verifies that a VC user is a friend of the promoter that is attempting to add them
 * to their guest list
 */
class Net_Gearman_Job_gearman_manager_manual_add extends Net_Gearman_Job_Common{
	
    public function run($args){

		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->load->library('Redis', '', 'redis');
		$CI->load->library('library_facebook', '', 'facebook');
		$handle = $this->handle;
		
		$user_oauth_uid = $args['user_oauth_uid'];
		$fan_page_id = $args['fan_page_id'];
		$access_token = $args['access_token'];
		$oauth_uids = json_decode($args['oauth_uids']);
		$tgla_id = $args['tgla_id'];
				
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
			
			$CI->redis->set($handle, 
									$data);
			$CI->redis->expire($handle, 120);
			return;
			
		}
		
		$CI->load->library('library_facebook_application', '', 'facebook_application');
		
		$message = new stdClass;
		
		$temp = new stdClass;
		foreach($oauth_uids as $uid){
			
			$temp->oauth_uid = $uid;
			$result = $CI->facebook_application->team_guest_list_join_request($tgla_id,
																				array(),
																				false,
																				false,
																				false,
																				false,
																				false,
																				$fan_page_id,
																				true,
																				true,
																				$temp);
			
			$message->$uid->tglr_id = $result['message'];
		}
		
		$data = json_encode(array('success' => true,
								'message' => $message));
				
		$CI->redis->set($handle, 
								$data);
		$CI->redis->expire($handle, 120);
		
		echo "Manager guest list manual add. TGLA_ID:" . $tgla_id . " User friend: " . (($result) ? "true" : "false") . PHP_EOL;
    }
}