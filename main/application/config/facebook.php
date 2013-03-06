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
	'local'			=> array(
		'facebook_app_id'				=> '362616127152925',
		'facebook_api_secret'			=> '7a6be74ff92589c1ae47d9258a608f8d',
		'facebook_app_access_token'		=> '362616127152925|JZ3waQHUCgtqa6rBh_IwSvB_UMM'

//		'facebook_app_id'				=> '236258849725316',
//		'facebook_api_secret'			=> '7d279eb359234840123a78522a5809bb',
//		'facebook_app_access_token'		=> '236258849725316|OK6iu5HSDxIexFEyZ6iG0OuK6qs'
	),
	
	//nemesis_staging
	'staging'		=> array(
		'facebook_app_id'				=> '395302060543702',
		'facebook_api_secret'			=> 'd71da8500017dc071efb48104062e3f0',
		'facebook_app_access_token'		=> '395302060543702|sAQGyTaBQYZ2XlISusrGoUrp4bo'
	),
	
	//clubbingowl
	'production'	=> array(
//		'facebook_app_id'				=> '286248728153271',
//		'facebook_api_secret'			=> '907ab65ecd8b8b82cfa976c02f523510',
//		'facebook_app_access_token'		=> '286248728153271|pPLR00nzyzqJow1XfR9Cqpbv9xg'

		'facebook_app_id'				=> '236258849725316',
		'facebook_api_secret'			=> '7d279eb359234840123a78522a5809bb',
		'facebook_app_access_token'		=> '236258849725316|OK6iu5HSDxIexFEyZ6iG0OuK6qs'

	)
);


$creds = $facebook_variables[MODE];


$config['facebook_app_id'] 				= $creds['facebook_app_id'];
$config['facebook_api_secret'] 			= $creds['facebook_api_secret'];
//The app access token only changes when the facbeook api secret changes
$config['facebook_app_access_token'] 	= $creds['facebook_app_access_token'];


$config['facebook_default_scope']	= 'email,publish_stream,birthday'; // E.G 'read_stream,birthday,users_events,rsvp_event'
$config['facebook_api_url'] 		= 'https://graph.facebook.com/'; // Just in case it changes.


/* End of file facebook.php */
/* Location: ./application/config/facebook.php */