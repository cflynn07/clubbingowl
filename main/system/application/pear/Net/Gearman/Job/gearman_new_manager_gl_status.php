<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Verifies that a VC user is a friend of the promoter that is attempting to add them
 * to their guest list
 */
class Net_Gearman_Job_gearman_new_manager_gl_status extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->load->library('library_facebook', 	'', 'facebook');
		$CI->load->library('Twilio', 			'', 'twilio');
		$CI->load->library('library_bulk_email', '', 'library_bulk_email');
		$handle = $this->handle;
		
		
		
		
		
		
		$team_fan_page_id	= $args['team_fan_page_id'];
		$manager_oauth_uid  = $args['manager_oauth_uid'];
		$tgla_id 			= $args['tgla_id'];
		$status 			= $args['status'];
		$human_time 		= $args['human_time'];
		
	

		
		//find pgla
		$CI->load->model('model_users', 			'users', 			true);
		$CI->load->model('model_guest_lists', 		'guest_lists', 		true);
		$CI->load->model('model_team_guest_lists', 	'team_guest_lists', true);
		$CI->load->model('model_users_managers', 	'users_managers',	true);
		$CI->load->model('model_teams', 			'teams', 			true);
		
		
		$tgla = $CI->team_guest_lists->retrieve_tgla(false, $tgla_id);
		
		
		
		
		
		//find list of users that have ever joined this guest-list
		$CI->db->select('DISTINCT u.oauth_uid, u.first_name, u.last_name, u.full_name, u.email, u.phone_number', false)
			->from('teams_guest_lists_reservations tglr')
			->join('users u', 'tglr.user_oauth_uid = u.oauth_uid')
			->join('teams_guest_lists tgl', 'tgl.id = tglr.team_guest_list_id')
			->where(array(
				'tgl.team_guest_list_authorization_id' => $tgla_id
			));
		$query = $CI->db->get();
			
	//	if(MODE == 'local')
	//		var_dump($CI->db->last_query());
		
		$result = $query->result();

		//create notifications...
		foreach($result as $res){
			
			echo 'SENDING NOTIFICATION TO: ' . $res->full_name . PHP_EOL;
			
			$CI->users->create_user_notifications(0, 'team_new_gl_status', array(
				'tgla_id'				=> $tgla_id,
				'tgla'					=> $tgla,
				'status'				=> $status,
				'human_time'			=> $human_time,
				'time'					=> time(),
				'occurance_date'		=> date('Y-m-d', time())
			), $res->oauth_uid);
			
		}
		
		
		//Find all users on the upcoming guest list
		
		$current_list = $CI->users_managers->retrieve_teams_guest_list_authorizations_current_guest_list($tgla->tgla_id, 
																	date('Y-m-d', strtotime(rtrim($tgla->tgla_day, 's'))));

		$current_list->groups = $CI->users_managers->retrieve_teams_guest_list_members($current_list->tgl_id);
		
		
		$email_count 	= 0;
		$sms_count 		= 0;
		foreach($current_list->groups as $group){
			
			if($group->tglr_manual_add == '1' || $group->tglr_approved != '1' || !$group->head_user_email)
				continue;
			
			
	
			//track this outgoing message for billing purposes
			$CI->teams->create_billable_message($team_fan_page_id, array(
				'type' => 'email'
			));
			
			$email_count++;
			
			
			
			$email_data 						= new stdClass;
			$email_data->tv_name				= $tgla->tv_name;
			$email_data->tgla_name 				= $tgla->tgla_name;
			$email_data->tgla_image 			= $tgla->tgla_image;
			$email_data->c_url_identifier		= $tgla->c_url_identifier;
			
			$email_data->to_user 					= new stdClass;
			$email_data->to_user->u_first_name 		= $group->head_user_first_name;
			$email_data->to_user->u_last_name 		= $group->head_user_last_name;
			$email_data->to_user->u_full_name 		= $group->head_user_full_name;
			$email_data->to_user->email_opts_hash 	= $group->head_user_email_opts_hash;
			
			$email_data->message_title 	= "Updated status for guest list \"$email_data->tgla_name\"";
			
			$email_data->status 		= $status;
						
			$html 						= $CI->load->view('emails/view_email_new_manager_status', array('email_data' => $email_data), true);
			
			
			$CI->library_bulk_email->add_queue(array(
				'html'		=> $html,
				'text'		=> strip_tags($html),
				'subject'	=> $email_data->message_title,
				'to_email'	=> $group->head_user_email, 
				'to_name'	=> $email_data->to_user->u_full_name,
			));
			
			
			
			
			
			
			
			
			
			
			
			if( $group->tglr_text_message != '1' || !$group->u_phone_number)
				continue;
			
			$sms_count++;
			
			
			//then sms
			
			$CI->teams->create_billable_message($team_fan_page_id, array(
				'type' => 'sms'
			));
			$CI->twilio->sms(false, $group->u_phone_number, "(ClubbingOwl) \"$group->tgla_name\" update:\n" . "\"$status\"");
			
		}

		$CI->library_bulk_email->flush_queue();
		
		
		echo 'NEW manager/team guest list status created. ' . count($email_count) . ' emails and ' . count($sms_count) . ' sms sent.' . PHP_EOL;
		
	}
}