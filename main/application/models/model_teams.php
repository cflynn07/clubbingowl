<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Teams correlate to facebook-fan pages that add our application
 * 
 * */
class Model_teams extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
	
	/*-------------------------------------------------------------------------
	 |	Create Methods (create)
	 | ------------------------------------------------------------------------ */
	
	
	
	
	
	
	
	
	
	
	function upgrade_check($team_fan_page_id){
		
		$this->load->model('model_team_messaging');
		$team_chat_members = $this->team_messaging->retrieve_team_members(array('teams_fan_page_id' => $team_fan_page_id));
		
		$sum = count($team_chat_members->managers) + count($team_chat_members->promoters) + count($team_chat_members->hosts);
		
		if($sum <= 5){
			//tier 0
			$new_tier = 0;
			
		}elseif($sum <= 10){
			//tier 1 (6 - 10)
			$new_tier = 1;
			
		}elseif($sum <= 15){
			//tier 2 (11 - 15)
			$new_tier = 2;
			
		}else{
			//tier 3 (16 - infinite)
			$new_tier = 3;
			
		}
		
		
		//what's the current tier?
		$this->db->select('billing_tier')
			->from('teams')
			->where(array(
				'fan_page_id' => $team_fan_page_id
			));
		$query = $this->db->get();
		$current_billing_tier = $query->row()->billing_tier;
		
		
		if($new_tier > intval($current_billing_tier)){
			//perform upgrade
			
			$this->db->where(array(
				'fan_page_id' => $team_fan_page_id
			))->update('teams', array(
				'billing_tier' => $new_tier
			));
			
		}
		
	}
	function downgrade_check($team_fan_page_id){
		
		$this->load->model('model_team_messaging');
		$team_chat_members = $this->team_messaging->retrieve_team_members(array('teams_fan_page_id' => $team_fan_page_id));
		
		$sum = count($team_chat_members->managers) + count($team_chat_members->promoters) + count($team_chat_members->hosts);
		
		if($sum <= 5){
			//tier 0
			$new_tier = 0;
			
		}elseif($sum <= 10){
			//tier 1 (6 - 10)
			$new_tier = 1;
			
		}elseif($sum <= 15){
			//tier 2 (11 - 15)
			$new_tier = 2;
			
		}else{
			//tier 3 (16 - infinite)
			$new_tier = 3;
			
		}
		
		
		//what's the current tier?
		$this->db->select('billing_tier')
			->from('teams')
			->where(array(
				'fan_page_id' => $team_fan_page_id
			));
		$query = $this->db->get();
		$current_billing_tier = $query->row()->billing_tier;
		
		if($new_tier < intval($current_billing_tier)){
			//perform downgrade
			
			$this->db->where(array(
				'fan_page_id' => $team_fan_page_id
			))->update('teams', array(
				'billing_tier' => $new_tier
			));
			
		}
		
	}
	
	
	
	
	function create_billable_message($team_fan_page_id, $options = array()){
		
		$this->db->insert('teams_billable_messages', array(
			'team_fan_page_id'	=> $team_fan_page_id,
			'type'				=> $options['type'],
			'date'				=> date('Y-m-d', time())
		));
		
	}
	
	
	
	
	
	
	
	 function retrieve_client_notes($options){
	 	
		$this->db->select('*')
			->from('clients_notes')
			->where($options);
		$query = $this->db->get();
		return $query->result();
		
	 }
	 function update_client_notes($options = array()){
	 	
		//find out if notes exist
		$query = $this->db->get_where('clients_notes', array(
			'user_oauth_uid'		=> $options['users_oauth_uid'],
			'client_oauth_uid'		=> $options['client_oauth_uid'],
			'team_fan_page_id'		=> $options['team_fan_page_id']
		));
		$result = $query->row();
		
		
		if(!$result){
			//notes don't exist yet
			
			$this->db->insert('clients_notes', array(
				'public_notes'		=> $options['public_notes'],
				'private_notes'		=> $options['private_notes'],
				'client_oauth_uid'	=> $options['client_oauth_uid'],
				'user_oauth_uid'	=> $options['users_oauth_uid'],
				'team_fan_page_id'	=> $options['team_fan_page_id']
			));
			
					
		}else{
			//notes exist -- update
			$this->db->where(array(
				'client_oauth_uid'	=> $options['client_oauth_uid'],
				'user_oauth_uid'	=> $options['users_oauth_uid'],
				'team_fan_page_id'	=> $options['team_fan_page_id']
			));
			$this->db->update('clients_notes', array(
				'public_notes'		=> $options['public_notes'],
				'private_notes'		=> $options['private_notes'],
				'team_fan_page_id'	=> $options['team_fan_page_id']
			));
			
		}
		
		
	 }
	
	 function edit_team_venue($fan_page_id, $tv_id, $venue_data){
	 	
		$this->db->where(array(
			'team_fan_page_id'	=> $fan_page_id,
			'id'				=> $tv_id
		))->update('team_venues', $venue_data);
				
		return true;
		
	 }
	 
	 /**
	  * 
	  */
	 function create_team_announcement($data){
	 	
		/* --------- CONFIGURATION SETTINGS --------- *
		 *	Test for required form fields
		 * 
		 * */
		$required_indexes = array(
								'manager_oauth_uid',
								'message',
								'team_fan_page_id'
								);
								
		foreach($required_indexes as $value){
			if(!array_key_exists($value, $data))
				die('missing required key: ' . $value);
		}
		
		
		$this->db->insert('manager_announcements', array(
			'manager_oauth_uid' 	=> $data['manager_oauth_uid'],
			'message' 				=> $data['message'],
			'created' 				=> time(),
			'team_fan_page_id'		=> $data['team_fan_page_id'],
			'type'					=> $data['type']
		));
		
		if($data['type'] == 'regular'){
			$this->load->helper('run_gearman_job');
			run_gearman_job('gearman_send_sms_mass_text_team_announcements', array(
				'message'			=> $data['message'],
				'manager_oauth_uid' => $data['manager_oauth_uid'],
				'team_fan_page_id' 	=> $data['team_fan_page_id']
			), false);
		}
		
		return true;
	 }
	 
	 
	 /**
	  * 
	  */
	 function retrieve_team_announcements($data){
	 	
		// -------------------------------
		$required_indexes = array(
								'team_fan_page_id'
								);
								
		foreach($required_indexes as $value){
			if(!array_key_exists($value, $data))
				die('missing required key: ' . $value);
		}
		// -------------------------------
		
		//first look up all managers
	//	$query = $this->db->get_where('managers_teams', array('fan_page_id' => $data['team_fan_page_id'], 'banned' => 0));
	//	$result = $query->result();
		
	//	$manager_oauth_uids = array();
	//	foreach($result as $res){
	//		$manager_oauth_uids[] = $res->user_oauth_uid;
	//	}
		
		
		
		//add promoters to mix
	//	$this->db->select('up.users_oauth_uid as user_oauth_uid')
	//		->from('users_promoters up')
	//		->join('promoters_teams pt', 'pt.promoter_id = up.id')
	//		->where(array(
	//			'pt.team_fan_page_id' => $data['team_fan_page_id']
	//		));
	//	$query = $this->db->get();
	//	$result = $query->result();
	//	foreach($result as $res){
	//		$manager_oauth_uids[] = $res->user_oauth_uid;
	//	}
		
		
	//	if(!$manager_oauth_uids)
	//		return array();
		
		
		//now retrieve all messages with these managers
		$sql = "SELECT
				
					*
				
				FROM 	manager_announcements ma 
				
				WHERE 	team_fan_page_id = " . $data['team_fan_page_id'];
	//	foreach($manager_oauth_uids as $ma){
	//		$sql .= "ma.manager_oauth_uid = ? || ";
	//	}
	//	$sql = rtrim($sql, ' || ');
		
		$sql .= " ORDER BY ma.created DESC";
		
		$query = $this->db->query($sql);
		
		
		return $query->result();
		
	 }
	 
	 
	 
	 
	 
	 
	 /**
	  * Creates a new team/fan page in the database
	  * 
	  * @param	array (team record fields)
	  * @return	num affected rows (1 or 0)
	  */
	 function create_team($data){
	 	/* --------- CONFIGURATION SETTINGS --------- *
		 *	Test for required form fields
		 * 
		 * */
		$required_indexes = array(
								'team_name',
								'team_primary_city',
								'team_description',
								'fan_page_id'
								);
								
		foreach($required_indexes as $value){

			if(!array_key_exists($value, $data))
				die('model_teams->create_team missing required key: ' . $value);

		}
		/* --------- END CONFIGURATION SETTINGS --------- */
	 	$team_data['name'] = $data['team_name'];
		$team_data['city_id'] = $data['team_primary_city'];
		$team_data['description'] = $data['team_description'];
		$team_data['fan_page_id'] = $data['fan_page_id'];
	 	
	 	$this->db->insert('teams', $team_data);
	 	return $this->db->affected_rows();
	 }
	 
	 /**
	  * Creates a record in managers_teams to associate a user/manager with a team
	  * 
	  * @param	int (user oauth_uid)
	  * @param	int (team fan_page_ids)
	  * @return	bool
	  */
	 function create_managers_teams($user_oauth_uid, $fan_page_id){
	 	
		$this->db->insert('managers_teams', array('user_oauth_uid' => $user_oauth_uid,
													'fan_page_id' => $fan_page_id));
		return $this->db->affected_rows();
		
	 }
	 
	 /**
	  * Creates 'venues' and associates them with a team
	  * 
	  * @param	int (fan_page_id)
	  * @param	array (venues)
	  * @return	bool
	  */
	function create_team_venues($fan_page_id, $venues){
		
		foreach($venues as $venue){
			
			$data = array(
						'team_fan_page_id' => $fan_page_id,
						'name' => $venue['name'],
						'description' => $venue['description'],
						'street_address' => $venue['street_address'],
						'state' => $venue['state'],
						'city' => $venue['city'],
						'zip' => $venue['zip'],
						'image' => $venue['image']
						);
			
			$this->db->insert('team_venues', $data);
			
		}
		
		//will only return the last one for a mutli-insert, we just want it for the situations where we insert 1
		return $this->db->insert_id();
		
	}
	
	/**
	 * Creates a new floor at a team venue
	 * 
	 * @param	string (floor name)
	 * @param 	int (team venue id)
	 * @return 	int (insert_id)
	 */
	function create_team_venues_floors($floor_name, $team_venue_id){
		
		$this->db->insert('venues_layout_floors', array('floor_name' => $floor_name, 'team_venue_id' => $team_venue_id));
		return $this->db->insert_id();
		
	}
	
	/**
	 * Creates an item that doesn't already exist on a venue floor
	 * 
	 * @param	int 
	 * @param	string
	 * @param 	int
	 * @param 	int
	 * @param 	int
	 * @param 	int
	 * @param 	string
	 * @param	string
	 * @return	int (insert_id)
	 */
	function create_team_venues_floors_item($vlf_id, $item_type, $pos_x, $pos_y, $width, $height, $image){
		
		$data = array(
			'venues_layout_floor_id' 	=> $vlf_id,
			'item_type'					=> $item_type,
			'pos_x'						=> $pos_x,
			'pos_y'						=> $pos_y,
			'width'						=> $width,
			'height'					=> $height,
			'image' 					=> $image
		);
		$this->db->insert('venues_layout_floors_items', $data);
		return $this->db->insert_id();
		
	}
	
	/**
	 * Creates a table record linked to a vlfi record
	 * 
	 * @return	int (insert_id)
	 */
	function create_team_venues_floors_item_table($vlfi_id, 
													$title,
													$monday_min,
													$tuesday_min,
													$wednesday_min,
													$thursday_min,
													$friday_min,
													$saturday_min,
													$sunday_min,
													$capacity){
		
		
		$title 			= strip_tags(trim($title));
		$monday_min 	= strip_tags(trim(preg_replace('/\D/', '', $monday_min)));
		$tuesday_min 	= strip_tags(trim(preg_replace('/\D/', '', $tuesday_min)));
		$wednesday_min 	= strip_tags(trim(preg_replace('/\D/', '', $wednesday_min)));
		$thursday_min 	= strip_tags(trim(preg_replace('/\D/', '', $thursday_min)));
		$friday_min 	= strip_tags(trim(preg_replace('/\D/', '', $friday_min)));
		$saturday_min 	= strip_tags(trim(preg_replace('/\D/', '', $saturday_min)));
		$sunday_min		= strip_tags(trim(preg_replace('/\D/', '', $sunday_min)));
		
		$data = array(
			'venues_layout_floors_items_id' 	=> $vlfi_id,
			'title'								=> $title,
			'capacity'							=> $capacity,
			'monday_min'						=> $monday_min,
			'tuesday_min'						=> $tuesday_min,
			'wednesday_min'						=> $wednesday_min,
			'thursday_min'						=> $thursday_min,
			'friday_min'						=> $friday_min,
			'saturday_min'						=> $saturday_min,
			'sunday_min'						=> $sunday_min
		);
		$this->db->insert('venues_layout_floors_items_tables', $data);		
		return 	$this->db->insert_id();		
	}
	
	/**
	 * Creates an invitation for a user to be a promoter of a team
	 * 
	 * @param 	int (team fan page id)
	 * @param	array (user oauth uids)
	 * @param 	int (manager_oauth_uid)
	 * @param 	string ('promoter' || 'manager' || 'host')
	 * @return 	bool
	 */
	function create_team_invitations($team_fan_page_id, $users, $manager_oauth_uid, $type){
		
		foreach($users as $user){
			$data[] = array(
							'oauth_uid' 					=> $user,
							'invitation_team_fan_page_id' 	=> $team_fan_page_id,
							'manager_oauth_uid' 			=> $manager_oauth_uid,
							'invitation_time' 				=> time(),
							'invitation_type'				=> $type
							);
		}
		
		$this->db->insert_batch('user_invitations', $data);
		
		return true;
	}
	
	/**
	 * Creates an invitation for a user to be a host of a team
	 * 
	 * @param	int (team fan page id)
	 * @param 	array (user_oauth_uids)
	 * @param	int (manager_oauth_uids)
	 * @return	bool
	 */
	function create_team_promoter_invitations($team_fan_page_id, $users, $manager_oauth_uid){
		
		$invitation = stdClass;
		$invitation->type = 'host';
		
		foreach($users as $user){
			$data[] = array(
							'oauth_uid' => $user,
							'invitation_team_fan_page_id' => $team_fan_page_id,
							'manager_oauth_uid' => $manager_oauth_uid,
							'invitation_time' => time(),
							'invitation_data' => json_encode($invitation)
							);
		}
		
		$this->db->insert_batch('user_invitations', $data);
		
		return true;
	}
	  	
	/*-------------------------------------------------------------------------
	 |	Retrieval Methods (retrieve)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Retrieve all team hosts for a given team (current and inactive)
	 * 
	 * @param	int (team fan page id)
	 * @return	array
	 */
	function retrieve_team_hosts($team_fan_page_id){
		
		$sql = "SELECT
		
					th.users_oauth_uid 		as th_users_oauth_uid,
					th.manager_oauth_uid 	as th_manager_oauth_uid,
					th.time_added			as th_time_added,
					th.banned 				as th_banned,
					th.time_banned 			as th_time_banned,
					th.banned_by 			as th_banned_by,
					th.quit					as th_quit,
					th.quit_time 			as th_quit_time
				
				FROM 	teams_hosts th 
				
				WHERE 	th.teams_fan_page_id = ?";
		$query = $this->db->query($sql, array($team_fan_page_id));
		return $query->result();
		
	}
	
	/**
	 * 
	 */
	function retrieve_team_promoters($fan_page_id, $options = array()){
		
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array(
								'cache' => false,
								'cache_length' => 900, //15 minutes
								'completed_setup' => false,
								'approved' => 'pt.approved = 1 ',
								'banned_quit' => 'AND pt.banned = 0 AND pt.quit = 0 '
								);
		foreach($options as $key => $value){
			//is this a recognized configuration setting?
			if(!array_key_exists($key, $default_options))
				die('model_teams->retrieve_team_promoters: unknown configuration setting - ' . $key);

			//overwrite default configuration value with new one specified in function call
			$default_options[$key] = $value;
		}
		foreach($default_options as $key => $value){
			//turn all default_config keys into local variables
			${$key} = $value;
		}
		/* --------- END CONFIGURATION SETTINGS --------- */
		
		$sql = "SELECT
					
					u.oauth_uid 			as u_oauth_uid,
					u.first_name 			as u_first_name,
					u.last_name 			as u_last_name,
					u.full_name 			as u_full_name,
					u.email 				as u_email,
					u.gender 				as u_gender,
					u.third_party_id 		as u_third_party_id,
					up.id 					as up_id,
					up.completed_setup 		as up_completed_setup,
					up.piwik_id_site 		as up_piwik_id_site,
					up.public_identifier	as up_public_identifier,
					up.biography 			as up_biography,
					up.profile_image 		as up_profile_image,
					up.banned 				as up_banned,
					pt.id					as pt_id
					
				FROM 	users u
				
				JOIN 	users_promoters up 
				ON 		up.users_oauth_uid = u.oauth_uid
				
				JOIN 	promoters_teams pt
				ON 		pt.promoter_id = up.id
				
				JOIN 	teams t 
				ON 		pt.team_fan_page_id = t.fan_page_id
				
				WHERE 	" . $approved . $banned_quit;
				
		if($completed_setup)
				$sql .= "AND 	t.completed_setup = 1 ";
		else
				$sql .= "AND 	t.fan_page_id = $fan_page_id";
		
		$query = $this->db->query($sql);
		return $query->result();
		
	}
	
	/**
	 * Retrieves a team/fan page from the database if it exists
	 * 
	 * @param	int (Facebook page id)
	 * @return	object || false
	 */
	function retrieve_team($fan_page_id, $options = array()){
		
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array(
								'cache' => false,
								'cache_length' => 900, //15 minutes
								'completed_setup' => false,
								'retrieve_venues' => true
								);
		foreach($options as $key => $value){
			//is this a recognized configuration setting?
			if(!array_key_exists($key, $default_options))
				die('model_teams->retrieve_team: unknown configuration setting - ' . $key);

			//overwrite default configuration value with new one specified in function call
			$default_options[$key] = $value;
		}
		foreach($default_options as $key => $value){
			//turn all default_config keys into local variables
			${$key} = $value;
		}
		/* --------- END CONFIGURATION SETTINGS --------- */
		
		$sql = "SELECT
					t.id				as team_id,
					t.fan_page_id		as team_fan_page_id, 
					t.name 				as team_name,
					t.description		as team_description,
					t.image				as team_image,
					t.piwik_id_site 	as team_piwik_id_site
					
				FROM 	teams t				
				WHERE 	t.fan_page_id = $fan_page_id";
				
				if($completed_setup)
					$sql .= " AND	t.completed_setup = 1";
				
		$query = $this->db->query($sql);

		if(!$team = $query->row())
			return false;
				
		$team_data = new stdClass;
		$team_data->team = $team;
		
		if(!$retrieve_venues)
			return $team_data;
		
		/* -------------- retrieve team venues -------------- */
		$sql = "SELECT
					tv.id				as team_venue_id,
					tv.name				as team_venue_name,
					tv.image 			as team_venue_image,
					tv.description		as team_venue_description,
					tv.street_address	as team_venue_street_address,
					tv.monday			as team_venue_monday,
					tv.tuesday			as team_venue_tuesday,
					tv.wednesday		as team_venue_wednesday,
					tv.thursday			as team_venue_thursday,
					tv.friday			as team_venue_friday,
					tv.saturday			as team_venue_saturday,
					tv.sunday			as team_venue_sunday
				
				FROM 	team_venues tv
				WHERE 	tv.team_fan_page_id = $fan_page_id";
		$query = $this->db->query($sql);		
		$team_data->team_venues = $query->result();
		
	//	var_dump($team_data);
		
		return $team_data;
	}
		
		
	/**
	 * Retrieves all supported cities in platform that a team can be associated with
	 * 
	 * @return	array
	 */
	function retrieve_team_cities(){
		
		$sql = "SELECT
					
					c.id							as c_id,
					CONCAT(c.name, ', ', c.state) 	as c_city_state
					
				FROM		cities c 
				
				ORDER BY 	name ASC";
		$query = $this->db->query($sql);
		return $query->result();
		
	}
	
