<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Verifies that a VC user is a friend of the promoter that is attempting to add them
 * to their guest list
 */
class Net_Gearman_Job_gearman_email_friends_gl_join extends Net_Gearman_Job_Common{
	
	
	private $CI;
	private $settings;

	
	
    public function run($args){
		
		echo 'Emailing friends new gl-join...' . PHP_EOL;
		return;
		
		
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->benchmark->mark('code_start');
		
		$CI->load->library('library_facebook', '', 'facebook');
		
		
		
		
		$type 			= $args['type'];
		$gl_query 		= json_decode($args['gl_query']);
			
		
		
		
		//retrieve users's access token
		if($type == 'team'){
			$query = $CI->db->get_where('users', array('oauth_uid' => $gl_query->tglr_user_oauth_uid));
			$user_oauth_uid = $gl_query->tglr_user_oauth_uid;
		}else if($type == 'promoter'){
			$query = $CI->db->get_where('users', array('oauth_uid' => $gl_query->pglr_user_oauth_uid));
			$user_oauth_uid = $gl_query->pglr_user_oauth_uid;
		}
		
		
		$temp_lookup = $query->row();
		$access_token = $temp_lookup->access_token;
		
		
		
			$CI->benchmark->mark('facebook_start');
		
		//First get list of user's friends that are vibecompass users
		$fields = array('uid');
		$result = $CI->facebook->retrieve_user_facebook_friends($access_token, $fields);
		
			$CI->benchmark->mark('facebook_end');
		
		if(!$result){
			return;
		}
		
		//extract uids
		$friends_oauth_uids = array();
		foreach($result as $res){
			if(isset($res['uid']))
				$friends_oauth_uids[] = $res['uid'];
		}
		
		
		//Look up the users in our database, grab their shit and build a custom email for each of them
		$CI->load->model('model_users', 'users', true);
		$vibecompass_users = $CI->users->retrieve_app_users($friends_oauth_uids, array('opt_out' => true));
		
		//retrieve current user
		$from_user = $CI->users->retrieve_app_users(array($user_oauth_uid));
		if($from_user)
			$from_user = $from_user[0];
		
		
		
			
		
		
		
		
		
		
		
			$CI->benchmark->mark('email_start');
		//create a separate curl request for each friend
		$curls = array();
		foreach($vibecompass_users as $key => $uf){
			
			$email = $uf->u_email;
		//	$email = "casey_flynn@vibecompass.com";		
		//	$email = "federico_ramirez@vibecompass.com";
			
			$to_emails = array($email);
			$to_names = array($uf->u_full_name);
			
			$email_data = new stdClass;
			$email_data->to_user 	= $uf;
			$email_data->type		= $type;
			$email_data->gl_query 	= $gl_query;
			$email_data->from_user 	= $from_user;
			$email_data->message_title = "Your friend " . $from_user->u_first_name . " has joined a guest list!";
			
			$email_text = $CI->load->view('emails/' . 'view_email_friend_join_gl', array('email_data' => $email_data), true);
			$message = array(
			    'html' => $email_text,
			    'text' => strip_tags($email_text),
			    'subject' => "Your friend " . $from_user->u_first_name . " joined a guest list!",
			    'from_name' => 'VibeCompass',
			    'from_email'=> 'no-reply@vibecompass.com',
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
					
			# Check if we can initialize a cURL connection
			$curls[$key] = curl_init();
			if ( $curls[$key] === false )
			{
				die( 'Could not initialise cURL!' );
			}
			
			curl_setopt($curls[$key], CURLOPT_URL,$url);
			curl_setopt($curls[$key], CURLOPT_POST,count($params));
			curl_setopt($curls[$key], CURLOPT_POSTFIELDS,http_build_query($params));
			curl_setopt($curls[$key], CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt($curls[$key], CURLOPT_POST, 1 );
			
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
			$CI->benchmark->mark('email_end');
		
		
		
		
		
		echo 'Guest-list join email sent to ' . count($vibecompass_users) . " friends." . PHP_EOL;
		echo 'Facebook time: ' . $CI->benchmark->elapsed_time('facebook_start', 'facebook_end') . ' | email time: ' . $CI->benchmark->elapsed_time('email_start', 'email_end') . PHP_EOL;
    
    }
}