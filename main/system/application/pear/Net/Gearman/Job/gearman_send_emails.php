<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Net_Gearman_Job_gearman_send_emails extends Net_Gearman_Job_Common{
	
	
	private $CI;
	private $settings;	
	
    public function run($args){
    	
		// Get Codeigniter instance, and config.
		$this->CI = get_instance();

		$to_emails 			= json_decode($args['to_emails']);
		$to_names 			= json_decode($args['to_names']);
		$email_view			= $args['email_view'];
		$email_view_data 	= json_decode($args['email_view_data']);
		
		
		
		$email_text = $this->CI->load->view('emails/' . $email_view, $email_view_data, true);
		
		$message = array(
		    'html' => $email_text,
		    'text' => $email_text,
		    'subject' => 'VibeCompass - New Reservation Request!',
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
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
		$result = curl_exec($ch);
		curl_close ($ch);
				
		$data = json_decode($result);
		echo "New GL Reservation EMAIL Sent To: " . PHP_EOL;
		var_dump($to_emails);
		echo "- Status = " . ((isset($data->status)) ? $data->status : 'NO STATUS') . PHP_EOL;
				
		
    }
}