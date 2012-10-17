<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This gearman job take a user's facebook access token, queries the facebook
 * graph api to get information about the current user, and saves the result 
 * to memcache for later retrieval by the apache process.
 * 
 */
class Net_Gearman_Job_facebook_user_authenticate extends Net_Gearman_Job_Common{
	
    public function run($args){
    					
    	//get all the stuff we're going to need...
    	$access_token = $args['access_token'];	
		$notify_admins = $args['notify_admins'];
		$CI =& get_instance();
		$CI->load->library('Redis', '', 'redis');
		$handle = $this->handle;
		
		//call graph API and get user basic info
		$CI->load->library('library_facebook', '', 'facebook');
				
		//if(!$fb_user_info = $CI->facebook->fb_api_query('me', $access_token)){
		$fql = "SELECT
					uid, 
					name,
					email,
					first_name,
					last_name,
					third_party_id,
					sex,
					username,
					timezone
				FROM user
				WHERE uid = me()";
		$fb_user_info = $CI->facebook->fb_fql_query($fql, $access_token);
		if(!isset($fb_user_info[0])){
			$data = json_encode(array('success' => false,
							  			'message' => 'fb api call failure'));
			$CI->redis->set($handle, 
									$data);
			$CI->redis->expire($handle, 120);	
			return;
		}
		
		
		$fb_user_info = $fb_user_info[0];
				
		//did facebook api return an error?
		if(array_key_exists('error', $fb_user_info)){
			$data = json_encode(array('success' => false,
										'message' => $fb_user_info['error']['message']));
			$CI->redis->set($handle, 
								$data);
			$CI->redis->expire($handle, 120);	
			return;
		}
		
		//NEW: Facebook offline-access extended permission deprication, we now can exchange a client-side (2 hour lifetime) access token for a long-lived access token (60 days)
		$extend_url = "https://graph.facebook.com/oauth/access_token?client_id=" . $CI->config->item('facebook_app_id') . "&client_secret=" . $CI->config->item('facebook_api_secret') . "&grant_type=fb_exchange_token&fb_exchange_token=" . $access_token;
		$extend_response = file_get_contents($extend_url);
		parse_str($extend_response, $extend_response_arr);
		
		if(!isset($extend_response_arr['access_token']) || !isset($extend_response_arr['expires'])){
	//		$data = json_encode(array('success' => false,
	//									'message' => 'Failed to extend access_token expiration'));
	//		$CI->memcached->add($handle,
	//							$data,
	//							120);
	//		return;
			
			$extend_response_arr['access_token'] 	= $access_token;
			$extend_response_arr['expires']			= (60 * 60 * 2) . '';
		}
						
		//is this user known to the system? If yes, set session. 
		//If no record, then set session
		$CI->load->model('model_users', 'users', true);
		if(!$vibecompass_user = $CI->users->retrieve_user($fb_user_info['uid'])){
			//user is not known
			
			var_dump($fb_user_info);
			var_dump($access_token);
			
			//record new user
			$CI->users->create_user($fb_user_info, $extend_response_arr['access_token'], $extend_response_arr['expires']);
			
			var_dump($CI->db->last_query());
			
			//add event to user_notifications table
			echo 'New user notification created: ' . $fb_user_info['name'] . PHP_EOL;
			$CI->users->create_user_notifications($fb_user_info['uid'], 'join_vibecompass', false);
			
			$vibecompass_user = $CI->users->retrieve_user($fb_user_info['uid']);
			
			
			//send an email to all of this user's friends alerting them that they've joined vibecompass
			$CI->load->helper('run_gearman_job');
			$arguments = array(
				'user_oauth_uid'	=> $fb_user_info['uid'],
				'access_token'		=> $access_token
			);
			run_gearman_job('gearman_email_friends_new_user', $arguments, false);
			
			
			
			/*
			
			//create wall post TODO: Remove for launch
			
			$first_name = $fb_user_info['first_name'];
			$oauth_uid = $fb_user_info['uid'];
			$params = array(
				'message' => "$first_name has joined VibeCompass! A new way to connect with nightclub promoters, join guest lists, book tables, and see what clubs and promoters your Facebook friends are using.",
				'link' => "http://www.vibecompass.com/",
				'picture' => 'http://vcweb2.s3.amazonaws.com/assets/web/images/icon_square.png',
				'name' => 'VibeCompass',
				'caption' => "Click to join!",
				'description' => 'VibeCompass launches May 14th! Click here to sign up and be one of our pre-launch users!'
			);
			
			$result = false;
			$result = $CI->facebook->fb_api_query('/' . $oauth_uid . '/feed/', false, 'POST', $params);
			var_dump($result);
			
			unset($first_name);
			unset($oauth_uid);
			unset($result);
			
			
			
			*/
			
			
			//notify fede & casey if this is a pre-launch user signup
			if($notify_admins){
				$to_johann = "@txt.att.net";
				$to_casey = "7745734580@vtext.com";
	
				$text_message = 'VC: New User -> ' . $fb_user_info['name'];	
	
				$to_emails = array($to_johann, $to_casey);
				$to_names = array('Fede', 'Casey');
				
				$message = array(
				    'html' => $text_message,
				    'text' => $text_message,
				    'subject' => 'VibeCompass',
				    'from_name' => 'VibeCompass',
				    'from_email'=> 'casey_flynn@vibecompass.com',
				    'to_email' => $to_emails,
				    'to_name' => $to_names
				);
				 
				$tags = array('');
				$apikey = 'e89d2f5cf7108bf92b416bebba68c52a-us4';
				 
				$params = array(
				    'apikey' => $apikey,
				    'message' => $message,
				    'track_opens' => false,
				    'track_clicks' => false,
				    'tags' => $tags
				);
				 
				$url = "http://us4.sts.mailchimp.com/1.0/SendEmail";
				 
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				 
				$result = curl_exec($ch);
				curl_close ($ch);
						
				$data = json_decode($result);
				echo "New PRE-launch notification - Status = " . ((isset($data->status)) ? $data->status : 'NO STATUS') . PHP_EOL;
		
			}
			
			
			
			
			
		}else{
			//update access token
			
			$CI->users->update_user_access_token($fb_user_info['uid'], $extend_response_arr['access_token'], intval($extend_response_arr['expires']));
			
		}
		
		//add their access token to the session for quick retrieval
		$vibecompass_user->users_access_token = $extend_response_arr['access_token'];
		$vibecompass_user->users_access_token_expiration_time = time() + intval($extend_response_arr['expires']);
								
		$data = json_encode(array('success' => true,
									'message' => $vibecompass_user));
																	
		//send result to memcached
		$CI->redis->set($handle, 
								$data);
		$CI->redis->expire($handle, 120);	
		
		//Possibly remove for production, kind of cool to look at tho.
		echo 'Facebook user authenticate: ' . $vibecompass_user->users_full_name . ' logged in.' . PHP_EOL;
		return;
    }
}