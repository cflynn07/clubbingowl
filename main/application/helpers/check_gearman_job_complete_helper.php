<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function check_gearman_job_complete($job_name){
	
	$CI =& get_instance();
	
	if(!$job = $CI->session->userdata($job_name))
		die(json_encode(array('success' => false,
								'message' => 'No retrieve request found')));	
									
	$job = json_decode($job);
	$job->attempt += 1;
	
	//check job status to see if it's completed
//	$CI->load->library('library_memcached', '', 'memcached');

	$CI->load->library('Redis', '', 'redis');
	if($job_result = $CI->redis->get($job->handle)){
		
		//free memory from memcached
		$CI->redis->del($job->handle);
		$CI->session->unset_userdata($job_name);
		
		$temp = json_decode($job_result);
		
		if(isset($temp->success) && $temp->success === false){
			//user's facebook session is invalid, delete session
			
			$vc_user = $CI->session->userdata('vc_user');
			$vc_user = json_decode($vc_user);
			if(isset($vc_user->oauth_uid)){
				$CI->users->update_user($vc_user->oauth_uid, array(
					'access_token_valid_seconds' => 0
				));
				$CI->session->unset_userdata('vc_user');
			}
			
			$job_result = json_decode($job_result);
			$job_result->trigger_refresh = true;
			$job_result = json_encode($job_result);
									
		}
		
		die($job_result); //<-- already json in memcache	
		
	}else{
						
		if($job->attempt > 4)
			$CI->session->unset_userdata($job_name);
		else 
			$CI->session->set_userdata($job_name, json_encode($job));
								
		die(json_encode(array('success' => false)));
		
	}
	
}