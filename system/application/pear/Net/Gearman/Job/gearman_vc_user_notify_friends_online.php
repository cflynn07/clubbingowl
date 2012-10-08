<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Verifies that a VC user is a friend of the promoter that is attempting to add them
 * to their guest list
 */
class Net_Gearman_Job_gearman_vc_user_notify_friends_online extends Net_Gearman_Job_Common{
	
	
	private $CI;
	private $settings;

	
	
    public function run($args){
    	
		// Get Codeigniter instance, and config.
		$this->CI = get_instance();
		$this->CI->load->config('pusher');

		// Setup defaults
		$this->settings['server']	= 'http://api.pusherapp.com';
		$this->settings['port']		= '80';
		$this->settings['auth_key']	= $this->CI->config->item('pusher_api_key');
		$this->settings['secret']	= $this->CI->config->item('pusher_secret');
		$this->settings['app_id']	= $this->CI->config->item('pusher_app_id');
		$this->settings['url']		= '/apps/' . $this->settings['app_id'];
		$this->settings['debug']	= false;
		$this->settings['timeout']	= 30;
		
		
		
		
		
    	
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->benchmark->mark('code_start');
		
		$CI->load->library('pusher', '', 'pusher');
		$CI->load->library('library_facebook', '', 'facebook');
		
		$user_oauth_uid = $args['user_oauth_uid'];
		$access_token = $args['access_token'];
		
		//First get list of user's friends that are vibecompass users
		$fields = array('uid');
		$result = $CI->facebook->retrieve_user_facebook_friends($access_token, $fields);
							
		if(isset($result['error_code']))
			$result = array();
		
		$data = array(
			'notification_type' => 'friend_online',
			'friend' => $user_oauth_uid
		);
		
		$CI->benchmark->mark('pusher_start');
		
		
		
		
		$curls = array();
		
		foreach($result as $key => $uf){
			
			
			$channel = 'private-vc-' . $uf['uid'];
			$event = 'notification';
			$payload = $data;
			$socket_id = null;
			$debug = false;
			$already_encoded = false;
			
			
			# Check if we can initialize a cURL connection
			$curls[$key] = curl_init();
			if ( $curls[$key] === false )
			{
				die( 'Could not initialise cURL!' );
			}
	
			# Add channel to URL..
			$s_url = $this->settings['url'] . '/channels/' . $channel . '/events';
	
			# Build the request
			$signature = "POST\n" . $s_url . "\n";
			$payload_encoded = $already_encoded ? $payload : json_encode( $payload );
			$query = "auth_key=" . $this->settings['auth_key'] . "&auth_timestamp=" . time() . "&auth_version=1.0&body_md5=" . md5( $payload_encoded ) . "&name=" . $event;
	
			# Socket ID set?
			if ( $socket_id !== null )
			{
				$query .= "&socket_id=" . $socket_id;
			}
	
			# Create the signed signature...
			$auth_signature = hash_hmac( 'sha256', $signature . $query, $this->settings['secret'], false );
			$signed_query = $query . "&auth_signature=" . $auth_signature;
			$full_url = $this->settings['server'] . ':' . $this->settings['port'] . $s_url . '?' . $signed_query;
			
			
			# Set cURL opts and execute request
			curl_setopt( $curls[$key], CURLOPT_URL, $full_url );
			curl_setopt( $curls[$key], CURLOPT_HTTPHEADER, array ( "Content-Type: application/json" ) );
			curl_setopt( $curls[$key], CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $curls[$key], CURLOPT_POST, 1 );
			curl_setopt( $curls[$key], CURLOPT_POSTFIELDS, $payload_encoded );
			curl_setopt( $curls[$key], CURLOPT_TIMEOUT, $this->settings['timeout'] );
			
						
	//		echo 'tigger notification for ' . $uf['uid'] . PHP_EOL;
			
		}
		
		$mh = curl_multi_init();
		
		foreach($curls as $key => $ch){
			curl_multi_add_handle($mh,$ch);
		}
		
		$active = null;
		//execute the handles
		do {
		    $mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);
		
		while ($active && $mrc == CURLM_OK) {
		    if (curl_multi_select($mh) != -1) {
		        do {
		            $mrc = curl_multi_exec($mh, $active);
		        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
		    }
		}
		
		//close the handles
		foreach($curls as $key => $ch){
			curl_multi_remove_handle($mh, $ch);
		}

		curl_multi_close($mh);
		
		
		$CI->benchmark->mark('pusher_end');

		echo "vc_user pusher notify friends online. Friends notified: " . count($result) . ' elapsed time: ';
		// Some code happens here

		$CI->benchmark->mark('code_end');
		
		echo $CI->benchmark->elapsed_time('code_start', 'code_end') . ' | pusher time: ' . $CI->benchmark->elapsed_time('pusher_start', 'pusher_end') . PHP_EOL;
  
  		return true;
  
    }
}