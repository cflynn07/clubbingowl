<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * database interaction related to managers/venues
 * */
class Model_users_managers extends CI_Model {

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
	 * Retrieves all teams that a manager represents
	 * 
	 * @param	string (oauth_uid)
	 * @return	array
	 */
	function retrieve_manager_team($users_oauth_uid){
		
		$sql = "SELECT
					
					mt.id 				as mt_id,
					t.name 				as team_name,
					t.fan_page_id 		as team_fan_page_id,
					t.description		as team_description,
					t.piwik_id_site		as team_piwik_id_site,
					t.completed_setup	as team_completed_setup,
					c.id 				as c_id,
					c.name 				as c_name,
					c.state 			as c_state
					
				FROM 	managers_teams mt
				
				JOIN	teams t
				ON		mt.fan_page_id = t.fan_page_id
				
				JOIN 	cities c
				ON 		c.id = t.city_id
				
				WHERE mt.user_oauth_uid = ?
						
				LIMIT 1";
				
		$query = $this->db->query($sql, array($users_oauth_uid));
		return $query->row();
		
	}
	
	/**
	 * Returns all of the venues associated with a particular team
	 * 
	 * @param	int (fan_page_id)
	 * @return 	array
	 */
	function retrieve_team_venues($fan_page_id){
		
		
		
		$sql = "SELECT
					
					tv.id 					as tv_id,
					tv.team_fan_page_id		as tv_team_fan_page_id,
					tv.image 				as tv_image,
					tv.name					as tv_name,
					tv.description			as tv_description,
					tv.street_address		as tv_street_address,
					tv.city 				as tv_city,
					tv.state 				as tv_state,
					tv.zip 					as tv_zip,
					tv.image 				as tv_image,	
					tv.monday				as tv_monday,
					tv.tuesday				as tv_tuesday,
					tv.wednesday			as tv_wednesday,
					tv.thursday				as tv_thursday,
					tv.friday				as tv_friday,
					tv.saturday				as tv_saturday,
					tv.sunday				as tv_sunday,
					c.name 					as c_name, 
					c.state					as c_state,
					c.url_identifier		as c_url_identifier
					
				FROM 	team_venues tv
				
				JOIN 	teams_venues_pairs tvp
				ON 		tvp.team_venue_id = tv.id
				
				JOIN 	teams t 
				ON 		tvp.team_fan_page_id = t.fan_page_id
				
				JOIN 	cities c 
				ON 		t.city_id = c.id
				
				WHERE 	t.fan_page_id = ?
				AND 	tv.banned = 0
				AND 	tvp.deleted = 0";
		$query = $this->db->query($sql, array($fan_page_id));		
		return $query->result();
	}
	
	/**
	 * Retrieve a specific venue
	 *
	 * @param	team_fan_page_id
	 * @param	tv_id
	 * @return 	object || false
	 */
	function retrieve_individual_team_venue($team_fan_page_id, $tv_id){
				
		$this->db->select('tvp.id')
			->from('teams_venues_pairs tvp')
			->where(array(
				'tvp.team_fan_page_id'	=> $team_fan_page_id,
				'tvp.team_venue_id'		=> $tv_id,
				'tvp.deleted'			=> 0
			));
		$query = $this->db->get();
		$result = $query->row();
		if(!$result)
			return $result;
			
		$query = $this->db->get_where('team_venues', array('id' => $tv_id));
		$result = $query->row();
		
		if(!$result)
			return $result;
			
		//attach guest lists
		$result->tgla = $this->retrieve_team_venue_guest_list_authorizations($result->id, $team_fan_page_id);
		return $result;
	}
	
