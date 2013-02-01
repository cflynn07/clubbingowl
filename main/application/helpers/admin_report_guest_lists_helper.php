<?php if(! defined('BASEPATH') ) exit('No direct script access allowed');

if(!function_exists('admin_report_guest_lists')){
	
	function admin_report_guest_lists($team_fan_page_id){
		
		
		$CI =& get_instance();
		
		$CI->load->model('model_users_managers', 	'users_managers',	true);
		$CI->load->model('model_teams', 			'teams', 			true);
		$CI->load->model('model_users_promoters', 'users_promoters', 	true);
	
		
		$team_trailing_gl_requests 						 = $CI->users_managers->retrieve_trailing_weekly_guest_list_reservation_requests($team_fan_page_id);
		$team_trailing_gl_requests_percentage_attendance = $CI->users_managers->retrieve_trailing_weekly_guest_list_reservation_requests_percentage_attendance($team_fan_page_id);
	 	$promoters 										 = $CI->teams->retrieve_team_promoters($team_fan_page_id);
		$team_venues 									 = $CI->users_managers->retrieve_team_venues($team_fan_page_id);
	
		$data['team_trailing_gl_requests'] 						 = $team_trailing_gl_requests;
		$data['team_trailing_gl_requests_percentage_attendance'] = $team_trailing_gl_requests_percentage_attendance;
		$data['promoters'] 										 = $promoters;
		$data['team_venues']									 = $team_venues;
	
		foreach($data['promoters'] as $key => &$pro){
			
			//if promoter hasn't completed setup, remove
			if($pro->up_completed_setup == '0' || $pro->up_completed_setup == 0){
				unset($data['promoters'][$key]);
				continue;
			}
			
			$statistics = new stdClass;
			$statistics->num_clients = $CI->users_promoters->retrieve_promoter_clients_list($pro->up_id, $team_fan_page_id, array('count' => true));
			$statistics->num_total_guest_list_reservations = $CI->users_promoters->retrieve_num_guest_list_reservation_requests($pro->up_id, array('upcoming' => true));
			$statistics->num_upcoming_guest_list_reservations = $CI->users_promoters->retrieve_num_guest_list_reservation_requests($pro->up_id);
			$statistics->trailing_weekly_guest_list_reservation_requests = $CI->users_promoters->retrieve_trailing_weekly_guest_list_reservation_requests($pro->up_id);
			$statistics->trailing_weekly_guest_list_reservation_requests_attendance = $CI->users_promoters->retrieve_trailing_weekly_guest_list_reservation_requests_percentage_attendance($pro->up_id);
			
			$pro->statistics = $statistics;
						
		}
		unset($pro);
		
		
		return $data;
		
	}

}