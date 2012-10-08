<?php if(! defined('BASEPATH') ) exit('No direct script access allowed');

if(!function_exists('facebook_signed_request_decode'))
{
	/**
	 * Decodes a Facebook signed request
	 * 
	 * @param	string (signed_request)
	 * @return 	? (object, array, etc)
	 */
	function facebook_signed_request_decode($signed_request){
		// the helper function doesn't have access to $this, so we need to get a reference to the 
        // CodeIgniter instance.  We'll store that reference as $CI and use it instead of $this
        $CI =& get_instance();
        
		list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
		
		$sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
		$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
		
		if(strtoupper($data['algorithm']) !== 'HMAC-SHA256'){
			error_log('Unknown algorithm. Expected HMAC-SHA256');
			return null;
		}
		
		//app secret
		$secret = $CI->config->item('facebook_api_secret');
		
		// check sig
	  	$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
	  	if ($sig !== $expected_sig) {
	    	error_log('Bad Signed JSON signature!');
	    	return null;
	  	}
	
	  	return $data;
	}
	
}

/* End of file facebook_signed_request_decode_helper.php */
/* Location: ./application/helpers/facebook_signed_request_decode_helper.php */