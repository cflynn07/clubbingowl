<?php if(! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| FACEBOOK APP SETTINGS
| -------------------------------------------------------------------
| Author: Casey Flynn
| Date: June 7, 2011
| 
| This file defines constants for useful variables supplied by facebook's
| app registration process.
| ------------------------------------------------------------------- 
 */
 
 
$facebook_variables = array(
	//nemesis
	'local'			=> array(),
	
	//nemesis_staging
	'staging'		=> array(),
	
	//clubbingowl
	'production'	=> array()
);
 
 
 
 

$project_avicii_local_app_id = '236258849725316';
$project_avicii_local_app_secret = '7d279eb359234840123a78522a5809bb';
$project_avicii_local_app_access_token = '236258849725316|OK6iu5HSDxIexFEyZ6iG0OuK6qs';

// old test app, not used anymore
//$project_avicii_app_id = '227135463992977';
//$project_avicii_app_secret = '5b248a72086081ac5bfd705f9ca393e6';
//$project_avicii_app_access_token = '227135463992977|LroWH8cKb51efavdF9h1srceVvY';

$vibecompass_app_id = '236915563048749';
$vibecompass_app_secret = 'c10e35fd0e33d45ebbd324ee9cb37562';
$vibecompass_app_access_token = '236915563048749|o6MOkeodeu1tzxied7MgFATIK_w';


if(DEPLOYMENT_ENV == 'cloudcontrol'){
	//cloudcontrol
	
	//vibecompass
	$config['facebook_app_id'] 			= $vibecompass_app_id;
	$config['facebook_api_secret'] 		= $vibecompass_app_secret;
	//The app access token only changes when the facbeook api secret changes
	$config['facebook_app_access_token'] = $vibecompass_app_access_token;
	
}else{
	//local
	
	// projectavicii_local
	$config['facebook_app_id'] 			= $project_avicii_local_app_id;
	$config['facebook_api_secret'] 		= $project_avicii_local_app_secret;
	//The app access token only changes when the facbeook api secret changes
	$config['facebook_app_access_token'] = $project_avicii_local_app_access_token;
	
}

$config['facebook_default_scope']	= 'email,publish_stream'; // E.G 'read_stream,birthday,users_events,rsvp_event'
$config['facebook_api_url'] 		= 'https://graph.facebook.com/'; // Just in case it changes.


/* End of file facebook.php */
/* Location: ./application/config/facebook.php */