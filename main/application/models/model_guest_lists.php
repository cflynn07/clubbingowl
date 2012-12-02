<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Model to handle all database interaction involved in authenticating users
 * */
class Model_guest_lists extends CI_Model {

	/*
	 * 
	 * */
    function __construct(){
        parent::__construct();
    }
	 
	/**
	 * called when a user wants to join a guest list a promoter has authorized.
	 * 
	 * First this method must check to see if this is the first user who wants to create a guest
	 * list for this week with this guest_list_authorization_id.
	 * 		If true, must create a record in 'promoters_guest_lists'
	 * Then adds a record to promoters_guest_list_reservations
	 * Then adds any entourage members to promoters_guest_list_reservations_entourages
	 * 
	 * @param	guest-list-authorization id
	 * @param	head user fbid
	 * @param	entourage fbid's
	 * 
	 */
	function create_new_promoter_guest_list_reservation($id,	//pgla_id
														$head, 
														$entourage,
														$promoter_id,
														$table_request, 
														$share_facebook,
														$request_message,
														$text_message, 
														$phone_number, 
														$phone_carrier,
														$table_min_spend = 0,
														$date_check_override = false,
														$approve_override = false,
														$pglr_supplied_name = ''){

		$phone_number = preg_replace('/\D/', '', $phone_number);

		//get the dates/epoch times of this week's start/end dates (sunday - saturday)
		//+ today's date
		$today_date = date('Y-m-d', time());
		$this->load->helper('week_range');
		//$list($human_start, $human_end, $epoch_start, $epoch_end) = week_range();
		
		//check to make sure that this guest list ID is active (prevent someone from spoofing a request
		//to join a guest list that has been decactivated).
	//	$this->db->select('deactivated, auto_approve');
	//	$query = $this->db->get_where('promoters_guest_list_authorizations', array('id' => $id));
		$sql = "SELECT
					
					pgla.deactivated 		as deactivated,
					pgla.auto_approve 		as auto_approve,
					t.fan_page_id			as t_fan_page_id,
					up.users_oauth_uid 		as up_users_oauth_uid,
					tv.name 				as tv_name,
					pgla.id 				as pgla_id,
					u.full_name				as u_full_name,
					u.email 				as u_email,
					u.twilio_sms_number		as u_twilio_sms_number
					
				FROM 	promoters_teams pt 
				
				JOIN 	users_promoters up 
				ON 		pt.promoter_id = up.id
				
				JOIN 	users u 
				ON 		up.users_oauth_uid = u.oauth_uid
				
				JOIN 	teams t 
				ON 		pt.team_fan_page_id = t.fan_page_id 
				
				JOIN 	teams_venues_pairs tvp 
				ON 		tvp.team_fan_page_id = t.fan_page_id
				
				JOIN 	team_venues tv
				ON 		tv.id = tvp.team_venue_id 
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgla.team_venue_id = tv.id
				
				WHERE 	pt.approved = 1
						AND pt.banned = 0
						AND pt.quit = 0
						AND up.id = ?
						AND pgla.id = ?";
		$query = $this->db->query($sql, array($promoter_id, $id));
		$result = $query->row();
		
		if(!$result = $query->row()){
		//	log_message('error', 'Guest List ' . $id . ' does not exist. model_guest_lists->create_new_guest_list_reservation');
			return array(false, 'Guest list does not exist');
		}
		if($result->deactivated){
		//	log_message('error', 'User attempted to add themselves to guest list' . $id . ' which is deactivated');
			return array(false, 'Unavailable guest list');
		}
		
		//grab the 'team_fan_page_id' for sending a PUSHER request later
		$teams_fan_page_id 		= $result->t_fan_page_id;
		$promoter_oauth_uid 	= $result->up_users_oauth_uid;
		$promoter_gl_venue_name = $result->tv_name;
		$promoter_gla_id		= $result->pgla_id;
		$promoter_email 		= $result->u_email;
		$promoter_full_name 	= $result->u_full_name;
		$promoter_twilio_number = $result->u_twilio_sms_number;
		
		//Is this guest list set to auto-approve?
		$auto_approve_requests = $result->auto_approve;
		
		//if this is a table request, no-auto approve (unless it's a manual table request -- next line)
		if($table_request == 1 || $table_request == '1' || $table_request == 'true')
			$auto_approve_requests = false;
		
		if($approve_override)
			$auto_approve_requests = true;
		
		/* --------------------- check to make sure user isn't already on guest list for this night --------------------- */
		//before we create a guest list, make sure this user doesn't already have a guest list
		//set up somewhere else on this same night
		/* ---------------- side task --------------- */
			//first we need to know what night of the week this guest list is.
			$this->db->select('day, name');
			$query = $this->db->get_where('promoters_guest_list_authorizations', array('id' => $id));
			$result = $query->row();
			
			//this is the weekday this guest list is set up for
			$guest_list_weekday = rtrim($result->day, 's'); // remove trailing 's' for how day is stored in database
			$promoter_guest_list_name = $result->name; //FOR PUSHER LATER
		/* ---------------- end side task --------------- */
				
		//given the weekday of this guest list, find the date of the next occurance of this weekday
		$guest_list_next_occurance_date = date('Y-m-d', strtotime($guest_list_weekday));
				
		$sql = "SELECT
		
					pglr.id
				
				FROM 	promoters_guest_lists_reservations pglr
								
				JOIN	promoters_guest_lists pgl
				ON		pgl.id = pglr.promoter_guest_lists_id
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgla.id = pgl.promoters_guest_list_authorizations_id
				
				WHERE	pglr.user_oauth_uid = ?
						AND 	
						pgl.date = ?
						AND
						pgla.id = ?
						AND
						pglr.manual_add = 0
				LIMIT   1";		
				
		$query = $this->db->query($sql, array($head, $guest_list_next_occurance_date, $id));
		if($result = $query->row() && !$date_check_override){
			//this user is already on a guest list for this date. return error
			$message = "You have already requested to join \"$promoter_guest_list_name\" on $guest_list_next_occurance_date.";
			return array(false, $message);
		}
		
		
		/* --------------------- end check to make sure user isn't already on guest list for this night --------------------- */

		//find out if a promoters_guest_list record exists given this week range and guest list id
		//if one doesn't exist yet, create it
		$sql = "SELECT 
			
					pgl.id										as pgl_id,
					pgl.canceled								as pgl_canceled,
					pgl.date									as pgl_date,
					pgl.promoters_guest_list_authorizations_id	as pgl_promoters_guest_list_authorizations_id
				
				FROM 	promoters_guest_lists pgl
				
				WHERE 	pgl.promoters_guest_list_authorizations_id = ?
				AND 	pgl.date >= ?
				LIMIT 1";
		$query = $this->db->query($sql, array($id, $guest_list_next_occurance_date));	
		
		//necessary to create a new guest list only if one doesn't already exist
		if(!$result = $query->row()){
			
			//There is no guest list for this week already. We will now create one.
			$data = array('promoters_guest_list_authorizations_id' => $id,
							'date' => $guest_list_next_occurance_date);
			$this->db->insert('promoters_guest_lists', $data);
			$promoters_guest_list_id = $this->db->insert_id();			
			
		}else{
			
			$promoters_guest_list_id = $result->pgl_id;
			
		}
		
		/* --------------------------------- */
		//add phone number to users table if set
		if($text_message && $phone_number){
			
			$this->db->where('oauth_uid', $head);
			$this->db->update('users', array('phone_number' => preg_replace('/\D/', '', $phone_number),
												'phone_carrier' => $phone_carrier));
			
		}
		/* --------------------------------- */
		
		// Check if this user has been 'maually added' by a promoter, if so -- delete promoter manual add and replace with this.
		$this->db->delete('promoters_guest_lists_reservations', array('manual_add' => 1, 'user_oauth_uid' => $head, 'promoter_guest_lists_id' => $promoters_guest_list_id));
		
		//now add HEAD user to promoters_guest_list_reservations
		$this->db->insert('promoters_guest_lists_reservations', array('promoter_guest_lists_id' => $promoters_guest_list_id,
																		'user_oauth_uid' 	=> $head,
																		'request_msg' 		=> $request_message,
																		'share_facebook' 	=> ($share_facebook == 'true') ? 1 : 0,
																		'text_message' 		=> ($text_message == 'true') ? 1 : 0,
																		'table_request' 	=> ($table_request == 'true') ? 1 : 0,
																		'manual_add'		=> ($approve_override) ? 1 : 0,
																		'table_min_spend'	=> $table_min_spend,
																		'supplied_name'		=> $pglr_supplied_name,
																		'create_time' => time()));
		$promoters_guest_lists_reservations_id = $this->db->insert_id();
		
		//send PUSHER notification to team channel (only if NOT a manual add)
		if($promoters_guest_lists_reservations_id){
			
			$this->load->library('pusher');
			$this->pusher->trigger('private-' . $promoter_oauth_uid, 'pending-requests-change', null);
			
			
			$this->pusher->trigger('presence-' . $teams_fan_page_id, 'promoter_guest_list_reservation', array('pgl_id' 					=> $promoters_guest_list_id,
																												'pglr_id'				=> $promoters_guest_lists_reservations_id,
																												'pgla_id'				=> $promoter_gla_id,
																												'promoter_id'			=> $promoter_id,
																												'approved'				=> $auto_approve_requests,
																												'table_request' 		=> ($table_request == 'true') ? 1 : 0,
																												
																												'promoter_oauth_uid'	=> $promoter_oauth_uid,
																												'guest_list_name'		=> $promoter_guest_list_name,
																												'venue_name'			=> $promoter_gl_venue_name,
																												'guest_list_date'		=> $guest_list_next_occurance_date,
																												
																												'entourage'				=> $entourage,
																												'head_oauth_uid' 		=> $head,
																												'request_msg' 			=> $request_message,
																												'manual_add' 			=> ($approve_override) ? 1 : 0));
																												
			if(!$approve_override){																			
				$email_view_data = array(
					'gla_name'	=> $promoter_guest_list_name,
					'auto_approve'	=> $auto_approve_requests
				);																								
				$this->load->helper('run_gearman_job');
				run_gearman_job('gearman_send_emails', array(
					'to_emails' 		=> json_encode(array(
												$promoter_email
											)),
					'to_names'  		=> json_encode(array(
												$promoter_full_name
											)),
					'email_view'		=> 'view_email_guest_list_request_response',
					'email_view_data'	=> json_encode($email_view_data)
				), false);
				
				//send text message to promoter ------------------------------------
				run_gearman_job('gearman_send_sms_notification', array(
					'twilio_number' 	=> $promoter_twilio_number,
					'user_oauth_uid' 	=> $head, 
					'guest_list_name'	=> $promoter_guest_list_name,
					'venue'				=> $promoter_gl_venue_name,
					'glr_id'			=> $promoters_guest_lists_reservations_id,
					'request_msg'		=> $request_message,
					'entourage'			=> $entourage,
					'auto_approved'		=> $auto_approve_requests,
					'table_request'		=> ($table_request == 1) ? true : false,
					'manager'			=> false
				), false);
				
				// ------------------------------------------------------------------
				
				
			}
			
		}
				
		//now add the HEAD user's entourage (if no entourage skip)
		if($entourage){

			foreach($entourage as $member){
				
				if(is_array($member)){
					
					if($member['oauth_uid'] == '0' || strtolower($member['oauth_uid']) == 'null')
						$member['oauth_uid'] = NULL;
					
					$insert_data[] = array('promoters_guest_lists_reservations_id' 	=> $promoters_guest_lists_reservations_id,
											'oauth_uid' 							=> $member['oauth_uid'],
											'supplied_name'							=> $member['name']);
											
					
				}else{
					
					if($member == '0' || strtolower($member) == 'null'){
						$member = NULL;
					}
					
					$insert_data[] = array('promoters_guest_lists_reservations_id' 	=> $promoters_guest_lists_reservations_id,
											'oauth_uid' 							=> $member);
					
				}
				
			}
			$this->db->insert_batch('promoters_guest_lists_reservations_entourages', $insert_data);
			
		}
				
		//if this guest list is set to auto-approve, approve the request
		if($auto_approve_requests)
			$this->update_promoter_guest_list_reservation_reject_or_approve(true,
																			$promoter_id,
																			$promoters_guest_lists_reservations_id,
																			'',
																			$approve_override);

	//	return array(true, '');
		return array(true, $promoters_guest_lists_reservations_id);
	}
	
