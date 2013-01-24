<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * operations related to retreiving app data such as known cities, venues, etc.
 * */
class Model_app_data extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
	
	/*-------------------------------------------------------------------------
	 |	Create Methods (create)
	 | ------------------------------------------------------------------------ */
	
	/*
	 * Insert new promoter into 'users_promoters' from signup form input
	 * 
	 * @param 	Array containing indexes club-name, email, first-name, last-name, phone, user-name, password
	 * @return	Integer representing how many rows were affected by operation
	 * */

	/*-------------------------------------------------------------------------
	 |	Retrieval Methods (retrieve)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Retrieve all teams and their associated promoters and venues
	 * 
	 * @return	array
	 */
	function retrieve_all_teams_promoters_venues(){
		
		$sql = "SELECT
		
					t.id 				as t_id,
					t.fan_page_id		as t_fan_page_id,
					t.name				as t_name,
					t.description		as t_description,
					t.completed_setup	as t_completed_setup,
					t.image				as t_image
		
				FROM 	teams t";
		$query = $this->db->query($sql);
		$teams = $query->result();
		
		//find all venues for each team
		foreach($teams as &$team){
			
			$sql = "SELECT
						
						tv.id					as tv_id,
						tv.team_fan_page_id 	as tv_team_fan_page_id,
						tv.name					as tv_name,
						tv.description			as tv_description,
						tv.street_address		as tv_street_address,
						tv.monday				as tv_monday,
						tv.tuesday				as tv_tuesdsay,
						tv.wednesday			as tv_wednesday,
						tv.thursday				as tv_thursday,
						tv.friday				as tv_friday,
						tv.saturday				as tv_saturday,
						tv.sunday				as tv_sunday
						
					FROM 	team_venues tv
					
					WHERE	tv.team_fan_page_id = ?";
			$query = $this->db->query($sql, array($team->t_fan_page_id));
			$team->team_venues = $query->result();
			
		}
		unset($team);
		
		//find all managers associated with this team
		foreach($teams as &$team){
			$sql = "SELECT
			
						mt.id 				as mt_id,
						mt.user_oauth_uid 	as mt_user_oauth_uid,
						mt.fan_page_id 		as mt_fan_page_id,
						mt.banned 			as mt_banned,
						mt.banned_time 		as mt_banned_time,
						u.first_name		as u_first_name,
						u.last_name 		as u_last_name,
						u.full_name 		as u_full_name,
						u.email 			as u_email,
						u.gender 			as u_gender
					
					FROM 	managers_teams mt 
					
					JOIN 	users u 
					ON 		mt.user_oauth_uid = u.oauth_uid
					
					WHERE 	mt.fan_page_id = ?
					AND 	mt.banned = 0";
			$query = $this->db->query($sql, array($team->t_fan_page_id));
			$team->managers = $query->result();
					
		}
		unset($team);
		
		//find all hosts associated with this team
		foreach($teams as &$team){
			$sql = "SELECT
			
						th.id					as th_id,
						th.teams_fan_page_id	as th_teams_fan_page_id,
						th.users_oauth_uid		as th_users_oauth_uid,
						th.manager_oauth_uid	as th_manager_oauth_uid,
						th.time_added 			as th_time_added,
						th.banned 				as th_banned,
						th.time_banned			as th_time_banned,
						th.banned_by			as th_banned_by,
						th.quit					as th_quit,
						th.quit_time 			as th_quit_time,
						u.first_name			as u_first_name,
						u.last_name 			as u_last_name,
						u.full_name 			as u_full_name,
						u.email 				as u_email,
						u.gender 				as u_gender
					
					FROM 	teams_hosts th
					
					JOIN 	users u 
					ON 		th.users_oauth_uid = u.oauth_uid
					
					WHERE 	th.teams_fan_page_id = ?
					AND		th.quit = 0
					AND 	th.banned = 0";
			$query = $this->db->query($sql, array($team->t_fan_page_id));
			$team->hosts = $query->result();
					
		}
		unset($team);
		
		//find all promoters associated with this team
		foreach($teams as &$team){
					
			$sql = "SELECT
						
						pt.id					as pt_id,
						pt.approved				as pt_approved,
						pt.approved_time		as pt_approved_time,
						pt.banned				as pt_banned,
						pt.banned_by			as pt_banned_by,
						pt.banned_time			as pt_banned_time,
						pt.quit					as pt_quit,
						pt.quit_time 			as pt_quit_time,
						up.id 					as up_id,
						up.users_oauth_uid		as up_users_oauth_uid,
						up.completed_setup		as up_completed_setup,
						up.banned				as up_banned,
						up.time_created			as up_time_created,
						up.public_identifier	as up_public_identifier,
						up.piwik_id_site		as up_piwik_id_site,
						up.biography			as up_biography,
						up.profile_image		as up_profile_image,
						u.first_name 			as u_first_name,
						u.last_name 			as u_last_name,
						u.full_name 			as u_full_name,
						u.email 				as u_email,
						u.gender 				as u_gender,
						u.oauth_uid				as u_oauth_uid
				
					FROM	promoters_teams pt
					
					JOIN 	users_promoters up
					ON		pt.promoter_id = up.id
					
					JOIN 	users u 
					ON		u.oauth_uid = up.users_oauth_uid
					
					WHERE	pt.team_fan_page_id = ?";
			$query = $this->db->query($sql, array($team->t_fan_page_id));
			$team->team_promoters = $query->result();
			
		}
		unset($team);
		
		return $teams;
		
	}
	
	/**
	 * Retrieve the total number of promoters in the vibecompass platform
	 * 
	 * @param 	array (options)
	 * @return	object
	 */
	function retrieve_num_promoters($options = array()){
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array('cache' => false,
								'cache_length' => 900 //15 minutes
								);
		foreach($options as $key => $value){
			//is this a recognized configuration setting?
			if(!array_key_exists($key, $default_options))
				die('retrieve_num_promoters: unknown configuration setting - ' . $key);

			//overwrite default configuration value with new one specified in function call
			$default_options[$key] = $value;
		}
		foreach($default_options as $key => $value){
			//turn all default_config keys into local variables
			${$key} = $value;
		}
		/* --------- END CONFIGURATION SETTINGS --------- */
		
		if($cache){
//			$this->load->library('library_memcached');
//			if($result = $this->library_memcached->get('model_app_data->retrieve_num_promoters')){
//				return $result;
//			}
			$this->load->library('Redis', '', 'redis');
			if($result = $this->redis->get('model_app_data->retrieve_num_promoters')){
				return $result;
			}
		}
		
		$sql = "SELECT 	
		
					count(*) as count
					
				FROM	users_promoters up 
				
				WHERE 	up.banned = 0";
		$query = $this->db->query($sql);
		$result = $query->row();
		
		if($cache){
			$this->redis->set('model_app_data->retrieve_num_promoters',
											$result);
			$this->redis->expire('model_app_data->retrieve_num_promoters', $cache_length);
		}
		
		return $result;
	}

	/**
	 * Retrieve the total number of vc_users in the vibecompass platform
	 * 
	 * @param 	array (options)
	 * @return	object
	 */
	function retrieve_num_vc_users($options = array()){
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array('cache' => false,
								'cache_length' => 900, //15 minutes
								'since_date' => false
								);
		foreach($options as $key => $value){
			//is this a recognized configuration setting?
			if(!array_key_exists($key, $default_options))
				die('retrieve_num_vc_users: unknown configuration setting - ' . $key);

			//overwrite default configuration value with new one specified in function call
			$default_options[$key] = $value;
		}
		foreach($default_options as $key => $value){
			//turn all default_config keys into local variables
			${$key} = $value;
		}
		/* --------- END CONFIGURATION SETTINGS --------- */
		
		if($cache){
	//		$this->load->library('library_memcached');
	//		if($result = $this->library_memcached->get('model_app_data->retrieve_num_vc_users' . ($since_date) ? $since_date : '')){
	//			return $result;
	//		}
	
			$this->load->library('Redis', '', 'redis');
			if($result = $this->redis->get('model_app_data->retrieve_num_vc_users' . ($since_date) ? $since_date : '')){
				return $result;
			}
	
		}
		
		$sql = "SELECT 	
		
					count(*) 	as count
		
				FROM	users u ";
		if($since_date){
			$sql .= "WHERE	u.join_time > ?";
			$query = $this->db->query($sql, array((time() - 86400 * $since_date)));
		}else{
			$query = $this->db->query($sql);
		}
				
		$result = $query->row();
		
		if($cache){
		//	$this->library_memcached->put('model_app_data->retrieve_num_vc_users' . ($since_date) ? $since_date : '',
		//									$result,
		//									$cache_length);
			$this->redis->set('model_app_data->retrieve_num_vc_users' . ($since_date) ? $since_date : '',
											$result);
			$this->redis->expire('model_app_data->retrieve_num_vc_users', $cache_length);
		}
		
		return $result;
	}

	/**
	 * Determines if a city exists in our database of cities
	 * 
	 * @param	string (city url id)
	 * @return 	bool
	 */
	function retrieve_valid_city($city_url_identifier){
		
		$query = $this->db->get_where('cities', array('url_identifier' => strtolower($city_url_identifier)));
		return $query->row();
		
	}
	
	/**
	 * Retrieves all cities which have active temas
	 * 
	 * @return 	array
	 */
	function retrieve_active_cities($promoters = false){
		
		
		$sql = "SELECT DISTINCT
	
			c.id					as c_id, 
			c.name					as c_name, 
			c.state 				as c_state, 
			c.timezone_identifier	as c_timezone_identifier, 
			c.url_identifier		as c_url_identifier
		
		FROM 	cities c
		
		JOIN 	team_venues tv 
		ON 		tv.city_id = c.id
		
		WHERE 	tv.banned = 0 ";
		
		if($promoters)
		//searches for teams that are active and have at least one active promoter in them
		$sql .= "
		AND
				tv.id IN 
		(SELECT DISTINCT
		
			tv.id
		
		FROM	team_venues tv
		
		JOIN 	promoters_guest_list_authorizations pgla
		ON 		pgla.team_venue_id = tv.id
		
		JOIN 	teams_venues_pairs tvp 
		ON 		tvp.team_venue_id = tv.id
		
		JOIN 	teams t 
		ON 		tvp.team_fan_page_id = t.fan_page_id
		
		JOIN 	promoters_teams pt 
		ON 		pt.team_fan_page_id = t.fan_page_id
		
		JOIN 	users_promoters up
		ON 		up.id = pgla.user_promoter_id
		
		WHERE 	pt.banned = 0
		AND 	pt.quit = 0
		AND 	pt.approved = 1
		AND 	up.banned = 0
		AND 	tvp.deleted = 0
		AND 	pgla.deactivated = 0)";
		
		$query = $this->db->query($sql);
		$result = $query->result();
				
	//	Kint::dump($this->db->last_query());
	//	Kint::dump($result);		
				
		return $result;
		
	}
	
	/**
	 * Retrieves all cities in VC database
	 * 
	 * @return 	array
	 */
	function retrieve_all_cities(){
		
		$query = $this->db->get('cities');
		return $query->result();
		
	}
	
	/**
	 * 
	 */
	function retrieve_all_users(){
		
		$this->db->select('third_party_id');
		$query = $this->db->get('users');
		return $query->result();
		
	}
	
	/**
	 * Retrieves all venues in platform
	 * 
	 * @param	string (city url identifier)
	 * @return 	array
	 */
	function retrieve_all_venues($city = false){
		
		$sql = "SELECT
	
					tv.id 				as tv_id,
					tv.name 			as tv_name,
					tv.description 		as tv_description,
					tv.street_address 	as tv_street_address,
					tv.city 			as tv_city,
					tv.zip 				as tv_zip,
					tv.image 			as tv_image,
					c.id 				as c_id,
					c.name 				as c_name,
					c.state 			as c_state,
					c.url_identifier	as c_url_identifier
										
				FROM 	team_venues tv 

				JOIN 	cities c 
				ON 		tv.city_id = c.id
				
				WHERE 	tv.banned = 0 ";
				
		if($city)
			$sql .= "AND c.url_identifier = ? ";		
		
		$query = $this->db->query($sql, array($city));
			
		return $query->result();
				
	}
	
	/**
	 * Retrieve venue by name and for a given city
	 * 
	 * @param	string (venue name)
	 * @param	string (city name)
	 * @return 	object || false
	 */
	function retrieve_venue_tv_id($tv_id){
				
		$sql = "SELECT
		
					tv.id 					as tv_id,
					tv.team_fan_page_id 	as tv_team_fan_page_id,
					tv.name 				as tv_name,
					tv.description	 		as tv_description,
					tv.street_address		as tv_street_address,
					tv.state 				as tv_state,
					tv.city 				as tv_city,
					tv.zip 					as tv_zip,
					tv.image 				as tv_image,
					c.id 					as c_id,
					c.name 					as c_name,
					c.state					as c_state,
					c.url_identifier		as c_url_identifier,
					c.timezone_identifier	as c_timezone_identifier
					
				FROM 	team_venues tv 
				
				JOIN 	teams t 
				ON 		tv.team_fan_page_id = t.fan_page_id
				
				JOIN 	cities c 
				ON 		t.city_id = c.id 
				
				WHERE  	tv.id = ?
				AND 	t.completed_setup = 1
				AND 	tv.banned = 0";
		$query = $this->db->query($sql, array($tv_id));			
		$result = $query->row();
		
		//attach promoters for this venue
		if($result){
			
			$sql = "SELECT DISTINCT
					
						up.id					as up_id,
						up.last_login_time		as up_last_login_time,
						up.public_identifier	as up_public_identifier,
						up.biography			as up_biography,
						up.profile_image		as up_profile_image,
						c.name					as c_name,
						c.url_identifier		as c_url_identifier,
						u.full_name				as u_full_name,
						u.first_name			as u_first_name,
						u.last_name				as u_last_name,
						t.name 					as t_name
					
					FROM 	promoters_guest_list_authorizations pgla 
					
					JOIN 	users_promoters up 
					ON 		pgla.user_promoter_id = up.id
					
					JOIN	users u 
					ON 		up.users_oauth_uid = u.oauth_uid
					
					JOIN 	promoters_teams pt
					ON 		pt.promoter_id = up.id
					
					JOIN	teams t 
					ON 		pt.team_fan_page_id = t.fan_page_id
					
					JOIN	cities c 
					ON		t.city_id = c.id
					
					JOIN 	team_venues tv 
					ON 		pgla.team_venue_id = tv.id
					
					WHERE	t.completed_setup = 1
					AND 	pt.approved = 1 
					AND 	pt.banned = 0 
					AND 	pt.quit = 0
					AND 	tv.banned = 0
					AND 	up.completed_setup = 1
					AND 	up.banned = 0
					AND 	pgla.team_venue_id = ?";
			$query = $this->db->query($sql, array($result->tv_id));

			$result->venue_promoters = $query->result();
		}
		
		return $result;
		
	}
	/**
	 * Retrieve venue by name and for a given city
	 * 
	 * @param	string (venue name)
	 * @param	string (city name)
	 * @return 	object || false
	 */
	function retrieve_venue($venue_name, $city){
				
		$sql = "SELECT
		
					tv.id 					as tv_id,
					tv.team_fan_page_id 	as tv_team_fan_page_id,
					tv.name 				as tv_name,
					tv.description	 		as tv_description,
					tv.street_address		as tv_street_address,
					tv.state 				as tv_state,
					tv.city 				as tv_city,
					tv.zip 					as tv_zip,
					tv.image 				as tv_image,
					c.id 					as c_id,
					c.name 					as c_name,
					c.state					as c_state,
					c.url_identifier		as c_url_identifier,
					c.timezone_identifier	as c_timezone_identifier
					
				FROM 	team_venues tv 
				
				JOIN 	cities c 
				ON 		tv.city_id = c.id 
				
				WHERE  	tv.name = ?
				AND 	c.url_identifier = ?
				AND 	tv.banned = 0";
		$query = $this->db->query($sql, array(str_replace('_', ' ', $venue_name), $city));			
		$result = $query->row();
		
		//attach promoters for this venue
		if($result){
			
			$sql = "SELECT DISTINCT
					
						up.id					as up_id,
						up.last_login_time		as up_last_login_time,
						up.public_identifier	as up_public_identifier,
						up.biography			as up_biography,
						up.profile_image		as up_profile_image,
						c.name					as c_name,
						c.url_identifier		as c_url_identifier,
						u.full_name				as u_full_name,
						u.first_name			as u_first_name,
						u.last_name				as u_last_name
					
					FROM 	promoters_guest_list_authorizations pgla 
					
					JOIN 	users_promoters up 
					ON 		pgla.user_promoter_id = up.id
					
					JOIN	users u 
					ON 		up.users_oauth_uid = u.oauth_uid
					
					JOIN 	promoters_teams pt
					ON 		pt.promoter_id = up.id
					
					JOIN	teams t 
					ON 		pt.team_fan_page_id = t.fan_page_id
					
					JOIN	cities c 
					ON		t.city_id = c.id
					
					JOIN 	team_venues tv 
					ON 		pgla.team_venue_id = tv.id
					
					WHERE	t.completed_setup = 1
					AND 	pt.approved = 1 
					AND 	pt.banned = 0 
					AND 	pt.quit = 0
					AND 	tv.banned = 0
					AND 	up.completed_setup = 1
					AND 	up.banned = 0
					AND 	pgla.team_venue_id = ?";
			$query = $this->db->query($sql, array($result->tv_id));
			
			$result->venue_promoters = $query->result();
		}
		
		return $result;
		
	}
	
	/**
	 * Retrieves all active promoters
	 * 
	 * @return	array
	 */
	function retrieve_all_promoters(){
		
		
		$sql = "SELECT
		
					up.id					as up_id,
					up.users_oauth_uid 		as up_users_oauth_uid,
					up.completed_setup 		as up_completed_setup,
					up.banned				as up_banned,
					up.banned_time			as up_banned_time,
					up.banned_by_user		as up_banned_by_user,
					up.time_created			as up_time_created,
					up.last_login_time		as up_last_login_time,
					up.last_login_ip		as up_last_login_ip,
					up.public_identifier	as up_public_identifier,
					up.piwik_id_site		as up_piwik_id_site,
					up.biography			as up_biography,
					up.profile_image 		as up_profile_image,
					up.thumbnail_image 		as up_thumbnail_image,
					u.id					as u_id,
					u.oauth_uid				as u_oauth_uid,
					u.access_token			as u_access_token,
					u.first_name			as u_first_name,
					u.last_name 			as u_last_name,
					u.full_name				as u_full_name,
					u.email					as u_email,
					u.gender 				as u_gender,
					u.promoter				as u_promoter,
					u.manager				as u_manager,
					u.host					as u_host,
					u.third_party_id		as u_third_party_id,
					t.name 					as t_name,
					t.fan_page_id			as t_fan_page_id,
					c.url_identifier 		as c_url_identifier
					
				FROM 	users_promoters up 
				
				JOIN 	users u 
				ON 		up.users_oauth_uid = u.oauth_uid
				
				JOIN 	promoters_teams pt 
				ON 		pt.promoter_id = up.id
				
				JOIN 	teams t 
				ON 		pt.team_fan_page_id = t.fan_page_id
				
				JOIN 	cities c 
				ON 		t.city_id = c.id
				
				WHERE 	pt.approved = 1
				AND 	pt.banned = 0
				AND 	pt.quit = 0
				AND 	up.banned = 0
				AND 	up.completed_setup = 1
				AND 	t.completed_setup = 1";
		$query = $this->db->query($sql);
		return $query->result();
		
	}
	
	/**
	 * Retrieves all managers
	 * 
	 * @return	array
	 */
	function retrieve_all_managers(){
				
		$sql = "SELECT
		
					mt.id					as mt_id,
					mt.user_oauth_uid		as mt_user_oauth_uid,
					mt.fan_page_id			as mt_fan_page_id,
					mt.banned				as mt_banned,
					mt.banned_time			as mt_banned_time,
					t.id 					as t_id,
					t.fan_page_id			as t_fan_page_id,
					t.city_id				as t_city_id,
					t.piwik_id_site 		as t_piwik_id_site,
					t.name 					as t_name,
					t.description 			as t_description,
					t.completed_setup 		as t_completed_setup,
					t.image 				as t_image,
					u.id					as u_id,
					u.oauth_uid				as u_oauth_uid,
					u.access_token			as u_access_token,
					u.first_name			as u_first_name,
					u.last_name 			as u_last_name,
					u.full_name				as u_full_name,
					u.email					as u_email,
					u.gender 				as u_gender,
					u.promoter				as u_promoter,
					u.manager				as u_manager,
					u.host					as u_host,
					u.third_party_id		as u_third_party_id
					
				FROM 	users u
				
				JOIN 	managers_teams mt 
				ON 		mt.user_oauth_uid = u.oauth_uid
				
				JOIN 	teams t 
				ON 		mt.fan_page_id = t.fan_page_id
				
				WHERE 	mt.banned = 0";
		$query = $this->db->query($sql);
		return $query->result();		
		
	}
	
	/**
	 * Retrieves all hosts
	 * 
	 * @return	array
	 */
	function retrieve_all_hosts(){
				
		$sql = "SELECT
		
					u.id					as u_id,
					u.oauth_uid				as u_oauth_uid,
					u.access_token			as u_access_token,
					u.first_name			as u_first_name,
					u.last_name 			as u_last_name,
					u.full_name				as u_full_name,
					u.email					as u_email,
					u.gender 				as u_gender,
					u.promoter				as u_promoter,
					u.manager				as u_manager,
					u.host					as u_host,
					u.third_party_id		as u_third_party_id
				
				FROM 	users u
				
				JOIN  	teams_hosts th 
				ON 		th.users_oauth_uid = u.oauth_uid
				
				WHERE 	th.banned = 0
				AND 	th.quit = 0";
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

/* End of file model_app_data.php */
/* Location: application/models/model_app_data.php */