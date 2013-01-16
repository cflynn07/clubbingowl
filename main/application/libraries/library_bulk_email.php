<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 


class library_bulk_email{
	
	private $ci;
	private $url;
	private $queue;
	
	
	/**
	 * Class constructor
	 * 
	 * @return	library_venues
	 */
	public function __construct(){
		$this->ci =& get_instance();
		
		$this->queue 	= array();
		$this->api_key 	= $this->ci->config->item('mandrill_api_key');
		$this->url 		= $this->ci->config->item('mandrill_endpoint') . 'messages/send.json';
		
		
		
	}
	
	
	public function add_queue($options = array()){
		
		$to = new stdClass;
		$to->email 	= $options['to_email'];
		$to->name 	= $options['to_name'];
	
		$email_obj 			= new stdClass;
		$email_obj->key		= $this->api_key;
		$email_obj->message = (object)array(
			'html'			=> $options['html'],
			'text'			=> $options['text'],
			'subject'		=> $options['subject'],
			'from_email'	=> 'do-not-reply@clubbingowl.com',
			'from_name'		=> 'ClubbingOwl',
			
			'to'			=> array($to),
			
			'track_opens'	=> true,
			'track_clicks'	=> true, 
			'async'			=> true
		);
		
		
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,				$this->url);
		curl_setopt($curl, CURLOPT_POSTFIELDS,		json_encode($email_obj));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 	1);
		curl_setopt($curl, CURLOPT_POST, 			1);
		curl_setopt($curl, CURLOPT_HTTPHEADER,		array('Content-Type: application/json')); 
		
		$this->queue[] = $curl;
		
	}
	
	
	
	public function flush_queue(){
		
		
		$mh = curl_multi_init();
		foreach($this->queue as $key => $ch){
			curl_multi_add_handle($mh, $ch);
		}
		
		$active = null;
		//execute the handles
		do {
		    $mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);
		
		while ($active && $mrc == CURLM_OK) {
		    if (curl_multi_select($mh) != -1) {
		        do {
		            $mrc = curl_multi_exec($mh, $active);
		        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
		    }
		}
		
		//close the handles
		foreach($this->queue as $key => $ch){
			
			echo var_dump(json_decode(curl_multi_getcontent($ch))) . PHP_EOL;
			
			curl_multi_remove_handle($mh, $ch);
		}

		curl_multi_close($mh);
		
		$this->queue = array();
		
		
	}
	
	
	
}
/* End of file library_bulk_email.php */
/* Location: ./application/libraries/library_bulk_email.php */