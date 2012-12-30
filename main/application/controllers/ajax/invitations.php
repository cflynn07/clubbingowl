<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invitations extends MY_Controller {
	
	/**
	 * constructor for controller, checks to see if controller methods were called via ajax
	 * 
	 * @return	null
	 */
	function __construct(){
		parent::__construct();
		
		//don't want users accessing this controller unless it's via an AJAX call
		if(!$this->input->is_ajax_request()){
			header('', true, 403);
			die(json_encode(array('success' => false,
									'message' => 'invalid access attempt')));
		}
	}
	
	/**
	 * Routing method of controller, decides what private method to call based
	 * on 'method' parameter of post request
	 * 
	 * @return	null
	 */
	function index(){		
		switch($this->input->post('vc_method')){
			case 'invitation_response':
				$this->_invitation_response();
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'unknown method: ' . $this->input->post('vc_method'))));
		}
	}
	
	/**
	 * Handles an ajax response to a invitation sent to a user
	 * 
	 * @return 	null
	 */
	private function _invitation_response(){
				
		if(!$vc_user = $this->session->userdata('vc_user'))
			die(json_encode(array('success' => false,
									'message' => 'User not authenticated')));
											
		$vc_user = json_decode($vc_user);
		
		if(!$ui_id = $this->input->post('ui_id'))
			die(json_encode(array('success' => false,
									'message' => 'ui_id required')));

		if(!$response = $this->input->post('response'))
			die(json_encode(array('success' => false,
									'message' => 'response required')));
		
		switch($response){
			case 'accept':
				$response = '1';
				break;
			case 'decline':
				$response = '-1';
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'Invalid response')));
		}
		
		//swap out this section, don't use sessions to store invitations, use database lookup instead		
		//check to see if user has any invitations
		$this->load->model('model_users', 'users', true);
		$vc_user->invitations = $this->users->retrieve_user_invitations($vc_user->oauth_uid);
		
		//verify user has this ui_id request
		if(!isset($vc_user->invitations))
			die(json_encode(array('success' => false,
									'message' => 'No invitations')));
		
		$invite_present = false;
		$current_invitation_object = null;
		
		foreach($vc_user->invitations as $key => $invite){
			
			if($ui_id == $invite->ui_id){
				//save this invite object for later
				$current_invitation_object = $invite;
				
				//invitation found
				$invite_present = true;
				
				//remove from vc_user object (remember to update session later w/ new vc_user)
				unset($vc_user->invitations->$key);
				break;
			}
			
		}
		
		if(!$invite_present)
			die(json_encode(array('success' => false,
									'message' => 'Unknown invitation id')));
		
		$this->load->model('model_users', 'users', true);
		
		if($response == '1'){
			
			//accept this invitation
			$this->users->update_invitation_status($ui_id, $response);			
			
			//decline all other invitations
			//NEW: Decline all other invitations of the same type
			foreach($vc_user->invitations as $key => $invite){
				
				if($invite->ui_invitation_type == $current_invitation_object->ui_invitation_type){
					$this->users->update_invitation_status($invite->ui_id, '-1');
					unset($vc_user->invitations[$key]);
				}
				
			}
			
	//		//remove all other invitations from vc_user object since we've accepted one
	//		unset($vc_user->invitations);
			
			$this->load->model('model_users_promoters', 'users_promoters', true);
			
			
			
			
			// ----------------------------------------- PROMOTER INVITATIONS ---------------------------------------------------
			//go through the steps to make a user a promoter
			if(isset($vc_user->promoter) && $current_invitation_object->ui_invitation_type == 'promoter'){
				//user is already a promoter
				
				//set 'completed_setup' == 0 in users_promoters
				$this->users_promoters->update_promoter(array('promoter_id' => $vc_user->promoter->up_id), array('completed_setup' => 0));
				
				//set 'quit' and 'quit_time' in promoters_teams for all records where approved = 0
				$this->users_promoters->update_promoter_quit_team($vc_user->promoter->up_id);
				
				//CHECK if there is a record in promoters_teams for this promoter_id and this team_
					//YES: unset quit
					//NO: add new record in promoters_teams linking this promoter_id and this team
					//this way they get all their old clients back if they previously worked for a team
				$this->users_promoters->update_promoter_join_team($vc_user->promoter->up_id, $current_invitation_object->ui_invitation_team_fan_page_id);
				
			}elseif($current_invitation_object->ui_invitation_type == 'promoter'){
				//user is not a promoter
				
				//create new promoter
				$this->users_promoters->create_promoter($vc_user->oauth_uid, $current_invitation_object->ui_invitation_team_fan_page_id);
				
			}
			
			if($current_invitation_object->ui_invitation_type == 'promoter')
				$vc_user->promoter = $this->users_promoters->retrieve_promoter(array('users_oauth_uid' => $vc_user->oauth_uid), array('completed_setup' => '', 'id_only' => true));

			// ----------------------------------------- PROMOTER INVITATIONS ---------------------------------------------------
			
			
			
			
			
			// -------------------------------------------- HOST INVITATIONS ----------------------------------------------------
			
			if($current_invitation_object->ui_invitation_type == 'host'){
				//user is already a promo
				
				$this->load->model('model_users_hosts', 'users_hosts', true);
				
				//Creates or updates a record in teams_hosts, sets user row to host=>1 and quits any other teams where user is a host				
				$this->users_hosts->create_team_host($vc_user->oauth_uid, $current_invitation_object->ui_invitation_team_fan_page_id, $current_invitation_object->ui_manager_oauth_uid);
				
				
				$vc_user->host = $this->users_hosts->retrieve_team_host($vc_user->oauth_uid);				
			}
			
			
			// -------------------------------------------- HOST INVITATIONS ----------------------------------------------------
			
			
			//perform an upgrade check on a team to see if max number of promoters / hosts / managers exceeded
			$this->load->model('model_teams', 'teams', true);
			$this->teams->upgrade_check($current_invitation_object->ui_invitation_team_fan_page_id);
			
						
			
		}else{
			
			$this->users->update_invitation_status($ui_id, $response);
			
		}
			
		//update session
		$this->session->set_userdata('vc_user', json_encode($vc_user));
			
		die(json_encode(array('success' => true, 'message' => '')));
		
	}
	
}

/* End of file invitations.php */
/* Location: ./application/controllers/ajax/invitations.php */