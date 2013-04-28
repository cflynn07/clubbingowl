<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Verifies that a VC user is a friend of the promoter that is attempting to add them
 * to their guest list
 */
class Net_Gearman_Job_gearman_new_promoter_gl_status extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$CI->load->library('library_facebook', '', 'facebook');
		$CI->load->library('Twilio', 			'', 'twilio');
		$CI->load->library('library_bulk_email', '', 'library_bulk_email');
		$handle = $this->handle;
		
		
		
		$team_fan_page_id	= $args['team_fan_page_id'];
		$up_id 		= $args['up_id'];
		$pgla_id 	= $args['pgla_id'];
		$status 	= $args['status'];
		$human_time = $args['human_time'];
	

		
		//find pgla
		$CI->load->model('model_users', 		'users', true);
		$CI->load->model('model_guest_lists', 	'guest_lists', true);
		$CI->load->model('model_teams', 		'teams', true);
		$pgla = $CI->guest_lists->retrieve_pgla($up_id, $pgla_id, $team_fan_page_id);
		
		
		//find list of users that have ever joined this guest-list
		$CI->db->select('DISTINCT u.oauth_uid, u.first_name, u.last_name, u.full_name, u.email, u.phone_number', false)
			->from('promoters_guest_lists_reservations pglr')
			->join('users u', 'pglr.user_oauth_uid = u.oauth_uid')
			->join('promoters_guest_lists pgl', 'pgl.id = pglr.promoter_guest_lists_id')
			->where(array(
				'pgl.promoters_guest_list_authorizations_id' => $pgla_id
			));
		$query = $CI->db->get();
			
	//	if(MODE == 'local')
	//		var_dump($CI->db->last_query());
		
		$result = $query->result();
		
		
		//create notifications...
		foreach($result as $res){
			
			echo 'SENDING NOTIFICATION TO: ' . $res->full_name . PHP_EOL;
			
			$CI->users->create_user_notifications(0, 'promoter_new_gl_status', array(
				'pgla_id'				=> $pgla_id,
				'pgla'					=> $pgla,
				'status'				=> $status,
				'human_time'			=> $human_time,
				'time'					=> time(),
				'occurance_date'		=> date('Y-m-d', time())
			), $res->oauth_uid);
			
		}
		
		
		
		
		$pgla->groups = $CI->guest_lists->retrieve_single_guest_list_and_guest_list_members($pgla->pgla_id, $pgla->pgla_day, false);
		
		
	//	var_dump($pgla);
		
		
		
		$email_count 	= 0;
		$sms_count 		= 0;
		foreach($pgla->groups as $group){
			
			if($group->pglr_manual_add == '1' || $group->pglr_approved != '1' || !$group->head_user_email)
				continue;
				
				
				
			//track this outgoing message for billing purposes
			$CI->teams->create_billable_message($team_fan_page_id, array(
				'type' => 'email'
			));
			
			$email_count++;
			
			
			
			
			$email_data 						= new stdClass;
			$email_data->up_public_identifier	= $pgla->up_public_identifier;
			$email_data->up_profile_image 		= $pgla->up_profile_image;
			$email_data->pgla_name 				= $pgla->pgla_name;
			$email_data->pgla_image 			= $pgla->pgla_image;
			$email_data->promoter_full_name		= $pgla->u_full_name;
			
			$email_data->to_user 					= new stdClass;
			$email_data->to_user->u_first_name 		= $group->head_user_first_name;
			$email_data->to_user->u_last_name 		= $group->head_user_last_name;
			$email_data->to_user->u_full_name 		= $group->head_user_full_name;
			$email_data->to_user->email_opts_hash 	= $group->head_user_email_opts_hash;
			
			$email_data->message_title 	= "Updated status for guest list \"$group->pgla_name\"";
			
			$email_data->status 		= $status;
						
			$html 						= $CI->load->view('emails/view_email_new_promoter_status', array('email_data' => $email_data), true);
			
		//	var_dump($email_data);
			
			$CI->library_bulk_email->add_queue(array(
				'html'		=> $html,
				'text'		=> strip_tags($html),
				'subject'	=> $email_data->message_title,
				'to_email'	=> $group->head_user_email, 
				'to_name'	=> $email_data->to_user->u_full_name,
			));
			
			
			
			
			
			
			
			
			if( $group->pglr_text_message != '1' || !$group->u_phone_number)
				continue;
			
			$sms_count++;
			
			
			
			$CI->teams->create_billable_message($team_fan_page_id, array(
				'type' => 'sms'
			));
			$CI->twilio->sms(false, $group->u_phone_number, "(ClubbingOwl) \"$group->pgla_name\" update:\n" . "\"$status\"");
			
			
			
			
			
		}
		
		$CI->library_bulk_email->flush_queue();
		
		echo 'NEW promoter guest list status created. ' . count($email_count) . ' emails and ' . count($sms_count) . ' sms sent.' . PHP_EOL;
		
		
    }
}