<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * operations related to team messages and admin manager announcements
 * */
class Model_team_messaging extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
	
	/*-------------------------------------------------------------------------
	 |	Create Methods (create)
	 | ------------------------------------------------------------------------ */

	 /**
	  * Creates a new message
	  * 
	  * @param 	array
	  * @return int
	  */
	 function create_message($options = array()){
	 	
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array(
								'users_oauth_uid'	=> false,
								'teams_fan_page_id' => false,
								'message_content' 	=> ''
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
		
		if(strlen($message_content) == 0)
			return false;
		
		$data = array('users_oauth_uid' 	=> $users_oauth_uid,
						'teams_fan_page_id' => $teams_fan_page_id,
						'message_content' 	=> strip_tags($message_content),
						'create_time' 		=> time());
		$this->db->insert('messages', $data);
		
		return $this->db->insert_id();
	 }

	/**
	 * Creates an inactive user status
	 * 
	 * @return	bool
	 */
	function create_inactive($oauth_uid, $fan_page_id){
		
		$data = array(
					'users_oauth_uid' 	=> $oauth_uid,
					'teams_fan_page_id'	=> $fan_page_id,
					'time'				=> time()
					);
		$this->db->insert('chat_inactives', $data);
		return $this->db->insert_id();
		
	}
	 
	/*-------------------------------------------------------------------------
	 |	Retrieval Methods (retrieve)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Retrieve the messages for a given user when chat feature loads
	 * 
	 * @return	object
	 */
	function retrieve_init($options = array()){
		
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array(
								'cache' 			=> true,
								'cache_length' 		=> 900, 	//15 minutes
								'users_oauth_uid'	=> false,
								'teams_fan_page_id' => false,
								'last_read_id'		=> false,
								'unread_only' 		=> false
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
		
		//user id is required
		if(!$users_oauth_uid || !$teams_fan_page_id)
			return array();
		
		$sql = "SELECT 
					
					t.m_id					as m_id,
					t.m_users_oauth_uid		as m_users_oauth_uid,
					t.m_message_content		as m_message_content,
					t.m_create_time			as m_create_time
				
				FROM 
				
					(SELECT
						
						m.id 				as m_id,
						m.users_oauth_uid 	as m_users_oauth_uid,
						m.message_content	as m_message_content,
						m.create_time 		as m_create_time
						
					FROM 	messages m
					
					WHERE	
					
							m.teams_fan_page_id = ?
							
					ORDER BY m.id DESC
					
					LIMIT 100) t 
					
				ORDER BY t.m_id ASC";
					
		$query = $this->db->query($sql, array($teams_fan_page_id));
		$result = new stdClass;
		$result->messages = $query->result();
		
		$sql = "SELECT
					
					ci.users_oauth_uid 	as ci_users_oauth_uid
					
				FROM 	chat_inactives ci
				
				WHERE 	ci.teams_fan_page_id = ?";
		$query = $this->db->query($sql, array($teams_fan_page_id));
		$result->chat_inactives = $query->result();
		
		return $result;
	}

	/**
	 * Retrieve the initial list of managers, promoters and hosts for a given team when the chat feature is initialized
	 * 
	 * @return 	object
	 */
	function retrieve_team_members($options = array()){
		
		/* --------- CONFIGURATION SETTINGS --------- *
		 * This method will require a lot of optional configuration settings. Passing in a configurable
		 * array of key/value pairs will work better than a method with 20 optional arguments.
		 */
		$default_options = array(
								'cache' 			=> true,
								'cache_length' 		=> 900, 	//15 minutes
								'teams_fan_page_id' => false,
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

		if(!$teams_fan_page_id)
			return array();
		
		//retrieve managers
		$sql = "SELECT
		
					mt.user_oauth_uid 	as oauth_uid,
					u.twilio_sms_number	as u_twilio_sms_number,
					u.full_name			as u_full_name,
					u.first_name		as u_first_name,
					u.email				as u_email
				
				FROM	managers_teams mt
				
				JOIN 	users u 
				ON 		mt.user_oauth_uid = u.oauth_uid
				
				WHERE 	mt.fan_page_id = ?
						AND
						mt.banned = 0";	
		$query = $this->db->query($sql, array($teams_fan_page_id));
		
		$result = new stdClass;
		$result->managers = $query->result();
		
		//retrieve promoters
		$sql = "SELECT 
		
					up.users_oauth_uid 		as oauth_uid,
					pt.banned				as pt_banned,
					pt.quit					as pt_quit,
					u.twilio_sms_number		as u_twilio_sms_number,
					u.full_name				as u_full_name,
					u.first_name			as u_first_name,
					u.email					as u_email
				
				FROM 	users_promoters up
				
				JOIN 	promoters_teams pt
				ON 		pt.promoter_id = up.id
				
				JOIN	teams t
				ON 		t.fan_page_id = pt.team_fan_page_id
				
				JOIN 	users u 
				ON 		up.users_oauth_uid = u.oauth_uid
				
				WHERE 	t.fan_page_id = ?
						AND
						t.completed_setup = 1";
		$query = $this->db->query($sql, array($teams_fan_page_id));
		$result->promoters = $query->result();
		
		//retrieve hosts
		$sql = "SELECT
		
					th.users_oauth_uid	as oauth_uid,
					th.banned 			as th_banned,
					th.quit				as th_quit
					
				FROM 	teams_hosts th 
				
				JOIN 	teams t 
				ON 		th.teams_fan_page_id = t.fan_page_id
				
				WHERE	th.teams_fan_page_id = ?
						AND
						t.fan_page_id = ?
						AND
						t.completed_setup = 1";
		$query = $this->db->query($sql, array($teams_fan_page_id, $teams_fan_page_id));
		$result->hosts = $query->result();
		
		return $result;
	}
	
	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	
	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Deletes an inactive user status
	 * 
	 * @return	bool
	 */
	function delete_inactive($oauth_uid, $fan_page_id){
		
		$data = array(
					'users_oauth_uid' 	=> $oauth_uid
					);
		$this->db->delete('chat_inactives', $data);
		return true;
		
	}
	
}

/* End of file model_team_messaging.php */
/* Location: application/models/model_team_messaging.php */