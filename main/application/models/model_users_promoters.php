<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * database interaction related to promoter/clubowner signup
 * */
class Model_users_promoters extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
	
	/*-------------------------------------------------------------------------
	 |	Create Methods (create)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * creates an unauthorized promoter
	 * 
	 * @param	int (facebook oauth_uid)
	 * @param	int (team fan page id)
	 * @return	bool (success)
	 */
	function create_promoter($oauth_uid, $team_fan_page_id){
					
			//update record in 'users' table to identify this user as a promoter
			$this->db->where('oauth_uid', $oauth_uid);
			$this->db->update('users', array('promoter' => 1));
						
			//create record in users_promoters
			
			//first check to see if record in users_promoters exists
			$query = $this->db->get_where('users_promoters', array('users_oauth_uid' => $oauth_uid));
			if($result = $query->row()){
				//promoter exists, update instead of insert
				
				$data = array(
					'completed_setup' => 0,
				);
				$this->db->where('users_oauth_uid', $oauth_uid);
				$this->db->update('users_promoters', $data);
				
				$promoter_id = $result->id;
				
				//update record in promoters_teams
				$this->db->where(array('promoter_id' => $promoter_id, 'team_fan_page_id' => $team_fan_page_id));
				$this->db->update('promoters_teams', array(
					'approved' => 1,
					'approved_time' => time(),
					'banned' => 0,
					'quit' => 0
				));

			}else{
				
				//promoter doesn't exist, insert instead of update
				$data = array(
						'users_oauth_uid' => $oauth_uid,
						'banned' => 0,
						'completed_setup' => 0,
						'public_identifier' => null
						);
				$this->db->insert('users_promoters', $data);			
				$promoter_id = $this->db->insert_id();
				
				$data = array(
						'promoter_id' => $promoter_id,
						'team_fan_page_id' => $team_fan_page_id,
						'approved' => 1,
						'approved_time' => time(),
						'banned' => 0,
						'quit' => 0
						);
					
				$this->db->insert('promoters_teams', $data);
				
			}
			
			return true;
			
	}
	
	/**
	 * Create a promoter_guest_list_authorization for 
	 * a promoter (invoked for a request from promoter admin panel)
	 * 
	 * @param	int (promoter_id)
	 * @param	int (team_venue_id)
	 * @param	string (weekday)
	 * @param	string (guest list name)
	 * @param	bool (auto-approve)
	 * @return	array
	 * */
	function create_promoter_guest_list_authorization($promoter_id, $team_venue_id, $weekday, $gl_name, $gl_description, $auto_approve, $gl_cover, $regular_cover, $door_opens, $door_closes, $min_age, $additional_info_1, $additional_info_2, $additional_info_3, $auto_promote){
		
		$gl_name = strip_tags($gl_name);
		$gl_description = strip_tags($gl_description);
		
		/*-------------- make sure this promoter is authorized with this team_venue --------------*/
		$sql = "SELECT
				
					t.fan_page_id	as t_fan_page_id,
					pt.id			as pt_id
				
				FROM 	users_promoters up
				
				JOIN	promoters_teams pt
				ON		up.id = pt.promoter_id
				
				JOIN	teams t
				ON		t.fan_page_id = pt.team_fan_page_id
				
				WHERE	up.id = ?
						AND pt.quit = 0
						AND pt.banned = 0 
						AND pt.approved = 1
						AND t.completed_setup = 1";

	//			JOIN	team_venues tv
	//			ON 		tv.team_fan_page_id = t.fan_page_id
		$query = $this->db->query($sql, array($promoter_id, $team_venue_id));		
		$result = $query->row();
		
	//	if(!$result)
	//		return array('success' => false,
	//						'message' => 'Promoter not authorized to promote for this venue');
		/*-------------- END make sure this promoter is authorized with this team_venue --------------*/
		
		$team_fan_page_id = $result->t_fan_page_id;
		
		/*-------------- Make sure this promoter doesn't already have a guest list at this venue on this night -----------*/
		$sql = "SELECT
					
					pgla.id 	as pgla_id
			
				FROM 	promoters_guest_list_authorizations pgla
				
				WHERE 	pgla.user_promoter_id = ?
						AND pgla.team_venue_id = ?
						AND pgla.day = ?
						AND pgla.deactivated = 0";
												
		$query = $this->db->query($sql, array($promoter_id, $team_venue_id, $weekday));
		if($result = $query->row()){
			return array('success' => false,
							'message' => 'You already have a guest list at this venue on ' . $weekday);
		}
		
		/*-------------- END Make sure this promoter doesn't already have a guest list at this venue on this night -----------*/
		
		/* ------------- make sure promoter doesn't have another guest list with the same name as this one ------------ */
				
		$sql = "SELECT
					
					pgla.id
				
				FROM 	promoters_guest_list_authorizations pgla

				JOIN 	team_venues tv 
				ON 		tv.id = pgla.team_venue_id

				WHERE	pgla.user_promoter_id = ?
				AND 	tv.team_fan_page_id = ?
				AND 	pgla.name = ?";
		$query = $this->db->query($sql, array($promoter_id, $team_fan_page_id, $gl_name));
								
		if($result = $query->row()){
			return array('success' => false,
							'message' => "You already have a guest list with the name '$gl_name', all of your guest lists must have unique names.");
		}
		
		/* ------------- END make sure promoter doesn't have another guest list with the same name as this one ------------ */
		
		if($auto_approve == 'true')
			$auto_approve = 1;
		else 
			$auto_approve = 0;
		
		if($auto_promote == 'true')
			$auto_promote = 1;
		else 
			$auto_promote = 0;
		
		$image_name = null;
		$x0 = 0;
		$y0 = 0;
		$x1 = 0;
		$y1 = 0;
		
		//IMAGE Handling
		if($manage_image = $this->session->flashdata('manage_image')){
						
			$manage_image = json_decode($manage_image);
			$x0 = $manage_image->image_data->x0;
			$y0 = $manage_image->image_data->y0;
			$x1 = $manage_image->image_data->x1;
			$y1 = $manage_image->image_data->y1;
			
			if(isset($manage_image->image_data)){
				//This user has uploaded an image
				
				$this->load->library('library_image_upload', '', 'image_upload');
				$image_name = $this->image_upload->make_image_live($manage_image->type, $manage_image->image_data->image, false); //<-- new guest list, not live image
				
			}
			
		}
						
		$data = array(
						'team_venue_id' 	=> $team_venue_id,
						'user_promoter_id' 	=> $promoter_id,
						'day' 				=> $weekday,
						'name' 				=> $gl_name,
						'description' 		=> $gl_description,
						'create_time' 		=> time(),
						'image'				=> $image_name,
						'auto_approve' 		=> $auto_approve,
						'x0'				=> $x0,
						'y0'				=> $y0,
						'x1'				=> $x1,
						'y1' 				=> $y1,
						
						'auto_promote'		=> $auto_promote,
						'min_age'			=> $min_age,
						'door_open'			=> $door_opens,
						'door_close'		=> $door_closes,
						'regular_cover'		=> $regular_cover,
						'gl_cover'			=> $gl_cover,
						'additional_info_1' => $additional_info_1,
						'additional_info_2' => $additional_info_2,
						'additional_info_3' => $additional_info_3
					);
		
		$this->db->insert('promoters_guest_list_authorizations', $data);	
		return array('success' => true);
		
	}

	/**
	 * Creates a view record for this user and this promoter profile
	 * 
	 * @param	int (users oauth uid)
	 * @param	int (pt_id)
	 * @return	bool
	 */
	function create_profile_view($users_oauth_uid, $pt_id){
		
		//first make sure no recorded view exists in the past 30 seconds (rate limiting)
		$sql = "SELECT
					
					uv.id
					
				FROM 	user_views uv
				
				WHERE	uv.users_oauth_uid = ?
						AND
						uv.promoters_teams_id = ?
						AND
						uv.time > ?
				
				LIMIT 1";
		$query = $this->db->query($sql, array($users_oauth_uid, $pt_id, (time() - 30)));		
		$result = $query->row();
		
		if($result)
			return false;
		
		$this->db->insert('user_views', array('users_oauth_uid' => $users_oauth_uid, 'promoters_teams_id' => $pt_id, 'time' => time()));
		return $this->db->insert_id();
		
	}
	
	/**
	 * Create a record indicating that a user (signed in or not) visited this profile via a link on another user's facebook wall
	 * 
	 * @param	object || false (vc_user)
	 * @param 	string (user_third_party_id)
	 * @param 	int (pgla_id)
	 * @return	int (insert_id)
	 */
	function create_facebook_post_reference($vc_user, $third_party_id, $pgla_id){
		
		if($vc_user){
			$vc_user = $vc_user->oauth_uid;
		}else{
			$vc_user = null;
		}
		
		//look up oauth_uid from third_party_id
		$this->db->select('oauth_uid');
		$query = $this->db->get_where('users', array('third_party_id' => $third_party_id));
		$result = $query->row();
		
		if(!$result)
			return false; //user doesn't exist
		
		$this->db->insert('facebook_post_references', array('referer_users_oauth_uid' => $result->oauth_uid, 'promoters_guest_list_authorizations_id' => $pgla_id, 'visitors_users_oauth_uid' => $vc_user, 'time' => time()));
		Kint::dump($this->db->last_query());
		
		return $this->db->insert_id();
	}
	
	
	/*-------------------------------------------------------------------------
	 |	Retrieval Methods (retrieve)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * returns an array of promoters
	 * 	
	 * 
	 * @param	int (max number of promoters to return)
	 * @param	string (order of promoters)
	 * @return	array
	 * */
	function retrieve_multiple_promoters($city = false){
		
		/*
		$sql = "SELECT
				
					u.oauth_uid 			as u_oauth_uid,
					u.first_name 			as u_first_name,
					u.last_name 			as u_last_name,
					u.full_name 			as u_full_name,
					u.gender 				as u_gender,
					up.public_identifier	as up_public_identifier,
					up.piwik_id_site 		as up_piwik_id_site,
					up.biography 			as up_biography,
					up.profile_image		as up_profile_image,
					t.name					as t_name,
					t.fan_page_id 			as t_fan_page_id,
					t.description 			as t_description,
					c.id 					as c_id,
					c.name 					as c_name,
					c.state 				as c_state,
					c.url_identifier		as c_url_identifier
				
				FROM 	users u 
				
				JOIN 	users_promoters up 
				ON 		up.users_oauth_uid = u.oauth_uid
				
				JOIN 	promoters_teams pt
				ON 		pt.promoter_id = up.id
				
				JOIN 	teams t 
				ON 		pt.team_fan_page_id = t.fan_page_id
				
				JOIN 	cities c 
				ON 		t.city_id = c.id
				
				WHERE 	pt.approved = 1
						AND up.completed_setup = 1
						AND up.banned = 0
						AND pt.approved = 1
						AND pt.banned = 0
						AND pt.quit = 0
						AND t.completed_setup = 1 ";
				
				if($city){
					$sql .= "AND c.url_identifier = ? ";
				}
				
		$sql .=	"ORDER BY 	c.id, u.full_name ASC";
		*/
		
		
		$sql = "SELECT DISTINCT
					
					u.oauth_uid 			as u_oauth_uid,
					u.first_name 			as u_first_name,
					u.last_name 			as u_last_name,
					u.full_name 			as u_full_name,
					u.gender 				as u_gender,
					up.id 					as up_id,
					up.public_identifier	as up_public_identifier,
					up.piwik_id_site 		as up_piwik_id_site,
					up.biography 			as up_biography,
					up.profile_image		as up_profile_image,
					t.name					as t_name,
					t.fan_page_id 			as t_fan_page_id,
					t.description 			as t_description,
					c.id 					as c_id,
					c.name 					as c_name,
					c.state 				as c_state,
					c.url_identifier		as c_url_identifier

				FROM	team_venues tv 
				
				JOIN 	cities c 
				ON 		tv.city_id = c.id
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgla.team_venue_id = tv.id 
				
				JOIN 	users_promoters up
				ON 		pgla.user_promoter_id = up.id
				
				JOIN 	users u 
				ON 		up.users_oauth_uid = u.oauth_uid
				
				JOIN 	promoters_teams pt
				ON 		pt.promoter_id = up.id
				
				JOIN 	teams t
				ON		pt.team_fan_page_id = t.fan_page_id
								
				WHERE 	pt.approved = 1
						AND up.completed_setup = 1
						AND up.banned = 0
						AND pt.approved = 1
						AND pt.banned = 0
						AND pt.quit = 0
						AND t.completed_setup = 1 ";
						
				if($city){
					$sql .= "AND c.url_identifier = ? ";
				}
				
		
			
		$query = $this->db->query($sql, array($city));
		$result = $query->result();
		
		
		//attach team venues
		foreach($result as &$res){
					
			//find and attach team venues to this promoter
			$sql = "SELECT DISTINCT
						
						tv.id 					as tv_id,
						tv.team_fan_page_id 	as tv_team_fan_page_id,
						tv.name 				as tv_name,
						tv.description 			as tv_description,
						tv.street_address		as tv_street_address,
						tv.city 				as tv_city,
						tv.state 				as tv_state,
						tv.image 				as tv_image,
						c.id 					as c_id,
						c.name 					as c_name,
						c.url_identifier 		as c_url_identifier,
						t.fan_page_id			as t_fan_page_id,
						t.name 					as t_name,
						t.description			as t_description,
						t.completed_setup 		as t_completed_setup
					
					FROM 	users_promoters up 
					
					JOIN 	promoters_teams pt 
					ON 		pt.promoter_id = up.id 
					
					JOIN 	teams t 
					ON 		pt.team_fan_page_id = t.fan_page_id
					
					JOIN 	team_venues tv
					ON 		tv.team_fan_page_id = t.fan_page_id
					
					JOIN 	cities c 
					ON 		tv.city_id = c.id
					
					JOIN 	promoters_guest_list_authorizations pgla
					ON 		pgla.team_venue_id = tv.id
					
					WHERE	pgla.deactivated = 0
							AND		pt.approved = 1
							AND 	pt.banned = 0
							AND 	pt.quit = 0
							AND 	tv.banned = 0
							AND 	pgla.user_promoter_id = ?
					
					ORDER BY	tv.id DESC";
			$query = $this->db->query($sql, array($res->up_id));
			$res->venues = $query->result();	
				
		}
		
		return $result;
		
	}

	/**
	 * Returns teams that this promoter is associated with
	 * 
	 * @param	array (options for looking up promoters)
	 * @param	array (options for query)
	 * @return	array
	 * */
	function retrieve_promoter($options = array(), $query_options = array()){
										
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array(
								'completed_setup' 	=> 'AND t.completed_setup = 1',
								'quit' 				=> 'AND pt.quit = 0',
								'banned' 			=> 'AND pt.banned = 0',
								'up_banned' 		=> 'AND up.banned = 0',
								'id_only' 			=> false
							);
		foreach($query_options as $key => $value){
			//is this a recognized configuration setting?
			if(!array_key_exists($key, $default_options))
				die('retrievepromoter: unknown configuration setting - ' . $key);

			//overwrite default configuration value with new one specified in function call
			$default_options[$key] = $value;
		}
		foreach($default_options as $key => $value){
			//turn all default_config keys into local variables
			${$key} = $value;
		}
		/* --------- END CONFIGURATION SETTINGS --------- */
		
		$promoter_id = false;
		$promoter_public_identifier = false;
		$users_oauth_uid = false;
		
		if(isset($options['promoter_id'])){
			
			$promoter_id = $options['promoter_id'];
			
		}elseif(isset($options['promoter_public_identifier'])){
			
			$promoter_public_identifier = $options['promoter_public_identifier'];
			
		}elseif(isset($options['users_oauth_uid'])){
			
			$users_oauth_uid = $options['users_oauth_uid'];
			
		}else{
			
			die('retrieve_promoters: no known identifier specified');
			
		}
		
		$sql = "SELECT ";
		
		if($id_only){
			
			$sql .= "up.id  	as up_id, ";
			
		}else{
			
			$sql .= "up.id					as up_id,
					up.users_oauth_uid		as up_users_oauth_uid,
					up.completed_setup		as up_completed_setup,
					up.time_created			as up_time_created,
					up.last_login_time		as up_last_login_time,
					up.last_login_ip		as up_last_login_ip,
					up.public_identifier	as up_public_identifier,
					up.piwik_id_site		as up_piwik_id_site,
					up.biography			as up_biography,
					up.banned 				as up_banned,
					up.profile_image		as up_profile_image,
					up.original_width		as up_original_width,
					up.original_height		as up_original_height,
					up.x0					as up_x0,
					up.x1					as up_x1,
					up.y0					as up_y0,
					up.y1					as up_y1,
					u.first_name			as u_first_name,
					u.last_name				as u_last_name,
					u.full_name				as u_full_name,
					u.twilio_sms_number		as u_twilio_sms_number,
					u.gender				as u_gender,
					u.third_party_id		as u_third_party_id,
					pt.id					as pt_id,
					pt.banned 				as pt_banned,
					pt.quit					as pt_quit, ";
			
		}	
		
		$sql .= "t.fan_page_id 		as t_fan_page_id 
		
				FROM	users_promoters up
				
				JOIN	users u
				ON		up.users_oauth_uid = u.oauth_uid
				
				JOIN 	promoters_teams pt
				ON 		pt.promoter_id = up.id
				
				JOIN 	managers_teams mt
				ON 		pt.team_fan_page_id = mt.fan_page_id
				
				JOIN	teams t 
				ON		mt.fan_page_id = t.fan_page_id
								
				WHERE 	
						pt.approved = 1 " . $banned . " " . $completed_setup . " " . $quit . " " . $up_banned . " ";
				
		if($promoter_id){
			$sql .= "AND up.id = $promoter_id ";
		}elseif($promoter_public_identifier){
			$sql .= "AND up.public_identifier = '$promoter_public_identifier' ";
		}elseif($users_oauth_uid){
			$sql .= "AND up.users_oauth_uid = $users_oauth_uid ";
		}
		
		//attach city if specified
	//	if($city && $city !== ''){
	//		$sql .= "AND c.url_identifier = ? ";
	//	}
		
		$sql .= "ORDER BY pt.approved_time DESC"; //<---- retrieve most recent association
		
		$query = $this->db->query($sql, array());		
	//	Kint::dump($this->db->last_query()); die();
		
		$result = $query->row();
					
		if(!$result || $id_only)
			return $result;
		
		if($result->pt_banned == '1' || $result->pt_quit == '1' || $result->up_banned == '1')
			return $result;
			
		//attach current team to this promoter
		$sql = "SELECT
		
					t.fan_page_id			as t_fan_page_id,
					t.name 					as t_name,
					t.description			as t_description,
					t.completed_setup 		as t_completed_setup,
					c.name 					as c_name,
					c.state 				as c_state,
					c.id 					as c_id,
					c.timezone_identifier 	as c_timezone_identifier,
					c.url_identifier		as c_url_identifier
					
				FROM 	teams t 
				
				JOIN 	promoters_teams pt 
				ON 		pt.team_fan_page_id = t.fan_page_id 
				
				JOIN 	cities c 
				ON 		t.city_id = c.id 
				
				WHERE 	pt.promoter_id = $result->up_id
						AND pt.approved = 1
						AND pt.banned = 0
						AND pt.quit = 0
						AND t.completed_setup = 1";						
		$query = $this->db->query($sql);
		$result->team = $query->row();
		
		//find and attach team venues to this promoter
		$sql = "SELECT DISTINCT
					
					tv.id 					as tv_id,
					tv.team_fan_page_id 	as tv_team_fan_page_id,
					tv.name 				as tv_name,
					tv.description 			as tv_description,
					tv.street_address		as tv_street_address,
					tv.city 				as tv_city,
					tv.state 				as tv_state,
					tv.image 				as tv_image,
					c.id 					as c_id,
					c.name 					as c_name,
					c.url_identifier 		as c_url_identifier,
					t.fan_page_id			as t_fan_page_id,
					t.name 					as t_name,
					t.description			as t_description,
					t.completed_setup 		as t_completed_setup
				
				FROM 	users_promoters up 
				
				JOIN 	promoters_teams pt 
				ON 		pt.promoter_id = up.id 
				
				JOIN 	teams t 
				ON 		pt.team_fan_page_id = t.fan_page_id
				
				JOIN 	team_venues tv
				ON 		tv.team_fan_page_id = t.fan_page_id
				
				JOIN 	cities c 
				ON 		c.id = tv.city_id
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgla.team_venue_id = tv.id
				
				WHERE	pgla.deactivated = 0
						AND		pt.approved = 1
						AND 	pt.banned = 0
						AND 	pt.quit = 0
						AND 	tv.banned = 0
						AND 	pgla.user_promoter_id = ?
				
				ORDER BY	tv.id DESC";
		$query = $this->db->query($sql, array($result->up_id));
		$result->promoter_team_venues = $query->result();
		
		return $result;
		
	}
	
	/**
	 * Retrieves a newsfeed unique to a single promoter and that promoter's activity with the users friends
	 * for display on a promoters profile.
	 * 
	 * @param	promoter_oauth_uid
	 * @param	array (users friends oauth_uids)
	 * @return 	array
	 */
	function retrieve_promoter_client_newsfeed($promoter_oauth_uid, $users_oauth_uids){
		
		//NOTE: $users_oauth_uids MUST be non-empty
		if(!$users_oauth_uids)
			return array();
		
		$sql = "SELECT
					
					un.id 					as un_id,
					un.vibecompass_id 		as un_vibecompass_id,
					un.promoter_oauth_uid 	as un_promoter_oauth_uid,
					un.notification_type 	as un_notification_type,
					un.notification_data	as un_notification_data,
					un.create_time 			as un_create_time,
					un.join_date 			as un_join_date,
					un.occurance_date 		as un_occurance_date
					
				FROM 	user_notifications un
				WHERE 	un.promoter_oauth_uid = $promoter_oauth_uid
						AND ( ";
						
		foreach($users_oauth_uids as $key => $user){
			if($key == (count($users_oauth_uids) - 1))
				$sql .= "un.vibecompass_id = $user ";
			else
				$sql .= "un.vibecompass_id = $user OR ";
		}
		
		$sql .= ")
		ORDER BY	un.create_time DESC
		LIMIT 10";
		
		$query = $this->db->query($sql);
		
		if($users_oauth_uids){
			$result = $query->result();
		}else{
			$result = array();
		}
		
		//remove potentially sensitive data to prevent it from being sent to client
		foreach($result as &$res){
			
			$un_notification_data = json_decode($res->un_notification_data);
			
			if(isset($un_notification_data->pglr_response_msg)){
			
				unset($un_notification_data->pglr_response_msg);
			
			}

			if(isset($un_notification_data->pglr_request_msg)){
			
				unset($un_notification_data->pglr_request_msg);
			
			}
			
			if(isset($un_notification_data->pglr_text_message)){
			
				unset($un_notification_data->pglr_text_message);
			
			}
			
			if(isset($un_notification_data->pglr_share_facebook)){
			
				unset($un_notification_data->pglr_share_facebook);
			
			}
	
			if(isset($un_notification_data->pglr_approved)){
			
				unset($un_notification_data->pglr_approved);
			
			}
			
			$res->un_notification_data = $un_notification_data;
			
		}
		
		return $result;
		
	}
	
	/**
	 * Pulls the number of guest list reservations for the trailing 6 weeks for a given promoter
	 * and a user's facebook friends to determine the popularity trend (displayed on promoter home page)
	 * 
	 * @param	promoter_id (user promoter id)
	 * @param	array (facebook friend oauth_uids)
	 * @return 	array
	 */
	function retrieve_promoter_client_trend_activity($promoter_id, $users_oauth_uids){
			
		$num_trailing_weeks = 8;
				
		//NOTE: $users_oauth_uids MUST be non-empty	
		if(!$users_oauth_uids)
			return array();
		
		//we care about the trailing 4 weeks, what is the date for the beginning of that period?
		$date_backtrack_limit = date('Y-m-d', strtotime('Sunday -' . ($num_trailing_weeks + 1) . ' weeks'));
			
		$sql = "SELECT

					pgl.date 	as date,
					count(*) 	as reservations
				
				FROM 	promoters_guest_lists_reservations pglr
				
				JOIN 	promoters_guest_lists pgl
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				WHERE 	pgla.user_promoter_id = $promoter_id
						AND pgl.date > $date_backtrack_limit
						AND ( ";
						
		foreach($users_oauth_uids as $key => $user){
			if($key == (count($users_oauth_uids) - 1))
				$sql .= "pglr.user_oauth_uid = $user ";
			else
				$sql .= "pglr.user_oauth_uid = $user OR ";
		}
		
		$sql .=")
				
				GROUP BY pgl.date DESC";
				
		$query = $this->db->query($sql);
		
		if($users_oauth_uids){
			$result = $query->result();
		}else{
			$result = array();
		}
		
		//now we have all the guest list join events grouped by date of occurance, group these by week
		$weeks_popularity = array();
		for($i = $num_trailing_weeks; $i > 0; $i--){
			
			$week_start = date('Y-m-d', strtotime('Sunday -' . ($i + 1) . ' weeks'));
			$week_end = date('Y-m-d', strtotime('Saturday -' . ($i) . ' weeks'));
			
			$date_range_key = $week_start . ' - ' . $week_end;
			$weeks_popularity[$date_range_key] = 0;
			
			//count how many guest list reservations occur in this week range
			$start_datetime = new DateTime($week_start . '00:00:00');
			$end_datetime = new DateTime($week_end . '23:59:59');
			foreach($result as $res){
				
				$occurance_datetime = new DateTime($res->date);	
				if($occurance_datetime >= $start_datetime && $occurance_datetime <= $end_datetime){
					$weeks_popularity[$date_range_key] = $weeks_popularity[$date_range_key] + 1;
				}
				
			}
			
		}
		
		return $weeks_popularity;
	}
	
	/**
	 * Retrieves all team_venues for all teams that this promoter is associated with
	 * 
	 * @param	
	 */
	function retrieve_promoter_team_venues($promoter_id){

		$sql = "SELECT
					
					tv.id 				as tv_id,
					tv.team_fan_page_id	as tv_team_fan_page_id,
					tv.name 			as tv_name,
					tv.image 			as tv_image,
					tv.description		as tv_description,
					tv.street_address	as tv_street_address,
					tv.monday 			as tv_monday,
					tv.tuesday			as tv_tuesday,
					tv.wednesday 		as tv_wednesday,
					tv.thursday 		as tv_thursday,
					tv.friday 			as tv_friday,
					tv.saturday 		as tv_saturday,
					tv.sunday 			as tv_sunday
					
				FROM 	users_promoters up
				
				JOIN	promoters_teams pt
				ON		up.id = pt.promoter_id
				
				JOIN	teams t
				ON		t.fan_page_id = pt.team_fan_page_id
				
				JOIN 	teams_venues_pairs tvp
				ON 		tvp.team_fan_page_id = t.fan_page_id
				
				JOIN	team_venues tv
				ON		tv.id = tvp.team_venue_id
				
				WHERE 	up.id = ?
						AND t.completed_setup = 1
						AND tv.banned = 0
						AND pt.approved = 1
						AND pt.banned = 0
						AND pt.quit = 0
						AND tvp.deleted = 0";
						
		$query = $this->db->query($sql, array($promoter_id));
		return $query->result();
		
	}
	
	/**
	 * Returns all the days of the week that a promoter has specified a guest list to be 
	 * created for at each venue
	 * 
	 * @param	int (promoter_id)
	 * @return	array (result objects)
	 */
	function retrieve_promoter_guest_list_authorizations($promoter_id){
		$sql = "SELECT
					
					pgla.id 			as pgla_id,
					pgla.day 			as pgla_day,
					pgla.name 			as pgla_name,
					pgla.auto_approve	as pgla_auto_approve,
					pgla.create_time	as pgla_create_time,
					pgla.image 			as pgla_image,
					t.name 				as t_name,
					t.fan_page_id		as t_fan_page_id,
					tv.name 			as tv_name,
					tv.id 				as tv_id
					
				FROM	users_promoters up
				
				JOIN	promoters_teams pt
				ON		pt.promoter_id = up.id
				
				JOIN 	teams t 
				ON		t.fan_page_id = pt.team_fan_page_id
				
				JOIN 	teams_venues_pairs tvp 
				ON 		tvp.team_fan_page_id = t.fan_page_id
				
				JOIN 	team_venues tv 
				ON		tv.id = tvp.team_venue_id
				
				JOIN	promoters_guest_list_authorizations pgla
				ON		tv.id = pgla.team_venue_id
				
				WHERE	up.id = $promoter_id
						AND pgla.user_promoter_id = $promoter_id
						AND pgla.deactivated = 0
						AND pgla.event = 0
						AND pt.banned = 0
						AND pt.approved = 1
						AND pt.quit = 0
						AND tv.banned = 0
						AND tvp.deleted = 0
						AND up.completed_setup = 1";	
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	/**
	 * Retrieves a promoter's guest list authorizations that are 'Events' and not
	 * regular recurrning guest lists
	 * 
	 * @param	int (promoter_id)
	 * @return 	array()
	 */
	function retrieve_promoter_guest_list_authorizations_events($promoter_id, $options = array()){
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array('cache' => false,
								'cache_length' => 900, //15 minutes
								'count' => false, 
								'date' => false,
								'current' => false,
								'expired' => false
								);
		foreach($options as $key => $value){
			//is this a recognized configuration setting?
			if(!array_key_exists($key, $default_options))
				die('retrieve_contest_entries: unknown configuration setting - ' . $key);

			//overwrite default configuration value with new one specified in function call
			$default_options[$key] = $value;
		}
		foreach($default_options as $key => $value){
			//turn all default_config keys into local variables
			${$key} = $value;
		}
		/* --------- END CONFIGURATION SETTINGS --------- */
		
		$sql = "SELECT
					
					pgla.id 			as pgla_id,
					pgla.day 			as pgla_day,
					pgla.name 			as pgla_name,
					pgla.auto_approve	as pgla_auto_approve,
					pgla.create_time	as pgla_create_time,
					pgla.event 			as pgla_event,
					pgla.event_date 	as pgla_event_date,
					pgla.event_override as pgla_event_override,
					pgla.image 			as pgla_image,
					t.name 				as t_name,
					t.fan_page_id		as t_fan_page_id,
					tv.name 			as tv_name
					
				FROM	users_promoters up
				
				JOIN	promoters_teams pt
				ON		pt.promoter_id = up.id
				
				JOIN 	teams t 
				ON		t.fan_page_id = pt.team_fan_page_id
				
				JOIN 	team_venues tv 
				ON		tv.team_fan_page_id = t.fan_page_id
				
				JOIN	promoters_guest_list_authorizations pgla
				ON		tv.id = pgla.team_venue_id
				
				WHERE	up.id = $promoter_id
						AND pgla.user_promoter_id = $promoter_id
						AND pgla.deactivated = 0
						AND pgla.event = 1 ";
				
				if($date){
					$sql .= "AND pgla.event_date = $date ";
				}
				
				if($current){
					$sql .= "AND pgla.event_date >= '" . date('Y-m-d', time()) . "' ";
				}

				if($expired){
					$sql .= "AND pgla.event_date < '" . date('Y-m-d', time()) . "' ";
				}
						
				$sql .=	"AND pt.banned = 0
						AND pt.approved = 1
						AND pt.quit = 0
						AND up.completed_setup = 1";
		$query = $this->db->query($sql);
		return $query->result();
		
	}
	
	/**
	 * retrieves a guest list authorization and verifies that it belongs to the specified promoter
	 * 
	 * @param	promoter id
	 * @param	guest list id
	 * @return	array (matches)
	 */
	function retrieve_promoter_guest_list($promoter_id, $guest_list_name){
			
		$guest_list_name = str_replace('_', ' ', $guest_list_name);	
		
		$sql = "SELECT DISTINCT
					
					pgla.id 			as pgla_id,
					pgla.team_venue_id	as pgla_team_venue_id,
					pgla.day 			as pgla_day,
					pgla.name 			as pgla_name,
					pgla.create_time 	as pgla_create_time,
					pgla.event 			as pgla_event,
					pgla.event_date 	as pgla_event_date,
					pgla.image 			as pgla_image,
					pgla.description 	as pgla_description,
					
					pgla.min_age			as pgla_min_age,
					pgla.door_open			as pgla_door_open,
					pgla.door_close			as pgla_door_close,
					pgla.regular_cover		as pgla_regular_cover,
					pgla.gl_cover			as pgla_gl_cover,
					pgla.additional_info_1	as pgla_additional_info_1,
					pgla.additional_info_2	as pgla_additional_info_2,
					pgla.additional_info_3	as pgla_additional_info_3,
					
					
					tv.id				as tv_id,
					tv.team_fan_page_id	as tv_team_fan_page_id,
					tv.name				as tv_name,
					tv.image 			as tv_image,
					tv.description 		as tv_description,
					tv.street_address 	as tv_street_address,
					tv.city 			as tv_city,
					tv.state 			as tv_state,
					tv.zip 				as tv_zip,
					tv.city_id 			as tv_city_id,
					
					c.url_identifier 	as c_url_identifier
				
				FROM 	users_promoters up
				
				JOIN	promoters_guest_list_authorizations pgla
				ON		up.id = pgla.user_promoter_id
				
				JOIN 	team_venues tv
				ON 		pgla.team_venue_id = tv.id
				
				JOIN 	teams_venues_pairs tvp
				ON 		tvp.team_venue_id = tv.id
				
				JOIN 	teams t 
				ON 		t.fan_page_id = tvp.team_fan_page_id
				
				JOIN 	cities c 
				ON	 	tv.city_id = c.id
				
				WHERE	up.id = ?
						AND	pgla.name = ?
						AND tvp.deleted = 0
						AND pgla.deactivated = 0";
		$query = $this->db->query($sql, array($promoter_id, $guest_list_name));
			
		return $query->row();
		
	}
	
	
	/**
	 * Retrieves a list of all the FBID's that are a promoter's 'clients'
	 * 
	 * @param	promoter id
	 * @param	promoter team fan page id
	 * @return	array
	 */
	function retrieve_promoter_clients_list($promoter_id, $promoter_team_fan_page_id = false, $options = array()){
		
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array('cache' => false,
								'cache_length' => 900,
								'count' => false, //15 minutes
								'cache_front' => false,
								'cache_front_length' => 2600 //60 minutes
								);
		foreach($options as $key => $value){
			//is this a recognized configuration setting?
			if(!array_key_exists($key, $default_options))
				die('retrieve_contest_entries: unknown configuration setting - ' . $key);

			//overwrite default configuration value with new one specified in function call
			$default_options[$key] = $value;
		}
		foreach($default_options as $key => $value){
			//turn all default_config keys into local variables
			${$key} = $value;
		}
		/* --------- END CONFIGURATION SETTINGS --------- */
				
		//cache bypass if this value is stored in memcache
		$this->load->library('Redis', '', 'redis');
		//if($cache && ($results = $this->memcached->get('retrieve_promoter_clients_list')))
		//	return json_decode($results);
		if($cache_front){
			if($results = $this->redis->get('RPCL-cache-front-' . $promoter_id)){
				return json_decode($results);
			}			
		}
		
		$sql = "SELECT ";

		if($count)
			$sql .= "count(DISTINCT pglr.user_oauth_uid) 	as count_clients ";
		else 
			$sql .= "DISTINCT pglr.user_oauth_uid	as pglr_user_oauth_uid ";
				
		$sql .=	"FROM 	promoters_guest_lists_reservations pglr

				JOIN 	promoters_guest_lists pgl
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN 	team_venues tv
				ON 		pgla.team_venue_id = tv.id
				
				WHERE
					pgla.user_promoter_id = ? ";
				
			//	if($promoter_team_fan_page_id !== false)
			//		$sql .= " AND tv.team_fan_page_id = ?";
				
				
				
				
	//	if($promoter_team_fan_page_id !== false)
	//		$query = $this->db->query($sql, array($promoter_id, $promoter_team_fan_page_id));
	//	else
			$query = $this->db->query($sql, array($promoter_id));
		
		
		
		
		$result = $query->result();
				
	//	if($cache)
	//		$this->memcached->add('retrieve_promoter_clients_list', json_encode($result), $cache_length);
		
		if($cache_front){
			$this->redis->set('RPCL-cache-front-' . $promoter_id, json_encode($result));
			$this->redis->expire('RPCL-cache-front-' . $promoter_id, 60*15);
		}
		
		return $result;
	}

	/**
	 * Retrieves the clients of a promoter for a front request
	 * 
	 * @param	int (promoter_id)
	 * @return 	array
	 */
	function retrieve_front_promoter_client_list($promoter_id){
						
		//cache bypass if this value is stored in memcache
		$this->load->library('Redis', '', 'redis');
		if($result = $this->redis->get('RFPCL-cache-front-' . $promoter_id)){
			return json_decode($result);
		}			
				
		$sql = "(SELECT	DISTINCT
					
					pglr.user_oauth_uid		as oauth_uid
				
				FROM	promoters_guest_list_authorizations pgla
				
				JOIN 	promoters_guest_lists pgl
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id 
				
				JOIN 	promoters_guest_lists_reservations pglr 
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				WHERE	pgla.user_promoter_id = ?)
		
					UNION
				
				(SELECT DISTINCT
					
					pglre.oauth_uid 	as oauth_uid
				
				FROM	promoters_guest_list_authorizations pgla
				
				JOIN 	promoters_guest_lists pgl
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id 
				
				JOIN 	promoters_guest_lists_reservations pglr 
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN 	promoters_guest_lists_reservations_entourages pglre 
				ON 		pglre.promoters_guest_lists_reservations_id = pglr.id
				
				WHERE	pgla.user_promoter_id = ?)";	
		$query = $this->db->query($sql, array($promoter_id, $promoter_id));
		$result = $query->result();
		
		$this->redis->set('RFPCL-cache-front-' . $promoter_id, json_encode($result));
		$this->redis->expire('RFPCL-cache-front-' . $promoter_id, 60);
		return $result;
	}
	
	/**
	 * Returns the total number of upcoming guest list reservations 
	 * 
	 * @param	int (promoter id)
	 * @param	array (options)
	 * @return 	int
	 */
	function retrieve_num_guest_list_reservation_requests($promoter_id, $options = array()){
		
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array('cache' => false,
								'cache_length' => 900,
								'upcoming' => false //15 minutes
								);
		foreach($options as $key => $value){
			//is this a recognized configuration setting?
			if(!array_key_exists($key, $default_options))
				die('retrieve_contest_entries: unknown configuration setting - ' . $key);

			//overwrite default configuration value with new one specified in function call
			$default_options[$key] = $value;
		}
		foreach($default_options as $key => $value){
			//turn all default_config keys into local variables
			${$key} = $value;
		}
		/* --------- END CONFIGURATION SETTINGS --------- */
		
		$sql = "SELECT
					
					count(*)	as count
				
				FROM 	promoters_guest_lists_reservations pglr
				
				JOIN 	promoters_guest_lists pgl
				ON		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN 	team_venues tv 
				ON 		pgla.team_venue_id = tv.id 
				
				JOIN 	teams t 
				ON 		tv.team_fan_page_id = t.fan_page_id
				
				JOIN 	promoters_teams pt 
				ON 		pt.team_fan_page_id = t.fan_page_id
				
				JOIN 	users_promoters up 
				ON 		pt.promoter_id = up.id
				
				WHERE 	pgla.user_promoter_id = $promoter_id
						AND pt.promoter_id = $promoter_id
						AND up.banned = 0
						AND pt.banned = 0
						AND pt.approved = 1
						AND pt.quit = 0
						AND t.completed_setup = 1 ";
				
		if($upcoming)
			$sql .= "AND pgl.date >= '" . date('Y-m-d', time()) . "' ";
		
		$query = $this->db->query($sql);
		return $query->row();
		
	}
	
	/**
	 * returns true or false if the given promoter has completed the initial setup yet.
	 * 
	 * @param	promoter id
	 * @return	bool
	 * */
	function retrieve_completed_setup($promoter_id){
		$this->db->select('completed_setup');
		$query = $this->db->get_where('users_promoters', array('id' => $promoter_id));
		$result = $query->row();
		if($result->completed_setup == '1')
			return true;
		else
			return false;
	}
	
	/**
	 * Retrieves the number of guest list reservation requests for the trailing 12 weeks
	 * 
	 * @param 	int (promoter_id)
	 * @return 	array
	 */
	function retrieve_trailing_weekly_guest_list_reservation_requests($promoter_id){

		$num_trailing_weeks = 12;
		$date_backtrack_limit = date('Y-m-d', strtotime('Sunday -' . ($num_trailing_weeks + 1) . ' weeks'));
				
		$sql = "SELECT
					
				pgl.date			as date, 
				count(pgl.date) 	as count
					
				FROM 	promoters_guest_lists_reservations pglr
				
				JOIN 	promoters_guest_lists pgl
				ON 			pglr.promoter_guest_lists_id = pgl.id
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				WHERE 	pgla.user_promoter_id = ?
						AND pgl.date >= ?

				GROUP BY 	pgl.date";
		$query = $this->db->query($sql, array($promoter_id, $date_backtrack_limit));
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
	 * @param 	int (promoter_id)
	 * @return 	array
	 */
	function retrieve_trailing_weekly_guest_list_reservation_requests_percentage_attendance($promoter_id){

		$num_trailing_weeks = 12;
		$date_backtrack_limit = date('Y-m-d', strtotime('Sunday -' . ($num_trailing_weeks + 1) . ' weeks'));
				
		$sql = "SELECT
					
				pgl.date			as date, 
				pglr.checked_in 	as pglr_checked_in
					
				FROM 	promoters_guest_lists_reservations pglr
				
				JOIN 	promoters_guest_lists pgl
				ON 			pglr.promoter_guest_lists_id = pgl.id
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				WHERE 	pgla.user_promoter_id = ?
						AND pgl.date >= ?";
		$query = $this->db->query($sql, array($promoter_id, $date_backtrack_limit));
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
						
					if($res->pglr_checked_in == '0' || $res->pglr_checked_in == 0)
						$weeks_popularity[$date_range_key]->did_not_attend += 1;
					else 
						$weeks_popularity[$date_range_key]->attended += 1;
					
				}
				
			}
			
		}
		return $weeks_popularity;
	}

	/**
	 * Retrieve the XX (25 as of writing method) most recent VibeCompass users to view this promoters profile
	 * 
	 * @param	int (pt_id)
	 * @return	array
	 */
	function retrieve_recent_profile_views($pt_id){
		
		$sql = "SELECT DISTINCT

					uv.users_oauth_uid 	as uv_users_oauth_uid
				
				FROM 	user_views uv
				
				WHERE 	uv.promoters_teams_id = ?
				
				ORDER BY	uv.id DESC
				
				LIMIT 100";
		$query = $this->db->query($sql, array($pt_id));
				
		return $query->result();
		
	}
	
	/**
	 * Retrieve the top profile visitors
	 * 
	 * @param	int (pt_id)
	 * @return	array
	 */
	function retrieve_top_profile_visitors($pt_id){
		
		$sql = "SELECT
		
					users_oauth_uid,
					count(*) as count
					
				FROM
				
					(SELECT
							
							uv.users_oauth_uid as users_oauth_uid
					
					FROM 	user_views uv
					
					WHERE 	uv.promoters_teams_id = ?) t 
				
				GROUP BY users_oauth_uid
				ORDER BY count DESC
				LIMIT 50";
		$query = $this->db->query($sql, array($pt_id));
		return $query->result();
		
	}
	
	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * 
	 */
	function update_pgla($pgla_id, $up_id, $options){
				
		$this->db->where(array('id' => $pgla_id, 'user_promoter_id' => $up_id));
		$this->db->update('promoters_guest_list_authorizations', $options);	
		
	}
	
	/**
	 * Updates a guest list for a promoter and sets it to deactivated
	 * 
	 * @param	int (promoter_guest_list_authorization_id)
	 * @param	int (promoter_id)
	 * @return	array
	 */
	function update_promoter_guest_list_set_deactivated($pgla_id, $promoter_id){
		
		//make sure this pgla_id belongs to this promoter
		$sql = "SELECT
		
					pgla.user_promoter_id 	as pgla_user_promoter_id
			
				FROM 	promoters_guest_list_authorizations pgla
				
				WHERE 	pgla.id = $pgla_id";
		$query = $this->db->query($sql);
		$result = $query->row();
		
		if(!$result){
			return array('success' => false,
							'message' => 'guest list does not exist');
		}

		if($result->pgla_user_promoter_id != $promoter_id){
			return array('success' => false,
							'message' => 'not authorized');
		}
		
		$this->db->where('id', $pgla_id);
		$this->db->update('promoters_guest_list_authorizations', array('deactivated' => 1, 
																		'deactivated_time' => time()));
		
		return array('success' => true);			
	}
	
	/**
	 * Updates a guest list with a new auto_approve value
	 * 
	 * @param	int (promoter_guest_list_authorization_id)
	 * @param	bool (auto_approve)
	 * @param	int (promoter_id)
	 * @return 	array
	 */
	function update_promoter_guest_list_set_auto_approve($pgla_id, $auto_approve, $promoter_id){
		
		//make sure this pgla_id belongs to this promoter
		$sql = "SELECT
		
					pgla.user_promoter_id 	as pgla_user_promoter_id
			
				FROM 	promoters_guest_list_authorizations pgla
				
				WHERE 	pgla.id = $pgla_id";
		$query = $this->db->query($sql);
		$result = $query->row();
		
		if(!$result){
			return array('success' => false,
							'message' => 'guest list does not exist');
		}

		if($result->pgla_user_promoter_id != $promoter_id){
			return array('success' => false,
							'message' => 'not authorized');
		}
		
		$this->db->where('id', $pgla_id);
		$this->db->update('promoters_guest_list_authorizations', array('auto_approve' => (($auto_approve == 'true') ? 1 : 0)));
		
		return array('success' => true);			
	}
	
	/**
	 * update fields in users_promoters for given promoter
	 * 
	 * @param	array (possible promoter identifiers)
	 * @param	array (fields and values to update)
	 * @return	int (affected rows)
	 * */
	function update_promoter($options = array(), $data){
		
		$promoter_id = false;
		$promoter_public_identifier = false;
		$users_oauth_uid = false;
		
		if(isset($options['promoter_id'])){
			
			$promoter_id = $options['promoter_id'];
			if(!$promoter_id){
				die('unknown promoter id ->' . $promoter_id);
			}
			
		}elseif(isset($options['promoter_public_identifier'])){
			
			$promoter_public_identifier = $options['promoter_public_identifier'];
			
		}elseif(isset($options['users_oauth_uid'])){
			
			$users_oauth_uid = $options['users_oauth_uid'];
			
		}else{
			
			die('retrieve_promoters: no known identifier specified');
			
		}
		
		if($promoter_id){
			$this->db->where('id', $promoter_id);
		}elseif($promoter_public_identifier){
			$this->db->where('public_identifier', $promoter_public_identifier);
		}elseif($users_oauth_uid){
			$this->db->where('users_oauth_uid', $users_oauth_uid);
		}
		
		$this->db->update('users_promoters', $data);		
		return $this->db->affected_rows();
	}
	
	/*
	 * update fields in users_promoters_profile for given promoter_id
	 * 
	 * @param	int (promoter id)
	 * @param	array (fields and values to update)
	 * @return	int (affected rows)
	 * */
	function update_user_promoter_profile($promoter_id, $data){
		$this->db->where('promoter_id', $promoter_id);
		$this->db->update('users_promoters_profile', $data);
		return $this->db->affected_rows();
	}
	
	/*
	 * clears all promoter language association records and creates new ones from input
	 * 
	 * @param	promoter_id
	 * @param	array [language ids]
	 * */
	function update_user_promoter_languages($promoter_id, $languages){
		//first clear all language associations
		$this->db->delete('promoters_languages', array('promoter_id' => $promoter_id));
		
		//test
		$languages = array(2, 4, 5);
		
		//now repopulate with languages specified by promoter
		$data = array();
		foreach($languages as $lang){
			array_push($data, array('promoter_id' => $promoter_id,
									'language_id' => $lang));
		}
		$this->db->insert_batch('promoters_languages', $data); 
	}

	/**
	 * updates database with form input from first page of promoter setup flow
	 * 
	 * @param	promoter id
	 * @param	chosen public identifier
	 * @param	array (language id's)
	 * @param	venues (venue_ id's)
	 * @param	string (promoter's biography)
	 * @return	null
	 * */
	function update_promoter_setup_form($promoter_id, $public_identifier, $languages, $venues, $biography){
		
		//first insert public identifier into users_promoters
		$this->db->where('id', $promoter_id);
		$this->db->update('users_promoters', array('public_identifier' => $public_identifier));
		
		//second insert biography
		$this->db->where('promoter_id', $promoter_id);
		$this->db->update('users_promoters_profile', array('biography' => $biography));
		
		//now insert venues
		$data = array();
		foreach($venues as $venue){
			$data[] = array('promoter_id' => $promoter_id,
							'venue_id' => $venue,
							'approved' => 0);
		}
		if($data)
			$this->db->insert_batch('promoters_venues', $data);
			
		//now insert languages
		$data = array();
		foreach($languages as $language){
			$data[] = array('promoter_id' => $promoter_id,
							'language_id' => $language);
		}
		if($data)
			$this->db->insert_batch('promoters_languages', $data);
	}

	/**
	 * Update a promoter's authorization status to authorized and add a new
	 * Piwik site to reflect promoter.
	 * 
	 * @param	promoter_id
	 * @return	bool (True if successful)
	 */
	function update_promoter_add_piwik($promoter_id){
		
		//call piwik api (rather unusual thing to do in a model function)
		//and register a new site_id for this promoter
		
		//get basic info about this promoter for piwik api
		$promoter = $this->retrieve_promoter(array('promoter_id' => $promoter_id));
		
		//Piwik api parameters for adding a new site
		$data = array(
			'method' => 'SitesManager.addSite',
			'siteName' => 'Promoter: ' . $promoter->u_first_name . ' ' . $promoter->u_last_name . ' - ' . $promoter->up_public_identifier,
			'urls[0]' => base_url() . 'promoters/' . $promoter->up_public_identifier . '/',
			'urls[1]' => base_url() . 'promoters/' . $promoter->up_public_identifier . '/all_guest_lists/',
			'urls[2]' => base_url() . 'promoters/' . $promoter->up_public_identifier . '/events/',
			'urls[3]' => base_url() . 'promoters/' . $promoter->up_public_identifier . '/contact/',
			'urls[4]' => base_url() . 'promoters/' . $promoter->up_public_identifier . '/reviews/',
		);
		
		
		//construct the http api query
		$query = '';
		foreach($data as $key=>$value){
		  $query .= '&' . $key . '=' . urlencode($value);
		}
		
		//make piwik api call
		$this->load->library('Piwik', '', 'piwik');
		$query_result = $this->piwik->api_call($query);
				
		//update promoter record in database with site_id that was returned by Piwik when
		//site was registered
		$this->db->where('id', $promoter_id);
		$this->db->update('users_promoters', array('piwik_id_site' => $query_result['value']));
		
		return true;
	}
	
	/**
	 * Delete a promoter's piwik id site
	 * 
	 * @param	promoter_id
	 * @return	bool (True if successful)
	 */
	function update_promoter_delete_piwik($promoter_id){
		
		//call piwik api (rather unusual thing to do in a model function)
		//and register a new site_id for this promoter
		
		//get basic info about this promoter for piwik api
		$promoter = $this->retrieve_promoter(array('promoter_id' => $promoter_id), 
												array('completed_setup' => '', 'banned' => '', 'quit' => '', 'up_banned' => ''));
		
		//Piwik api parameters for adding a new site
		$data = array(
			'method' => 'SitesManager.deleteSite',
			'idSite' => $promoter->up_piwik_id_site
		);
		
		//construct the http api query
		$query = '';
		foreach($data as $key=>$value){
		  $query .= '&' . $key . '=' . urlencode($value);
		}
		
		//make piwik api call
		$this->load->library('Piwik');
		$query_result = $this->piwik->api_call($query);
				
		//update promoter record in database with site_id that was returned by Piwik when
		//site was registered
		$this->db->where('id', $promoter_id);
		$this->db->update('users_promoters', array('piwik_id_site' => '-1'));
		
		return true;
	}
	
	/**
	 * Quit a promoter's current team
	 * 
	 * @param	int (promoter_id)
	 * @return 	bool (success)
	 */
	function update_promoter_quit_team($promoter_id){
		
		$this->db->where(array('promoter_id' => $promoter_id, 'quit' => 0));
		$this->db->update('promoters_teams', array('quit' => 1, 'quit_time' => time()));
		
	}
	
	/**
	 * Manager bans a promoter from their team
	 * 
	 * @param	int (promoter_id)
	 * @param	int (team fan page id)
	 * @return 	bool (success)
	 */
	function update_promoter_ban_team($promoter_id, $team_fan_page_id){
		
		$this->db->where(array('promoter_id' => $promoter_id, 'team_fan_page_id' => $team_fan_page_id));
		$this->db->update('promoters_teams', array('banned' => 1, 'banned_time' => time()));
		
	}
	
	/**
	 * Attaches a promoter to a new team, checks to see if promoter previously worked for another
	 * team and reestablishes that relationship.
	 * 
	 * @param 	int (promoter_id)
	 * @param   int (team fan page id)
	 * @return 	success
	 */
	function update_promoter_join_team($promoter_id, $team_fan_page_id){
		
		$sql = "SELECT
					
					pt.id 	as pt_id
				
				FROM 	promoters_teams pt
				
				WHERE 	pt.promoter_id = $promoter_id
						AND pt.team_fan_page_id = $team_fan_page_id";
		$query = $this->db->query($sql);
		if($promoters_teams = $query->row()){
			//promoter previously worked for this team
			
			$this->db->where('id', $promoters_teams->pt_id);
			$this->db->update('promoters_teams', array('quit' => 0, 'banned' => 0));
			
		}else{
			//promoter has never worked for this team
			
			$this->db->insert('promoters_teams', array('promoter_id' 		=> $promoter_id, 
														'team_fan_page_id' 	=> $team_fan_page_id,
														'approved' 			=> 1,
														'approved_time'	 	=> time(),
														'banned' 			=> 0,
														'quit' 				=> 0));
			
		}
		
	}
	
	/**
	 * Update a specific promoter guest list with new data
	 * 
	 * @param	int (promoter_id)
	 * @param	int (guest list id)
	 * @param	array(data)
	 */
	function update_guest_list($promoter_id, $pgla_id, $data){
		
		$new_data = array(
			'x0' 		=> $data['x0'],
			'y0' 		=> $data['y0'],
			'x1' 		=> $data['x1'],
			'y1' 		=> $data['y1'],
			'image' 	=> $data['image']
		);
		
		$this->db->where(array('id' => $pgla_id));
		$this->db->update('promoters_guest_list_authorizations', $new_data);
		
	}

	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
	
}

/* End of file model_users_promoters.php */
/* Location: application/models/model_users_promoters.php */