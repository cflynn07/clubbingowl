<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function retrieve_vc_user_friends($user_oauth_uid, $access_token, $fields = array()){
    $CI =& get_instance();
	
	$CI->load->library('library_memcached', '', 'memcached');
	$CI->load->library('library_facebook', '', 'facebook');
	
	if($fields === array()){
		$fields = array('uid', 'pic_square', 'first_name', 'last_name', 'name', 'third_party_id');
	}
	
	//First get list of user's friends that are vibecompass users						
	
	if(!$result = $CI->memcached->get('cache_user_friends_' . $user_oauth_uid)){
		$result = $CI->facebook->retrieve_user_facebook_friends($access_token, $fields);
		$CI->memcached->add('cache_user_friends_' . $user_oauth_uid, $result, (60 * 15));
		echo 'Cached user '  . $user_oauth_uid . ' friends' . PHP_EOL;
	}
	
	return $result;
}