	/**
	 * Retrieves all guest lists that have been authorized at a particular venue
	 * 
	 * @param	id (team venue id)
	 * @return	array
	 */
	function retrieve_team_venue_guest_list_authorizations($team_venue_id, $team_fan_page_id){
		$sql = "SELECT
					
					tgla.id 				as tgla_id,
					tgla.team_fan_page_id	as tgla_team_fan_page_id,
					tgla.team_venue_id 		as tgla_team_venue_id,
					tgla.day 				as tgla_day,
					tgla.name				as tgla_name,
					tgla.create_time		as tgla_create_time,
					tgla.deactivated		as tgla_deactivated,
					tgla.deactivated_time	as tgla_deactivated_time,
					tgla.auto_approve		as tgla_auto_approve,
					tgla.description		as tgla_description
					
				FROM team_venues tv
				
				JOIN teams_guest_list_authorizations tgla
				ON tv.id = tgla.team_venue_id
				
				WHERE tv.id = ?
					AND tgla.deactivated = 0 
					AND tgla.team_fan_page_id = ?";
		$query = $this->db->query($sql, array($team_venue_id, $team_fan_page_id));
		return $query->result();
	}
	
	/**
	 * Retrieves this week's current guest list (if one exists yet) for a given guest list authoriation
	 * 
	 * @param	int (teams_guest_list_authorizations_id)
	 * @return	object || false
	 */
	function retrieve_teams_guest_list_authorizations_current_guest_list($teams_guest_list_authorizations_id){
		$sql = "SELECT
					
					tgl.id									as tgl_id,
					tgl.team_guest_list_authorization_id 	as tgl_team_guest_list_authorization_id,
					tgl.date 								as tgl_date,
					tgl.canceled 							as tgl_canceled
					
				FROM teams_guest_lists tgl
				JOIN teams_guest_list_authorizations tgla
				ON tgl.team_guest_list_authorization_id = tgla.id
				
				WHERE tgla.id = $teams_guest_list_authorizations_id
					AND tgl.date >= '" . date('Y-m-d', time()) . "'";
										
		$query = $this->db->query($sql);
		return $query->row();
	}
	
	/**
	 * Retrieves all the head users and their entourage of users for a particular guest list
	 * 
	 * @param 	int (teams_guest_lists_id)
	 * @return	array
	 */
	function retrieve_teams_guest_list_members($teams_guest_lists_id){
		$sql = "SELECT
		
					tglr.id					as tglr_id,
					tglr.user_oauth_uid		as tglr_user_oauth_uid,
					tglr.table_request		as tglr_table_request,
					tglr.request_msg 		as tglr_request_msg,
					tglr.response_msg 		as tglr_response_msg,
					tglr.host_message		as tglr_host_message,
					tglr.approved			as tglr_approved,
					tglr.create_time 		as tglr_create_time,
					tglr.table_min_spend	as table_min_spend
				
				FROM 	teams_guest_lists_reservations tglr
				
				WHERE 	tglr.team_guest_list_id = $teams_guest_lists_id
				
				ORDER BY 	tglr.create_time ASC";
		$query = $this->db->query($sql);
		$result = $query->result();
		
		//loop over the head users and attach their entourages
		foreach($result as &$res){
					
			$sql = "SELECT
					
						tglre.oauth_uid		as tglre_oauth_uid
					
					FROM teams_guest_lists_reservations_entourages tglre
					
					WHERE tglre.team_guest_list_reservation_id = ?";
					
			$query = $this->db->query($sql, array($res->tglr_id));
			$res->entourage = $query->result();
			
			//ALSO ATTACH PHONE NUMBER FOR HEAD USER
			$sql = "SELECT
			
						u.phone_number as u_phone_number
					
					FROM 	users u 
					
					WHERE 	u.oauth_uid = ?";
			$query2 = $this->db->query($sql, array($res->tglr_user_oauth_uid));
			$result2 = $query2->row();
			$res->u_phone_number = $result2->u_phone_number;
			
		}
		
		return $result;
	}
	
