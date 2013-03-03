<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Model_team_guest_lists extends CI_Model {

	/*
	 * 
	 * */
    function __construct(){
        parent::__construct();
    }
	
	/*-------------------------------------------------------------------------
	 |	Create Methods (create)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Creates a team_guest_list_authorization attached to a team_venue
	 * 
	 * @param	
	 * @return	bool
	 */
	function create_team_guest_list_authorization($data){
		
		$this->db->insert('teams_guest_list_authorizations', $data);
		return $this->db->affected_rows();
		
	}
	
	/**
	 * called when a user wants to join an authorized team guest list
	 * 
	 * First this method must check to see if this is the first user who wants to create a guest
	 * list for this week with this team_guest_list_authorization_id.
	 * 		If true, must create a record in 'teams_guest_lists'
	 * Then adds a record to team_guest_lists_reservations
	 * Then adds any entourage members to team_guest_lists_reservations_entourages
	 * 
	 * @param	string (user oauth uid)
	 * @param	...
	 * @param	array (user's entourage)
	 * @param	string (id of guest list)
	 * @param	bool (table request)
	 * @param	bool (share with facebook news feed story)
	 * @param	bool (send text confirmation)
	 * @param	string (phone number)
	 * @param	int (phone carrier)
	 * @return 	array
	 */
	function create_team_guest_list_reservation($oauth_uid,
												$team_fan_page_id,
												$entourage, 
												$id, 
												$table_request, 
												$share_facebook, 
												$text_message, 
												$request_message,
												$phone_number, 
												$phone_carrier,
												$date_check_override 	= false,
												$approve_override 		= false,
												$table_min_spend 		= 0,
												$tglr_supplied_name 	= '',
												$vlfit_id 				= NULL){

		$phone_number = preg_replace('/\D/', '', $phone_number);
		
		//get the dates/epoch times of this week's start/end dates (sunday - saturday)
		//+ today's date
		$today_date = date('Y-m-d', time());
		$this->load->helper('week_range');
		//$list($human_start, $human_end, $epoch_start, $epoch_end) = week_range();
		
		//check to make sure that this guest list ID is active (prevent someone from spoofing a request
		//to join a guest list that has been decactivated).
		$this->db->select('deactivated, auto_approve');
		$query = $this->db->get_where('teams_guest_list_authorizations', array('id' => $id));
		if(!$result = $query->row()){
			return array('success' => false, 'message' => 'Unknown Error');
		}
		if($result->deactivated){
			return array('success' => false, 'message' => 'Unavailable Guest List');
		}
				
		//Is this guest list set to auto-approve?
		$auto_approve_requests = $result->auto_approve;
		
		//if this is a table request, no-auto approve (unless it's a manual table request -- next line)
		if($table_request == 1 || $table_request == '1')
			$auto_approve_requests = false;
		
		if($approve_override)
			$auto_approve_requests = true;
		
		/* --------------------- check to make sure user isn't already on guest list for this night --------------------- */
		//	before we create a guest list, make sure this user doesn't already have a guest list
		//	set up somewhere else on this same night
		/* ---------------- side task --------------- */
			//first we need to know what night of the week this guest list is.
			$this->db->select('day');
			$query = $this->db->get_where('teams_guest_list_authorizations', array('id' => $id));
			$result = $query->row();
			//this is the weekday this guest list is set up for
			$guest_list_weekday = rtrim($result->day, 's'); // remove trailing 's' (how day is stored in database)
		/* ---------------- end side task --------------- */
				
		//given the weekday of this guest list, find the date of the next occurance of this weekday
		$guest_list_next_occurance_date = date('Y-m-d', strtotime($guest_list_weekday));
				
		$sql = "SELECT
					
					tgl.id
					
				FROM 	teams_guest_lists tgl
				
				JOIN 	teams_guest_lists_reservations tglr
				ON 		tglr.team_guest_list_id = tgl.id
				
				WHERE 	tgl.date = ?
						AND 	
						tglr.user_oauth_uid = ?
						AND
						tglr.manual_add = 0";
				
	//	$query = $this->db->query($sql, array($guest_list_next_occurance_date, $oauth_uid));
	//	if($result = $query->row() && !$date_check_override){
			//this user is already on a guest list for this date. return error
	//		$message = "You are already on a guest list for $guest_list_next_occurance_date! You cannot join two guest lists on the same night.";
	//		return array('success' => false, 'message' => $message);
	//	}
		/* --------------------- end check to make sure user isn't already on guest list for this night --------------------- */
		
		
		
		
		
		//find out if a teams_guest_lists record exists given this next occurance date and team_guest_list_id
		//if one doesn't exist yet - create it
		$sql = "SELECT
		
					tgl.id 					as id,
					tgla.id 				as tgla_id,
					tgla.name 				as tgla_name,
					tgla.team_fan_page_id 	as tgla_team_fan_page_id,
					tv.name 				as tv_name,
					tv.id 					as tv_id,
					u.full_name 			as u_full_name,
					u.email					as u_email,
					u.twilio_sms_number		as u_twilio_sms_number
					
				FROM 	teams_guest_list_authorizations tgla
				
				JOIN 	team_venues tv
				ON 		tv.id = tgla.team_venue_id
				
				JOIN 	teams_guest_lists tgl
				ON 		tgla.id = tgl.team_guest_list_authorization_id
				
				JOIN 	teams t 
				ON 		tv.team_fan_page_id = t.fan_page_id 
				
				JOIN 	managers_teams mt 
				ON 		t.fan_page_id = mt.fan_page_id 
				
				JOIN 	users u 
				ON 		mt.user_oauth_uid = u.oauth_uid
				
				WHERE 	
						tgla.id = ?
						AND 
						tgl.date >= ?
						
				LIMIT 	1";
		
		$query = $this->db->query($sql, array($id, $guest_list_next_occurance_date));
		
		//necessary to create a new guest list only if one doesn't already exist
		if(!$result = $query->row()){
			//There is no guest list for this week already. We will now create one.
			$data = array('team_guest_list_authorization_id' => $id,
							'date' => $guest_list_next_occurance_date);
			$this->db->insert('teams_guest_lists', $data);
			$teams_guest_list_id = $this->db->insert_id();
			
	//		var_dump('1');
	//		var_dump($result);
	//		var_dump($this->db->last_query()); //die();
			
			//retrieve row we just inserted
			$query = $this->db->query($sql, array($id, $guest_list_next_occurance_date));
			$result = $query->row();
		}else{
			$teams_guest_list_id = $result->id;
			
	//		var_dump('2');
	//		var_dump($result);
	//		var_dump($this->db->last_query()); //die();
			
		}
		
		
		$team_fan_page_id 	= $result->tgla_team_fan_page_id;
		$tgla_id 			= $result->tgla_id;
		$tgla_name 			= $result->tgla_name;
		$tv_name 			= $result->tv_name;
		$tv_id 				= $result->tv_id;
		$manager_name 		= $result->u_full_name;
		$manager_email 		= $result->u_email;
		$manager_twilio_number = $result->u_twilio_sms_number;	
				
		/* --------------------------------- */
		//add phone number to users table if set
		if($text_message && !$approve_override && $phone_number){
			
			$this->db->where('oauth_uid', $oauth_uid);
			$this->db->update('users', array('phone_number' => preg_replace('/\D/', '', $phone_number),
												'phone_carrier' => $phone_carrier));
			
		}
		/* --------------------------------- */
		
		// Check if this user has been 'maually added' by a manager, if so -- delete manager manual add and replace with this.
		
		if(!$approve_override)
			$this->db->delete('teams_guest_lists_reservations', array('manual_add' => 1, 
																		'user_oauth_uid' => $oauth_uid, 
																		'team_guest_list_id' => $teams_guest_list_id));
			
		
		//now add HEAD user to promoters_guest_list_reservations
		$this->db->insert('teams_guest_lists_reservations', array('team_guest_list_id' 		=> $teams_guest_list_id,
																		'user_oauth_uid' 	=> $oauth_uid,
																		'share_facebook' 	=> ($share_facebook == 1) ? 1 : 0,
																		'text_message' 		=> ($text_message == 1) ? 1 : 0,
																		'request_msg'		=> ($request_message) ? $request_message : '',
																		'table_request' 	=> ($table_request == 1) ? 1 : 0,
																		'manual_add' 		=> ($approve_override) ? 1 : 0,
																		'approved'			=> ($approve_override) ? 1 : 0,
																		'table_min_spend'	=> $table_min_spend,
																		'supplied_name'		=> $tglr_supplied_name,
																		'venues_layout_floors_items_table_id'	=> (($vlfit_id && $vlfit_id != 'false') ? $vlfit_id : NULL),
																		'create_time' 		=> time()));
	//	var_dump($this->db->last_query()); //die();
		
		$teams_guest_lists_reservations_id = $this->db->insert_id();
	//	var_dump($teams_guest_lists_reservations_id); die();
		
		
		//send PUSHER notification to team channel (only if NOT a manual add)
		if($teams_guest_lists_reservations_id){
			
			$this->load->library('pusher');
			
			if(!$approve_override){
				$this->pusher->trigger('presence-' . $team_fan_page_id, 'team_guest_list_reservation', array('tgl_id' 					=> $teams_guest_list_id,
																												'tglr_id' 			=> $teams_guest_lists_reservations_id,
																												'tgla_id'			=> $tgla_id,
																												'entourage' 		=> $entourage,
																												'approved'			=> $auto_approve_requests,
																												'table_request'		=> $table_request,
																												
																												'guest_list_name'	=> $tgla_name,
																												'venue_name'		=> $tv_name,
																												'tv_id'				=> $tv_id,
																												'guest_list_date'	=> $guest_list_next_occurance_date,
																												
																												'head_oauth_uid'	=> $oauth_uid,
																												'request_msg'		=> '',
																												'manual_add'		=> ($approve_override) ? 1 : 0));
																												
				$this->pusher->trigger('presence-' . $team_fan_page_id, 'pending-requests-change', null);
				
			}
			
			
			
			
			
			if(!$approve_override){
				$email_view_data = array(
					'gla_name'	=> $tgla_name,
					'auto_approve'	=> $auto_approve_requests
				);																								
				$this->load->helper('run_gearman_job');
				run_gearman_job('gearman_send_emails', array(
					'to_emails' 		=> json_encode(array(
												$manager_email
											)),
					'to_names'  		=> json_encode(array(
												$manager_name
											)),
					'email_view'		=> 'view_email_guest_list_request_response',
					'email_view_data'	=> json_encode($email_view_data)
				), false);
			}
			
			
			
			
			
		}
		
		//now add the HEAD user's entourage (if no entourage skip)
		if($entourage){ // && !$approve_override

	//		foreach($entourage as $member){
	//			$insert_data[] = array('team_guest_list_reservation_id' => $teams_guest_lists_reservations_id,
	//									'oauth_uid' => $member);
	//		}
	//		$this->db->insert_batch('teams_guest_lists_reservations_entourages', $insert_data);
			
			
			
			
			
			
			
			
			foreach($entourage as $member){
				
				if(is_array($member)){
					
					if($member['oauth_uid'] == '0' || strtolower($member['oauth_uid']) == 'null')
						$member['oauth_uid'] = NULL;
					
					$insert_data[] = array('team_guest_list_reservation_id' 		=> $teams_guest_lists_reservations_id,
											'oauth_uid' 							=> $member['oauth_uid'],
											'supplied_name'							=> $member['name']);
											
					
				}else{
					
					if($member == '0' || strtolower($member) == 'null'){
						$member = NULL;
					}
					
					$insert_data[] = array('team_guest_list_reservation_id' 	=> $teams_guest_lists_reservations_id,
											'oauth_uid' 						=> $member);
					
				}
				
			}
			$this->db->insert_batch('teams_guest_lists_reservations_entourages', $insert_data);			
			
		}




				
		//if this guest list is set to auto-approve, approve the request
		if($auto_approve_requests)
			$this->update_team_guest_list_reservation_reject_or_approve(true,
																		'',
																		$team_fan_page_id,
																		$teams_guest_lists_reservations_id,
																		$approve_override);
																		
																		
																		
																		
																		
																		
		if(!$approve_override && $team_fan_page_id && $team_fan_page_id != '0'){
			
			$this->load->helper('run_gearman_job');
			//send text message to promoter ------------------------------------
			run_gearman_job('gearman_send_sms_notification', array(
				'team_fan_page_id'	=> $team_fan_page_id,
				'twilio_number' 	=> $manager_twilio_number,
				'user_oauth_uid' 	=> $oauth_uid, 
				'guest_list_name'	=> $tgla_name,
				'venue'				=> $tv_name,
				'glr_id'			=> $teams_guest_lists_reservations_id,
				'request_msg'		=> ($request_message) ? $request_message : '',
				'entourage'			=> $entourage,
				'auto_approved'		=> $auto_approve_requests,
				'table_request'		=> ($table_request == 1) ? true : false,
				'manager'			=> true
			), false);
			
			// ------------------------------------------------------------------
			
		}
		
		return array('success' => true, 'message' => $teams_guest_lists_reservations_id);
	}
	
	/*-------------------------------------------------------------------------
	 |	Retrieval Methods (retrieve)
	 | ------------------------------------------------------------------------ */

	 /**
	  * Used for retrieving a single TGLA from a gearman worker for FB requests
	  * 
	  * @param	int (tv_id)
	  * @param	int (tgla_id)
	  * @return	object || false
	  */
	 function retrieve_tgla($tv_id, $tgla_id){
	 	
		$sql = "SELECT
		
					tgla.id					as tgla_id,
					tgla.team_venue_id		as tgla_team_venue_id,
					tgla.day				as tgla_day,
					tgla.name				as tgla_name,
					tgla.create_time		as tgla_create_time,
					tgla.deactivated		as tgla_deactivated,
					tgla.deactivated_time	as tgla_deactivated_time,
					tgla.image 				as tgla_image,
					tv.name 				as tv_name,
					tv.id					as tv_id,
					tv.description 			as tv_description,
					tv.street_address		as tv_street_address,
					c.name 					as c_name,
					c.state 				as c_state,
					c.url_identifier 		as c_url_identifier
		
				FROM 	teams_guest_list_authorizations tgla 
				
				JOIN 	team_venues tv 
				ON 		tgla.team_venue_id = tv.id
				
				JOIN 	cities c 
				ON 		tv.city_id = c.id
		
				WHERE 	tgla.id = ?";
				
				
		$query = $this->db->query($sql, array($tgla_id));
		
		
		return $query->row();
	 	
	 }
	 
	 /**
	  * Retrieve guest lists for a given team
	  * 
	  * @param	array (query options)
	  * @param	array (result/query filter parameters)
	  * @return array
	  */
	 function retrieve_team_guest_lists_authorizations($options = array(), $filters = array()){
	 	/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array(
								'cache' => false,
								'cache_length' => 900, //15 minutes
								'limit' => 1000
								);
		foreach($options as $key => $value){
			//is this a recognized configuration setting?
			if(!array_key_exists($key, $default_options))
				die('model_team_guest_lists->retrieve_team_guest_lists: unknown configuration setting - ' . $key);

			//overwrite default configuration value with new one specified in function call
			$default_options[$key] = $value;
		}
		foreach($default_options as $key => $value){
			//turn all default_config keys into local variables
			${'config_' . $key} = $value;
		}
		/* --------- END CONFIGURATION SETTINGS --------- */
		
		/* --------- FILTER SETTINGS --------- */
		//FILTERS... remove results based on these paramters
		$default_filters = array(
								'fan_page_id'		=> false,
								'team_venue_id' 	=> false,
								'weekday' 			=> false,
								'deactivated' 		=> true 	//true means 'remove deactivated'
								);
		foreach($filters as $key => $value){
			//is this a recognized configuration setting?
			if(!array_key_exists($key, $default_filters))
				die('model_team_guest_lists->retrieve_team_guest_lists_authorizations: unknown filter setting - ' . $key);

			//overwrite default configuration value with new one specified in function call
			$default_filters[$key] = $value;
		}
		foreach($default_filters as $key => $value){
			//turn all default_config keys into local variables
			${'filter_' . $key} = $value;
		}
		/* --------- END FILTER SETTINGS --------- */
		
		$sql = "SELECT DISTINCT
					tgla.id					as tgla_id,
					tgla.team_venue_id		as tgla_team_venue_id,
					tgla.day				as tgla_day,
					tgla.name				as tgla_name,
					tgla.image 				as tgla_image,
					tgla.create_time		as tgla_create_time,
					tgla.deactivated		as tgla_deactivated,
					tgla.deactivated_time	as tgla_deactivated_time,
					tv.name 				as tv_name,
					tv.id					as tv_id,
					tv.description 			as tv_description,
					tv.street_address		as tv_street_address,
					tv.monday				as tv_monday,
					tv.tuesday				as tv_tuesday,
					tv.wednesday			as tv_wednesday,
					tv.thursday				as tv_thursday,
					tv.friday				as tv_friday,
					tv.saturday				as tv_saturday,
					tv.sunday				as tv_sunday,
					c.url_identifier 		as c_url_identifier
					
				FROM	teams_guest_list_authorizations tgla 
				
				JOIN	team_venues tv
				ON		tgla.team_venue_id = tv.id 
				
				JOIN 	teams_venues_pairs tvp 
				ON 		tvp.team_venue_id = tv.id
				
				JOIN	teams t
				ON 		tvp.team_fan_page_id = t.fan_page_id
				
				JOIN 	cities c 
				ON 		tv.city_id = c.id
				
				WHERE	
						tv.banned = 0
						AND tvp.deleted = 0
						AND t.completed_setup = 1 
						AND tgla.deactivated = 0 ";
		
		if($filter_fan_page_id)
			$sql .= "AND tgla.team_fan_page_id = $filter_fan_page_id AND tvp.team_fan_page_id = $filter_fan_page_id AND t.fan_page_id = $filter_fan_page_id ";
		
		if($filter_team_venue_id)
			$sql .= "AND tgla.team_venue_id = $filter_team_venue_id ";
		
		if($filter_weekday)
			$sql .= "AND tgla.day = '$filter_weekday' ";
		
	//	if($filter_deactivated)
	//		$sql .= "AND tgla.deactivated = 0 ";
		
		$sql .= "GROUP BY tgla.id";
		
		$query = $this->db->query($sql);
		return $query->result();
	 }

	 /**
	  * Retrieve all guest lists at a venue
	  * 
	  * @return	array
	  */
	 function retrieve_all_guest_lists($venue_id){
	  						  			
	  	$sql = "SELECT DISTINCT
	  	
	  				tgla.id					as tgla_id,
	  				tgla.team_venue_id		as tgla_team_venue_id,
	  				tgla.day				as tgla_day,
	  				
					tgla.event 				as tgla_event,
					tgla.event_date 		as tgla_event_date,
					tgla.event_override 	as tgla_event_override,
	  				
	  				tgla.name 				as tgla_name,
	  				tgla.create_time		as tgla_create_time,
	  				tgla.deactivated		as tgla_deactivated,
	  				tgla.deactivated_time	as tgla_deactivated_time,
	  				tgla.auto_approve 		as tgla_auto_approve,
	  				tgla.description 		as tgla_description,
	  				tgla.image 				as tgla_image,
	  				
	  				tv.banned 				as tv_banned,
	  				tv.city_id 				as tv_city_id,
	  				tv.name 				as tv_name,
	  				tv.image 				as tv_image,
	  				tv.id 					as tv_id,
					
	  				c.url_identifier		as c_url_identifier
	  				
	  			FROM 	teams_guest_list_authorizations tgla
	  			
	  			JOIN 	team_venues tv
	  			ON 		tgla.team_venue_id = tv.id
	  				  							
				JOIN 	teams_venues_pairs tvp
				ON 		tvp.team_venue_id = tv.id
												
				JOIN 	teams t
				ON 		tv.team_fan_page_id = t.fan_page_id
				
				JOIN 	cities c 
				ON 		tv.city_id = c.id
													  			
	  			WHERE		tgla.deactivated 	= 0
	  			AND 		tgla.team_venue_id 	= ? 
	  			AND 		tvp.deleted 		= 0";
	  	$query 	= $this->db->query($sql, array($venue_id));
	  	$result = $query->result();
		
		return $result;
	  	
	  	//NOTE: might be a need to attach this week's guest list reservations for friends lookup
	 }
	  
	 /**
	  * Retrieves an individual guest list at a venue
	  * 
	  * @return	object || false
	  */
	 function retrieve_individual_guest_list($venue_id, $guest_list_name){
	 			
	 		
	 	//prep guest list name			
	 	$guest_list_name = str_replace('_', ' ', $guest_list_name);				
	 				
	 			
	 	$sql = "SELECT DISTINCT
	  	
	  				tgla.id					as tgla_id,
	  				tgla.team_venue_id		as tgla_team_venue_id,
	  				tgla.team_fan_page_id	as tgla_team_fan_page_id,
	  				tgla.day				as tgla_day,
	  				tgla.name 				as tgla_name,
	  				tgla.create_time		as tgla_create_time,
	  				tgla.deactivated		as tgla_deactivated,
	  				tgla.deactivated_time	as tgla_deactivated_time,
	  				tgla.auto_approve 		as tgla_auto_approve,
	  				tgla.description 		as tgla_description,
	  				tgla.image 				as tgla_image,
	  				tgla.min_age			as tgla_min_age,
	  				tgla.door_open			as tgla_door_open,
	  				tgla.door_close 		as tgla_door_close,
	  				tgla.regular_cover		as tgla_regular_cover,
	  				tgla.gl_cover			as tgla_gl_cover,
	  				tgla.additional_info_1	as tgla_additional_info_1,
	  				tgla.additional_info_2	as tgla_additional_info_2,
	  				tgla.additional_info_3	as tgla_additional_info_3,
					
					
	  				tv.name					as tv_name,
	  				tv.id 					as tv_id,
	  				tv.image 				as tv_image,
	  				tv.street_address		as tv_street_address,
	  				tv.city 				as tv_city,
	  				tv.state 				as tv_state,
	  				tv.zip					as tv_zip,
	  				tv.team_fan_page_id 	as tv_team_fan_page_id,
	  				
					c.url_identifier		as c_url_identifier
	  				
	  				
	  			FROM 	teams_guest_list_authorizations tgla 
	  			
				
				
				
	  			JOIN 	team_venues tv
	  			ON 		tgla.team_venue_id = tv.id
	  				  							
				JOIN 	teams_venues_pairs tvp
				ON 		tvp.team_venue_id = tv.id
												
				JOIN 	teams t
				ON 		tv.team_fan_page_id = t.fan_page_id
	  			
				
				
				
				
				JOIN 	cities c 
				ON 		tv.city_id = c.id
	  			
	  			WHERE	tgla.deactivated = 0
	  			AND 	tgla.team_venue_id = ? 
	  			AND 	tgla.name = ?
	  			AND 	tvp.deleted = 0
	  			AND 	t.completed_setup = 1
	  			AND 	tv.banned = 0";
	  	$query = $this->db->query($sql, array($venue_id, $guest_list_name));
	  	$result = $query->row();
		
		
		if(!$result){
					
			return $result;	
			
		}
		
		
		//attach latest status
		$this->db->select('
				glas.id 				as glas_id,
				glas.status 			as glas_status,
				glas.create_time 		as glas_create_time,
				glas.users_oauth_uid 	as glas_users_oauth_uid')
			->from('guest_list_authorizations_statuses glas')	
			->where(array(
				'glas.team_guest_list_authorizations_id' => $result->tgla_id
			))
			->order_by('glas_id', 'desc')
			->limit(1, 0);
		$query = $this->db->get();
		$result->status = $query->row();
		
		if($result->status)
			$result->status->glas_human_date = date('l m/d/y h:i:s A', $result->status->glas_create_time);
		
		Kint::dump($result);
		
		return $result;
	  	
	  	//NOTE: might be a need to attach this week's guest list reservations for friends lookup
	 	
	 }
	 
	 
	 /**
	  * 
	  */
	 function retrieve_individual_guest_list_for_plugin($tgla_id){
	 	
		$sql = "SELECT
	  	
	  				tgla.id					as tgla_id,
	  				tgla.team_venue_id		as tgla_team_venue_id,
	  				tgla.day				as tgla_day,
	  				tgla.name 				as tgla_name,
	  				tgla.create_time		as tgla_create_time,
	  				tgla.deactivated		as tgla_deactivated,
	  				tgla.deactivated_time	as tgla_deactivated_time,
	  				tgla.auto_approve 		as tgla_auto_approve,
	  				tgla.description 		as tgla_description,
	  				tgla.image 				as tgla_image,
	  				tgla.min_age			as tgla_min_age,
	  				tgla.door_open			as tgla_door_open,
	  				tgla.door_close 		as tgla_door_close,
	  				tgla.regular_cover		as tgla_regular_cover,
	  				tgla.gl_cover			as tgla_gl_cover,
	  				tgla.additional_info_1	as tgla_additional_info_1,
	  				tgla.additional_info_2	as tgla_additional_info_2,
	  				tgla.additional_info_3	as tgla_additional_info_3,
					
					
	  				tv.name					as tv_name,
	  				tv.id 					as tv_id,
	  				tv.image 				as tv_image,
	  				tv.street_address		as tv_street_address,
	  				tv.city 				as tv_city,
	  				tv.state 				as tv_state,
	  				tv.zip					as tv_zip,
	  				tv.team_fan_page_id 	as tv_team_fan_page_id,
	  				
					c.url_identifier		as c_url_identifier
	  				
	  				
	  			FROM 	teams_guest_list_authorizations tgla 
	  			
	  			JOIN 	team_venues tv
	  			ON 		tgla.team_venue_id = tv.id
	  			
	  			JOIN 	teams t 
	  			ON 		tv.team_fan_page_id	= t.fan_page_id
	  			
				JOIN 	cities c 
				ON 		tv.city_id = c.id
	  			
	  			WHERE	tgla.deactivated = 0
	  			AND 	tgla.id = ?
	  			AND 	t.completed_setup = 1
	  			AND 	tv.banned = 0";
	  	$query = $this->db->query($sql, array($tgla_id));
	  	$result = $query->row();
	  	
	  	
	  	if(!$result){
					
			return $result;	
			
		}
		
	  	
	  	//attach latest status
		$this->db->select('
				glas.id 				as glas_id,
				glas.status 			as glas_status,
				glas.create_time 		as glas_create_time,
				glas.users_oauth_uid 	as glas_users_oauth_uid')
			->from('guest_list_authorizations_statuses glas')	
			->where(array(
				'glas.team_guest_list_authorizations_id' => $result->tgla_id
			))
			->order_by('glas_id', 'desc')
			->limit(1, 0);
		$query = $this->db->get();
		$result->status = $query->row();
		
		if($result->status)
			$result->status->glas_human_date = date('l m/d/y h:i:s A', $result->status->glas_create_time);
		
		
		return $result;
		
	 }
	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	
	
	function update_reservation_reassign($options){
					
				
			
		
		//retrieve & simultaniously confirm this guest list matches this team
		$sql = "SELECT
		
					tglr.user_oauth_uid 	as tglr_user_oauth_uid,
					tglr.approved			as tglr_approved,
					tglr.text_message		as tglr_text_message,
					tglr.share_facebook		as tglr_share_facebook,
					tgl.date				as tgl_date,
					tgla.name				as tgla_name,
					tgla.image 				as tgla_image,
					tv.id				 	as tv_id,
					tv.name					as tv_name,
					tv.image 				as tv_image,
					c.name					as c_name,
					c.url_identifier		as c_url_identifier
					
				FROM 	teams_guest_lists_reservations tglr
				
				JOIN 	teams_guest_lists tgl
				ON 		tglr.team_guest_list_id = tgl.id

				JOIN 	teams_guest_list_authorizations tgla
				ON 		tgl.team_guest_list_authorization_id = tgla.id

				JOIN 	team_venues tv
				ON 		tgla.team_venue_id = tv.id
				
				JOIN 	cities c 
				ON 		tv.city_id = c.id
		
				WHERE 	tglr.id = ? AND tgla.team_fan_page_id = ? ";
				
			//	if($team_fan_page_id)
			//		$sql .= "AND tgla.team_fan_page_id = $team_fan_page_id";
				
			//		AND tv.team_fan_page_id = $team_venue_id";
		$query = $this->db->query($sql, array($options['tglr_id'], $options['team_fan_page_id']));
		$result = $query->row();
		
		if(!$result)
			return false;
				
		if($result->tglr_approved == 0)
			return false; //this guest list reservation hasn't been approved
			
		
		if($options['vlfit_id'] !== false){
			//verify vlfit_id is for a venue that belongs to this team
			$sql = "SELECT
						
						*
					
					FROM 	venues_layout_floors_items_tables vlfit
					
					JOIN 	venues_layout_floors_items vlfi
					ON 		vlfit.venues_layout_floors_items_id = vlfi.id
					
					JOIN 	venues_layout_floors vlf
					ON 		vlfi.venues_layout_floor_id = vlf.id
					
					WHERE 	vlf.team_venue_id = ?
							AND
							vlfit.id = ?";
			$query = $this->db->query($sql, array($result->tv_id, $options['vlfit_id']));
			$result2_temp = $query->row();
			if(!$result2_temp){
				return false; //this vlfit_id doesn't belong to a venue that's part of this team
			}
		}
		
		
		
		
		//update record, after we recieve original data (necessary for this to work...)
		$this->db->where('id', $options['tglr_id']);
		$this->db->update('teams_guest_lists_reservations', array('venues_layout_floors_items_table_id' => $options['vlfit_id']));	
		
		
		return true;
		
	}
	
	
	
	/**
	 * Update a user's guest list reservation request as either 'approved' or 'rejected'
	 * 
	 * Also perform all related functions such as text-message notifications, emails, etc
	 * 
	 * @param	bool (approved or rejected)
	 * @param	string (response message)
	 * @param	int (team_fan_page_id)
	 * @param	int (team_guest_list_reservation_id)
	 * @return 	bool
	 */
	function update_team_guest_list_reservation_reject_or_approve($approved,
																	$message,
																	$team_venue_id,
																	$team_guest_list_reservation_id,
																	$approve_override 			= false,
																	$table_request 				= false,
																	$vlfit_id 					= false,
																	$team_fan_page_id 			= false){

		//retrieve & simultaniously confirm this guest list matches this team
		$sql = "SELECT
		
					tglr.user_oauth_uid 	as tglr_user_oauth_uid,
					tglr.approved			as tglr_approved,
					tglr.text_message		as tglr_text_message,
					tglr.share_facebook		as tglr_share_facebook,
					tglr.table_request 		as tglr_table_request,
					tglr.manual_add 		as tglr_manual_add,
					
					tgl.date				as tgl_date,
					tgla.name				as tgla_name,
					tgla.image 				as tgla_image,
					tgla.team_fan_page_id	as tgla_team_fan_page_id,
					tv.id				 	as tv_id,
					tv.name					as tv_name,
					tv.image 				as tv_image,
					c.name					as c_name,
					c.url_identifier		as c_url_identifier,
					
					tv.id 					as tv_id
					
				FROM 	teams_guest_lists_reservations tglr
				
				JOIN 	teams_guest_lists tgl
				ON 		tglr.team_guest_list_id = tgl.id

				JOIN 	teams_guest_list_authorizations tgla
				ON 		tgl.team_guest_list_authorization_id = tgla.id

				JOIN 	team_venues tv
				ON 		tgla.team_venue_id = tv.id
				
				JOIN 	cities c 
				ON 		tv.city_id = c.id
		
				WHERE 	tglr.id = ? ";
				
				if($team_fan_page_id)
					$sql .= "AND tgla.team_fan_page_id = $team_fan_page_id";
				
			//		AND tv.team_fan_page_id = $team_venue_id";
		$query = $this->db->query($sql, array($team_guest_list_reservation_id));
		$result = $query->row();
		
		
		
		
		if(!$result)
			return false;
				
		if($result->tglr_approved == 1)
			return false; //this guest list reservation request has already been approved, don't continue
		
		
		$team_fan_page_id 	= $result->tgla_team_fan_page_id;
		$tglr_manual_add 	= $result->tglr_manual_add;
		
		
		if($vlfit_id !== false){
			//verify vlfit_id is for a venue that belongs to this team
			$sql = "SELECT
						
						*
					
					FROM 	venues_layout_floors_items_tables vlfit
					
					JOIN 	venues_layout_floors_items vlfi
					ON 		vlfit.venues_layout_floors_items_id = vlfi.id
					
					JOIN 	venues_layout_floors vlf
					ON 		vlfi.venues_layout_floor_id = vlf.id
					
					WHERE 	vlf.team_venue_id = ?
							AND
							vlfit.id = ?";
			$query = $this->db->query($sql, array($result->tv_id, $vlfit_id));
			$result2_temp = $query->row();
			if(!$result2_temp){
				return false; //this vlfit_id doesn't belong to a venue that's part of this team
			}
		}
		
		
		
		
		//update record, after we recieve original data (necessary for this to work...)
		$this->db->where('id', $team_guest_list_reservation_id);
		$this->db->update('teams_guest_lists_reservations', array('approved' 								=> ($approved) ? 1 : -1,
																	'response_msg' 							=> $message,
																	'venues_layout_floors_items_table_id' 	=> (($table_request !== false && $vlfit_id !== false) ? $vlfit_id : NULL)));
																	
												
												
						
						
						
												
																	
		if($approved){
			
			$pusher_data 		= new stdClass;
			$pusher_data->type 	= 'new_reservation';
			$pusher_data->date 	= $result->tgl_date;
			$pusher_data->tv_id	= $result->tv_id;
			
			$this->load->library('Pusher', '', 'pusher');
			$this->pusher->trigger('presence-' . $team_fan_page_id, 'host_recieve', $pusher_data);
								
		}
		
		
		
		
		
		
		
																		
		
		//get entourage count
		$sql = "SELECT
					count(*) 	as count
				FROM 	teams_guest_lists_reservations_entourages tglre
				WHERE	tglre.team_guest_list_reservation_id = $team_guest_list_reservation_id";
		$query = $this->db->query($sql);
		$entourage_result = $query->row();
		
		if($approved && !$approve_override){
			
			//Create notification for this user's vc_friends
			$this->load->model('model_users', 'users', true);
			$result->team_venue_id 		= $team_venue_id;
			$result->entourage_count 	= $entourage_result->count;
			$this->users->create_user_notifications($result->tglr_user_oauth_uid, 'join_team_guest_list', $result);
			
			
			//EMAIL USERS FRIENDS THAT THEY"RE ON THE GL
			$this->load->helper('run_gearman_job');
			run_gearman_job('gearman_email_friends_gl_join', array(
				'type'		=> 'team',
				'gl_query'	=> json_encode($result)
			), false);
			
		}
		
		
		
		
		
		if(!$approve_override && !$tglr_manual_add){
			//send confirmation email
			
			
			$this->db->cache_off();
			$sql5 	= "SELECT * FROM users WHERE oauth_uid = ?";
			$query 	= $this->db->query($sql5, array($result->tglr_user_oauth_uid));
			$confirm_email_user = $query->row();
			
			
			$this->load->helper('run_gearman_job');
			run_gearman_job('gearman_confirmation_email_team', array(
				'user_json'			=> json_encode($confirm_email_user),
				'tglr'				=> json_encode($result),
				'team_fan_page_id'	=> $team_fan_page_id,
				'approved'			=> $approved,
				'message' 			=> $message
			), false);
			
			
		}
		
		
		
		
		
		
		if($result->tglr_text_message && !$approve_override){
				
			if($approved)
				$text_message = "(ClubbingOwl) You have been approved to join \"$result->tgla_name\" at $result->tv_name on $result->tgl_date.";
			else
				$text_message = "(ClubbingOwl) Your request to join \"$result->tgla_name\" at $result->tv_name on $result->tgl_date has been denied.";
						
			if($message)
				$text_message = $text_message . " MSG: \"$message.\"";		
						
			# ---------------------------------------------------------- #
			#	Send text message job to gearman as background job		 #
			# ---------------------------------------------------------- #	
			//NOTE: We don't need to retrieve the results of this job... just launch it.			
			//add job to a task
			$this->load->library('pearloader');
			$gearman_client = $this->pearloader->load('Net', 'Gearman', 'Client');
						
			/* ---------- notify all VC friends of this user that they have joined VibeCompass --------- */
			$gearman_task = $this->pearloader->load('Net', 'Gearman', 'Task', array('func' => 'guest_list_text_message',
																					'arg'  => array('user_oauth_uid' 	=> $result->tglr_user_oauth_uid,
																									'text_message' 		=> $text_message)));
			$gearman_task->type = Net_Gearman_Task::JOB_BACKGROUND;
			
			//add test to a set
			$gearman_set = $this->pearloader->load('Net', 'Gearman', 'Set');
			$gearman_set->addTask($gearman_task);
			 
			//launch that shit
			$gearman_client->runSet($gearman_set);
						
			# ---------------------------------------------------------- #
			#	END Send text message job to gearman as background job	 #
			# ---------------------------------------------------------- #
			
		}
		
		if($result->tglr_share_facebook && $approved && !$approve_override){

			# ---------------------------------------------------------- #
			#	Send share facebook job to gearman as background job	 #
			# ---------------------------------------------------------- #	
			//NOTE: We don't need to retrieve the results of this job... just launch it.			
			//add job to a task
			$this->load->library('pearloader');
			$gearman_client = $this->pearloader->load('Net', 'Gearman', 'Client');
						
			/* ---------- notify all VC friends of this user that they have joined VibeCompass --------- */
			$gearman_task = $this->pearloader->load('Net', 'Gearman', 'Task', array('func' => 'guest_list_share_facebook',
																					'arg'  => array('team_guest_list' 	=> true,
																									'user_oauth_uid' 	=> $result->tglr_user_oauth_uid,
																									'venue_name' 		=> $result->tv_name,
																									'date' 				=> $result->tgl_date,
																									'guest_list_name' 	=> $result->tgla_name,
																									
																									'image'				=> $result->tgla_image,
																									
																									'c_url_identifier' 	=> $result->c_url_identifier,
																									
																									'team_venue_id' 	=> $team_venue_id)));
			$gearman_task->type = Net_Gearman_Task::JOB_BACKGROUND;
			
			//add test to a set
			$gearman_set = $this->pearloader->load('Net', 'Gearman', 'Set');
			$gearman_set->addTask($gearman_task);
			 
			//launch that shit
			$gearman_client->runSet($gearman_set);
						
			# ---------------------------------------------------------- #
			#	END Send share facebook job to gearman as background job #
			# ---------------------------------------------------------- #
			
		}




//		$this->load->helper('run_gearman_job');
//		$arguments = array(
//							'reservation_type' 	=> 'team',
//							'guest_list_name' 	=> $result->tgla_name,
//							'venue_name'		=> $result->tv_name
//							);
//		run_gearman_job('send_ses_email', $arguments);




		//send pusher notification to user
		if(!$approve_override){
			
			$this->load->library('Pusher');
			
			//update pending requests everywhere
			$this->pusher->trigger('presence-' . $team_fan_page_id, 'pending-requests-change', null);
			
			
			$payload 						= new stdClass;
			$payload->notification_type 	= 'request_response';
			$payload->venue_name 			= $result->tv_name;
			$payload->response 				= ($approved) ? 'approved' : 'declined';
			$payload->guest_list_name 		= $result->tgla_name;
			$payload->response_message 		= $message;
			
			$this->load->model('model_users', 'users', true);
			$insert_id 		= $this->users->create_user_sticky_notification($result->tglr_user_oauth_uid, 
																			$result->tglr_user_oauth_uid, 
																			json_encode($payload));
			$payload->id 	= $insert_id;
			
			$pusher_channel = 'private-vc-' . $result->tglr_user_oauth_uid;
			$this->pusher->trigger($pusher_channel, 'notification', $payload);

		}
		
		return true;
	}

	/**
	 * Update a message to a host from a team manager
	 * 
	 * @param 	string (host message)
	 * @param 	int (tglr id)
	 * @return 	array
	 */
	function update_promoter_reservation_host_notes($host_message, $tglr_id){
		
		//retrieve & simultaniously confirm this guest list matches this team
		$sql = "SELECT
		
					tglr.user_oauth_uid 	as tglr_user_oauth_uid,
					tglr.approved			as tglr_approved,
					tglr.text_message		as tglr_text_message,
					tglr.share_facebook		as tglr_share_facebook,
					tgl.date				as tgl_date,
					tgla.name				as tgla_name,
					tv.name					as tv_name
					
				FROM teams_guest_lists_reservations tglr
				JOIN teams_guest_lists tgl
				ON tglr.team_guest_list_id = tgl.id

				JOIN teams_guest_list_authorizations tgla
				ON tgl.team_guest_list_authorization_id = tgla.id

				JOIN team_venues tv
				ON tgla.team_venue_id = tv.id
				
				WHERE tglr.id = ?";
			//		AND tv.team_fan_page_id = $team_venue_id";
		$query = $this->db->query($sql, array($tglr_id));
		$result = $query->row();
		
		if(!$result)
			return false;
		
		$this->db->where('id', $tglr_id);
		$this->db->update('teams_guest_lists_reservations', array('host_message' => $host_message));
		
		return 	array('success' => true);
	}
	
	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
}
	

/* End of file model_team_guest_lists.php */
/* Location: application/models/model_team_guest_lists.php */