	/*-------------------------------------------------------------------------
	 |	Retrieval Methods (retrieve)
	 | ------------------------------------------------------------------------ */
	
	/*-------------------------------------------------------------------------
	 |	Create Methods (create)
	 | ------------------------------------------------------------------------ */

	 /**
	  * Retrieve a pgla by ID for editing purposes
	  * 
	  * @param	promoter_id
	  * @param	pgla_id
	  * @return	object || false
	  */
	 function retrieve_pgla($promoter_id, $pgla_id){
	 	
		$sql = "SELECT
					
					pgla.id 				as pgla_id,
					pgla.user_promoter_id 	as pgla_user_promoter_id,
					pgla.team_venue_id		as pgla_team_venue_id,
					pgla.day				as pgla_day,
					pgla.name 				as pgla_name,
					pgla.description		as pgla_description,
					pgla.create_time		as pgla_create_time,
					pgla.deactivated		as pgla_deactivated,
					pgla.deactivated_time	as pgla_deactivated_time,
					pgla.auto_approve		as pgla_auto_approve,
					pgla.image				as pgla_image,
					pgla.x0					as pgla_x0,
					pgla.y0					as pgla_y0,
					pgla.x1					as pgla_x1,
					pgla.y1					as pgla_y1,
					pgla.auto_promote		as pgla_auto_promote,
					
					
					
					
					
					
					pgla.min_age			as pgla_min_age,
					pgla.door_open			as pgla_door_open,
					pgla.door_close			as pgla_door_close,
					pgla.regular_cover		as pgla_regular_cover,
					pgla.gl_cover			as pgla_gl_cover,
					pgla.additional_info_1	as pgla_additional_info_1,
					pgla.additional_info_2	as pgla_additional_info_2,
					pgla.additional_info_3	as pgla_additional_info_3,
					
					
					
					
					
					tv.name					as tv_name,
					tv.id					as tv_id,
					tv.description			as tv_description,
					c.name 					as c_name,
					c.state 				as c_state,
					c.url_identifier 		as c_url_identifier,
					up.public_identifier 	as up_public_identifier,
					up.profile_image		as up_profile_image,
					c.id 					as c_id,
					c.name 					as c_name,
					c.state 				as c_state,
					c.url_identifier		as c_url_identifier,
					u.first_name			as u_first_name,
					u.full_name				as u_full_name
				
				FROM 	promoters_guest_list_authorizations pgla
				
				JOIN	team_venues tv
				ON 		pgla.team_venue_id = tv.id
				
				JOIN 	users_promoters up 
				ON 		pgla.user_promoter_id = up.id 
				
				JOIN	users u 
				ON 		up.users_oauth_uid = u.oauth_uid
				
				JOIN 	teams t 
				ON 		tv.team_fan_page_id = t.fan_page_id 
				
				JOIN 	cities c 
				ON 		t.city_id = c.id
				
				WHERE	pgla.id = ?
				AND
						pgla.user_promoter_id = ?";
		$query = $this->db->query($sql, array('id' => $pgla_id, 'user_promoter_id' => $promoter_id));
			
		return $query->row();
		
	 }
	 
