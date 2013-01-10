<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sends an email to an SMS gateway for a user to generate a text message
 * 
 */
class Net_Gearman_Job_guest_list_text_message extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$user_oauth_uid = $args['user_oauth_uid'];
		$text_message = $args['text_message'];
		
		//find user's phone number and provider
		$CI->load->model('model_users', 'users', true);
		
		
		$user = $CI->users->retrieve_user($user_oauth_uid);
		$phone_number = $user->users_phone_number;
		$phone_carrier = $user->users_phone_carrier;
		
		
		
		
		$CI->load->library('Twilio', '', 'twilio');
		$CI->twilio->sms(false, $phone_number, $text_message);
		
		
		
		
		/*
		
		
		switch($phone_carrier){
			case 'att':
				$to = "$phone_number@txt.att.net";
				break;
			case 'verizon':
				$to = "$phone_number@vtext.com";
				break;
			case 'tmobile':
				$to = "$phone_number@tmomail.net";
				break;
			case 'sprint':
				$to = "$phone_number@messaging.sprintpcs.com";
				break;
			default:
				return; //error?
		}
		
		$to_emails = array($to);
		$to_names = array($user->users_full_name);
		
		$message = array(
		    'html' => $text_message,
		    'text' => $text_message,
		    'subject' => 'ClubbingOwl',
		    'from_name' => 'ClubbingOwl',
		    'from_email'=> 'no-reply@clubbingowl.com',
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
		 
		 
		 
		 */
		 
		 
		 
		 
		 /*
		 
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
		$result = curl_exec($ch);
		curl_close ($ch);
				
		$data = json_decode($result);
		
		*/
		
		
		echo "Text Confirmation Sent To: $user->users_full_name - Status = " . ((isset($data->status)) ? $data->status : 'NO STATUS') . PHP_EOL;
				
    }
}