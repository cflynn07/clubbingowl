<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Net_Gearman_Job_gearman_send_sms_notification extends Net_Gearman_Job_Common{
	
	
	private $CI;
	private $settings;	
	
    public function run($args){
    	
		// Get Codeigniter instance, and config.
		$CI =& get_instance();
		
		$CI->load->library('library_facebook', '', 'facebook');
		$CI->load->library('Twilio', '', 'twilio');
				
		
		$twilio_number 		= $args['twilio_number'];
		$user_oauth_uid		= $args['user_oauth_uid'];
		$guest_list_name	= $args['guest_list_name'];
		$venue 				= $args['venue'];
		$glr_id				= $args['glr_id'];
		$entourage			= $args['entourage'];
		$request_msg		= $args['request_msg'];
		$auto_approved 		= $args['auto_approved'];
		$table_request 		= $args['table_request'];
		$manager 			= $args['manager'];
		
		
		$fql = "SELECT
					uid, 
					name,
					first_name,
					last_name
				FROM user
				WHERE uid = $user_oauth_uid";
		$fb_user_info = $CI->facebook->fb_fql_query($fql);
		if(!isset($fb_user_info[0])){
			echo 'SMS ERROR';
			var_dump($fb_user_info);
			return;   
		}
		$fb_user_info = $fb_user_info[0];
		
		
		
		
		$message = $fb_user_info['name'] . " has requested to join \"" . $guest_list_name . "\" at $venue.\n";
		
		if($request_msg)
			$message .= "\"$request_msg\"\n";
		
		if($table_request)
			$message .= "TABLE REQUEST\n";
		
		
		if(!$auto_approved){
			
			$message .= "Reply: [yes/no] $glr_id [response message]\n";
	
		}else{
			
			if($table_request){
				
				if($manager)
					$message .= "Table Requests must be approved/declined through www.vibecompass.com\n";
				else 
					$message .= "Reply: [yes/no] $glr_id [response message]\n";
			
			}else{
			
				$message .= "Request was automatically approved.\n";
				
			}
		
		}
			
		$CI->twilio->sms('', $twilio_number, $message);


		
		
		echo 'SENT SMS NEW GUEST LIST RESERVATION NOTIFICATION to ' . $twilio_number . PHP_EOL;

    }
}