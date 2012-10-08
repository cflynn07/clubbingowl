<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Retrieves list of NEW user's friends that are VibeCompass users and sends all of them a notification
 * that a new user has joined VibeCompass
 * 
 */
class Net_Gearman_Job_notification_vc_friends_new_user extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
		//get all the stuff we're going to need...
		$CI =& get_instance();
		$vibecompass_user = json_decode($args['vibecompass_user']);
		
		$CI->load->library('library_notifications');
		$notification_count = $CI->library_notifications->create_notification_friend_join($vibecompass_user);
		
		echo 'Notified friends new user: ' . count($notification_count) . PHP_EOL;
    }
}