<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Verifies that a VC user is a friend of the promoter that is attempting to add them
 * to their guest list
 */
class Net_Gearman_Job_gearman_new_promoter_gl_status extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->load->library('library_facebook', '', 'facebook');
		$handle = $this->handle;
		
		
		
		
		
		
		
		$up_id 		= $args['up_id'];
		$pgla_id 	= $args['pgla_id'];
		$status 	= $args['status'];
		$human_time = $args['human_time'];
	

		
		//find pgla
		$CI->load->model('model_users', 'users', true);
		$CI->load->model('model_guest_lists', 'guest_lists', true);
		$pgla = $CI->guest_lists->retrieve_pgla($up_id, $pgla_id);
		
		
		//find list of users that have ever joined this guest-list
		$CI->db->select('DISTINCT u.oauth_uid, u.first_name, u.last_name, u.full_name, u.email, u.phone_number', false)
			->from('promoters_guest_lists_reservations pglr')
			->join('users u', 'pglr.user_oauth_uid = u.oauth_uid')
			->join('promoters_guest_lists pgl', 'pgl.id = pglr.promoter_guest_lists_id')
			->where(array(
				'pgl.promoters_guest_list_authorizations_id' => $pgla_id
			));
		$query = $CI->db->get();
			
		if(MODE == 'local')
			var_dump($CI->db->last_query());
		
		$result = $query->result();
		
		
		//email users...
		foreach($result as $res){
			echo 'SENDING EMAIL TO: ' . $res->full_name . PHP_EOL;
		}
		
		
		//create notifications...
		foreach($result as $res){
			
			echo 'SENDING NOTIFICATION TO: ' . $res->full_name . PHP_EOL;
			
			$CI->users->create_user_notifications(0, 'promoter_new_gl_status', array(
				'pgla_id'				=> $pgla_id,
				'pgla'					=> $pgla,
				'status'				=> $status,
				'human_time'			=> $human_time,
				'time'					=> time(),
				'occurance_date'		=> date('Y-m-d', time())
			), $res->oauth_uid);
			
		}
		
		
		//sms users...
		
				
		
		
		return;
		
		
		
		
		
		
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

			$CI->redis->set($handle, 
									$data);
			$CI->redis->expire($handle, 120);
			
			
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

		$CI->redis->set($handle, 
								$data);
		$CI->redis->expire($handle, 120);
		
		echo "Promoter guest list manual add. PGLA_ID:" . $pgla_id . " User friend: " . (($result) ? "true" : "false") . PHP_EOL;
    }
}