<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Retrieves list of NEW user's friends that are VibeCompass users and sends all of them a notification
 * that a new user has joined VibeCompass
 * 
 */
class Net_Gearman_Job_notification_vc_friends_join_guest_list extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$vibecompass_user = json_decode($args['vibecompass_user']);
		$guest_list = json_decode($args['guest_list']);
		$guest_list_type = $args['guest_list_type'];
		
		$CI->load->library('library_notifications');
		$notification_count = $CI->library_notifications->create_notification_friend_join_guest_list($vibecompass_user, $guest_list, $guest_list_type);
		
		echo 'Notified friends vc_user joined guest list: ' . count($notification_count) . PHP_EOL;
    }
}