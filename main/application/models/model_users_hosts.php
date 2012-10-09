<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * database interaction related to managers/venues
 * */
class Model_users_hosts extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
	
	/*-------------------------------------------------------------------------
	 |	Create Methods (create)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Create a new team host
	 * 
	 */
	function create_team_host($oauth_uid, $teams_fan_page_id, $manager_oauth_uid){
		
		//check to see if a record already exists
		$query = $this->db->get_where('teams_hosts', array('users_oauth_uid' => $oauth_uid, 'teams_fan_page_id' => $teams_fan_page_id));
		if($result = $query->row()){
			//update existing
			
			$this->db->where('id', $result->id);
			$this->db->update('teams_hosts', array('banned' => 0, 'quit' => 0, 'time_added' => time()));
			
		}else{
			//create new
			$data = array(
				'teams_fan_page_id' => $teams_fan_page_id,
				'users_oauth_uid' 	=> $oauth_uid, 
				'manager_oauth_uid' => $manager_oauth_uid,
				'time_added' 		=> time()
			);
			
			$this->db->insert('teams_hosts', $data);
			
		}
		
		//update user record
		$this->db->where('oauth_uid', $oauth_uid);
		$this->db->update('users', array('host' => 1));
		
		//quit all other teams
		$this->db->update('teams_hosts', array('quit' => 1, 'quit_time' => time()), 'teams_fan_page_id != ' . $teams_fan_page_id);
		
		return true;
		
	}
	
	/*-------------------------------------------------------------------------
	 |	Retrieval Methods (retrieve)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Retrieves most recent team user is/was host with
	 * 
	 * @param	int (user_oauth_uid)
	 * @return	object || false
	 */
	function retrieve_team_host($oauth_uid){
		
		$sql = "SELECT
						
						th.id 					as th_id,
						th.teams_fan_page_id 	as th_teams_fan_page_id,
						th.users_oauth_uid 		as th_users_oauth_uid,
						th.manager_oauth_uid	as th_manager_oauth_uid,
						th.time_added			as th_time_added,
						th.banned 				as th_banned,
						th.time_banned 			as th_time_banned,
						th.banned_by			as th_banned_by,
						th.quit 				as th_quit,
						th.quit_time 			as th_quit_time
					
				FROM teams_hosts th 
				
				WHERE th.users_oauth_uid = ?
				
				ORDER BY th.time_added DESC";
		$query = $this->db->query($sql, array($oauth_uid));
		return $query->row();
		
	}

	/**
	 * Retrieves all guest lists for a venue on a given date
	 * 
	 * @param	int (tv_id)
	 * @param	string (iso date)
	 * @return 	object || false
	 */
	function retrieve_venue_guest_lists($tv_id, $date, $team_fan_page_id){
		
		$date = '2012-05-18';
		
		$sql = "SELECT
		
					'promoter'					as reservation_type,
					'false'						as guest_of,
					pgla.id						as pgla_id,
					pgla.user_promoter_id		as pgla_user_promoter_id,
					pgla.team_venue_id			as pgla_team_venue_id,
					pgla.day					as pgla_day,
					pgla.name					as pgla_name,
					pgla.image					as pgla_image,
					pgl.date					as pgl_date,
					pglr.id						as pglr_id,
					pglr.create_time			as pglr_create_time,
					pglr.user_oauth_uid			as pglr_user_oauth_uid,
					pglr.host_message			as pglr_host_message,
					pglr.checked_in				as pglr_checked_in,
					pglr.checked_in_time		as pglr_checked_in_time,
					pglr.checked_in_by_host		as pglr_checked_in_by_host,
					pglr.table_request			as pglr_table_request,
					up.users_oauth_uid			as up_users_oauth_uid,
					u.first_name				as u_first_name,
					u.last_name 				as u_last_name,
					u.full_name 				as u_full_name
					

				FROM 	promoters_guest_lists_reservations pglr
				
				JOIN	promoters_guest_lists pgl
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN	promoters_guest_list_authorizations pgla
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN	users_promoters up 
				ON 		pgla.user_promoter_id = up.id
				
				JOIN 	promoters_teams pt
				ON 		pt.promoter_id = up.id
				
				JOIN	users u 
				ON 		up.users_oauth_uid = u.oauth_uid
				
				WHERE	
				
						pgla.deactivated = 0
				AND		pgl.date = ?
				AND		pglr.approved = 1
				AND		pgla.team_venue_id = ?
				AND		up.banned = 0
				AND 	pt.approved = 1
				AND		pt.quit = 0
				AND 	pt.banned = 0
				AND		pt.team_fan_page_id = ?";
		$query = $this->db->query($sql, array($date, $tv_id, $team_fan_page_id));
		$promoter_result = $query->result();
		
		//attach entourages
		foreach($promoter_result as &$res){
					
			$sql = "SELECT
			
						oauth_uid 		as oauth_uid
						
					FROM	promoters_guest_lists_reservations_entourages pglre
					
					WHERE	pglre.promoters_guest_lists_reservations_id = ?";	
			$query = $this->db->query($sql, array($res->pglr_id));
			$res->pglre = $query->result();
			
		}unset($res);
		
		
		// ----- repeat for venue guest lists
		
		$sql = "SELECT
		
					'team'						as reservation_type,
					'false'						as guest_of,
					tgla.id						as tgla_id,
					tgla.team_venue_id			as tgla_team_venue_id,
					tgla.day					as tgla_day,
					tgla.name					as tgla_name,
					tgla.image					as tgla_image,
					tgl.date					as tgl_date,
					tglr.id						as tglr_id,
					tglr.create_time			as tglr_create_time,
					tglr.user_oauth_uid			as tglr_user_oauth_uid,
					tglr.host_message			as tglr_host_message,
					tglr.checked_in				as tglr_checked_in,
					tglr.checked_in_time		as tglr_checked_in_time,
					tglr.checked_in_by_host		as tglr_checked_in_by_host,
					tglr.table_request			as tglr_table_request
					
					
					FROM 	teams_guest_lists_reservations tglr 
					
					JOIN 	teams_guest_lists tgl 
					ON 		tglr.team_guest_list_id = tgl.id  

					JOIN 	teams_guest_list_authorizations tgla
					ON 		tgl.team_guest_list_authorization_id = tgla.id
					
					
					WHERE	
							tgla.deactivated = 0
					AND		tgl.date = ?
					AND 	tgla.team_venue_id = ?
					AND 	tglr.approved = 1";
		$query = $this->db->query($sql, array($date, $tv_id));
		$team_result = $query->result();
		
		foreach($team_result as &$res){
					
			$sql = "SELECT
			
						oauth_uid 		as oauth_uid
						
					FROM	teams_guest_lists_reservations_entourages tglre
					
					WHERE	tglre.team_guest_list_reservation_id = ?";	
			$query = $this->db->query($sql, array($res->tglr_id));
			$res->tglre = $query->result();	
			
		}unset($res);
		
		
		$final_result = array_merge($promoter_result, $team_result);
		
		
		//remove all entourage users that are ALSO head users of guest lists
		// --------------------------------------------------------------------------------------------
		foreach($final_result as $key => &$group){
			
			 
			if($group->reservation_type == 'promoter'){
											
										
									
				//for all entourage users in all requests, check to see if they're a head user on any request made this week & venue
				foreach($group->pglre as $key => $ent_user){
					
					foreach($final_result as $request){
						
						if(isset($request->pglr_user_oauth_uid)){
							
							if($ent_user->oauth_uid == $request->pglr_user_oauth_uid){
								//there is a match, remove this ent_user from this request
								unset($group->pglre[$key]);
								continue;
							}
							
						}elseif(isset($request->tglr_user_oauth_uid)){
							
							if($ent_user->oauth_uid == $request->tglr_user_oauth_uid){
								//there is a match, remove this ent_user from this request
								unset($group->pglre[$key]);
								continue;
							}
							
						}
						
					}
					
				}
				
				
									
				
			}elseif($group->reservation_type == 'team'){
								
								
							
							
				//for all entourage users in all requests, check to see if they're a head user on any request made this week & venue
				foreach($group->tglre as $key => $ent_user){
					
					foreach($final_result as $request){
						
						if(isset($request->pglr_user_oauth_uid)){
							
							if($ent_user->oauth_uid == $request->pglr_user_oauth_uid){
								//there is a match, remove this ent_user from this request
								unset($group->tglre[$key]);
								continue;
							}
							
						}elseif(isset($request->tglr_user_oauth_uid)){
							
							if($ent_user->oauth_uid == $request->tglr_user_oauth_uid){
								//there is a match, remove this ent_user from this request
								unset($group->tglre[$key]);
								continue;
							}
							
						}
						
					}
					
				}		
				
				
				
				
			}
			
		}
		
		
		// --------------------------------------------------------------------------------------------
		
		$cmp_function = function($a, $b){
						
			if(isset($a->pglr_create_time)){
				$a_create_time = $a->pglr_create_time;
			}elseif(isset($a->tglr_create_time)){
				$a_create_time = $a->tglr_create_time;
			}
			
			if(isset($b->pglr_create_time)){
				$b_create_time = $b->pglr_create_time;
			}elseif(isset($b->tglr_create_time)){
				$b_create_time = $b->tglr_create_time;
			}
			
			return $a_create_time - $b_create_time;
		}; 
		
		usort($final_result, $cmp_function);
		
		// --------------------------------------------------------------------------------------------
	
	
	
	
	
	
	
	/*
	
		// Move all entourage users to head users that are also 'guests of' some other bloke
		// --------------------------------------------------------------------------------------------
		$entourage_reservations = array();
		foreach($final_result as $key => $result){
	
			if($result->reservation_type == 'promoter'){
			
				
				foreach($result->pglre as $pglre_user){
					
					$test_check = false;
					foreach($entourage_reservations as $ent_res){
						
						
						if($ent_res->reservation_type == 'promoter'){
							if($ent_res->pglr_user_oauth_uid == $pglre_user->oauth_uid){
								$test_check = true;
							}
						}else{
							if($ent_res->tglr_user_oauth_uid == $pglre_user->oauth_uid){
								$test_check = true;
							}
						
						}
					 
					}
					
					if(!$test_check){
						//user is NOT in the array already, go ahead and add
						
						$ent_res_obj 							= new stdClass;
						$ent_res_obj->reservation_type	 		= 'promoter';
						$ent_res_obj->guest_of 					= array($result->pglr_user_oauth_uid);
						$ent_res_obj->pgla_id 					= $result->pgla_id;
						$ent_res_obj->pgla_user_promoter_id 	= $result->pgla_user_promoter_id;
						$ent_res_obj->pgla_team_venue_id		= $result->pgla_team_venue_id;
						$ent_res_obj->pgla_day 					= $result->pgla_day;
						$ent_res_obj->pgla_name 				= $result->pgla_name;
						$ent_res_obj->pgla_image 				= $result->pgla_image;
						$ent_res_obj->pgl_date 					= $result->pgl_date;
						$ent_res_obj->pglr_id 					= $result->pglr_id;
						$ent_res_obj->pglr_create_time 			= $result->pglr_create_time;
						$ent_res_obj->pglr_user_oauth_uid 		= $pglre_user->oauth_uid;
						$ent_res_obj->pglr_host_message 		= $result->pglr_host_message;
						$ent_res_obj->pglr_checked_in 			= $result->pglr_checked_in;
						$ent_res_obj->pglr_checked_in_time 		= $result->pglr_checked_in_time;
						$ent_res_obj->pglr_checked_in_by_host 	= $result->pglr_checked_in_by_host;
						$ent_res_obj->pglr_table_request 		= $result->pglr_table_request;
						$ent_res_obj->up_users_oauth_uid 		= $result->up_users_oauth_uid;
						$ent_res_obj->u_first_name 				= $result->u_first_name;
						$ent_res_obj->u_last_name 				= $result->u_last_name;
						$ent_res_obj->u_full_name 				= $result->u_full_name;
						
						$entourage_reservations[] = $ent_res_obj;
						$test_check = false;
					}
					
				
				}
				
				
				
			}else{
				
				
				foreach($result->tglre as $tglre_user){
					
					$test_check = false;
					foreach($entourage_reservations as $ent_res){
						
						
						if($ent_res->reservation_type == 'promoter'){
							if($ent_res->pglr_user_oauth_uid == $pglre_user->oauth_uid){
								$test_check = true;
							}
						}else{
							if($ent_res->tglr_user_oauth_uid == $pglre_user->oauth_uid){
								$test_check = true;
							}
						
						}
					 
					}
					
					if(!$test_check){
						//user is NOT in the array already, go ahead and add
					
						
						$ent_res_obj 							= new stdClass;
						$ent_res_obj->reservation_type	 		= 'team';
						$ent_res_obj->guest_of 					= array($result->tglr_user_oauth_uid);
						$ent_res_obj->tgla_id 					= $result->tgla_id;
						$ent_res_obj->tgla_team_venue_id		= $result->tgla_team_venue_id;
						$ent_res_obj->tgla_day 					= $result->tgla_day;
						$ent_res_obj->tgla_name 				= $result->tgla_name;
						$ent_res_obj->tgla_image 				= $result->tgla_image;
						$ent_res_obj->tgl_date 					= $result->tgl_date;
						$ent_res_obj->tglr_id 					= $result->tglr_id;
						$ent_res_obj->tglr_create_time 			= $result->tglr_create_time;
						$ent_res_obj->tglr_user_oauth_uid 		= $tglre_user->oauth_uid;
						$ent_res_obj->tglr_host_message 		= $result->tglr_host_message;
						$ent_res_obj->tglr_checked_in 			= $result->tglr_checked_in;
						$ent_res_obj->tglr_checked_in_time 		= $result->tglr_checked_in_time;
						$ent_res_obj->tglr_checked_in_by_host 	= $result->tglr_checked_in_by_host;
						$ent_res_obj->tglr_table_request 		= $result->tglr_table_request;
						
						$entourage_reservations[] = $ent_res_obj;
						$test_check = false;
					}
					
				
				}
				
				
			}	
				
			
		}

	*/
		// --------------------------------------------------------------------------------------------

		
				
		return $final_result;
	}

	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Updates a host and bans them from their current team
	 * 
	 * @param	string (oauth_uid)
	 * @param	string (team fan page id)
	 * @return 	bool
	 */
	function update_host_ban_team($host_oauth_uid, $team_fan_page_id){
				
		$this->db->where(array('users_oauth_uid' => $host_oauth_uid, 'teams_fan_page_id' => $team_fan_page_id));
		$this->db->update('teams_hosts', array('banned' => '1', 'time_banned' => time()));
		return true;
		
	}

	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
	
}

/* End of file model_users_hosts.php */
/* Location: application/models/model_users_hosts.php */