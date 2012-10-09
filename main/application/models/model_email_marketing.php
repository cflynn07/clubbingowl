<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Operations related to email marketing campaigns
 * 
 */
class Model_email_marketing extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
	
	/*-------------------------------------------------------------------------
	 |	Create Methods (create)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * 
	 */
	function create_marketing_campaign($data){
		
		
		$this->db->insert('marketing_campaigns', $data);
		$insert_id = $this->db->insert_id();
		
		
		$mcr_data = array();
		$clients = $this->retrieve_team_clients($data['team_fan_page_id']);
		foreach($clients as $c){
			
			$mcr_data[] = array(
				'marketing_campaign_id' 	=> $insert_id,
				'oauth_uid'					=> $c->oauth_uid
			);
			
		}
		

		$this->db->insert_batch('marketing_campaigns_recipients', $mcr_data);		
	
		return true;
		
	}

	
	
	/*-------------------------------------------------------------------------
	 |	Retrieval Methods (retrieve)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * 
	 */
	function retrieve_team_clients($team_fan_page_id){
		
		$user_fields = "u.first_name, u.last_name, u.full_name, u.email, u.gender, u.oauth_uid";
		
		$sql = "(SELECT DISTINCT
					
					$user_fields
				
				FROM 	teams_guest_list_authorizations tgla
				
				JOIN 	teams_guest_lists tgl 
				ON 		tgl.team_guest_list_authorization_id = tgla.id
				
				JOIN	teams_guest_lists_reservations tglr 
				ON 		tglr.team_guest_list_id = tgl.id
				
				JOIN 	team_venues tv 
				ON 		tgla.team_venue_id = tv.id
				
				JOIN 	users u 
				ON 		tglr.user_oauth_uid = u.oauth_uid
				
				WHERE	tv.team_fan_page_id = ? AND u.opt_out_email = 0)
				
					UNION
				
				(SELECT DISTINCT
				
					$user_fields
				
				FROM 	teams_guest_list_authorizations tgla
				
				JOIN 	teams_guest_lists tgl 
				ON 		tgl.team_guest_list_authorization_id = tgla.id
				
				JOIN	teams_guest_lists_reservations tglr 
				ON 		tglr.team_guest_list_id = tgl.id
				
				JOIN 	teams_guest_lists_reservations_entourages tglre 
				ON 		tglre.team_guest_list_reservation_id = tglr.id
				
				JOIN 	team_venues tv 
				ON 		tgla.team_venue_id = tv.id
				
				JOIN 	users u 
				ON 		tglre.oauth_uid = u.oauth_uid
				
				WHERE	tv.team_fan_page_id = ? AND u.opt_out_email = 0)
					
					UNION
				
				(SELECT DISTINCT
				
					$user_fields
				
				FROM	promoters_guest_list_authorizations pgla 
				
				JOIN 	promoters_guest_lists pgl
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN 	promoters_guest_lists_reservations pglr
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN 	team_venues tv 
				ON 		pgla.team_venue_id = tv.id
				
				JOIN 	users u 
				ON 		pglr.user_oauth_uid = u.oauth_uid
				
				WHERE	tv.team_fan_page_id = ? AND u.opt_out_email = 0)
					
					UNION
				
				(SELECT DISTINCT
				
					$user_fields
				
				FROM	promoters_guest_list_authorizations pgla 
				
				JOIN 	promoters_guest_lists pgl
				ON 		pgl.promoters_guest_list_authorizations_id = pgla.id
				
				JOIN 	promoters_guest_lists_reservations pglr
				ON 		pglr.promoter_guest_lists_id = pgl.id
				
				JOIN	promoters_guest_lists_reservations_entourages pglre
				ON 		pglre.promoters_guest_lists_reservations_id = pglr.id
				
				JOIN 	team_venues tv 
				ON 		pgla.team_venue_id = tv.id
				
				JOIN 	users u 
				ON 		pglre.oauth_uid = u.oauth_uid
				
				WHERE	tv.team_fan_page_id = ? AND u.opt_out_email = 0)";
								
		$query = $this->db->query($sql, array($team_fan_page_id, $team_fan_page_id, $team_fan_page_id, $team_fan_page_id));
		return $query->result();
		
	}
	
	/**
	 * 
	 */
	function retrieve_marketing_campaigns($team_fan_page_id){
		
		$sql = "SELECT
		
					mc.*,
					u.full_name
				
				FROM 	marketing_campaigns mc
				
				JOIN 	users u
				ON 		mc.manager_oauth_uid = u.oauth_uid
				
				WHERE 	mc.team_fan_page_id = ?
				ORDER BY 	mc.id DESC";
		
		$query = $this->db->query($sql, array($team_fan_page_id));
		$result = $query->result();
		
		foreach($result as &$res){
					
			$sql = "SELECT
			
						mcr.*
						
					FROM 	marketing_campaigns_recipients mcr 
					
					JOIN 	marketing_campaigns mc
					ON 		mcr.marketing_campaign_id = mc.id
					
					WHERE 	mc.id = ?";
			$query2 = $this->db->query($sql, array($res->id));
			$res->recipients = $query2->result();	
			
		}unset($res);
		
		return $result;
			
	}	
	
	
	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	
	
	
	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
	
}

/* End of file model_email_marketing.php */
/* Location: application/models/model_email_marketing.php */