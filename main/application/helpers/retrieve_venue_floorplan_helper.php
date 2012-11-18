<?php if(! defined('BASEPATH') ) exit('No direct script access allowed');

if(!function_exists('retrieve_venue_floorplan'))
{

	/**
	 * options keys:
	 * 		tv_id
	 *  	team_fan_page_id
	 * 
	 * 		retrieve_approved_reservations
	 * 		date
	 */
	function retrieve_venue_floorplan($options){
        // the helper function doesn't have access to $this, so we need to get a reference to the 
        // CodeIgniter instance.  We'll store that reference as $CI and use it instead of $this
        $ci =& get_instance();
        
		$venue = new stdClass;
		
		
		$ci->load->model('model_teams', 'teams', true);
		
		//------------------------------------- EXTRACT FLOORPLAN -----------------------------------------
		$venue_floorplan = $ci->teams->retrieve_venue_floorplan($options['tv_id'], $options['team_fan_page_id']);
		$venue_floors = new stdClass;
		
		//iterate over all items to extract floors
		foreach($venue_floorplan as $key => $vlf){
			if(!isset($vlf->vlf_id))
				continue;
			
			if($vlf->vlf_deleted == 1)
				continue;
			
			if(!array_key_exists($vlf->vlf_id, $venue_floors)){
				
				$floor_object = new stdClass;
				$floor_object->items = array();
				$floor_object->name = $vlf->vlf_floor_name;
				
				$floor_id = $vlf->vlf_id;
				$venue_floors->$floor_id = $floor_object;
				
			}
		}
		
		//for each floor, extract the items
		foreach($venue_floors as $key => &$vf){
						
			foreach($venue_floorplan as $vlf){
				if($key == $vlf->vlf_id){
					//item is on THIS floor
					
					if($vlf->vlfi_id == NULL)
						continue;
					
					if($vlf->vlfi_deleted == 1)
						continue;
										
					$vf->items[] = $vlf;
					
				}
			}
			
		}
		
		$venue->venue_floorplan = (array)$venue_floors;
		
		
		if($options['retrieve_approved_reservations']){
			$venue_reservations = $ci->teams->retrieve_venue_floorplan_reservations($options['tv_id'],
																						$options['team_fan_page_id'],
																						date('Y-m-d', strtotime("$month/$day/$year")));
			
			foreach($venue_reservations as $vr){
			
				if(isset($vr->tglr_user_oauth_uid))
					$init_users[] = $vr->tglr_user_oauth_uid;
				elseif(isset($vr->pglr_user_oauth_uid)){
					$init_users[] = $vr->pglr_user_oauth_uid;
					$init_users[] = $vr->up_users_oauth_uid;
				}
				
				if($vr->entourage)
					foreach($vr->entourage as $ent){
						$init_users[] = $ent;
					}
			}
			
			$venue->venue_reservations = $venue_reservations;		
		}	
		//------------------------------------- END EXTRACT FLOORPLAN -----------------------------------------
		
		//temp
		$venue->venue_reservations = array();		
		return $venue;
		
	}
}

/* End of file retrieve_venue_floorplan_helper.php */
/* Location: ./application/helpers/retrieve_venue_floorplan_helper.php */