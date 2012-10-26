<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * operations related to standard application users
 * */
class Model_users extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
	
	/*-------------------------------------------------------------------------
	 |	Create Methods (create)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Creates a new VibeCompass user when Facebook user authenticates with
	 * application
	 * 
	 * @param	array (fql response)
	 * @param	string (access token)
	 * @param	int (acess token expiration seconds)
	 * @return	bool (success)
	 */
	function create_user($api_response, $access_token, $access_token_expires){
		
		//add record in 'users' table
		$data = array('oauth_uid' 						=> $api_response['uid'],
						'access_token' 					=> $access_token,
						'access_token_valid_seconds' 	=> $access_token_expires,
						'access_token_acquired_time'	=> time(),
						'first_name' 					=> $api_response['first_name'],
						'last_name' 					=> $api_response['last_name'],
						'full_name' 					=> $api_response['name'],
						'email' 						=> $api_response['email'],
						'gender' 						=> $api_response['sex'],
						'facebook_username' 			=> (isset($api_response['username'])) ? $api_response['username'] : null, //apparently not all facebook users have a username
						'timezone' 						=> $api_response['timezone'],
						'third_party_id'   				=> $api_response['third_party_id'],
						'join_time' 					=> time());
		
		$this->db->insert('users', $data);
		if(php_sapi_name() == 'cli')
			var_dump($data);
		
		return true;
	}

	/**
	 * Creates notification records in database for specified users
	 * 
	 * @param	int (vibecompass_id -- this can be either a user oauth_uid, or a promoter id/event id)
	 * @param	enum (notification type)
	 * @param	text (notification data)
	 * @return	bool
	 */
	function create_user_notifications($vibecompass_id, $notification_type, $notification_data){
		
		//'join_vibecompass','join_team_guest_list','join_promoter_guest_list','join_event','promoter_new_event'
		$data = array(
					'vibecompass_id' => $vibecompass_id,
					'notification_type' => $notification_type,
					'create_time' => time()
					);
		
		//add promoter id to notification if user is joining a promoter guest list (aids in lookup later)
		if(isset($notification_data->up_users_oauth_uid))
			$data['promoter_oauth_uid'] = $notification_data->up_users_oauth_uid;
		
		if(isset($notification_data->tv_id))
			$data['team_venue_id'] = $notification_data->tv_id;
		
		if(isset($notification_data->pgl_date))
			$data['occurance_date'] = $notification_data->pgl_date;
		
		if(isset($notification_data->tgl_date))
			$data['occurance_date'] = $notification_data->tgl_date;
		
		if($notification_data){
			$notification_data = json_encode($notification_data);
			$data['notification_data'] = $notification_data;
		}
		
		$data['join_date'] = date('Y-m-d', time());
				
		$this->db->insert('user_notifications', $data);
				
	}
	
	/**
	 * Creates a stick page notification
	 * 
	 * @param	
	 */
	 function create_user_sticky_notification($head_user_oauth_uid,
	 											$notify_user,
												$data){
	 	
		$data = array(
			'head_user_oauth_uid' 	=> $head_user_oauth_uid,
			'notify_user'			=> $notify_user,
			'data'					=> $data,
			'read_status'			=> 0,
			'create_time'			=> time()
		);
		$this->db->insert('sticky_notifications', $data);
		$insert_id = $this->db->insert_id();
		
		//set to read all but the last three unread sticky notifications
		$unread_sticky_notifications = $this->retrieve_user_sticky_notifications($notify_user, 99999);
		
		if(count($unread_sticky_notifications) > 3){
						
			for($i = 0; $i < (count($unread_sticky_notifications) - 3); $i++){
				
				$this->update_user_sticky_notification($unread_sticky_notifications[$i]->notify_user, 
														$unread_sticky_notifications[$i]->id, 
														array('read_status' => 1));
				
			}
			
		}

		return $insert_id;
		
	 }
	
	/*-------------------------------------------------------------------------
	 |	Retrieval Methods (retrieve)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Retrieves a vibecompass user by oauth_uid
	 * 
	 * @param	string (facebook user id)
	 * @return	array || bool(false)
	 * */
	function retrieve_user($oauth_uid){
		
		$sql = "SELECT	
						u.id					as users_id,
						u.oauth_uid				as users_oauth_uid,
						u.access_token			as users_access_token,
						u.access_token_acquired_time 	as users_access_token_acquired_time,
						u.access_token_valid_seconds	as users_access_token_valid_seconds,
						u.first_name			as users_first_name,
						u.last_name				as users_last_name,
						u.full_name				as users_full_name,
						u.email					as users_email,
						u.gender				as users_gender,
						u.facebook_username		as users_facebook_username,
						u.timezone				as users_timezone,
						u.promoter				as users_promoter,
						u.manager				as users_manager,
						u.super_admin			as users_super_admin,
						u.host 					as users_host,
						u.phone_number			as users_phone_number,
						u.phone_carrier			as users_phone_carrier,
						u.third_party_id		as users_third_party_id,
						u.opt_out_search		as users_opt_out_search,
						u.opt_out_email			as users_opt_out_email
						
				FROM	users u
				
				WHERE	u.oauth_uid = $oauth_uid";
		
		$query = $this->db->query($sql);
		$result = $query->row();
		
		return $result;
	}
	
	/**
	 * Accepts a list of app users from a facebook query, determines which users we know about
	 * and returns subset
	 * 
	 * @param	array (oauth_uids)
	 * @return	array (oauth_uids)
	 */
	function retrieve_app_users($oauth_uids, $options = array()){
						
		if(!$oauth_uids)
			return array();			
		
		$opt_out = false;
		if(isset($options['opt_out']))
			$opt_out = true; //don't retrieve users that have opted out
				
		$sql = "SELECT
					
					u.oauth_uid 		as u_oauth_uid,
					u.first_name		as u_first_name,
					u.last_name 		as u_last_name,
					u.full_name			as u_full_name,
					u.third_party_id 	as u_third_party_id,
					u.email 			as u_email,
					u.gender			as u_gender,
					u.opt_out_email		as u_opt_out_email
				
				FROM 	users u
				
				WHERE 	" . (($opt_out) ? "u.opt_out_email = 0 AND " : "") . "(";
				
		foreach($oauth_uids as $key => $uid){
			
			if($key == (count($oauth_uids) - 1)){
				
				$sql .= "oauth_uid = ?)";	
				
			}else{
						
				$sql .= "oauth_uid = ? OR ";	
				
			}
			
		}
		
		$query = $this->db->query($sql, $oauth_uids);
		return $query->result();
		
	}
	
	/**
	 * Retrieve a user if they exist and all their associated data
	 * 
	 * @param	string (third_party_id)
	 * @return 	object || false
	 */
	function retrieve_friend_data($oauth_uid){
				
		//attach various data to user object
		
		$result = new stdClass;
		
		//retrieve 10 most recent user notifications/activities
		$sql = "SELECT
		
					un.id 					as un_id,
					un.promoter_oauth_uid 	as un_promoter_oauth_uid,
					un.notification_type 	as un_notification_type,
					un.notification_data 	as un_notification_data,
					un.create_time 			as un_create_time,
					un.join_date			as un_join_date,
					un.occurance_date 		as un_occurance_date
				
				FROM 	user_notifications un
				
				WHERE 	un.vibecompass_id = $oauth_uid
				
				ORDER BY 	un.id DESC
				
				LIMIT 		10";
		$query = $this->db->query($sql);
		
		if(!$query){
			echo 'model_users retrieve_friend_data';
			echo '---------------------------------------------------' . PHP_EOL;
			var_dump($this->db->last_query());
		}
		
		$result->activity_feed = $query->result();
		
		//retrieve user's clubbing mates
		$sql = "(SELECT

					DISTINCT pglre.oauth_uid		as oauth_uid
				
				FROM 	promoters_guest_lists_reservations_entourages pglre
				
				WHERE 	pglre.promoters_guest_lists_reservations_id IN
				(SELECT
				
					pglr.id
				
				FROM 	promoters_guest_lists_reservations_entourages pglre
				
				JOIN 	promoters_guest_lists_reservations pglr
				ON 		pglre.promoters_guest_lists_reservations_id = pglr.id
				
				WHERE 	pglre.oauth_uid = $oauth_uid) AND pglre.oauth_uid != $oauth_uid
				
				LIMIT 10)
				
				UNION
				
				(SELECT
				
					pglre.oauth_uid as 	oauth_uid
				
				FROM 	promoters_guest_lists_reservations pglr
				
				JOIN 	promoters_guest_lists_reservations_entourages pglre
				ON 		pglre.promoters_guest_lists_reservations_id = pglr.id 
				
				WHERE 	pglr.user_oauth_uid = $oauth_uid
				
				LIMIT 10)
				
				UNION
				
				(SELECT
				
					DISTINCT tglre.oauth_uid		as oauth_uid
				
				FROM 	teams_guest_lists_reservations_entourages tglre
				
				WHERE 	tglre.team_guest_list_reservation_id IN
				(SELECT
				
					tglr.id
				
				FROM 	teams_guest_lists_reservations_entourages tglre
				
				JOIN 	teams_guest_lists_reservations tglr
				ON 		tglre.team_guest_list_reservation_id = tglr.id
				
				WHERE 	tglre.oauth_uid = $oauth_uid) AND tglre.oauth_uid != $oauth_uid
				
				LIMIT 10)
				
				UNION
				
				(SELECT
				
					tglre.oauth_uid as 	oauth_uid
				
				FROM 	teams_guest_lists_reservations tglr
				
				JOIN 	teams_guest_lists_reservations_entourages tglre
				ON 		tglre.team_guest_list_reservation_id = tglr.id 
				
				WHERE 	tglr.user_oauth_uid = $oauth_uid
				
				LIMIT 10)";
		$query = $this->db->query($sql);
		$result->vc_mates = $query->result();
		
		//find this user's promoters
		$sql = "SELECT DISTINCT
		
					up.profile_image 		as up_profile_image,
					up.public_identifier	as up_public_identifier,
					u.full_name				as u_full_name,
					c.url_identifier 		as c_url_identifier
										
				FROM 	promoters_guest_lists_reservations pglr
				
				JOIN 	promoters_guest_lists pgl
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN 	users_promoters up
				ON 		pgla.user_promoter_id = up.id 
				
				JOIN 	promoters_teams pt 
				ON 		pt.promoter_id = up.id
				
				JOIN 	teams t 
				ON 		pt.team_fan_page_id = t.fan_page_id
				
				JOIN 	cities c 
				ON 		t.city_id = c.id
				
				JOIN 	users u 
				ON 		up.users_oauth_uid = u.oauth_uid
				
				WHERE 	pglr.user_oauth_uid = ?
				AND 	pt.quit = 0
				AND 	pt.banned = 0
				AND 	t.completed_setup = 1
				AND 	up.banned = 0";
		$query = $this->db->query($sql, array($oauth_uid));
		$result->vc_promoters = $query->result();
		
		
		
		
		//find this user's mutual promoters
	
	
	
	
		//find this user's venues
		$sql = "(SELECT DISTINCT
					
								c.url_identifier 	as c_url_identifier,
								tv.image 			as tv_image,
								tv.name				as tv_name
													
							FROM 	promoters_guest_lists_reservations pglr
							
							JOIN 	promoters_guest_lists pgl
							ON 		pglr.promoter_guest_lists_id = pgl.id
							
							JOIN 	promoters_guest_list_authorizations pgla
							ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
							
							JOIN 	team_venues tv
							ON 		pgla.team_venue_id = tv.id 
			
							JOIN 	teams t
							ON 		tv.team_fan_page_id = t.fan_page_id
			
							JOIN 	cities c
							ON 		t.city_id = c.id
			
							WHERE 	t.completed_setup = 1
							AND 	tv.banned = 0
							AND 	pglr.user_oauth_uid = ?)
			UNION
			(SELECT DISTINCT
					
								c.url_identifier 	as c_url_identifier,
								tv.image 			as tv_image,
								tv.name				as tv_name
													
							FROM 	teams_guest_lists_reservations tglr
							
							JOIN 	teams_guest_lists tgl
							ON 		tglr.team_guest_list_id = tgl.id
							
							JOIN 	teams_guest_list_authorizations tgla
							ON 		tgl.team_guest_list_authorization_id = tgla.id
							
							JOIN 	team_venues tv
							ON 		tgla.team_venue_id = tv.id 
			
							JOIN 	teams t
							ON 		tv.team_fan_page_id = t.fan_page_id
			
							JOIN 	cities c
							ON 		t.city_id = c.id
			
							WHERE 	t.completed_setup = 1
							AND 	tv.banned = 0
							AND 	tglr.user_oauth_uid = ?)";
		$query = $this->db->query($sql, array($oauth_uid, $oauth_uid));
		$result->vc_venues = $query->result();
		
		return $result;
		
	}
	
	/**
	 * Retrieve a user by third party
	 * 
	 * @param	string (third party facebook identifier)
	 * @param 	bool (removes sensitive fields such as access_token)
	 * @return	object || false
	 */
	function retrieve_user_by_third_party_id($third_party_id, $remove_sensitive = false){
		
		$sql = "SELECT ";
					
			if(!$remove_sensitive){
				$sql .= "u.access_token			as users_access_token,
						u.email					as users_email,
						u.timezone				as users_timezone,
						u.super_admin			as users_super_admin,
						u.phone_number			as users_phone_number,
						u.phone_carrier			as users_phone_carrier,
						u.manager				as users_manager, ";
			}
	
			$sql .= "u.id					as users_id,
					u.join_time				as users_join_time,
					u.oauth_uid				as users_oauth_uid,
					u.first_name			as users_first_name,
					u.last_name				as users_last_name,
					u.full_name				as users_full_name,
					u.gender				as users_gender,
					u.facebook_username		as users_facebook_username,
					u.promoter				as users_promoter,
					u.third_party_id		as users_third_party_id
					
				FROM users u
				
				WHERE u.third_party_id = '$third_party_id'";
		$query = $this->db->query($sql);
		return $query->row();
		
	}
	
	/**
	 * Retreives all requested guest list and event reservations with promoters and teams
	 * 
	 * @param	int (user's oauth uid)
	 * @return	array
	 */
	function retrieve_user_reservation_requests($oauth_uid){
		
		/**
		 * I need to query for team guest lists and promoter guest lists separately
		 *  
		 */
		
		/* -------------- Retrieve Team Guest List Reservations ----------------- */
		$sql = "SELECT
					
					tglr.id 					as tglr_id,
					tglr.team_guest_list_id 	as tglr_team_guest_list_id,
					tglr.user_oauth_uid 		as tglr_user_oauth_uid,
					tglr.approved 				as tglr_approved,
					tglr.create_time			as tglr_create_time,
					tglr.table_request			as tglr_table_request,
					tglr.text_message 			as tglr_text_message,
					tglr.share_facebook			as tglr_share_facebook,
					tglr.request_msg 			as tglr_request_msg,
					tglr.response_msg 			as tglr_response_msg,
					tgl.id 						as tgl_id,
					tgl.date  					as tgl_date,
					tgl.canceled 				as tgl_canceled,
					tgla.day 					as tgla_day,
					tgla.name 					as tgla_name,
					tgla.deactivated 			as tgla_deactivated,
					tgla.deactivated_time 		as tgla_deactivated_time,
					
					tv.name 						as tv_name,
					
					c.name 							as c_name,
					c.state 						as c_state,
					c.url_identifier 				as c_url_identifier
					
				FROM	teams_guest_lists_reservations tglr
				
				JOIN	users u
				ON		tglr.user_oauth_uid = u.oauth_uid
				
				JOIN 	teams_guest_lists tgl
				ON		tglr.team_guest_list_id = tgl.id
				
				JOIN  	teams_guest_list_authorizations tgla
				ON 		tgl.team_guest_list_authorization_id = tgla.id
				
				JOIN 	team_venues tv
				ON		tgla.team_venue_id = tv.id
				
				JOIN 	teams t 
				ON 		tv.team_fan_page_id = t.fan_page_id
				
				JOIN 	cities c 
				ON 		t.city_id = c.id
				
				
				WHERE	tglr.user_oauth_uid = ?
				ORDER BY 	tgl.date DESC";
		$query = $this->db->query($sql, array($oauth_uid));
		$team_result = $query->result();
		
		//attach entourage data to each reservation
		foreach($team_result as &$tr){
			$sql = "SELECT
			
						tglre.oauth_uid		as tglre_oauth_uid
						
					FROM 	teams_guest_lists_reservations_entourages tglre
					
					WHERE	tglre.team_guest_list_reservation_id = $tr->tglr_id";
			$query = $this->db->query($sql);
			$tr->entourage = $query->result();
		}
		/* -------------- Retrieve Team Guest List Reservations ----------------- */
		

		/* -------------- Retrieve Promoter Guest List Reservations ----------------- */
		$sql = "SELECT
					
					pglr.id 						as pglr_id,
					pglr.promoter_guest_lists_id	as pglr_promoter_guest_list_id,
					pglr.user_oauth_uid 			as pglr_user_oauth_uid,
					pglr.approved 					as pglr_approved,
					pglr.create_time				as pglr_create_time,
					pglr.table_request				as pglr_table_request,
					pglr.text_message 				as pglr_text_message,
					pglr.share_facebook				as pglr_share_facebook,
					pglr.request_msg				as pglr_request_msg,
					pglr.response_msg 				as pglr_response_msg,
					pgl.id 							as pgl_id,
					pgl.date  						as pgl_date,
					pgl.canceled 					as pgl_canceled,
					pgla.day 						as pgla_day,
					pgla.name 						as pgla_name,
					pgla.deactivated 				as pgla_deactivated,
					pgla.deactivated_time 			as pgla_deactivated_time,
					up.public_identifier			as up_public_identifier,
					up.profile_image 				as up_profile_image,
					u.first_name 					as u_first_name,
					u.last_name 					as u_last_name,
					u.full_name 					as u_full_name,
					u.gender 						as u_gender,
					u.oauth_uid						as u_oauth_uid,
					
					tv.name 						as tv_name,
					
					c.name 							as c_name,
					c.state 						as c_state,
					c.url_identifier 				as c_url_identifier
								
					
				FROM	promoters_guest_lists_reservations pglr
				
				JOIN 	promoters_guest_lists pgl
				ON		pglr.promoter_guest_lists_id = pgl.id 
				
				JOIN  	promoters_guest_list_authorizations pgla
				ON		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN 	users_promoters up
				ON		pgla.user_promoter_id = up.id

				JOIN 	users u
				ON 		up.users_oauth_uid = u.oauth_uid
				
				JOIN 	team_venues tv 
				ON		pgla.team_venue_id = tv.id 
				
				JOIN	teams t 
				ON 		tv.team_fan_page_id = t.fan_page_id			
				
				JOIN 	cities c 
				ON 		t.city_id = c.id	
				
				WHERE	pglr.user_oauth_uid = $oauth_uid
				ORDER 	BY pgl.date DESC";
				
		$query = $this->db->query($sql);
		$promoter_result = $query->result();
		
		//attach entourage data to each reservation
		foreach($promoter_result as &$pr){
			$sql = "SELECT
			
						pglre.oauth_uid		as pglre_oauth_uid
						
					FROM 	promoters_guest_lists_reservations_entourages pglre
					
					WHERE	pglre.promoters_guest_lists_reservations_id = $pr->pglr_id";
			$query = $this->db->query($sql);
			$pr->entourage = $query->result();
		}
		/* -------------- Retrieve Promoter Guest List Reservations ----------------- */
		
		$result = new stdClass;
		$result->team_result = $team_result;
		$result->promoter_result = $promoter_result;
		
		return $result;
	}
	
	/**
	 * Retrieves all of a user's promoters that he/she has booked guest lists with
	 * 
	 * @param	int (user's oauth_uid)
	 * @return 	array
	 */
	function retrieve_users_favorite_promoters($user_oauth_uid){
		$sql = "SELECT DISTINCT
				
						up.id 					as up_id,
						up.profile_image 		as up_profile_image,
						up.biography			as up_biography,
						up.public_identifier 	as up_public_identifier,
						u.full_name 			as u_full_name,
						u.first_name 			as u_first_name,
						u.last_name 			as u_last_name,
						u.gender 				as u_gender,
						c.name 					as c_name,
						c.state					as c_state,
						c.url_identifier		as c_url_identifier
				
				FROM 	promoters_guest_lists_reservations pglr
				
				JOIN 	promoters_guest_lists pgl
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN 	users_promoters up
				ON 		pgla.user_promoter_id = up.id
				
				JOIN 	users u
				ON 		up.users_oauth_uid = u.oauth_uid
				
				JOIN 	promoters_teams pt 
				ON 		pt.promoter_id = up.id 
				
				JOIN 	teams t 
				ON 		pt.team_fan_page_id = t.fan_page_id 
				
				JOIN 	cities c 
				ON 		t.city_id = c.id
				
				WHERE 	pglr.user_oauth_uid = $user_oauth_uid";
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	/**
	 * Checks the user_invitations table to see if this user was invited
	 * to the VibeCompass platform to be a promoter
	 * 
	 * @param	string (oauth_uid)
	 * @return	array (invitations)
	 */
	function retrieve_user_invitations($oauth_uid){
		
		$sql = "SELECT
		
					ui.id							as ui_id,
					ui.oauth_uid					as ui_oauth_uid,
					ui.invitation_team_fan_page_id 	as ui_invitation_team_fan_page_id,
					ui.manager_oauth_uid 			as ui_manager_oauth_uid,
					ui.invitation_data				as ui_invitation_data,
					ui.invitation_time				as ui_invitation_time,
					ui.response						as ui_response,
					ui.invitation_type 				as ui_invitation_type,
					t.city_id						as t_city_id,
					t.name							as t_name,
					t.description 					as t_description,
					c.name							as c_name,
					c.state							as c_state
					
				FROM	user_invitations ui
				
				JOIN	teams t
				ON 		t.fan_page_id = ui.invitation_team_fan_page_id
					
				JOIN 	cities c
				ON 		t.city_id = c.id
					
				WHERE 	ui.oauth_uid = ?
						AND ui.response = '0'
						AND ui.invitation_time < (ui.invitation_time + 432000)"; // 5 days
	
		$query = $this->db->query($sql, array($oauth_uid));
		return $query->result();
	}
	
	/**
	 * Retrieves a user's notifications
	 * 
	 * @param	array (vibecompass_id's to look up)
	 * @param	int (id of record to begin looking after)
	 * @return	array
	 */
	function retrieve_user_notifications($user_friends, $iterator_position, $options = array()){
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array(
								'cache' 		=> false,
								'cache_length' 	=> 900, //15 minutes
								'limit' 		=> 8,
								'lang_locale' 	=> 'en_EN.utf8'
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
		
		$sql = "SELECT	
					
					un.id 					as un_id,
					un.vibecompass_id		as un_vibecompass_id,
					un.notification_type 	as un_notification_type,
					un.notification_data	as un_notification_data,
					un.create_time 			as un_create_time,
					u.first_name			as u_first_name,
					u.last_name 			as u_last_name,
					u.full_name				as u_full_name,
					u.gender				as u_gender,
					u.third_party_id		as u_third_party_id
					
				FROM		user_notifications un
				LEFT JOIN 	users u 
				ON 			un.vibecompass_id = u.oauth_uid 
				WHERE ";
				
		if($iterator_position === false || $iterator_position == 'false'){
			$sql .= "(";
		}else{
			$sql .= "un.id < $iterator_position AND (";
		}
		
		foreach($user_friends as $key => $uf){
			if($key == (count($user_friends) - 1))
				$sql .= "un.vibecompass_id = $uf) ";
			else 
				$sql .= "un.vibecompass_id = $uf OR ";
		}
			
		$sql .=	"ORDER BY	un.id DESC ";
		
		if($limit){
			$sql .= " LIMIT $limit";
		}
		
		//if user friends is empty, don't run the query
		if($user_friends){
								
							
						
					
				
			if(php_sapi_name() == 'cli'){
			
			//	echo 'DIAGNOSTIC OUTPUT ------ ';					
			//	var_dump($sql);						
			}
							
							
							
							
			$query = $this->db->query($sql);
			$result = $query->result();	
			
		}else{
					
			$result = array();	
			
		}
		
		
		setlocale(LC_ALL, $lang_locale);
		foreach($result as &$res){
					
			$time = $res->un_create_time;
			$res->occurance_day = strftime('%A', $time);
			$res->occurance_date = strftime('%A', $time) . ' ' . strftime('%b %e', $time);
			
		}
	
		if($last_result = end($result))
			$iterator_position = $last_result->un_id;
		
		$return_result = new stdClass;
		$return_result->data = $result;
		$return_result->iterator_position = $iterator_position;
				
		return $return_result;
	}

	
	/**
	 * Retrieves all unread sticky notifications (limit 3)
	 * 
	 * @param	string (user oauth uid)
	 * @return	array
	 */
	function retrieve_user_sticky_notifications($user_oauth_uid, $limit = 3){
		
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get_where('sticky_notifications', array('notify_user' => $user_oauth_uid, 'read_status' => 0), $limit);
		
		return $query->result();
	
	}
	
	/**
	 * Retrieve the popularity of the VibeCompass venues, promoters and guestlists amongst this user's facebook friends
	 * 
	 * @param	int (user oauth uid)
	 * @param	array (oauth_uids of user's friends)
	 * @return	array
	 */
	function retrieve_user_friend_popular_promoters_venues_guestlists($vc_user_oauth_uid, $vc_user_friends_oauth_uids, $lang_locale = 'en_EN.uft8'){
		
		if(!$vc_user_friends_oauth_uids){
			return array();
		}
		
		$backtime = time() - 60 * 60 * 24 * 365; //365 days
				
				
		$sql = "SELECT 
					
					gl_type 					as gl_type,
					gla_id						as gla_id,
					gla_name 					as gla_name,
					gla_image 					as gla_image,
					gl_day 						as gl_day,
					tv_name						as tv_name,
					u_full_name					as u_full_name,
					up_public_identifier		as up_public_identifier,
					c_url_identifier 			as c_url_identifier,
					c_name 						as c_name,
					c_state 					as c_state,
					COUNT(gla_name) 			as count,
					COUNT(DISTINCT oauth_uid) 	as oauth_uid_count
			
				FROM ";		
				
				
		$sql .= "(((SELECT
				
				'promoter'				as gl_type,
				pgla.id					as gla_id,
				pgla.name				as gla_name,
				pgla.image 				as gla_image,
				pgla.day 				as gl_day,
				tv.name 				as tv_name,
				u.full_name 			as u_full_name,
				up.public_identifier	as up_public_identifier,
				c.url_identifier 		as c_url_identifier,
				c.name 					as c_name,
				c.state 				as c_state,
				pglre.oauth_uid 		as oauth_uid
				
			FROM 	promoters_guest_lists_reservations_entourages pglre
			
			JOIN 	promoters_guest_lists_reservations pglr
			ON 		pglre.promoters_guest_lists_reservations_id = pglr.id
			
			JOIN 	promoters_guest_lists pgl
			ON 		pglr.promoter_guest_lists_id = pgl.id
			
			JOIN 	promoters_guest_list_authorizations pgla 
			ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
			
			JOIN 	users_promoters up  
			ON 		pgla.user_promoter_id = up.id
			
			JOIN 	users u 
			ON 		up.users_oauth_uid = u.oauth_uid
			
			JOIN 	team_venues tv 
			ON 		pgla.team_venue_id = tv.id
			
			JOIN 	promoters_teams pt 
			ON 		pt.promoter_id = up.id
			
			JOIN 	teams t 
			ON 		pt.team_fan_page_id = t.fan_page_id 
			
			JOIN 	cities c 
			ON 		t.city_id = c.id
			
			WHERE	
					pgla.deactivated 	= 0 AND
					up.banned 			= 0 AND
					pt.quit 			= 0 AND
					pt.approved 		= 1 AND
					tv.banned 			= 0 AND
					t.completed_setup 	= 1 AND
					
			pglr.create_time > $backtime AND (";
		
		foreach($vc_user_friends_oauth_uids as $uid){
		
			$sql .= "pglre.oauth_uid = ? OR ";
			
		}$sql = rtrim($sql, " OR ");
		$sql .= "))";
						
						
						
						
		$sql .= " UNION ALL ( ";
		
		$sql .= "SELECT
					
				'promoter'				as gl_type,
				pgla.id					as gla_id,
				pgla.name				as gla_name,
				pgla.image 				as gla_image,
				pgla.day 				as gl_day,
				tv.name 				as tv_name,
				u.full_name				as u_full_name,
				up.public_identifier	as up_public_identifier,
				c.url_identifier 		as c_url_identifier,
				c.name 					as c_name,
				c.state 				as c_state,
				pglr.user_oauth_uid 	as oauth_uid
			
			FROM 	promoters_guest_lists_reservations pglr
			
			JOIN 	promoters_guest_lists pgl
			ON 		pglr.promoter_guest_lists_id = pgl.id
				
			JOIN 	promoters_guest_list_authorizations pgla 
			ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
			
			JOIN 	users_promoters up  
			ON 		pgla.user_promoter_id = up.id
			
			JOIN 	users u 
			ON 		up.users_oauth_uid = u.oauth_uid
			
			JOIN 	team_venues tv 
			ON 		pgla.team_venue_id = tv.id
			
			JOIN 	promoters_teams pt 
			ON 		pt.promoter_id = up.id
			
			JOIN 	teams t 
			ON 		pt.team_fan_page_id = t.fan_page_id 
			
			JOIN 	cities c 
			ON 		t.city_id = c.id
			
			WHERE 	
					pgla.deactivated 	= 0 AND
					up.banned 			= 0 AND
					pt.quit 			= 0 AND
					pt.approved 		= 1 AND
					tv.banned 			= 0 AND
					t.completed_setup 	= 1 AND
					
			pglr.create_time > $backtime AND (";
				
		foreach($vc_user_friends_oauth_uids as $uid){
			
			$sql .= "pglr.user_oauth_uid = ? OR ";
			
		}$sql = rtrim($sql, " OR ");
		$sql .= ")";
		
		
		$sql .= "))";
		
		$sql .= " UNION ALL (";
		
		$sql .= "SELECT 
					
				'venue'					as gl_type,
				tgla.id					as gla_id,
				tgla.name				as gla_name,
				tgla.image 				as gla_image,
				tgla.day 				as gl_day,
				tv.name 				as tv_name,
				''						as u_full_name,
				''						as up_public_identifier,
				c.url_identifier 		as c_url_identifier,
				c.name 					as c_name,
				c.state 				as c_state,
				tglre.oauth_uid 		as oauth_uid
			
			FROM 	teams_guest_lists_reservations_entourages tglre
			
			JOIN 	teams_guest_lists_reservations tglr 
			ON 		tglre.team_guest_list_reservation_id = tglr.id 
			
			JOIN 	teams_guest_lists tgl 
			ON 		tglr.team_guest_list_id = tgl.id 
			
			JOIN 	teams_guest_list_authorizations tgla 
			ON 		tgl.team_guest_list_authorization_id = tgla.id 
			
			JOIN 	team_venues tv 
			ON 		tgla.team_venue_id = tv.id
			
			JOIN 	teams t 
			ON 		tv.team_fan_page_id = t.fan_page_id 
			
			JOIN 	cities c 
			ON 		t.city_id = c.id
			
			WHERE 	
					tgla.deactivated = 0 AND
					tv.banned = 0 AND
					t.completed_setup = 1 AND
			tglr.create_time > $backtime AND (";
		foreach($vc_user_friends_oauth_uids as $uid){
			
			$sql .= "tglre.oauth_uid = ? OR ";
			
		}$sql = rtrim($sql, " OR ");
		$sql .= "))";
		
		
		$sql .= " UNION ALL ( ";
		
		$sql .= "SELECT 
					
				'venue'					as gl_type,
				tgla.id					as gla_id,
				tgla.name				as gla_name,
				tgla.image 				as gla_image,
				tgla.day 				as gl_day,
				tv.name 				as tv_name,
				''						as u_full_name,
				''						as up_public_identifier,
				c.url_identifier 		as c_url_identifier,
				c.name 					as c_name,
				c.state 				as c_state,
				tglr.user_oauth_uid 	as oauth_uid
			
			FROM 	teams_guest_lists_reservations tglr 
			
			JOIN 	teams_guest_lists tgl 
			ON 		tglr.team_guest_list_id = tgl.id 
			
			JOIN 	teams_guest_list_authorizations tgla 
			ON 		tgl.team_guest_list_authorization_id = tgla.id 
			
			JOIN 	team_venues tv 
			ON 		tgla.team_venue_id = tv.id
			
			JOIN 	teams t 
			ON 		tv.team_fan_page_id = t.fan_page_id 
			
			JOIN 	cities c 
			ON 		t.city_id = c.id 
			
			WHERE 	
					tgla.deactivated = 0 AND
					tv.banned = 0 AND
					t.completed_setup = 1 AND
			tglr.create_time > $backtime AND (";
		foreach($vc_user_friends_oauth_uids as $uid){
			
			$sql .= "tglr.user_oauth_uid = ? OR ";
			
		}$sql = rtrim($sql, " OR ");
		$sql .= "))) t ";
		
		
		$sql .= "GROUP BY 	gl_type, gla_id
				ORDER BY	count DESC
				LIMIT 5";
		
		$query = $this->db->query($sql, array_merge($vc_user_friends_oauth_uids, $vc_user_friends_oauth_uids, $vc_user_friends_oauth_uids, $vc_user_friends_oauth_uids));		
		$result = $query->result();
		
		
		
		
		
		
		
		
		
		setlocale(LC_ALL, $lang_locale);
		foreach($result as &$res){
					
			$time = strtotime('next ' . $res->gl_day);
			$res->occurance_day = strftime('%A', $time);
			$res->occurance_date = strftime('%b %e', $time);
			
		}
		
		
		
		
		
		
		
		
		
		 
		return $result;
		
	}
	
	
	
	/**
	 * 
	 */
	function retrieve_twilio_number($oauth_uid, $number){
			
		$query = $this->db->get_where('users', array('twilio_sms_number' => $number, 'oauth_uid != ' => $oauth_uid));
		return $query->row();
		
	}
	/**
	 * 
	 */
	function update_twilio_number($oauth_uid, $number){
		$this->db->where('oauth_uid', $oauth_uid);
		$this->db->update('users', array('twilio_sms_number' => $number));
	}
	/**
	 * 
	 */
	function retrieve_promoter_by_twilio($number){
				
		$query = $this->db->get_where('users', array('twilio_sms_number' => $number));
		$result = $query->row();
		
		if(!$result)
			return false;
		
		$this->load->model('model_users_promoters', 'users_promoters', true);
		$result2 = $this->users_promoters->retrieve_promoter(array(
			'users_oauth_uid' => $result->oauth_uid
		));
		
		return $result2;
	}
	/**
	 * 
	 */
	function retrieve_manager_by_twilio($number){
		$query = $this->db->get_where('users', array('twilio_sms_number' => $number));
		$result = $query->row();
		
		if(!$result)
			return false;
		
		$this->load->model('model_users_managers', 'users_managers', true);
		$result2 = $this->users_managers->retrieve_manager_team($result->oauth_uid);
		
		return $result2;
	}
	
	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Update a user's access token
	 * 
	 * @param	string (oauth uid)
	 * @param	string (facebook access token)
	 * @param 	int (number of seconds access token is valid for)
	 * @return	bool
	 */
	function update_user_access_token($oauth_uid, $access_token, $access_token_expires){
	
		$sql = "UPDATE users 
				SET access_token = ?, 
				access_token_valid_seconds = ?, 
				access_token_acquired_time = ? 
				WHERE oauth_uid = ?";
		$query = $this->db->query($sql, array($access_token, $access_token_expires, time(), $oauth_uid));

	 /*
		$this->db->where('oauth_uid', $oauth_uid);		
		$this->db->update('users', array('access_token' => $access_token, 'access_token_valid_seconds' => $access_token_expires, 'access_token_acquired_time' => time()));
		return true;
		*/
	}
	
	/**
	 * Update a user's fields
	 * 
	 * @param	array (key/values to update)
	 * @return	int (affected rows)
	 */
	function update_user($oauth_uid, $data){
		
		$this->db->where('oauth_uid', $oauth_uid);
		$this->db->update('users', $data);
		
		return true;

	}
		 	
	/**
	 * Called when a user responds to an invitation to join a team as a promoter
	 * 
	 * @param	int (invitation_id)
	 * @param	1 || -1
	 * @return 	bool
	 */
	function update_invitation_status($ui_id, $response){
		
		$this->db->where(array('id' => $ui_id, 'response' => 0));
		$this->db->update('user_invitations', array('response' => $response));
		
	}
	
	/**
	 * Update a sticky notification
	 * 
	 * @param	string (user oauth uid)
	 * @param	string
	 * @param 	array
	 * @return 	null
	 */
	function update_user_sticky_notification($user_oauth_uid, $sticky_notification_id, $data){
		
		$this->db->where(array('id' => $sticky_notification_id, 'notify_user' => $user_oauth_uid));
		$this->db->update('sticky_notifications', $data);
		
	}
	
	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
	
}

/* End of file model_users.php */
/* Location: application/models/model_users.php */