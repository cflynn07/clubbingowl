<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * database interaction related to managers/venues
 * */
class Model_users_venue_managers extends CI_Model {

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
	 * Retrieves all venues that a manager represents
	 * 
	 * @param	string (oauth_uid)
	 * @return	array
	 */
	function retrieve_manager_venues($oauth_uid){
		
		$sql = "SELECT 	vm.venue_id		 	as venue_id,
						v.venue				as venue_name
				
				FROM	users u
				JOIN	venue_managers vm
				ON		vm.users_id = u.id
				
				JOIN	venues v
				ON 		vm.venue_id = v.id
								
				WHERE	u.oauth_uid = $oauth_uid
				AND		vm.banned = 0";
				
		$query = $this->db->query($sql);
		return $query->result();
		
	}
	
	/**
	 * Retrieves all vibecompass users that have joined a guest list or reserved a table
	 * at a venue
	 * 
	 * @param	int (venue id)
	 * @return	array
	 */
	function retrieve_venue_clients($venue_id){
		$sql = "SELECT  DISTINCT(pglr.users_oauth_uid) as oauth_uid

				FROM	promoters_venues pv
				JOIN	users_promoters_guest_list_authorizations upgla
				ON		pv.id = upgla.promoters_venues_id
				
				JOIN	promoters_guest_lists pgl
				ON		upgla.id = pgl.users_promoters_guest_list_authorizations_id
				
				JOIN	promoters_guest_lists_reservations pglr
				ON		pgl.id = pglr.promoters_guest_lists_id
				
				WHERE	pv.venue_id = '$venue_id'";
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	/**
	 * Returns all the occasions a user has requested to join a venue's guest list
	 *
	 * TODO: refine
	 * @param	int (oauth_uid)
	 * @param	int (venue_id)
	 */
	function retrieve_user_guest_list_join_requests($oauth_uid, $venue_id){
		$sql = "SELECT 	*

				FROM	promoters_guest_lists_reservations pglr
				
				JOIN	promoters_guest_lists pgl
				ON		pglr.promoters_guest_lists_id = pgl.id
				
				JOIN	users_promoters_guest_list_authorizations upgla
				ON		pgl.users_promoters_guest_list_authorizations_id = upgla.id
				
				JOIN	promoters_venues pv
				ON		upgla.promoters_venues_id = pv.id
				
				WHERE	pglr.users_oauth_uid = '$oauth_uid'
				AND		pv.venue_id = $venue_id";
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	/**
	 * Returns the count of the number of different clients for a given venue
	 * 
	 * @param	int (venue id)
	 * @return	object
	 */
	function retrieve_venue_clients_count($venue_id){
		
		$sql = "SELECT 	COUNT(DISTINCT(pglr.users_oauth_uid)) as count

				FROM	promoters_venues pv
				JOIN	users_promoters_guest_list_authorizations upgla
				ON		pv.id = upgla.promoters_venues_id
				
				JOIN	promoters_guest_lists pgl
				ON		upgla.id = pgl.users_promoters_guest_list_authorizations_id
				
				JOIN	promoters_guest_lists_reservations pglr
				ON		pgl.id = pglr.promoters_guest_lists_id
				
				WHERE	pv.venue_id = $venue_id";
		$query = $this->db->query($sql);
		return $query->row();
	}
	
	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	

	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
	
}

/* End of file model_users_venue_managers.php */
/* Location: application/models/model_users_venue_managers.php */