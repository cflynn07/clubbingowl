<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This gearman job take a user's facebook access token, queries the facebook
 * graph api to get information about the current user, and saves the result 
 * to memcache for later retrieval by the apache process.
 * 
 */
class Net_Gearman_Job_user_friends_popular_venues extends Net_Gearman_Job_Common{
	
    public function run($args){
    	
		echo 'user_friends_popular_venues job recieved - ';
		
    	//get all the stuff we're going to need...
    	$vc_user = json_decode($args['vc_user']);		
		$CI =& get_instance();
		$CI->load->library('library_memcached', '', 'memcached');
		$handle = $this->handle;	
		
				
		$CI->load->library('library_friends');
		$CI->library_friends->initialize($vc_user);
		
		$result = $CI->library_friends->retrieve_facebook_friends_popular_venues();
		
		$data = json_encode($result);
																	
		//send result to memcached
		$CI->memcached->add($handle, 
								$data,
								120);
								
		echo 'retrieved ' . $vc_user->first_name . ' ' . $vc_user->last_name . ' friends most popular venues' . PHP_EOL;
		return;
		
    }
}