	/**
	 * Retrieves a single promoter guest list reservation
	 * 
	 * @param	int (promoter_id)
	 * @param	int (pglr_id)
	 * @return 	object
	 */
	function retrieve_pglr($promoter_id, $pglr_id){
				
		$sql = "SELECT 
					
					pglr.user_oauth_uid 	as pglr_user_oauth_uid,
					pglr.approved 			as pglr_approved,
					pglr.text_message 		as pglr_text_message,
					pglr.share_facebook 	as pglr_share_facebook,
					pglr.request_msg		as pglr_request_msg,
					pglr.response_msg		as pglr_response_msg,
					pglr.host_message		as pglr_host_message,
					pglr.id					as pglr_id,
					pgla.id 				as pgla_id,
					pgla.user_promoter_id 	as pgla_user_promoter_id,
					pgla.team_venue_id		as pgla_team_venue_id,
					pgla.day				as pgla_day,
					pgla.name 				as pgla_name,
					pgla.description		as pgla_description,
					pgla.create_time		as pgla_create_time,
					pgla.deactivated		as pgla_deactivated,
					pgla.deactivated_time	as pgla_deactivated_time,
					pgla.auto_approve		as pgla_auto_approve,
					pgla.image				as pgla_image,
					tv.name					as tv_name,
					tv.id					as tv_id,
					tv.description			as tv_description
				
				FROM 	promoters_guest_lists_reservations pglr
				
				JOIN 	promoters_guest_lists pgl
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN 	team_venues	tv
				ON 		pgla.team_venue_id = tv.id
				
				WHERE 	pglr.id = ?
				AND 	
						pgla.user_promoter_id = ?";
				
		$query = $this->db->query($sql, array($pglr_id, $promoter_id));	
		$result = $query->row();
		
		$sql = "SELECT
					
					oauth_uid
				
				FROM 	promoters_guest_lists_reservations_entourages pglre
				
				WHERE 	pglre.promoters_guest_lists_reservations_id = ?";
		$query = $this->db->query($sql, array($result->pglr_id));
		
		$result2 = $query->result();
		$entourage = array();
		foreach($result2 as $ent){
			$entourage[] = $ent->oauth_uid;
		}
		
		$result->entourage = $entourage;
		
		return $result;
		
	}
	
