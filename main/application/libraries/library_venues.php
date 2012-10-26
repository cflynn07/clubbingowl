<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Library of helper functions for use with the promoters controller. Intended to be 
 * loaded from every url of the promoters controller that showcases a specific promoter.
 * 
 */
class library_venues{
	
	private $CI;
	public $venue;
	
	/**
	 * Class constructor
	 * 
	 * @return	library_venues
	 */
	public function __construct(){
		$this->CI =& get_instance();
	}
	
	/**
	 * Loads all data common to any page that showcases a specific venue and makes it available
	 * globally for all views.
	 * 
	 * @param	string (venue public identifier)
	 * @param	string (venue city)
	 * @return	null
	 */
	public function initialize($city, $venue_name){
		
		//Look up promoter information based on public identifier, if promoter does not exist throw 404
		$this->CI->load->model('model_app_data', 'app_data', true);
		if(!$venue = $this->CI->app_data->retrieve_venue($city, $venue_name)){
			
			//venue doesn't exist
			show_404('Venue does not exist', 404);
			die();
			
		}
						
		//add promoter object as property of this object and make globally available to all views
		$this->venue = $venue;
		$this->CI->load->vars('venue', $venue);
					
		//TODO: FIX	
		//load the piwik site id of this venues on this page for tracking purposes
//		$this->CI->load->vars('additional_sites_ids', array($promoter->up_piwik_id_site));
	}
	
	/**
	 * Initialize for facebook
	 */
	public function initialize_tv_id($tv_id){
		
		//Look up promoter information based on public identifier, if promoter does not exist throw 404
		$this->CI->load->model('model_app_data', 'app_data', true);
		if(!$venue = $this->CI->app_data->retrieve_venue_tv_id($tv_id)){
			
			var_dump($this->CI->db->last_query()); die();
			
			//venue doesn't exist
			show_404('Venue does not exist', 404);
			die();
			
		}
						
		//add promoter object as property of this object and make globally available to all views
		$this->venue = $venue;
		$this->CI->load->vars('venue', $venue);
		
	} 
	
	
	/**
	 * Retrieves all of the available guest lists at this venue
	 * 
	 * @return	array
	 */
	 public function retrieve_all_guest_lists(){
	 	
		$this->CI->load->model('model_team_guest_lists', 'team_guest_lists', true);
		return $this->CI->team_guest_lists->retrieve_all_guest_lists($this->venue->tv_id);
	 	
	 }
	 
	 /**
	  * Retrieves an individual guest list at this venue
	  * 
	  * @param	string (guest list name)
	  * @return	object || false
	  */
	  public function retrieve_individual_guest_list($guest_list_name){
	  	
		$this->CI->load->model('model_team_guest_lists', 'team_guest_lists', true);
		return $this->CI->team_guest_lists->retrieve_individual_guest_list($this->venue->tv_id, $guest_list_name);
		
	  }
	
}
/* End of file library_venues.php */
/* Location: ./application/libraries/library_venues.php */