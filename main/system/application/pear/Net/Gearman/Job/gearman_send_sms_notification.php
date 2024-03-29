<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Net_Gearman_Job_gearman_send_sms_notification extends Net_Gearman_Job_Common{
	
	
	private $CI;
	private $settings;	
	
    public function run($args){
    	
		// Get Codeigniter instance, and config.
		$CI =& get_instance();
		
		$CI->load->library('library_facebook', '', 'facebook');
		$CI->load->library('Twilio', '', 'twilio');
				
		$team_fan_page_id   = $args['team_fan_page_id'];
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
				WHERE uid = ?";
	//	$fb_user_info = $CI->facebook->fb_fql_query($fql, array($user_oauth_uid));
		
		$fb_user_info = $CI->facebook->fb_api_query($user_oauth_uid);
		var_dump($fb_user_info);
		
		
		if(!$fb_user_info){
			echo 'SMS ERROR';
			var_dump($fb_user_info);
			return;   
		}
	
		
		//track this outgoing message for billing purposes
		$CI->load->model('model_teams', 'teams', true);
		$CI->teams->create_billable_message($team_fan_page_id, array(
			'type' => 'sms'
		));
		
		
		
		
		
		
		$message = $fb_user_info['name'] . " has requested to join \"" . $guest_list_name . "\" at $venue.\n";
		
		if($request_msg)
			$message .= "\"$request_msg\"\n";
		
		if($table_request)
			$message .= "TABLE REQUEST\n";
		
		
		if(!$auto_approved){
			
			if($manager){
				
				
				if($table_request)
					$message .= "Table requests must be approved/declined through www.clubbingowl.com\n";	
				else 
					$message .= "Reply: [yes/no] m" . "$glr_id [response message]\n";	
			
			
			}else{
				
				$message .= "Reply: [yes/no] p" . "$glr_id [response message]\n";
				
			}
			
	
		}else{
			
						
			$message .= "Request was automatically approved.\n";
			
		
		}
			
		$CI->twilio->sms('', $twilio_number, $message);


		
		
		echo 'SENT SMS NEW GUEST LIST RESERVATION NOTIFICATION to ' . $twilio_number . PHP_EOL;

    }
}