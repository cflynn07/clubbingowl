<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * database interaction related to managers of teams
 * */
class Model_users_team_managers extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
	
	/*-------------------------------------------------------------------------
	 |	Create Methods (create)
	 | ------------------------------------------------------------------------ */
	
	/**
	 * Retrieve the id of the team this user is a manager of
	 * 
	 * @param	string (oauth_uid)
	 * @return	int
	 */
	function retrieve_manager_team($oauth_uid){
		
		$sql = "SELECT	
					tm.promoter_team_id 	as promoter_team_id
		
				FROM	users u
				JOIN	team_managers tm
				ON		tm.users_id = u.id
				
				WHERE	u.oauth_uid = $oauth_uid";
		$query = $this->db->query($sql);
		
		if($result = $query->row()){
			$result = $result->promoter_team_id;
		}
		
		return $result;
		
	}
	
	/*-------------------------------------------------------------------------
	 |	Retrieval Methods (retrieve)
	 | ------------------------------------------------------------------------ */
	
	
	
	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	

	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
	
}

/* End of file model_users_team_managers.php */
/* Location: application/models/model_users_team_managers.php */