//EXAMPLE BELOW:	 ----------------------------------
	/**
	 * 
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
				die('???: unknown configuration setting - ' . $key);

			//overwrite default configuration value with new one specified in function call
			$default_options[$key] = $value;
		}
		foreach($default_options as $key => $value){
			//turn all default_config keys into local variables
			${$key} = $value;
		}
		/* --------- END CONFIGURATION SETTINGS --------- */
		
		if($cache){
			$this->load->library('Redis', '', 'redis');
			if($result = $this->redis->get('model_app_data->retrieve_num_promoters')){
				return $result;
			}
		}
		
		$sql = "SELECT 	count(*) as count
				FROM	users_promoters";
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
	 * User to check if there is an ourstanding invitation to a specific user from a specific team
	 * Called from library_admin_managers->invitation_create()
	 * 
	 * Oustanding invitation means the user has not accepted/declined and it is not more than 5 days old
	 * 
	 * @param	int (team fan page id)
	 * @param	int (user oauth uid)
	 * @return 	object || false
	 */
	function retrieve_team_invitation($team_fan_page_id, $user_oauth_uid, $type = 'promoter'){
		
		$sql = "SELECT
					
					ui.id							as ui_id,
					ui.oauth_uid					as ui_oauth_uid,
					ui.invitation_team_fan_page_id 	as ui_invitation_team_fan_page_id,
					ui.invitation_data				as ui_invitation_data,
					ui.invitation_time				as ui_invitation_time,
					ui.response						as ui_response,
					ui.invitation_type				as ui_invitation_type
				
				FROM 	user_invitations ui
				
				WHERE 	ui.oauth_uid = ? 
						AND ui.invitation_team_fan_page_id = ? 
						AND ui.response = '0'
						AND ui.invitation_type = ?
						AND ui.invitation_time < (ui.invitation_time + 432000)"; // 5 days
		$query = $this->db->query($sql, array($user_oauth_uid, $team_fan_page_id, $type));
		return $query->row();
		
	}
	
	/**
	 * Retrieves all invitations sent to facebook users from a specific team
	 * 
	 * @param	int (team fan page id)
	 * @param	string (invitation_type)
	 * @return 	array
	 */
	function retrieve_all_team_invitations($team_fan_page_id, $invite_type = 'promoter'){
		
		$sql = "SELECT
					
					ui.id							as ui_id,
					ui.oauth_uid					as ui_oauth_uid,
					ui.invitation_team_fan_page_id 	as ui_invitation_team_fan_page_id,
					ui.invitation_data				as ui_invitation_data,
					ui.invitation_time				as ui_invitation_time,
					ui.response						as ui_response,
					ui.invitation_type 				as ui_invitation_type
					
				FROM 	user_invitations ui
				
				WHERE 	ui.invitation_team_fan_page_id = ?
				AND 	ui.invitation_type = ? 
				
				ORDER BY 	ui.id DESC";
				
		$query = $this->db->query($sql, array($team_fan_page_id, $invite_type));
		return $query->result();
	}
	
	/**
	 * Retrieves a venue and it's associated floorplan
	 * return false if venue_id does not belong to this team
	 * 
	 * @param	venue_id
	 * @param 	team_fan_page_id
	 * @return 	array || false
	 */
	function retrieve_venue_floorplan($venue_id, $team_fan_page_id){
		
		
		
		//check for team-venue association
		$this->db->select('tvp.id')
			->from('teams_venues_pairs tvp')
			->where(array(
				'tvp.team_fan_page_id'	=> $team_fan_page_id,
				'tvp.team_venue_id'		=> $venue_id,
				'tvp.deleted'			=> 0
			));
		$query = $this->db->get();
		$result = $query->row();
		if(!$result)
			return $result;		
		
		
		$sql = "SELECT
					
					tv.id 				as tv_id,
					tv.name 			as tv_name,
					vlf.id 				as vlf_id,
					vlf.deleted 		as vlf_deleted,
					vlf.floor_name		as vlf_floor_name,
					vlfi.id 			as vlfi_id,
					vlfi.deleted		as vlfi_deleted,
					vlfi.item_type		as vlfi_item_type,
					vlfi.pos_x			as vlfi_pos_x,
					vlfi.pos_y			as vlfi_pos_y,
					vlfi.width			as vlfi_width,
					vlfi.height			as vlfi_height,
					vlfi.notes 			as vlfi_notes,
					vlfi.image			as vlfi_image,
					vlfit.id 			as vlfit_id,
					vlfit.deleted 		as vlfit_deleted,
					vlfit.title			as vlfit_title,
					vlfit.capacity 		as vlfit_capacity,
					vlfit.monday_min 	as vlfit_monday_min,
					vlfit.tuesday_min 	as vlfit_tuesday_min,
					vlfit.wednesday_min as vlfit_wednesday_min,
					vlfit.thursday_min 	as vlfit_thursday_min,
					vlfit.friday_min 	as vlfit_friday_min,
					vlfit.saturday_min 	as vlfit_saturday_min,
					vlfit.sunday_min 	as vlfit_sunday_min
				
				FROM 	team_venues tv
				
				LEFT JOIN	venues_layout_floors vlf
				ON 			vlf.team_venue_id = tv.id
				
				LEFT JOIN 	venues_layout_floors_items vlfi
				ON 			vlfi.venues_layout_floor_id = vlf.id
				
				LEFT JOIN	venues_layout_floors_items_tables vlfit
				ON 			vlfit.venues_layout_floors_items_id = vlfi.id
				
				WHERE 	tv.id = ?";
						//AND
						//tv.team_fan_page_id = ?";
			//			AND
			//			(vlf.deleted = 0 || ISNULL(vlf.deleted)) 
			//			AND 
			//			(vlfi.deleted = 0 || ISNULL(vlfi.deleted)) 
			//			AND 
			//			(vlfit.deleted = 0 || ISNULL(vlfit.deleted))";
		$query = $this->db->query($sql, array($venue_id, $team_fan_page_id));
		return $query->result();
		
	}

	/**
	 * Retrieve all of the venue table reservations on a given date
	 * 
	 * @param	team_venue_id
	 * @param	team_fan_page_id
	 * @param 	date
	 * @return	array
	 */
	function retrieve_venue_floorplan_reservations($team_venue_id = false, 
													$team_fan_page_id, 
													$date = false,
													$pglr_id = false,
													$tglr_id = false){
		
		//find tv_id && date
		if($pglr_id){
			
			$sql = "SELECT
			
						pgl.date 									as pgl_date,
						pgla.team_venue_id 							as pgla_team_venue_id,
						pglr.venues_layout_floors_items_table_id 	as pglr_venues_layout_floors_items_table_id,
						pglr.table_request							as pglr_table_request
					
					FROM 	promoters_guest_lists_reservations pglr
					
					JOIN 	promoters_guest_lists pgl
					ON 		pglr.promoter_guest_lists_id = pgl.id
					
					JOIN 	promoters_guest_list_authorizations pgla
					ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
					
					WHERE	pglr.id = ?
								AND
							pglr.approved = 1
								AND
							pglr.manager_table_approved = 0";
			$query = $this->db->query($sql, array($pglr_id));		
			$result = $query->row();
			
			if(!$result)
				return array();
			
			$team_venue_id = $result->pgla_team_venue_id;
			$date = $result->pgl_date;
			
		}elseif($tglr_id){
			
			$sql = "SELECT
			
						tgl.date 									as tgl_date,
						tgla.team_venue_id 							as tgla_team_venue_id,
						tglr.venues_layout_floors_items_table_id 	as tglr_venues_layout_floors_items_table_id,
						tglr.table_request 							as tglr_table_request
					
					FROM 	teams_guest_lists_reservations tglr
					
					JOIN 	teams_guest_lists tgl
					ON 		tglr.team_guest_list_id = tgl.id
					
					JOIN 	teams_guest_list_authorizations tgla
					ON 		tgl.team_guest_list_authorization_id = tgla.id
					
					WHERE	tglr.id = ?
							AND
							tglr.approved = 1";
			$query = $this->db->query($sql, array($tglr_id));	
			$result = $query->row();
			
			if(!$result)
				return array();
			
			$team_venue_id = $result->tgla_team_venue_id;
			$date = $result->tgl_date;
			
		}
		
		//if no pglr_id or tglr_id was specified... return all for the entire team on the specific date
		if(!$team_venue_id)
			return array();
		
		if(!$date && ($tglr_id || $pglr_id))
			return array(); //error?
		
		$sql = "SELECT
					
					vlfit.id 									as vlfit_id,
					vlfit.venues_layout_floors_items_id			as vlfit_vlfi_id,
					pglr.id										as pglr_id,
					pglr.user_oauth_uid 						as pglr_user_oauth_uid,
					pglr.supplied_name							as pglr_supplied_name,
					pglr.host_message 							as pglr_host_message,
					pglr.request_msg							as pglr_request_msg,
					pglr.response_msg 							as pglr_response_msg,
					pglr.approved 								as pglr_approved,
					pglr.create_time 							as pglr_create_time,
					pglr.table_request 							as pglr_table_request,
					pglr.text_message 							as pglr_text_message,
					pglr.share_facebook							as pglr_share_facebook,
					pglr.venues_layout_floors_items_table_id 	as pglr_vlfit_id,
					pgla.day									as pgla_day,
					pgla.image 									as pgla_image,
					pgla.name									as pgla_name,
					tv.name 									as tv_name,
					up.users_oauth_uid 							as up_users_oauth_uid,
					up.profile_image 							as up_profile_image
					
								
				FROM 	promoters_guest_lists_reservations pglr
				
				LEFT JOIN 	venues_layout_floors_items_tables vlfit
				ON 			pglr.venues_layout_floors_items_table_id = vlfit.id
				
				JOIN 	promoters_guest_lists pgl
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN 	promoters_guest_list_authorizations pgla
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN 	team_venues tv
				ON 		pgla.team_venue_id = tv.id
				
				JOIN	users_promoters up
				ON		pgla.user_promoter_id = up.id
				
				WHERE 	pgla.team_venue_id = ?
						AND
						tv.team_fan_page_id = ?
						AND
						pglr.approved = 1
						AND ";
		$future_date_only = false;
		if($date){
			$sql .= "pgl.date = ?";
		}else{
			$future_date_only = true;
			$sql .= "pgl.date >= ?";
			$date = date('Y-m-d', time());//today and the future ;)
		}
		
		$query = $this->db->query($sql, array($team_venue_id, $team_fan_page_id, $date));
		$result1 = $query->result();
		
		//add entourages to promoter reservations
		foreach($result1 as $res){
			$sql = "SELECT
			
						oauth_uid
						
					FROM 	promoters_guest_lists_reservations_entourages pglre
					
					WHERE	pglre.promoters_guest_lists_reservations_id = ?";
			$query = $this->db->query($sql, array($res->pglr_id));
			$res->entourage = $query->result();
			foreach($res->entourage as &$temp){
				$temp = $temp->oauth_uid;
			}
			unset($temp);
		}
		unset($res);
		
		$sql = "SELECT
		
					vlfit.id 									as vlfit_id,
					vlfit.venues_layout_floors_items_id			as vlfit_vlfi_id,
					tglr.id										as tglr_id,
					tglr.user_oauth_uid 						as tglr_user_oauth_uid,
					tglr.supplied_name							as tglr_supplied_name,
					tglr.host_message 							as tglr_host_message,
					tglr.request_msg							as tglr_request_msg,
					tglr.response_msg							as tglr_response_msg, 
					tglr.approved								as tglr_approved,
					tglr.create_time							as tglr_create_time,
					tglr.table_request							as tglr_table_request,
					tglr.text_message 							as tglr_text_message,
					tglr.share_facebook 						as tglr_share_facebook,
					tglr.venues_layout_floors_items_table_id 	as tglr_vlfit_id,
					tgla.day 									as tgla_day,
					tgla.image 									as tgla_image,
					tgla.name 									as tgla_name,
					tv.name										as tv_name
					
				FROM 	teams_guest_lists_reservations tglr
				
				LEFT JOIN 	venues_layout_floors_items_tables vlfit
				ON 			tglr.venues_layout_floors_items_table_id = vlfit.id
				
				JOIN 	teams_guest_lists tgl
				ON 		tglr.team_guest_list_id = tgl.id
				
				JOIN 	teams_guest_list_authorizations tgla
				ON 		tgl.team_guest_list_authorization_id = tgla.id
				
				JOIN 	team_venues tv
				ON 		tgla.team_venue_id = tv.id
				
				WHERE	tgla.team_venue_id = ?
						AND
						tv.team_fan_page_id = ?
						AND
						tglr.approved = 1
						AND ";
		if(!$future_date_only){
			$sql .= "tgl.date = ?";
		}else{
			$sql .= "tgl.date >= ?";
			$date = date('Y-m-d', time());//today and the future ;)
		}
		
		$query = $this->db->query($sql, array($team_venue_id, $team_fan_page_id, $date));
		$result2 = $query->result();
		
		//add entourages to promoter reservations
		foreach($result2 as $res){
			$sql = "SELECT
			
						oauth_uid
						
					FROM 	teams_guest_lists_reservations_entourages tglre
					
					WHERE	tglre.team_guest_list_reservation_id = ?";
			$query = $this->db->query($sql, array($res->tglr_id));
			$res->entourage = $query->result();
			foreach($res->entourage as &$temp){
				$temp = $temp->oauth_uid;
			}
			unset($temp);
		}
		unset($res);
		
		return array_merge($result1, $result2);
		
	}
	
	/**
	 * Retrieve all clients for a given venue
	 * 
	 * @param	string (tv_id)
	 * @return	array
	 */
	function retrieve_venue_clients($tv_id, $cache_front = false){
				
		$this->load->library('Redis', '', 'redis');
				
		if($cache_front){
			if($result = $this->redis->get('MT-RVC-cache-front-' . $tv_id)){
				return json_decode($result);
			}		
		}	
		
		$sql = "(SELECT DISTINCT
				
					tglr.user_oauth_uid 	as oauth_uid
				
				FROM 	teams_guest_list_authorizations tgla
				
				JOIN 	teams_guest_lists tgl 
				ON 		tgl.team_guest_list_authorization_id = tgla.id
				
				JOIN	teams_guest_lists_reservations tglr 
				ON 		tglr.team_guest_list_id = tgl.id
				
				WHERE	tgla.team_venue_id = ?)
				
					UNION
				
				(SELECT DISTINCT
				
					tglre.oauth_uid 		as oauth_uid
				
				FROM 	teams_guest_list_authorizations tgla
				
				JOIN 	teams_guest_lists tgl 
				ON 		tgl.team_guest_list_authorization_id = tgla.id
				
				JOIN	teams_guest_lists_reservations tglr 
				ON 		tglr.team_guest_list_id = tgl.id
				
				JOIN 	teams_guest_lists_reservations_entourages tglre 
				ON 		tglre.team_guest_list_reservation_id = tglr.id
				
				WHERE	tgla.team_venue_id = ?)
					
					UNION
				
				(SELECT DISTINCT
				
					pglr.user_oauth_uid 	as oauth_uid
				
				FROM	promoters_guest_list_authorizations pgla 
				
				JOIN 	promoters_guest_lists pgl
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN 	promoters_guest_lists_reservations pglr
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				WHERE	pgla.team_venue_id = ?)
					
					UNION
				
				(SELECT DISTINCT
				
					pglre.oauth_uid 		as oauth_uid
				
				FROM	promoters_guest_list_authorizations pgla 
				
				JOIN 	promoters_guest_lists pgl
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN 	promoters_guest_lists_reservations pglr
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN	promoters_guest_lists_reservations_entourages pglre
				ON 		pglre.promoters_guest_lists_reservations_id = pglr.id
				
				WHERE	pgla.team_venue_id = ?)";
				
		$query = $this->db->query($sql, array($tv_id, $tv_id, $tv_id, $tv_id));
		
		if(!$query){
			echo '---------------------------------------------------' . PHP_EOL;
			var_dump($this->db->last_query());
		}
		
		$result = $query->result();
		
		//convert to array of uids
		$uids = array();
		foreach($result as $res){
			$uids[] = $res->oauth_uid;
		}
		$result = $uids;
		
		if($cache_front){
			$this->redis->set('MT-RVC-cache-front-' . $tv_id, json_encode($result));
			$this->redis->expire('MT-RVC-cache-front-' . $tv_id, 60*30);
		}
		
		return $result;
		
	}
	
	/**
	 * Retrieves a team_venue news feed
	 * 
	 * @param	user_friend_uids
	 * @param	team_venue_id
	 * @return	array
	 */
	function retrieve_venue_news_feed($user_friend_uids, $team_venue_id){
						
		if(!$user_friend_uids)
			return array();
				
				
		$sql = "SELECT
		
					un.*
				
				FROM 	user_notifications un 
				
				WHERE 	(";
				
		foreach($user_friend_uids as $ufi){
					
				$sql .= "un.vibecompass_id = $ufi OR ";
			
		}
		$sql = rtrim($sql, " OR ");
		$sql .= ") AND un.team_venue_id = $team_venue_id";
						
		$query = $this->db->query($sql);
		return $query->result();	
		
	}
	
	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	
	 /**
	  * Updates a team
	  * 
	  * @param	array (updated fields)
	  * @return	bool
	  */
	 function update_team($fan_page_id, $data){
	 	
		$this->db->where('fan_page_id', $fan_page_id);
	 	$this->db->update('teams', $data);
		
	 }
	 
	/**
	 * Add a new piwik site for a team
	 * 
	 * @param	promoter_id
	 * @return	bool (True if successful)
	 */
	function update_team_add_piwik($team_fan_page_id){
		
		//call piwik api (rather unusual thing to do in a model function)
		//and register a new site_id for this promoter
		
		//get basic info about this promoter for piwik api
		$team = $this->retrieve_team($team_fan_page_id, array('retrieve_venues' => false));

		if($team->team->team_piwik_id_site != '-1')
			return;
		
		//Piwik api parameters for adding a new site
		$data = array(
			'method' 	=> 'SitesManager.addSite',
			'urls[]'  	=> 'http://www.vibecompass.com/',
			'siteName' 	=> 'Team: ' . $team->team->team_name . ' - ' . $team_fan_page_id
		);
		
		//construct the http api query
		$query = '';
		foreach($data as $key=>$value){
		  $query .= '&' . $key . '=' . urlencode($value);
		}
		
		//make piwik api call
		$this->load->library('Piwik');
		$query_result = $this->piwik->api_call($query);
		
		if(!isset($query_result['value'])){
			return false;
		}
		
		//update promoter record in database with site_id that was returned by Piwik when
		//site was registered
		$this->db->where('fan_page_id', $team_fan_page_id);
		$this->db->update('teams', array('piwik_id_site' => $query_result['value']));
				
		return true;
	}

	/**
	 * Updates information about an existing tvf
	 * 
	 * @param 	
	 * @return	bool
	 */
	function update_team_venues_floors($vlf_id, $floor_name){
		
		$this->db->where('id', $vlf_id);
		$this->db->update('venues_layout_floors', array('floor_name' => $floor_name));
		
	}
	
	/**
	 * Updates a team_venues_floors item
	 * 
	 * @return	bool
	 */
	function update_team_venues_floors_item($vlfi_id, $item_class, $left, $top, $width, $height, $image){
		
		$data = array(
			'pos_x' 	=> $left,
			'pos_y'		=> $top,
			'width'		=> $width,
			'height'	=> $height,
			'image'		=> $image
		);
		
		$this->db->where('id', $vlfi_id);
		$this->db->update('venues_layout_floors_items', $data);
		return true;
	}
	
	/**
	 * Updates a team_venues_floors_item_table
	 * 
	 * @return 	bool
	 */
	function update_team_venues_floors_item_table($vlfi_id, 
													$title,
													$monday_min,
													$tuesday_min,
													$wednesday_min,
													$thursday_min,
													$friday_min,
													$saturday_min,
													$sunday_min,
													$capacity){
														
	
		$title 			= strip_tags(trim($title));
		$monday_min 	= strip_tags(trim(preg_replace('/\D/', '', $monday_min)));
		$tuesday_min 	= strip_tags(trim(preg_replace('/\D/', '', $tuesday_min)));
		$wednesday_min 	= strip_tags(trim(preg_replace('/\D/', '', $wednesday_min)));
		$thursday_min 	= strip_tags(trim(preg_replace('/\D/', '', $thursday_min)));
		$friday_min 	= strip_tags(trim(preg_replace('/\D/', '', $friday_min)));
		$saturday_min 	= strip_tags(trim(preg_replace('/\D/', '', $saturday_min)));
		$sunday_min		= strip_tags(trim(preg_replace('/\D/', '', $sunday_min)));
		
		$data = array(
			'title'								=> $title,
			'capacity'							=> $capacity,
			'monday_min'						=> $monday_min,
			'tuesday_min'						=> $tuesday_min,
			'wednesday_min'						=> $wednesday_min,
			'thursday_min'						=> $thursday_min,
			'friday_min'						=> $friday_min,
			'saturday_min'						=> $saturday_min,
			'sunday_min'						=> $sunday_min
		);
		$this->db->where('venues_layout_floors_items_id', $vlfi_id);
		$this->db->update('venues_layout_floors_items_tables', $data);
		return true;
		
	}
	
	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Deletes a team_venue floor (logical)
	 * 
	 * @param 	int (vlf_id)
	 * @return	bool
	 */
	function delete_team_venues_floors($vlf_id){
		
		$this->db->where('id', $vlf_id);
		$this->db->update('venues_layout_floors', array('deleted' => 1, 'deleted_time' => time()));
		
		return true;
		
	//	$this->db->delete('venues_layout_floors', array('id' => $vlf_id));
	//	return true;
		
	}
	
	/**
	 * Delete a tvfi (logical)
	 * 
	 * @param	int (tvfi_id)
	 * @return	bool
	 */
	function delete_team_venues_floors_item($tvfi_id){
		$this->db->where('id', $tvfi_id);
		$this->db->update('venues_layout_floors_items', array('deleted' => 1, 'deleted_time' => time()));
		
		$this->db->where('venues_layout_floors_items_id', $tvfi_id);
		$this->db->update('venues_layout_floors_items_tables', array('deleted' => 1));
		
		return true;
	}
	
}

/* End of file model_teams.php */
/* Location: application/models/model_teams.php */