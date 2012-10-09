<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function run_gearman_job($job_name, $arguments = array(), $poll_job = true){
    $CI =& get_instance();
	
	$CI->benchmark->mark('start');
	
	$job = false;
	if($poll_job)
		$job = $CI->session->userdata($job_name);
	
	if($job !== false){
		$job = json_decode($job);
	}
	
	if(!$poll_job || !$job || ($job->req_time < (time() - 10))){
		unset($job);
		
		//start gearman job for retrieving promoter popularity w/ friends
		$CI->load->library('pearloader');
		$gearman_client = $CI->pearloader->load('Net', 'Gearman', 'Client');
		
		# ------------------------------------------------------------- #
		#	Send guest list request to gearman as a background job		#
		# ------------------------------------------------------------- #				
		//add job to a task
		
		
		//add job to a task
		$gearman_task = $CI->pearloader->load('Net', 'Gearman', 'Task', array('func' => $job_name,
														'arg' => $arguments));
														
		$gearman_task->type = Net_Gearman_Task::JOB_BACKGROUND;
		
		//add test to a set
		$gearman_set = $CI->pearloader->load('Net', 'Gearman', 'Set');
		$gearman_set->addTask($gearman_task);
		 
		//launch that shit
		$gearman_client->runSet($gearman_set);
		# ------------------------------------------------------------- #
		#	END Send guest list request to gearman as a background job	#
		# ------------------------------------------------------------- #
	
		if($poll_job){
			//Save background handle and server to user's session
			$job = new stdClass;
			$job->handle = $gearman_task->handle;
			$job->server = $gearman_task->server;
			$job->attempt = 0;
			$job->req_time = time();
			
			$CI->session->set_userdata($job_name, json_encode($job));
		}
		
		$CI->benchmark->mark('end');
		
//		echo 'Elapsed gearman time ' . $CI->benchmark->elapsed_time('start', 'end') . PHP_EOL;
		
	}
	
}