<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


class library_payments{
	
	private $ci;
	
	/**
	 * Class constructor
	 * 
	 * @return	library_venues
	 */
	public function __construct(){
		$this->ci =& get_instance();
		$this->ci->load->library('stripe', '', 'stripe');
		
	}
	
	
	
	
	
	public function update_stripe_token($options){

		//do we already have a customer object from stripe for this team?
		$this->ci->db->select('*')
			->from('teams')
			->where(array(
				'fan_page_id' 	=> $options['team_fan_page_id']
			));
		$query 	= $this->ci->db->get();
		$result = $query->row();





		
		if($result->stripe_token){
			//yes, update existing
			
			$stripe_result = $this->ci->stripe->customer_update($result->stripe_token, array(
				'card' => $options['token']
			));
			$stripe_result = json_decode($stripe_result);
			
			if(!isset($stripe_result->id))
				return false;
				
			$this->ci->db->where(array(
				'fan_page_id'	=> $options['team_fan_page_id']
			));
			$this->ci->db->update('teams', array(
				'stripe_token'	=> $stripe_result->id,
				'last4'			=> $stripe_result->active_card->last4,
				'card_type'		=> $stripe_result->active_card->type,
				'live_status'	=> '1'
			));			
			
			return true;
			
		}else{			
			//no, create one
		
			$stripe_result = $this->ci->stripe->customer_create($options['token'], 
																	'', 
																	$options['team_fan_page_id'] . ' - ' . $options['team_name']);
			$stripe_result = json_decode($stripe_result);
			
			if(!isset($stripe_result->id))
				return false;
			
			$this->ci->db->where(array(
				'fan_page_id'	=> $options['team_fan_page_id']
			));
			$this->ci->db->update('teams', array(
				'stripe_token'	=> $stripe_result->id,
				'last4'			=> $stripe_result->active_card->last4,
				'card_type'		=> $stripe_result->active_card->type,
				'live_status'	=> '1'
			));
			
			return true;
			
		}
					
					
					
					
					
	}
	
	
	
	
	
	public function bill_manager($options){
		
		$this->ci->db->select('stripe_token as stripe_token, monthly_base_price as monthly_base_price')
			->from('managers_teams')
			->where(array(
				'id' => $options['managers_teams_id']
			));
			
		$result = $this->ci->db->get();
		
		$stripe_token 		= $result->row()->stripe_token;
		$monthly_base_price = $result->row()->monthly_base_price;
		
		$final = $this->ci->stripe->charge_customer($monthly_base_price, $stripe_token, '');
		
	//	var_dump(json_decode($final));
		
	}
	
}
/* End of file library_payments.php */
/* Location: ./application/libraries/library_payments.php */