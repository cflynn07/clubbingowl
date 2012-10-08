<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * operations related to venues
 * */
class Model_venues extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
	
	/*-------------------------------------------------------------------------
	 |	Create Methods (create)
	 | ------------------------------------------------------------------------ */
	
	
	/*-------------------------------------------------------------------------
	 |	Retrieval Methods (retrieve)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Retrieve a given venue that matches a city and a venue name
	 * 
	 * @param	string (public identifier)
	 * @param	string (city)
	 * @return	object
	 */
	function retrieve_venue($public_identifier, $city){
		
		$sql = "SELECT 	dv.venue 			as venue_name,
						dv.description 		as venue_description,
						dv.street_address	as venue_street_address,
						dv.zip 				as venue_zip,
						dv.main_image		as venue_main_image,
						dv.medium_image		as venue_medium_image,
						dv.thumbnail_image	as venue_thumbnail_image,
						dv.icon_image		as venue_icon_image,
						dc.city				as venue_city
		
				FROM	data_venues dv
				JOIN	data_cities dc
				ON		dv.city_id = dc.id
				
				WHERE	dv.venue = '$public_identifier'
						AND	dc.city = '$city'";
		$query = $this->db->query($sql);
		return $query->row();
		
	}
	
	/**
	 * Retrieve a given venue that matches a venue_id (more efficient lookup)
	 * 
	 * @param	venue_id
	 * @return	object
	 */
	function retrieve_venue_by_id($venue_id){
		
		$sql = "SELECT 	dv.venue 			as venue_name,
						dv.description 		as venue_description,
						dv.street_address	as venue_street_address,
						dv.zip 				as venue_zip,
						dv.main_image		as venue_main_image,
						dv.medium_image		as venue_medium_image,
						dv.thumbnail_image	as venue_thumbnail_image,
						dv.icon_image		as venue_icon_image,
						dc.city				as venue_city
		
				FROM	data_venues dv
				JOIN	data_cities dc
				ON		dv.city_id = dc.id
				
				WHERE	dv.id = $venue_id";
		$query = $this->db->query($sql);
		return $query->row();
		
	}
	
	/**
	 * Retrieve all venues, or all venues for a given city if specified
	 * 
	 * @param	string (city)
	 * @return	array
	 */
	function retrieve_all_venues($city = ''){
			
		$sql = "SELECT 	dv.venue 			as venue_name,
						dv.description 		as venue_description,
						dv.street_address	as venue_street_address,
						dv.zip 				as venue_zip,
						dv.main_image		as venue_main_image,
						dv.medium_image		as venue_medium_image,
						dv.thumbnail_image	as venue_thumbnail_image,
						dv.icon_image		as venue_icon_image,
						dc.city				as venue_city
		
				FROM	data_venues dv
				JOIN	data_cities dc
				ON		dv.city_id = dc.id";
				
				if($city != '')
					$sql .= " WHERE	dc.city = '$city'";
					
		$query = $this->db->query($sql);
		return $query->result();
		
	}
	
	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	
	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
	
}

/* End of file model_venues.php */
/* Location: application/models/model_venues.php */