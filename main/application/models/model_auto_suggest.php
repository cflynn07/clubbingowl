<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * operations related to auto suggestion of query matches for a given search pattern
 * */
class Model_auto_suggest extends CI_Model {

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
	 * Finds clubs and promoters that match the given search pattern
	 * 
	 * @param	string (pattern to search with)
	 * @return 	array (matches to pattern)
	 */
	function retrieve_promoter_matches($search_pattern){
				
		$sql = "SELECT
					
					u.full_name				as label,
					up.public_identifier	as id,
					'Promoter'				as value,
					up.profile_image		as thumb,
					u.oauth_uid				as oauth_uid,
					up.id 					as up_id,
					c.url_identifier		as c_url_identifier
					
				FROM 	users_promoters up
				
				JOIN 	users u
				ON 		up.users_oauth_uid = u.oauth_uid
				
				JOIN 	promoters_teams pt
				ON 		pt.promoter_id = up.id 
				
				JOIN 	teams t
				ON 		pt.team_fan_page_id = t.fan_page_id
				
				JOIN 	cities c 
				ON 		t.city_id = c.id
				
				WHERE	u.full_name LIKE ?
						AND up.completed_setup = 1
						AND up.banned = 0
						AND t.completed_setup = 1
						AND pt.approved = 1
						AND pt.banned = 0
						AND pt.quit = 0
						
				LIMIT	6";
		
		$query = $this->db->query($sql, array('%' . $search_pattern . '%'));
		$result = $query->result();
		
		//slap in all the venues this promoter is associated with
		foreach($result as $key => &$res){
			
			$sql = "SELECT DISTINCT
						
						tv.id		as tv_id,
						tv.name 	as tv_name
						
					FROM 	team_venues tv 
					
					JOIN 	teams_venues_pairs tvp
					ON 		tvp.team_venue_id = tv.id
					
					JOIN 	teams t 
					ON 		tvp.team_fan_page_id = t.fan_page_id
					
					JOIN 	promoters_guest_list_authorizations pgla
					ON		pgla.team_venue_id = tv.id
					
					WHERE 	
						pgla.user_promoter_id = ?
						AND 	pgla.deactivated = 0
						AND 	tvp.deleted = 0
					
					ORDER BY 	tv.id DESC";
			$query = $this->db->query($sql, array($res->up_id));
			$res->p_venues = $query->result();
			
			//ghetto hack
			if(!$res->p_venues)
				unset($result[$key]);
			
		}
		
		
		//slap in venues
		$sql = "SELECT
		
					tv.id 				as tv_id,
					tv.name 			as tv_name,
					tv.image			as tv_image,
					c.url_identifier	as c_url_identifier,
					c.name 				as c_name,
					c.state				as c_state,
					'Venue'				as value
				
				FROM 	team_venues tv 
				
				JOIN 	cities c 
				ON 		tv.city_id = c.id
				
				WHERE	tv.name LIKE ?
				AND 	tv.banned = 0";		
		$query = $this->db->query($sql, array('%' . $search_pattern . '%'));
		$result = array_merge($result, $query->result());
		
		
		
		
		//slap in guest lists
		$sql = "(SELECT DISTINCT
		
					'promoter' 				as gl_type,
					'Guestlist' 			as value,
					pgla.id					as gl_id,
					pgla.name 				as gl_name,
					pgla.image				as gl_image,
					pgla.day				as gl_day,
					tv.name					as tv_name,
					c.name					as c_name,
					c.state 				as c_state,
					c.url_identifier		as c_url_identifier,
					up.public_identifier 	as up_public_identifier,
					u.full_name 			as u_full_name
					
				FROM 	promoters_guest_list_authorizations pgla 
				
				JOIN 	users_promoters up 
				ON 		pgla.user_promoter_id = up.id
				
				JOIN 	users u 
				ON 		up.users_oauth_uid = u.oauth_uid
				
				JOIN 	team_venues tv 
				ON 		pgla.team_venue_id = tv.id 

				JOIN 	teams_venues_pairs tvp
				ON 		tvp.team_venue_id = tv.id

				JOIN 	teams t 
				ON 		tvp.team_fan_page_id = t.fan_page_id
				
				JOIN	cities c 
				ON 		t.city_id = c.id
					
				WHERE	t.completed_setup = 1
				AND 	tv.banned = 0
				AND 	tvp.deleted = 0
				AND 	pgla.deactivated = 0
				AND 	pgla.name LIKE ?)
			UNION
				(SELECT DISTINCT
					
					'venue'					as gl_type,
					'Guestlist' 			as value,
					tgla.id 				as gl_id,
					tgla.name				as gl_name,
					tgla.image				as gl_image,
					tgla.day 				as gl_day,
					tv.name 				as tv_name,
					c.name 					as c_name,
					c.state					as c_state,
					c.url_identifier		as c_url_identifier,
					'null'					as up_public_identifier,
					'null'					as u_full_name
				
				FROM 	teams_guest_list_authorizations tgla
				
				JOIN 	team_venues tv 
				ON 		tgla.team_venue_id = tv.id 

				JOIN 	teams_venues_pairs tvp
				ON 		tvp.team_venue_id = tv.id

				JOIN 	teams t 
				ON 		tvp.team_fan_page_id = t.fan_page_id

				JOIN 	cities c 
				ON 		t.city_id = c.id 
				
				WHERE	t.completed_setup = 1 
				AND 	tv.banned = 0 
				AND 	tvp.deleted = 0
				AND 	tgla.deactivated = 0
				AND 	tgla.name LIKE ?)";
		$query = $this->db->query($sql, array('%' . $search_pattern . '%', '%' . $search_pattern . '%'));		
	
		$result_temp = $query->result();
		
		setlocale(LC_ALL, $this->config->item('current_lang_locale'));
		foreach($result_temp as &$res){
					
			$time = strtotime('next ' . $res->gl_day);
			$res->occurance_date = strftime('%A', $time) . ' ' . strftime('%B %e', $time);
			
		}
		
		$result = array_merge($result, $result_temp);
		
		return $result;
	}
	
	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	
	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
	
}

/* End of file model_auto_suggest.php */
/* Location: application/models/model_auto_suggest.php */