<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');





class Net_Gearman_Job_gearman_confirmation_email_promoter extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->load->library('library_facebook', 		'', 'facebook');
		$CI->load->library('Twilio', 				'', 'twilio');
		$CI->load->library('library_bulk_email', 	'', 'library_bulk_email');
		$handle = $this->handle;
		
		
		
		$team_fan_page_id	= $args['team_fan_page_id'];
		$user 				= json_decode($args['user_json']);
		$pglr 				= json_decode($args['pglr']);
		$approved			= $args['approved'];
		$message 			= $args['message'];

		
		//find pgla
		$CI->load->model('model_users', 		'users', true);
		$CI->load->model('model_guest_lists', 	'guest_lists', true);
		$CI->load->model('model_teams', 		'teams', true);
		
		
		$email_data 				= new stdClass;
		$email_data->pglr 			= $pglr;
		$email_data->to_user 		= $user;
		$email_data->approved 		= $approved;
		$email_data->message 		= $message;
		$email_data->message_title 	= "Your reservation request for \"$pglr->pgla_name\"";
		
					
		$html 						= $CI->load->view('emails/view_email_request_respond_promoter', array('email_data' => $email_data), true);
		


		
		$CI->library_bulk_email->add_queue(array(
			'html'		=> $html,
			'text'		=> strip_tags($html),
			'subject'	=> $email_data->message_title,
			'to_email'	=> $email_data->to_user->email, 
			'to_name'	=> $email_data->to_user->full_name,
		));
		
		
		
		//track this outgoing message for billing purposes
		$CI->teams->create_billable_message($team_fan_page_id, array(
			'type' => 'email'
		));
		
		
		
		$CI->library_bulk_email->flush_queue();
		
		echo 'SEND PROMOTER GL CONFIRMATION EMAIL -- 1 sms sent.' . PHP_EOL;
		
		
    }
}