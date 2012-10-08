<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function friends_venues_correlate($friends_uids, $venues){
    $CI =& get_instance();
	
	
	$CI->load->model('model_teams', 'teams', true);
	
	$venues_friends = array();	
	foreach($venues as $venue){
		
		//first find all the clients for each venue
		$venue_clients = $CI->teams->retrieve_venue_clients($venue, true);

		$venues_friends[$venue] = array_intersect($venue_clients, $friends_uids);
		$venues_friends[$venue] = array_values($venues_friends[$venue]);

	}
	unset($venue);
	
	return $venues_friends;
}