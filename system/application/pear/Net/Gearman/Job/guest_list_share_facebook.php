<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sends a request to the facbeook API to post a message on a user's
 *  facebook wall when they've been accepted on a guest list
 * 
 */
class Net_Gearman_Job_guest_list_share_facebook extends Net_Gearman_Job_Common{

	public function run($args){
		
		//get all the stuff we're going to need...
		$CI =& get_instance();
		

		$team_guest_list	= $args['team_guest_list'];
		$user_oauth_uid 	= $args['user_oauth_uid'];
		$venue_name 		= $args['venue_name'];
		$date 				= $args['date'];
		$guest_list_name 	= $args['guest_list_name'];
		$image 				= (isset($args['image'])) ? $args['image'] : false;
		
		$facebook_application_id = $CI->config->item('facebook_app_id');
		
		$CI->load->model('model_users', 'users', true);
		$vc_user = $CI->users->retrieve_user($user_oauth_uid);

		$CI->load->library('library_facebook', '', 'facebook');
		
		if($team_guest_list){
			//team guest list
			
			$team_venue_id		= $args['team_venue_id'];
			
			$params = array(
				'message' => "$vc_user->users_full_name is on the VibeCompass guest list '$guest_list_name' at $venue_name " . ((date('l', strtotime($date)) == date('l', time())) ? 'today' : date('l', strtotime($date))) . "!",
				'link' => "www.facebook.com/pages/@/$team_venue_id?sk=app_$facebook_application_id",
				'picture' => ($image) ? '' : $CI->config->item('global_assets') . 'images/vibecompass_logo.png',
				'name' => $guest_list_name,
				'caption' => "Click here to Join '$guest_list_name'",
				'description' => 'VibeCompass is the best way to connect with your favorite promoters and get on guest lists & reserve tables at hot venues.'
			);
			
		}else{
			//promoter guest list
			
			$promoter_public_identifier	= $args['promoter_public_identifier'];
			$promoter_full_name 		= $args['promoter_full_name'];
			$user_third_party_id  		= $args['user_third_party_id'];
			
			$guest_list_url_name = str_replace(' ', '_', $guest_list_name);
			
			//we need the promoter_public_identifier and the pgla_name to form the hyperlink to post on facebook
			
			if(DEPLOYMENT_ENV == 'local')
				$base_url = 'http://www.vibecompass.com/';
			else
				$base_url = 'http://www.vibecompass.com/';
				
			$params = array(
				'message' => "$vc_user->users_full_name is on $promoter_full_name's guest list '$guest_list_name' at $venue_name " . ((date('l', strtotime($date)) == date('l', time())) ? 'today' : date('l', strtotime($date))) . "!",
				'link' => $base_url . "promoters/boston/$promoter_public_identifier/guest_lists/$guest_list_url_name/?ref=$user_third_party_id",
				'picture' => ($image) ? $CI->config->item('s3_uploaded_images_base_url') . 'guest_lists/' . $image . '_t.jpg' : $CI->config->item('global_assets') . 'images/vibecompass_logo.png',
				'name' => $guest_list_name,
				'caption' => "Click here to Join '$guest_list_name'",
				'description' => 'VibeCompass is the best way to connect with your favorite promoters and get on guest lists & reserve tables at hot venues.'
			);
			
		}

	//	$result = $CI->facebook->fb_api_query('/me/feed/', $vc_user->users_access_token, 'POST', $params);
		//we don't need a user access token to post to a user's facebook wall if they've granted our application 'publish-stream' access
		$result = $CI->facebook->fb_api_query('/' . $user_oauth_uid . '/feed/', false, 'POST', $params);
		
		var_dump($result);
		
		echo "Posted to $vc_user->users_full_name's wall" . PHP_EOL;
    }

}