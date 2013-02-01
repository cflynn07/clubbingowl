<?php if(! defined('BASEPATH') ) exit('No direct script access allowed');

if(!function_exists('admin_report_guest_lists_checkin')){
	
	function admin_report_guest_lists_checkin($team_fan_page_id){
		
		
		$CI =& get_instance();
		
		$CI->load->model('model_users_managers', 	'users_managers', 	true);
		$CI->load->model('model_teams', 			'teams', 			true);
		
		$start_date  = $CI->input->post('start_date');
		$end_date 	 = $CI->input->post('end_date');
		
		$team_venues = $CI->users_managers->retrieve_team_venues($team_fan_page_id, $CI->input->post('venues'));
		
		
		$reservations = array();
		foreach($team_venues as $key => $venue){
			
			$result = $CI->teams->retrieve_venue_floorplan_reservations_range($venue->tv_id,
																						$CI->input->post('promoters'),
																						$team_fan_page_id,
																						$start_date,
																						$end_date);
			$reservations = array_merge($reservations, $result);
			
		}
		
		
		return $reservations;	

	}

}