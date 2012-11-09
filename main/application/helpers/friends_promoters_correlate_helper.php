<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function friends_promoters_correlate($friends_uids, $promoters){
    $CI =& get_instance();
	
		
	
	$CI->load->model('model_users_promoters', 'users_promoters', true);
	
	
	/*
	$venues_friends = array();	
	foreach($venues as $venue){
		
		//first find all the clients for each venue
		$venue_clients = $CI->teams->retrieve_venue_clients($venue, true);

		$venues_friends[$venue] = array_intersect($venue_clients, $friends_uids);
		$venues_friends[$venue] = array_values($venues_friends[$venue]);

	}
	unset($venue);
	
	return $venues_friends;
	
	*/
	
	
	$promoters_friends = array();
	foreach($promoters as $pro){
		
		
		$promoter_clients = $CI->users_promoters->retrieve_promoter_clients_list($pro);
		
		
		
		$temp = array();
		foreach($promoter_clients as $pc){
			$temp[] = $pc->pglr_user_oauth_uid;
		}
		$promoter_clients = $temp;
	
	
		$promoters_friends[$pro] = array_intersect($promoter_clients, $friends_uids);		
		$promoters_friends[$pro] = array_values($promoters_friends[$pro]);
		
	}
	unset($pro);
	
	
	return $promoters_friends;

    
}