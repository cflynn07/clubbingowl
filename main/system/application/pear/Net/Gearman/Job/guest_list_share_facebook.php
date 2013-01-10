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
		
		$c_url_identifier 	= (isset($args['c_url_identifier'])) ? $args['c_url_identifier'] : '';
		
		
		$facebook_application_id = $CI->config->item('facebook_app_id');
		
		$CI->load->model('model_users', 'users', true);
		$vc_user = $CI->users->retrieve_user($user_oauth_uid);

		$CI->load->library('library_facebook', '', 'facebook');
		
		$app_description = 'ClubbingOwl is the fastest way to plan your evening! Find out where your friends party and join them. With ClubbingOwl getting on a guest-list or reserving a table is only one click away.';
		
		
		
		
		if(MODE == 'local')
				$base_url = 'https://www.clubbingowl.com/';
			else
				$base_url = 'https://www.clubbingowl.com/';
		
		
		
		if($team_guest_list){
			//team guest list
			
			$team_venue_id		= $args['team_venue_id'];
			
			$params = array(
//				'message' 		=> "$vc_user->users_full_name is on the ClubbingOwl guest list '$guest_list_name' at $venue_name " . ((date('l', strtotime($date)) == date('l', time())) ? 'today' : date('l', strtotime($date))) . "!",
				'message' 		=> "I'm on the guest list '$guest_list_name' at $venue_name " . ((date('l', strtotime($date)) == date('l', time())) ? 'today' : date('l', strtotime($date))) . "!\n\n Click here to join \"$guest_list_name.\"",
			//	'link' 			=> "www.facebook.com/pages/@/$team_venue_id?sk=app_$facebook_application_id",
				'link'			=> $base_url . 'venues/' . $c_url_identifier . '/' . str_replace(' ', '_', $venue_name) . '/guest_lists/',
				'picture' 		=> ($image) ? '' : $CI->config->item('global_assets') . 'images/ClubbingOwlBackgroundWeb_small2.png',
				'name' 			=> $guest_list_name,
				'caption' 		=> "Click here to join \"$guest_list_name\"",
				'description' 	=> $app_description
			);
			
		}else{
			//promoter guest list
			
			$promoter_public_identifier	= $args['promoter_public_identifier'];
			$promoter_full_name 		= $args['promoter_full_name'];
			$user_third_party_id  		= $args['user_third_party_id'];
			
			$guest_list_url_name = str_replace(' ', '_', $guest_list_name);
			
			//we need the promoter_public_identifier and the pgla_name to form the hyperlink to post on facebook
			
			
				
			$params = array(
				'message' 		=> "I'm on $promoter_full_name's guest list \"$guest_list_name\" at $venue_name " . ((date('l', strtotime($date)) == date('l', time())) ? 'today' : date('l', strtotime($date))) . "!\n\n Click here to join \"$guest_list_name.\"",
				'link' 			=> $base_url . "promoters/$promoter_public_identifier/guest_lists/$guest_list_url_name/?ref=$user_third_party_id",
				'picture' 		=> ($image) ? $CI->config->item('s3_uploaded_images_base_url') . 'guest_lists/' . $image . '_t.jpg' : $CI->config->item('global_assets') . 'images/vibecompass_logo.png',
				'name' 			=> $guest_list_name,
				'caption' 		=> "Click here to join \"$guest_list_name\"",
				'description' 	=> $app_description
			);
			
		}

	//	$result = $CI->facebook->fb_api_query('/me/feed/', $vc_user->users_access_token, 'POST', $params);
		//we don't need a user access token to post to a user's facebook wall if they've granted our application 'publish-stream' access
		$result = $CI->facebook->fb_api_query($user_oauth_uid . '/feed/', false, 'POST', $params);
		
		var_dump($result);
		
		echo "Posted to $vc_user->users_full_name's wall" . PHP_EOL;
    }

}