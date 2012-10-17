<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Returns all clients for a given venue to a venue manager
 * 
 */
class Net_Gearman_Job_admin_manager_piwik_stats extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
    	$piwik_id_site = $args['piwik_id_site'];
		$CI =& get_instance();
		$CI->load->library('Redis', '', 'redis');
		$handle = $this->handle;
		
		$CI->load->library('library_piwik', '', 'piwik');
		
		$CI->piwik->set_site_id($piwik_id_site);
		
		$visits = $CI->piwik->visits('day', 30);
		$unique_visitors = $CI->piwik->unique_visitors('day', 30);
		
		$result = new stdClass;
		$result->visits = $visits;
		$result->unique_visitors = $unique_visitors;
				
		$data = json_encode(array('success' => true,
									'message' => $result));
																	
		//send result to memcached
		$CI->redis->set($handle, 
								$data);
		$CI->redis->expire($handle, 120);					
		
		echo 'Team piwik stats recieved for piwik_site_id: ' . $piwik_id_site . PHP_EOL;
			
    }
}