	/**
	 * Retrieve all guest list authorizations for all venues for all teams that a manager is associated with
	 * 
	 * @param	int (manager oauth_uid)
	 * @return	array
	 */
	function retrieve_all_manager_team_guest_list_reservations($user_oauth_uid){
		$sql = "SELECT
					
					t.id 				as t_id,
					t.fan_page_id 		as t_fan_page_id,
					t.name      		as t_name,
					tv.id 				as tv_id,
					tv.name				as tv_name,
					tgla.id 			as tgla_id,
					tgla.day 			as tgla_day,
					tgla.name 			as tgla_name,
					tgla.deactivated	as tgla_deactivated
				
				FROM managers_teams mt
				JOIN teams t
				ON mt.fan_page_id = t.fan_page_id
				
				JOIN team_venues tv
				ON tv.team_fan_page_id = t.fan_page_id
				
				JOIN teams_guest_list_authorizations tgla
				ON tgla.team_venue_id = tv.id
				
				WHERE mt.user_oauth_uid = $user_oauth_uid
						AND t.completed_setup = 1";
						
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	function retrieve_venue_clients_detailed($venue_id, $team_fan_page_id){
		
		$clients_oauth_uids = $this->retrieve_venue_clients($venue_id, $team_fan_page_id);
				
		$this->db->select('u.full_name 		as u_full_name,
							u.first_name	as u_first_name,
							u.last_name		as u_last_name,
							u.email 		as u_email,
							u.oauth_uid		as u_oauth_uid,
							u.phone_number	as u_phone_number,
							u.opt_out_email	as u_opt_out_email')
			->from('users u');
			
		foreach($clients_oauth_uids as $c_uid){
			$this->db->or_where('u.oauth_uid', $c_uid->tglr_user_oauth_uid);
		}
		
		if($clients_oauth_uids){
			$query = $this->db->get();
			$result = $query->result();
		}else{
			$result = array();
		}
		
		//attach gl bookings
		foreach($result as &$user){
			
			$this->db->select('*')
				->from('teams_guest_list_authorizations tgla')
				->join('teams_guest_lists tgl', 					'tgl.team_guest_list_authorization_id = tgla.id')
				->join('teams_guest_lists_reservations tglr', 		'tglr.team_guest_list_id = tgl.id')
				->where(array(
					'tgla.team_venue_id' 	=> $venue_id,
					'tglr.user_oauth_uid'	=> $user->u_oauth_uid,
					'tgla.team_fan_page_id' => $team_fan_page_id
				));
			
			$query = $this->db->get();
			$user->gl_history = $query->result();
			
		}
		
		return $result;
		
	}
	
	/**
	 * Retrieve all clients that have reserved guest lists and tables at a venue
	 * 
	 * @param	int (venue_id)
	 * @return 	array
	 */
	function retrieve_venue_clients($venue_id, $team_fan_page_id = false){
		
		$sql = "SELECT
		
					DISTINCT 	tglr.user_oauth_uid 	as tglr_user_oauth_uid
					
				FROM 	teams_venues_pairs tvp
				
				JOIN 	team_venues tv
				ON 		tvp.team_venue_id = tv.id	
								
				JOIN	teams_guest_list_authorizations tgla
				ON 		tgla.team_venue_id = tv.id
				
				JOIN  	teams_guest_lists tgl
				ON 		tgl.team_guest_list_authorization_id = tgla.id
				
				JOIN 	teams_guest_lists_reservations tglr
				ON 		tglr.team_guest_list_id = tgl.id
				
				WHERE 	tvp.team_fan_page_id = ? 
				AND tv.id = ? 
				AND tgla.team_fan_page_id = ?";
		$query = $this->db->query($sql, array($team_fan_page_id, $venue_id, $team_fan_page_id));
				
		$result = $query->result();
				
		return $result;
	}
	
	/**
	 * Retrieves all team_venue guest list reservations for a specific team_venue
	 * 
	 * @param	int (team_venue id)
	 * @param 	bool (current only if TRUE)
	 * @return 	array
	 */
	function retrieve_team_venue_guest_list_reservations($tv_id, $current = false){
		
		$sql = "SELECT
					
					tglr.id 	as tglr_id
				
				FROM 	teams_guest_lists_reservations tglr
				
				JOIN 	teams_guest_lists tgl
				ON 		tglr.team_guest_list_id = tgl.id
				
				JOIN 	teams_guest_list_authorizations tgla
				ON 		tgl.team_guest_list_authorization_id = tgla.id
				
				JOIN 	team_venues tv 
				ON 		tgla.team_venue_id = tv.id
				
				WHERE 	tv.id = $tv_id ";
		if($current){
			$sql .= "AND tgl.date >= '" . date('Y-m-d', time()) . "'";
		}
				
		$query = $this->db->query($sql);
		return $query->result();
		
	}
	
	/**
	 * Retrieve's the UID of recent visitors to a team's pages (widget, promoters, venue pages etc)
	 * 
	 */
	function retrieve_recent_team_visitors($pt_ids){
		
		if(!$pt_ids)
			return array();
			
		$sql = "SELECT DISTINCT

					uv.users_oauth_uid 	as uv_users_oauth_uid
				
				FROM 	user_views uv 
				
				WHERE ";
				foreach($pt_ids as $key => $pt_id){
					if($key == (count($pt_ids) - 1)){
						//last
						
						$sql .= "uv.promoters_teams_id = ? ";
						
					}else{
							
						$sql .= "uv.promoters_teams_id = ? OR ";
						
					}
							
				}
				
		$sql .=	"ORDER BY	uv.id DESC
				
				LIMIT 100";
		$query = $this->db->query($sql, $pt_ids);		
		return $query->result();	
		
	}
	
	/**
	 * Retrieve all the team visitors for this teams promoters and venues
	 * 
	 * @param 	array (promoter pt ids)
	 * @param 	int (team fan page id)
	 * @return 	array
	 */
	function retrieve_top_team_visitors($promoters_pt_ids, $team_fan_page_id){
		
		if(!$promoters_pt_ids)
			return array();
		
		$sql = "SELECT
		
					users_oauth_uid,
					count(*) as count
		
				FROM 
		
				(SELECT 
		
					uv.users_oauth_uid as 	users_oauth_uid
						
				FROM 	user_views uv
				
				WHERE 	";
				
		foreach($promoters_pt_ids as $key => $pt_id){
			if($key == (count($promoters_pt_ids) - 1)){
				//last
				
				$sql .= "uv.promoters_teams_id = ? ";
				
			}else{
					
				$sql .= "uv.promoters_teams_id = ? OR ";
				
			}
		}
		
		$sql .= ") t
		
				GROUP BY users_oauth_uid
				ORDER BY count DESC
				LIMIT 50";
		
		$query = $this->db->query($sql, $promoters_pt_ids);
		return $query->result();
		
	}
	
	/**
	 * Retrieves the number of guest list reservation requests for the trailing 12 weeks
	 * 
	 * @param 	int (team fan page id)
	 * @return 	array
	 */
	function retrieve_trailing_weekly_guest_list_reservation_requests($team_fan_page_id){

		$num_trailing_weeks = 12;
		$date_backtrack_limit = date('Y-m-d', strtotime('Sunday -' . ($num_trailing_weeks + 1) . ' weeks'));
	
		$sql = "SELECT
		
					tgl.date 			as date,
					count(tgl.date) 	as count
					
				FROM 	teams_guest_lists_reservations tglr
				
				JOIN 	teams_guest_lists tgl
				ON 		tglr.team_guest_list_id = tgl.id
				
				JOIN 	teams_guest_list_authorizations tgla
				ON 		tgl.team_guest_list_authorization_id = tgla.id
				
				JOIN 	team_venues tv
				ON 		tgla.team_venue_id = tv.id
				
				WHERE 	tv.team_fan_page_id = ?
						AND
						tgl.date >= ?
						
				GROUP BY 	tgl.date";
		$query = $this->db->query($sql, array($team_fan_page_id, $date_backtrack_limit));
		$result = $query->result();
				
		$weeks_popularity = array();
		for($i = ($num_trailing_weeks - 1); $i >= 0; $i--){
			
			$week_start = date('Y-m-d', strtotime('Sunday -' . ($i + 1) . ' weeks'));
			$week_end = date('Y-m-d', strtotime('Saturday -' . ($i) . ' weeks'));
			
			$week_start_short = date('m/d', strtotime('Sunday -' . ($i + 1) . ' weeks'));
			$week_end_short = date('m/d', strtotime('Saturday -' . ($i) . ' weeks'));
			
			$date_range_key = $week_start_short . ' - ' . $week_end_short;
			$weeks_popularity[$date_range_key] = 0;
			
			//count how many guest list reservations occur in this week range
			$start_datetime = new DateTime($week_start . '00:00:00');
			$end_datetime = new DateTime($week_end . '23:59:59');
			foreach($result as $res){
				
				$occurance_datetime = new DateTime($res->date);	
				if($occurance_datetime >= $start_datetime && $occurance_datetime <= $end_datetime){
					$weeks_popularity[$date_range_key] += intval($res->count);
				}
				
			}
			
		}
		
		return $weeks_popularity;
	}
	
	/**
	 * Retrieves the number of guest list reservation requests for the trailing 12 weeks
	 * 
	 * @param 	int (team fan page id)
	 * @return 	array
	 */
	function retrieve_trailing_weekly_guest_list_reservation_requests_percentage_attendance($team_fan_page_id){

		$num_trailing_weeks = 12;
		$date_backtrack_limit = date('Y-m-d', strtotime('Sunday -' . ($num_trailing_weeks + 1) . ' weeks'));
	
		$sql = "SELECT
		
					tgl.date 			as date,
					tglr.checked_in 	as tglr_checked_in
					
				FROM 	teams_guest_lists_reservations tglr
				
				JOIN 	teams_guest_lists tgl
				ON 		tglr.team_guest_list_id = tgl.id
				
				JOIN 	teams_guest_list_authorizations tgla
				ON 		tgl.team_guest_list_authorization_id = tgla.id
				
				JOIN 	team_venues tv
				ON 		tgla.team_venue_id = tv.id
				
				WHERE 	tv.team_fan_page_id = ?
						AND
						tgl.date >= ?";
		$query = $this->db->query($sql, array($team_fan_page_id, $date_backtrack_limit));
		$result = $query->result();
				
		$weeks_popularity = array();
		for($i = ($num_trailing_weeks - 1); $i >= 0; $i--){
			
			$week_start = date('Y-m-d', strtotime('Sunday -' . ($i + 1) . ' weeks'));
			$week_end = date('Y-m-d', strtotime('Saturday -' . ($i) . ' weeks'));
			
			$week_start_short = date('m/d', strtotime('Sunday -' . ($i + 1) . ' weeks'));
			$week_end_short = date('m/d', strtotime('Saturday -' . ($i) . ' weeks'));
			
			$date_range_key = $week_start_short . ' - ' . $week_end_short;
			$object = new stdClass;
			$object->attended = 0;
			$object->did_not_attend = 0;
			$weeks_popularity[$date_range_key] = $object;
			
			//count how many guest list reservations occur in this week range
			$start_datetime = new DateTime($week_start . '00:00:00');
			$end_datetime = new DateTime($week_end . '23:59:59');
			foreach($result as $res){
				
				$occurance_datetime = new DateTime($res->date);	
				if($occurance_datetime >= $start_datetime && $occurance_datetime <= $end_datetime){
					
					if($res->tglr_checked_in == '0' || $res->tglr_checked_in == 0)
						$weeks_popularity[$date_range_key]->did_not_attend += 1;
					else 
						$weeks_popularity[$date_range_key]->attended += 1;
				
				}
				
			}
			
		}
		return $weeks_popularity;
	}

	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	

	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
	
}

/* End of file model_users_managers.php */
/* Location: application/models/model_users_managers.php */