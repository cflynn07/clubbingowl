<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Returns all clients for a given venue to a venue manager
 * 
 */
class Net_Gearman_Job_gearman_admin_manager_promoter_piwik_stats extends Net_Gearman_Job_Common{
	
    public function run($args){
    			
    	$promoter_site_ids = $args['promoter_site_ids'];
		$CI =& get_instance();
		$CI->load->library('library_memcached', '', 'memcached');
		$handle = $this->handle;
		
		$CI->load->library('library_piwik', '', 'piwik');
		
		
		foreach($promoter_site_ids as $key => $psi){
			$CI->piwik->set_site_id($psi);
		
			$visits = $CI->piwik->visits('day', 30);
			$unique_visitors = $CI->piwik->unique_visitors('day', 30);
			
			$result = new stdClass;
			$result->visits = $visits;
			$result->unique_visitors = $unique_visitors;
					
			$data[$key] = $result;
		}
		
		$data = json_encode(array('success' => true,
									'message' => $data));
																	
		//send result to memcached
		$CI->memcached->add($handle, 
								$data,
								120);
		
		echo 'Admin Manager Promoter piwik stats recieved for piwik_site_ids: '; 
		foreach($promoter_site_ids as $psi){ echo $psi . ', '; }
		echo PHP_EOL;
			
    }
}