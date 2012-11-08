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
		
		$result = json_decode($this->ci->stripe->customer_create($options['token'], ''));
		
		$this->ci->db->where(array(
			'id'	=> $options['managers_teams_id']
		));
		$this->ci->db->update('managers_teams', array(
			'stripe_token'	=> $result->id,
			'last4'			=> $result->active_card->last4,
			'card_type'		=> $result->active_card->type
		));
				
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