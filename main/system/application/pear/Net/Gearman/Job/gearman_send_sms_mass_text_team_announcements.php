<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Net_Gearman_Job_gearman_send_sms_mass_text_team_announcements extends Net_Gearman_Job_Common{
	
	
	private $CI;
	private $settings;	
	
    public function run($args){
    	
		// Get Codeigniter instance, and config.
		$CI =& get_instance();
		
		$CI->load->model('model_teams', 'teams', true);
		
		$CI->load->library('library_facebook', '', 'facebook');
		$CI->load->library('Twilio', '', 'twilio');
				
		
		$message			= $args['message'];
		$team_fan_page_id	= $args['team_fan_page_id'];
		$manager_oauth_uid 	= $args['manager_oauth_uid'];
		
		
		$fql = "SELECT
					uid, 	
					name,		
					first_name,	
					last_name	
				FROM user		
				WHERE uid = $manager_oauth_uid";
				
	//	$fb_user_info = $CI->facebook->fb_fql_query($fql, array($user_oauth_uid));
		
		$fb_user_info = $CI->facebook->fb_api_query($manager_oauth_uid);
		
		if(!$fb_user_info){
			echo 'SMS ERROR';
			var_dump($fb_user_info);
			return;   
		}
		

		//retrieve team members
		$CI->load->model('model_team_messaging', 'team_messaging', true);
		$team_chat_members = $CI->team_messaging->retrieve_team_members(array('teams_fan_page_id' => $team_fan_page_id));


		
		$sms = "(ClubbingOwl) New Announcement from " . $fb_user_info['name'] . ":\n";
		$sms .= '"' . $message . '"';
		
		
		
		
		
		
		
		
		
		$sent_numbers = array();
		$curls = array();
		$key = 0;
		foreach($team_chat_members->managers as $manager){
			
			if($manager->u_twilio_sms_number)
				$CI->twilio->sms('', $manager->u_twilio_sms_number, $sms);

			$sent_numbers[] = $manager->u_twilio_sms_number;
			

			$email = $manager->u_email;
		//	$email = "casey_flynn@vibecompass.com";		
		//	$email = "federico_ramirez@vibecompass.com";
			
			$to_emails = array($email);
			$to_names = array($manager->u_full_name);
			
			$email_data = new stdClass;
			$email_data->to_user = $manager;
			$email_data->message_title = "New announcement from " . $fb_user_info['name'];
			$email_data->message = $sms;
			
			$email_text = $CI->load->view('emails/' . 'view_email_generic', array('email_data' => $email_data), true);
			$message = array(
			    'html' => $email_text,
			    'text' => strip_tags($email_text),
			    'subject' => $email_data->message_title,
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
			$key++;
			
			//track this outgoing message for billing purposes
			$CI->teams->create_billable_message($team_fan_page_id, array(
				'type' => 'sms'
			));
			
			
		}
		
		
		
		foreach($team_chat_members->promoters as $promoter){
			
			if($promoter->u_twilio_sms_number)
				$CI->twilio->sms('', $promoter->u_twilio_sms_number, $sms);

			$sent_numbers[] = $promoter->u_twilio_sms_number;
						

			$email = $promoter->u_email;
		//	$email = "casey_flynn@vibecompass.com";		
		//	$email = "federico_ramirez@vibecompass.com";
			
			$to_emails = array($email);
			$to_names = array($promoter->u_full_name);
			
			$email_data = new stdClass;
			$email_data->to_user = $promoter;
			$email_data->message_title = "New announcement from " . $fb_user_info['name'];
			$email_data->message = $sms;
			
			$email_text = $CI->load->view('emails/' . 'view_email_generic', array('email_data' => $email_data), true);
			$message = array(
			    'html' => $email_text,
			    'text' => strip_tags($email_text),
			    'subject' => $email_data->message_title,
			    'from_name' => 'ClubbingOwl',
			    'from_email'=> 'no-reply@ClubbingOwl.com',
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
			$key++;
			
			
			//track this outgoing message for billing purposes
			$CI->teams->create_billable_message($team_fan_page_id, array(
				'type' => 'sms'
			));
			

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
		
		
		
		
		
		//-------------------------------------------------------------------------
	
		
		
		
		
		
		
		
		
		
		
		echo 'SENT SMS ANNOUNCEMENTS TO TEAM MEMBERS -> ' . count($sent_numbers) . ' : ' . $team_fan_page_id . PHP_EOL;
		var_dump($sent_numbers);
		
    }
}