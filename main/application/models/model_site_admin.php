<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * operations related to website administrator login/setting promoter/venue owner settings manually
 * 
 * */
class Model_site_admin extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
	
	/*-------------------------------------------------------------------------
	 |	Create Methods (create)
	 | ------------------------------------------------------------------------ */
	
	/*-------------------------------------------------------------------------
	 |	Retrieval Methods (retrieve)
	 | ------------------------------------------------------------------------ */
	
	/*
	 * Retrieves an administrative user for a given username/password
	 * 
	 * @param	string (username)
	 * @param	string (hashed password)
	 * @return 	array (matches to pattern)
	 * */
	function retrieve_admin_user($username, $password_hash){
		$query = $this->db->get_where('site_admin_users', array('username' => $username,
														'password' => $password_hash));
		return $query->result();
	}
	
	/*-------------------------------------------------------------------------
	 |	Update Methods (update)
	 | ------------------------------------------------------------------------ */
	
	/*-------------------------------------------------------------------------
	 |	Delete Methods (delete)
	 | ------------------------------------------------------------------------ */
	
}

/* End of file model_auto_suggest.php */
/* Location: application/models/model_auto_suggest.php */