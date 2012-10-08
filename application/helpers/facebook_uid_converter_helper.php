<?php if(! defined('BASEPATH') ) exit('No direct script access allowed');

/**
 * Helps convert a facebook uid in the form of a float to a more usable string
 * 
 */
if (!function_exists('facebook_uid_converter')){
	   
    function facebook_uid_converter($uid){
    	
        $uid = sprintf('%f', $uid);
		$uid = substr($uid, 0, strpos($uid, '.'));
				
        return $uid;
    }
}

/* End of file facebook_uid_converter_helper.php */
/* Location: ./application/helpers/facebook_uid_converter_helper.php */