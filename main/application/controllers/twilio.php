<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Twilio extends MY_Controller {
	
	public $msg_invalid_response_format = "";
	public $unknown_id = "Error, unknown request id";
	
	/**
	 * Controller constructor. Perform any universal operations here.
	 * 
	 * @return	null
	 * */
	function __construct(){
		parent::__construct();
	}

	/**
	 * Error controller
	 */
	function index(){
		
		
		$msg_no_recognize 	= "Sorry, I don't recognize the number you're texting me from.";
		$msg_bad_format 	= "Sorry, I don't recognize the format of your message.";
		
		$from_number = $this->input->post('From');
		if(!$from_number)
			$this->_set_response($msg_no_recognize);
		$from_number = ltrim($from_number, '+1');

		$body = strtolower($this->input->post('Body'));
		
		
		//other types of responses
		if(strpos($body, 'notify ') === 0){
			
		}
		
		
		//yes or no	
		if(strpos($body, 'yes ') === 0){
			
			$offset = 4;
			
		}elseif(strpos($body, 'no ') === 0){
			
			$offset = 3;
			
		}else{
			
			$this->_set_response($msg_bad_format);
			return;
			
		}
								
		if((strpos($body, 'm', $offset) - $offset) === 0){
			
			$manager  = $this->users->retrieve_manager_by_twilio($from_number);
			if($manager){
				$this->_managers($manager, $from_number);
				return;
			}
			
		}else if((strpos($body, 'p', $offset) - $offset) === 0){
			
			$this->load->model('model_users', 'users', true);
			$promoter = $this->users->retrieve_promoter_by_twilio($from_number);
			if($promoter){
				$this->_promoters($promoter, $from_number);
				return;
			}
			
		}else{
			
			$this->_set_response($msg_bad_format);
			return;					
			
		}

				
		$this->_set_response($msg_no_recognize);

	}

	/**
	 * 
	 */
	private function _promoters($promoter, $from_number){
				
		$this->msg_invalid_response_format = "Sorry, I don't understand your response. Reply: [yes/no] p[Reservation ID] [response message] --> EXAMPLE: yes p15 Sure John, see you tonight!";		
		
		
		
		//track this outgoing message for billing purposes
		$this->load->model('model_teams', 'teams', true);
		$this->teams->create_billable_message($promoter->t_fan_page_id, array(
			'type' => 'sms_incoming'
		));
		
		
				
		$body = strtolower($this->input->post('Body'));
		
		//remove extra white space
		$body = trim(preg_replace('/\s\s+/', ' ', $body));
		$body = explode(' ', $body, 3);
		
		if(isset($body[0]) && (strtolower($body[0]) == 'yes' || strtolower($body[0]) == 'no')){
						
			
			if(!isset($body[1])){
			
				//track this outgoing message for billing purposes
				$this->load->model('model_teams', 'teams', true);
				$this->teams->create_billable_message($promoter->t_fan_page_id, array(
					'type' => 'sms'
				));
				
			
				$this->_set_response($msg_invalid_response_format);
			}
			
			if(!isset($body[2]))
				$body[2] = '';
			
			$body[1] = ltrim($body[1], 'p');
			
			$this->load->model('model_guest_lists', 'guest_lists', true);
			if(strtolower($body[0]) == 'yes'){
				//yes
				
				$result = $this->guest_lists->update_promoter_guest_list_reservation_reject_or_approve(true, $promoter->up_id, $body[1], $body[2]);
				
			}else{
				//no
				
				$result = $this->guest_lists->update_promoter_guest_list_reservation_reject_or_approve(false, $promoter->up_id, $body[1], $body[2]);
				
			}
			
			if($result){
				$message = "Request $body[1] " . ((strtolower($body[0]) == 'yes') ? 'approved' : 'declined');
			}else{
				$message = $this->unknown_id;
			}
			
			
			//track this outgoing message for billing purposes
			$this->load->model('model_teams', 'teams', true);
			$this->teams->create_billable_message($promoter->t_fan_page_id, array(
				'type' => 'sms'
			));
			
			
			$this->_set_response($message);
			
		}else{
			
			
			//track this outgoing message for billing purposes
			$this->load->model('model_teams', 'teams', true);
			$this->teams->create_billable_message($promoter->t_fan_page_id, array(
				'type' => 'sms'
			));
			
			
			$this->_set_response($this->msg_invalid_response_format);
		}
		
	}
	
	/**
	 * 
	 */
	private function _managers($manager, $from_number){
		
		$this->msg_invalid_response_format = "Sorry, I don't understand your response. Reply: [yes/no] m[Reservation ID] [response message] --> EXAMPLE: yes m15 Sure John, see you tonight!";
		
		$body = strtolower($this->input->post('Body'));
		
		//remove extra white space
		$body = trim(preg_replace('/\s\s+/', ' ', $body));
		
		$body = explode(' ', $body, 3);
		
		if(isset($body[0]) && (strtolower($body[0]) == 'yes' || strtolower($body[0]) == 'no')){
			
			if(!isset($body[1])){
			
				//track this outgoing message for billing purposes
				$this->load->model('model_teams', 'teams', true);
				$this->teams->create_billable_message($manager->team_fan_page_id, array(
					'type' => 'sms'
				));
			
				$this->_set_response($msg_invalid_response_format);
			}
			
			if(!isset($body[2]))
				$body[2] = '';
			
			$body[1] = ltrim($body[1], 'm');
			
			$this->load->model('model_team_guest_lists', 'team_guest_lists', true);
			
			
			
			//need tv_id			
			$sql = "SELECT
			
						tgla.team_venue_id as team_venue_id
						
					FROM 	teams_guest_list_authorizations tgla
					
					JOIN 	teams_guest_lists tgl 
					ON 		tgl.team_guest_list_authorization_id = tgla.id
					
					JOIN 	teams_guest_lists_reservations tglr
					ON 		tglr.team_guest_list_id = tgl.id 
					
					WHERE	tglr.id = ?";
			$query = $this->db->query($sql, array($body[1]));
			$temp = $query->row();
			if(!$temp){
				
				//track this outgoing message for billing purposes
				$this->load->model('model_teams', 'teams', true);
				$this->teams->create_billable_message($manager->team_fan_page_id, array(
					'type' => 'sms'
				));
				
				$this->_set_response($this->unknown_id);
			}
			
			
			
			if(strtolower($body[0]) == 'yes'){
				//yes
				
				$result = $this->team_guest_lists->update_team_guest_list_reservation_reject_or_approve(true, $body[2], $temp->team_venue_id, $body[1]);
				
			}else{
				//no
				
				$result = $this->team_guest_lists->update_team_guest_list_reservation_reject_or_approve(false, $body[2], $temp->team_venue_id, $body[1]);
				
			}
			
			if($result){
				$message = "Request $body[1] " . ((strtolower($body[0]) == 'yes') ? 'approved' : 'declined');
			}else{
				$message = "Error, unknown request id: $body[1]";
			}
			
			//track this outgoing message for billing purposes
			$this->load->model('model_teams', 'teams', true);
			$this->teams->create_billable_message($manager->team_fan_page_id, array(
				'type' => 'sms'
			));
			
			$this->_set_response($message);
			
		}else{
			
			//track this outgoing message for billing purposes
			$this->load->model('model_teams', 'teams', true);
			$this->teams->create_billable_message($manager->team_fan_page_id, array(
				'type' => 'sms'
			));
			
			$this->_set_response($this->msg_invalid_response_format);
		}
		
		
		
		
		
	}
	
	private function _set_response($msg){
		$response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$response .= "<Response>\n";
		$response .= "<Sms>$msg</Sms>\n";
		$response .= "</Response>";
		
   		header("content-type: text/xml");
		echo $response;
		exit;
	}
}

/* End of file twilio.php */
/* Location: ./application/controllers/twilio.php */