	/**
	 * Retrieves all users (facebook-ids) on a specific guest list
	 * 
	 * @param	guest list identifier
	 * @return	array
	 */
	function retrieve_promoter_guest_list_members($guest_list_id){
				
		$sql = "(SELECT	pglr.user_oauth_uid 	as users_oauth_uid
					FROM	promoters_guest_lists pgl
					
					JOIN	promoters_guest_lists_reservations pglr
					ON		pglr.promoter_guest_lists_id = pgl.id
					
					WHERE	pgl.id = $guest_list_id)
				
				UNION 
				
				(SELECT	pglre.oauth_uid 	as users_oauth_uid
					FROM	promoters_guest_lists pgl
					
					JOIN	promoters_guest_lists_reservations pglr
					ON		pglr.promoter_guest_lists_id = pgl.id
					
					JOIN	promoters_guest_lists_reservations_entourages pglre
					ON		pglre.promoters_guest_lists_reservations_id = pglr.id
					
					WHERE	pgl.id = $guest_list_id)";
		
		$query = $this->db->query($sql);
		return $query->result();
		
	}
	
	/**
	 * retrieves guest lists for a given promoter and a given day
	 * 
	 * @param	string (promoter public identifier)
	 * @param	string (current day of the week)
	 * @return	array (results)
	 */
	function retrieve_day_guest_lists($promoter_id, $weekday = false){

		$sql = "SELECT DISTINCT
		
					pgla.id						as pgla_id,
					pgla.team_venue_id			as pgla_team_venue_id,
					pgla.day 					as pgla_day,
					pgla.name					as pgla_name,
					pgla.image 					as pgla_image,
					tv.name 					as tv_name,
					c.id						as c_id,
					c.name 						as c_name,
					c.state 					as c_state,
					c.timezone_identifier		as c_timezone_identifier,
					c.url_identifier 			as c_url_identifier
					
				FROM	users_promoters up
				
				JOIN	promoters_teams	pt
				ON		up.id = pt.promoter_id
				
				JOIN	teams t
				ON 		pt.team_fan_page_id = t.fan_page_id

				JOIN 	teams_venues_pairs tvp 
				ON 		tvp.team_fan_page_id = t.fan_page_id

				JOIN 	team_venues tv
				ON 		tvp.team_venue_id = tv.id
				
				JOIN 	cities c 
				ON 		tv.city_id = c.id

				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgla.team_venue_id = tv.id
				
				WHERE	pgla.user_promoter_id = ? ";
				//		AND up.id = ? ";
				
				if($weekday)
					$sql .= "AND pgla.day = ? ";
				
				$sql .=	"AND pgla.deactivated = 0
						AND pgla.event = 0
						AND pt.approved = 1
						AND pt.banned = 0
						AND pt.quit = 0
						AND tv.banned = 0
						AND tvp.deleted = 0
						AND t.completed_setup = 1 ";
		$query = $this->db->query($sql, array($promoter_id, $promoter_id, $weekday));
		$results = $query->result();
		
	//	Kint::dump($this->db->last_query());
	//	Kint::dump($results);
		
		//TODO: Check to see if there's an event that over-rides it on this day
		#
		#
		#
		#
		
		
		//we also need the id of this current-week's guest list
		foreach($results as &$res){
			
			$sql = "SELECT	pgl.id 		as id
						
					FROM	promoters_guest_lists pgl
					
					WHERE	pgl.date >= '" . date('Y-m-d', time()) . "' 
					AND		pgl.promoters_guest_list_authorizations_id = $res->pgla_id";
			$query = $this->db->query($sql);
					
			if($id_row = $query->row()){
				
				$sql = "(SELECT

									pglr.user_oauth_uid		as oauth_uid
						
						FROM		promoters_guest_lists_reservations pglr
						LEFT JOIN 	promoters_guest_lists_reservations_entourages pglre
						ON 			pglr.id = pglre.promoters_guest_lists_reservations_id
						
						JOIN 		promoters_guest_lists pgl
						ON 			pglr.promoter_guest_lists_id = pgl.id
						
						WHERE 		pgl.id = $id_row->id AND pglr.approved = 1)
						
						UNION
						
						(SELECT
						
									pglre.oauth_uid		as oauth_uid
						
						FROM		promoters_guest_lists_reservations pglr
						LEFT JOIN 	promoters_guest_lists_reservations_entourages pglre
						ON 			pglr.id = pglre.promoters_guest_lists_reservations_id
						
						JOIN 		promoters_guest_lists pgl
						ON 			pglr.promoter_guest_lists_id = pgl.id
						
						WHERE 		pgl.id = $id_row->id AND pglr.approved = 1 AND pglre.oauth_uid IS NOT NULL)";
				$query = $this->db->query($sql);
				$res->promoters_guest_list_id = $query->result();
												
			}else{
				
				$res->promoters_guest_list_id = false;
				
			}
				
		}
		
		return $results;
	}
	
	/**
	 * Retrieves the weekday that the guest list for pgla_id matches and also verifies that pgla_id belongs to the current promoter
	 * 
	 * @param	int (pgla_id)
	 * @param 	int (promoter_id)
	 * @return 	array
	 */
	function retrieve_plga_day_promoter_check($pgla_id, $promoter_id){
				
		$sql = "SELECT
		
					pgla.day 			as pgla_day,
					pgla.create_time 	as pgla_create_time
		
				FROM 	promoters_guest_list_authorizations pgla
				
				WHERE 	pgla.id = $pgla_id
						AND 	pgla.user_promoter_id = $promoter_id";
		$query = $this->db->query($sql);	
		return $query->row();
		
	}
	
	/**
	 * Retrieves the list of all oauth_uid's of users that have joined a 'current' guest list
	 * 
	 * @param	id of 'promoters_guest_list_authorizations'
	 * @return	array
	 * */
	function retrieve_single_guest_list_and_guest_list_members($promoters_guest_list_authorizations_id, $pgla_day, $index = false, $pgla_create_time = 0){
			
		if($index !== false){
			
			if($index < 0)
				$index = 0;
			if($index > 500)
				$index = 500;
			
			$guest_list_date = date('Y-m-d', strtotime('next ' . rtrim($pgla_day, 's') . '-' . $index . ' weeks'));
			
			//before guest list was ever created?
			if($guest_list_date < date('Y-m-d', $pgla_create_time)){
				return false;
			}
			
		}
			
		$sql = "SELECT 
		
					pglr.user_oauth_uid 	as head_user,
					pglr.create_time 		as time,
					pglr.id					as id,
					pglr.approved			as pglr_approved,
					pglr.table_request 		as pglr_table_request,
					pglr.request_msg 		as pglr_request_msg,
					pglr.response_msg		as pglr_response_msg,
					pglr.host_message		as pglr_host_message,
					pglr.table_min_spend	as table_min_spend,
					pglr.manual_add			as pglr_manual_add,
					pglr.supplied_name		as pglr_supplied_name
 						
				FROM 	promoters_guest_lists pgl
				
				JOIN 	promoters_guest_lists_reservations pglr
				ON 		pgl.id = pglr.promoter_guest_lists_id
				
				WHERE 	pgl.promoters_guest_list_authorizations_id = $promoters_guest_list_authorizations_id ";
		if($index === false){
			
			$sql .= "AND 	pgl.date >= '" . date('Y-m-d', time()) . "'";
			
		}else{
					
			$sql .= "AND 	pgl.date = '" . $guest_list_date . "'";
			
		}
		
		$sql .= " ORDER BY pglr.create_time ASC";
				
		$query = $this->db->query($sql);
		$result = $query->result();
		//this gives us all of the head users, for every head user find all the entourage users
				
		foreach($result as &$res){
			
			$sql = "SELECT 	
			
						pglre.oauth_uid 	as pglre_oauth_uid,
						pglre.supplied_name	as pglre_supplied_name
						
					FROM 	promoters_guest_lists_reservations_entourages pglre
					
					WHERE 	pglre.promoters_guest_lists_reservations_id = $res->id";
			$query = $this->db->query($sql);
			
			$entourage_users = array();
			foreach($query->result() as $entourage_user_object){
							
				$entourage_users[] = $entourage_user_object;
					
			//	if($entourage_user_object->entourage_user !== NULL && $entourage_user_object->entourage_user != 0)
			//		$entourage_users[] = $entourage_user_object->entourage_user;
			//	else 
			//		$entourage_users[] = $entourage_user_object->pglr_supplied_name;
				
				
				
			}
			$res->entourage_users = $entourage_users;

			
			
			if(isset($res->head_user) && $res->head_user !== NULL){
				//ALSO ATTACH PHONE NUMBER FOR HEAD USER
				$sql = "SELECT
				
							u.phone_number as u_phone_number
						
						FROM 	users u 
						
						WHERE 	u.oauth_uid = ?";
				$query2 = $this->db->query($sql, array($res->head_user));
				$result2 = $query2->row();
				
				if(isset($result2->u_phone_number)){
					$res->u_phone_number = $result2->u_phone_number;
				}else{
					$res->u_phone_number = '';
				}

			}else{
				
				$res->u_phone_number = '';
				
			}
			
			
		}		
		
		return $result;
				
	}
	
	/**
	 * Retrieves all members on a guest list for all guest lists over the trailing 7 days
	 * 
	 * @param	bool (if true, don't return cached result)
	 * @return	array
	 * */
	function retrieve_all_members_for_all_guest_lists($cache = false){
		
		//cache bypass if this value is stored in memcache
//		$this->load->library('library_memcached', '', 'memcached');
//		if($cache && ($results = $this->memcached->get('retrieve_all_members_for_all_guest_lists')))
//			return $results;
		$this->load->library('Redis', '', 'redis');
		if($cache && ($results = $this->redis->get('retrieve_all_members_for_all_guest_lists')))
			return $results;
		
		//value not stored in memcache, query DB
		$sql = "SELECT venue, COUNT(*) as count
				FROM (
					(SELECT dv.venue as venue, pglr.users_oauth_uid as user_id, pgl.date as guest_list_date
					FROM promoters_guest_lists pgl
					JOIN users_promoters_guest_list_authorizations upgla 
					ON pgl.users_promoters_guest_list_authorizations_id = upgla.id
					JOIN promoters_guest_lists_reservations pglr
					ON pgl.id = pglr.promoters_guest_lists_id
					JOIN promoters_venues pv
					ON pv.id = upgla.promoters_venues_id
					JOIN data_venues dv
					ON dv.id = pv.venue_id
					WHERE upgla.deactivated = 0)
				
					UNION ALL
				
					(SELECT dv.venue as venue, pglre.oauth_uid as user_id, pgl.date as guest_list_date
					FROM promoters_guest_lists pgl
					JOIN users_promoters_guest_list_authorizations upgla 
					ON pgl.users_promoters_guest_list_authorizations_id = upgla.id
					JOIN promoters_guest_lists_reservations pglr
					ON pgl.id = pglr.promoters_guest_lists_id
					JOIN promoters_guest_lists_reservations_entourages pglre
					ON pglr.id = pglre.promoters_guest_lists_reservations_id
					JOIN promoters_venues pv
					ON pv.id = upgla.promoters_venues_id
					JOIN data_venues dv
					ON dv.id = pv.venue_id
					WHERE upgla.deactivated = 0)
					ORDER BY 1) t
				GROUP BY t.venue;";
		$query = $this->db->query($sql);
		
		//Only save this query result if caching is true, don't save if false to avoid using up cache memory
		if($cache){
			$this->redis->set('retrieve_all_members_for_all_guest_lists', $query->result());
			$this->redis->expire('retrieve_all_members_for_all_guest_lists', 1200); //cache 10 minutes
		}
		
		return $query->result();
	}
	
	/**
	 * Retrieves all users on guest lists for all promoters for the trailing 7 days
	 * 
	 * @param	bool (if true, don't return cached result)
	 * @return	array
	 * */
	function retrieve_all_members_for_all_promoters($cache = false){
		
		//cache bypass if this value is stored in memcache
		$this->load->library('Redis', '', 'redis');
		if($cache && ($results = $this->redis->get('retrieve_all_members_for_all_promoters')))
			return $results;
		
		//value not stored in memcache, query DB
		$sql = "SELECT promoter_name, COUNT(*) as count
				FROM ((SELECT ubd.full_name as promoter_name, pglr.users_oauth_uid as guest_list_member, pgl.date as guest_list_date
					FROM users u
					JOIN users_basic_data ubd
					ON ubd.users_id = u.id
					JOIN users_promoters up
					ON up.users_id = u.id
					JOIN promoters_venues pv
					ON pv.promoter_id = up.id
					JOIN users_promoters_guest_list_authorizations upgla
					ON upgla.promoters_venues_id = pv.id
					JOIN promoters_guest_lists pgl
					ON pgl.users_promoters_guest_list_authorizations_id = upgla.id
					JOIN promoters_guest_lists_reservations pglr
					ON pgl.id = pglr.promoters_guest_lists_id
					JOIN data_venues dv
					ON dv.id = pv.venue_id
					WHERE upgla.deactivated = 0)
			
					UNION ALL
			
					(SELECT ubd.full_name as promoter_name, pglre.oauth_uid as guest_list_member, pgl.date as guest_list_date
					FROM users u
					JOIN users_basic_data ubd
					ON ubd.users_id = u.id
					JOIN users_promoters up
					ON up.users_id = u.id
					JOIN promoters_venues pv
					ON pv.promoter_id = up.id
					JOIN users_promoters_guest_list_authorizations upgla
					ON upgla.promoters_venues_id = pv.id
					JOIN promoters_guest_lists pgl
					ON pgl.users_promoters_guest_list_authorizations_id = upgla.id
					JOIN promoters_guest_lists_reservations pglr
					ON pgl.id = pglr.promoters_guest_lists_id
					JOIN promoters_guest_lists_reservations_entourages pglre
					ON pglre.promoters_guest_lists_reservations_id = pglr.id
					JOIN data_venues dv
					ON dv.id = pv.venue_id
					WHERE upgla.deactivated = 0)
					ORDER BY 1) t
				GROUP BY t.promoter_name;";
		$query = $this->db->query($sql);
		
		//Only save this query result if caching is true, don't save if false to avoid using up cache memory
		if($cache){
			$this->redis->set('retrieve_all_members_for_all_promoters', $query->result()); //cache 5 minutes
			$this->redis->expire('retrieve_all_members_for_all_promoters', 1200);
		}
		
		
		return $query->result();
	}	
	 	
	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Approve or deny a promoter guest list reservation request
	 * 
	 * @param	bool (approve/deny)
	 * @param	int (promoter_id)
	 * @param	int (pglr_id)
	 * @return	null
	 */
	function update_promoter_guest_list_reservation_reject_or_approve($approved, 
																		$promoter_id, 
																		$pglr_id, 
																		$message = '',
																		$approve_override = false){
				
		//retrieve & simultaniously confirm this guest list matches this promoter
		$sql = "SELECT	
		
					pgla.image 				as pgla_image,
					pglr.user_oauth_uid 	as pglr_user_oauth_uid,
					pglr.approved 			as pglr_approved,
					pglr.text_message 		as pglr_text_message,
					pglr.share_facebook 	as pglr_share_facebook,
					pglr.request_msg		as pglr_request_msg,
					pglr.response_msg		as pglr_response_msg,
					pglr.host_message		as pglr_host_message,
					pgl.date 				as pgl_date,
					pgla.name 				as pgla_name,
					pgla.id 				as pgla_id,
					tv.name 				as tv_name,
					tv.image 				as tv_image,
					tv.id					as tv_id,
					up.users_oauth_uid		as up_users_oauth_uid,
					up.public_identifier 	as up_public_identifier,
					up.id					as up_id,
					up.profile_image 		as up_profile_image,
					up.users_oauth_uid		as up_users_oauth_uid,
					u.full_name				as u_full_name,
					u.third_party_id		as u_third_party_id,
					c.name 					as c_name,
					c.url_identifier		as c_url_identifier
		
				FROM 	promoters_guest_lists_reservations pglr
				
				JOIN	promoters_guest_lists pgl
				ON		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN	promoters_guest_list_authorizations pgla
				ON		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN	team_venues tv
				ON		pgla.team_venue_id = tv.id
				
				JOIN	teams t 
				ON 		tv.team_fan_page_id = t.fan_page_id
				
				JOIN	cities c 
				ON 		t.city_id = c.id
				
				JOIN 	users_promoters up 
				ON  	up.id = pgla.user_promoter_id
				
				JOIN 	users u 
				ON 		up.users_oauth_uid = u.oauth_uid
				
				WHERE	pglr.id = ?
				AND		pgla.user_promoter_id = ?";
		$query = $this->db->query($sql, array($pglr_id, $promoter_id));
		$result = $query->row();
			
		if(!$result)
			return false;
			
		if($result->pglr_approved == 1 || $result->pglr_approved == -1)
			return false; //this guest list reservation request has already been approved, don't continue
		
		
		
		
		
		
		//find third_party_id of head user on this guest list
		
		if($result->pglr_user_oauth_uid !== NULL){
			$this->db->select('third_party_id');
			$query = $this->db->get_where('users', array('oauth_uid' => $result->pglr_user_oauth_uid));
			$temp = $query->row();
			
			if(isset($temp->third_party_id)){
				$head_user_third_party_id = $temp->third_party_id;
			}else{
				$head_user_third_party_id = NULL;
			}
			
			unset($temp);
		}else{
			$head_user_third_party_id = NULL;
		}
		
		
		
		//update record, after we recieve original data (necessary for this to work...)
		$this->db->where('id', $pglr_id);
		$this->db->update('promoters_guest_lists_reservations', array('approved' => (($approved) ? 1 : -1),
																		'response_msg' => $message));	
		
		//get entourage count
		$sql = "SELECT
					count(*) 	as count
				FROM 	promoters_guest_lists_reservations_entourages pglre
				WHERE	pglre.promoters_guest_lists_reservations_id = $pglr_id";
		$query = $this->db->query($sql);
		$entourage_result = $query->row();
		
		if($approved && !$approve_override){
			
			//Create notification for this user's vc_friends
			$this->load->model('model_users', 'users', true);
			$result->entourage_count = $entourage_result->count;
			$this->users->create_user_notifications($result->pglr_user_oauth_uid, 'join_promoter_guest_list', $result);			


			//EMAIL USERS FRIENDS THAT THEY"RE ON THE GL
			$this->load->helper('run_gearman_job');
			run_gearman_job('gearman_email_friends_gl_join', array(
			
				'type'		=> 'promoter',
				'gl_query'	=> json_encode($result)
								
			), false);


			
		}
		
		if($result->pglr_text_message){
				
			if($approved)
				$text_message = "VibeCompass: Request Approved. Msg: " . $message; //"You have been approved to join \"$result->pgla_name\" at $result->tv_name on $result->pgl_date";
			else
				$text_message = "VibeCompass: Request Declined. Msg: " . $message; //"Your request to join \"$result->pgla_name\" at $result->tv_name on $result->pgl_date has been denied.";
						
			# ---------------------------------------------------------- #
			#	Send text message job to gearman as background job		 #
			# ---------------------------------------------------------- #	
			//NOTE: We don't need to retrieve the results of this job... just launch it.			
			//add job to a task
			$this->load->library('pearloader');
			$gearman_client = $this->pearloader->load('Net', 'Gearman', 'Client');
						
			/* ---------- notify all VC friends of this user that they have joined VibeCompass --------- */
			$gearman_task = $this->pearloader->load('Net', 'Gearman', 'Task', array('func' => 'guest_list_text_message',
																					'arg'  => array('user_oauth_uid' => $result->pglr_user_oauth_uid,
																									'text_message' => $text_message)));
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
		
		if($result->pglr_share_facebook && $approved){

			# ---------------------------------------------------------- #
			#	Send share facebook job to gearman as background job	 #
			# ---------------------------------------------------------- #	
			//NOTE: We don't need to retrieve the results of this job... just launch it.			
			//add job to a task
			$this->load->library('pearloader');
			$gearman_client = $this->pearloader->load('Net', 'Gearman', 'Client');
						
			/* ---------- notify all VC friends of this user that they have joined VibeCompass --------- */
			$gearman_task = $this->pearloader->load('Net', 'Gearman', 'Task', array('func' => 'guest_list_share_facebook',
																					'arg'  => array('team_guest_list' 		=> false,
																									'user_oauth_uid' 		=> $result->pglr_user_oauth_uid,
																									'user_third_party_id' 	=> $head_user_third_party_id,
																									'venue_name' 			=> $result->tv_name,
																									'date' 					=> $result->pgl_date,
																									'guest_list_name'		=> $result->pgla_name,
																									'image' 				=> $result->pgla_image,
																									'promoter_full_name' 			=> $result->u_full_name,
																									'promoter_public_identifier' 	=> $result->up_public_identifier)));
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

		//send pusher notification to user
		if(!$approve_override){
			
			$this->load->library('Pusher');
			$payload = new stdClass;
			$payload->notification_type = 'request_response';
			$payload->promoter_name = $result->u_full_name;
			$payload->response = ($approved) ? 'approved' : 'declined';
			$payload->guest_list_name = $result->pgla_name;
			$payload->response_message = $message;
			
			$this->load->model('model_users', 'users', true);
			$insert_id = $this->users->create_user_sticky_notification($result->pglr_user_oauth_uid, $result->pglr_user_oauth_uid, json_encode($payload));
			$payload->id = $insert_id;
			
			$pusher_channel = 'private-vc-' . $result->pglr_user_oauth_uid;
			$this->pusher->trigger($pusher_channel, 'notification', $payload);
			
			//tell admin dashboard that requests have changed
			$this->pusher->trigger('private-' . $result->up_users_oauth_uid, 'pending-requests-change', null);
			
		}
		
		return true;
		
	}
	
	/**
	 * Update the notes on a guest list reservation for the host/hostess at the door
	 * 
	 * @param	int (promoter id)
	 * @param	int (pglr id)
	 * @param	string (host message)
	 * @return 	array
	 */
	function update_promoter_reservation_host_notes($promoter_id, $pglr_id, $host_message){
		
		//retrieve & simultaniously confirm this guest list matches this promoter
		$sql = "SELECT	
		
					pgla.image 				as pgla_image,
					pglr.user_oauth_uid 	as pglr_user_oauth_uid,
					pglr.approved 			as pglr_approved,
					pglr.text_message 		as pglr_text_message,
					pglr.share_facebook 	as pglr_share_facebook,
					pglr.request_msg		as pglr_request_msg,
					pglr.response_msg		as pglr_response_msg,
					pglr.host_message		as pglr_host_message,
					pgl.date 				as pgl_date,
					pgla.name 				as pgla_name,
					pgla.id 				as pgla_id,
					tv.name 				as tv_name,
					up.public_identifier 	as up_public_identifier,
					up.id					as up_id,
					up.users_oauth_uid		as up_users_oauth_uid,
					u.full_name				as u_full_name
		
				FROM 	promoters_guest_lists_reservations pglr
				
				JOIN	promoters_guest_lists pgl
				ON		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN	promoters_guest_list_authorizations pgla
				ON		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN	team_venues tv
				ON		pgla.team_venue_id = tv.id
				
				JOIN 	users_promoters up 
				ON  	up.id = pgla.user_promoter_id
				
				JOIN 	users u 
				ON 		up.users_oauth_uid = u.oauth_uid
				
				WHERE	pglr.id = ?
				AND		pgla.user_promoter_id = ?";
		$query = $this->db->query($sql, array($pglr_id, $promoter_id));
		$result = $query->row();
			
		if(!$result)
			return array('success' => false);
			
		//update record, after we recieve original data (necessary for this to work...)
		$this->db->where('id', $pglr_id);
		$this->db->update('promoters_guest_lists_reservations', array('host_message' => $host_message));
		
		return array('success' => true);
		
	}
	
	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
}
	

/* End of file model_guest_lists.php */
/* Location: application/models/model_guest_lists.php */