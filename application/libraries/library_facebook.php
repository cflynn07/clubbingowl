<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Various helper methods designed to facilitate facebook api calls
 * 
 */
class Library_facebook{
	
	//codeigniter super object
	private $CI;
	private $vibecompass_app_access_token;
	
	/**
	 * class constructor
	 * 
	 * @return	null
	 */
	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->config('facebook');
		$this->vibecompass_app_access_token = $this->CI->config->item('facebook_app_access_token');
	}
	
	/**
	 * Perform curl operation to facebook graph and FQL url endpoints
	 * 
	 * @param	string (url)
	 * @param	string (http method)
	 * @return	string
	 */
	private function _curl_method($url, $request = 'GET', $params = array()){
	    $ch = curl_init();
		
	    $curlopt = array(
	        CURLOPT_URL => $url,
	        CURLOPT_CUSTOMREQUEST => $request,
	        CURLOPT_CONNECTTIMEOUT => 10,
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_TIMEOUT        => 60,
	        CURLOPT_USERAGENT      => 'facebook-php-2.0',
	    );
		
		if($request == 'POST'){
			
			$params_string = '';
			//url-ify the data for the POST
			foreach($params as $key => $value) { $params_string .= $key . '=' . $value . '&'; }
			rtrim($params_string, '&');
			
			$curlopt[CURLOPT_POST] = count($params);
			$curlopt[CURLOPT_POSTFIELDS] = $params_string;
			
		}
		
	    curl_setopt_array($ch, $curlopt);
		
		//workaround for local env - accepts facebook's cert
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
	    $response = curl_exec($ch);
	    if($response === false)
	        trigger_error(curl_error($ch));
	    curl_close($ch);
	    return $response;
	}
	
	/**
	 * Executes a regular facebook graph api query
	 * 
	 * @param	string (graph api method)
	 * @param	string (access_token (optional))
	 * @param	string (http method -- get || post)
	 * @return	array
	 */
	public function fb_api_query($method, $access_token = false, $http_method = 'GET', $params = array()){
			
		$graph_api_endpoint = 'https://graph.facebook.com/';
		
		$api_request = $graph_api_endpoint . urlencode($method);
		
	    if($access_token){
	    	if($http_method == 'POST'){
	    		$params['access_token'] = $access_token;
	    	}else{
	    		$api_request .= '&access_token=' . $access_token;
	    	}
	    }else{
	    	if($http_method == 'POST'){
	    		$params['access_token'] = $this->vibecompass_app_access_token;
	    	}else{
	    		$api_request .= '&access_token=' . $this->vibecompass_app_access_token;
	    	}
	    }
		
		if($http_method == 'POST'){
			$params['format'] = 'json';			
		}else{
			$api_request .= '&format=json';
		}
				
		$result = $this->_curl_method($api_request, $http_method, $params);
		return json_decode(preg_replace('/("\w+"):(\d+)/', '\\1:"\\2"', $result), true);
	}
	
	/**
	 * Executes a FQL query with facebook and returns the results
	 * 
	 * @param	string (fql query)
	 * @param	string (access_token (optional))
	 * @param	string (http method -- get || post)
	 * @return	array
	 */
	public function fb_fql_query($fql_string, $access_token = false, $http_method = 'GET'){
		
		$fql_api_endpoint = 'https://api.facebook.com/method/fql.query?query=';
		
		$fql_request = $fql_api_endpoint . urlencode($fql_string) . '&format=json';
		
		if($access_token)
			$fql_request .= '&access_token=' . $access_token;
		else
			$fql_request .= '&access_token=' . $this->vibecompass_app_access_token;

		$result = $this->_curl_method($fql_request, $http_method);
		return json_decode(preg_replace('/("\w+"):(\d+)/', '\\1:"\\2"', $result), true);
		
	}
	
	/**
	 * Processes request_ids that are returned by the facebook js sdk
	 * during a UI dialog apprequest event. First instance of this in
	 * application is on invite friends to join guest list page.
	 * 
	 * TODO: This could be optimized with 1 FQL query
	 * 
	 * @param	array of request id's
	 * @return 	array
	 * */
	public function process_signed_requests($request_ids){
		
		$recipients = array();
		//extract 'to' portion of every request and combine into an array
		foreach($request_ids as $id){
			
			$response = $this->fb_api_query($id, $this->vibecompass_app_access_token);
			
			$recipients[] = $response->to;
		}

		return $recipients;
	}
	
	/**
	 * Retrieve the list of the current user's friends that are also vibecompass users
	 * 
	 * @param	string (user's access token)
	 * @param	array (fields to query facebook api for)
	 * @return 	array
	 */
	public function retrieve_user_facebook_friends($access_token, $fields){
		
		$fql = "SELECT ";
		
		foreach($fields as $key => $field){
			if($key == (count($fields) - 1))
				$fql .= "$field ";
			else
				$fql .= "$field, ";
		}
						
		$fql .=	"FROM 	user 
				WHERE 	uid IN (SELECT 	uid2 
								FROM 	friend 
								WHERE 	uid1 = me())
				AND 	is_app_user = 1";
				
		$result = $this->fb_fql_query($fql, $access_token);
		
		return $result;	
	}
	
	/**
	 * Retrieve info from facebook about individual user
	 */
	public function retrieve_facebook_user($oauth_uid, $param = array('uid', 'name', 'email', 'first_name', 'last_name', 'pic', 'third_party_id'), $access_token = false){
		
		$fql = "SELECT ";
			foreach($params as $param){
				$fql .= "$param, ";
			} $fql = rtrim($fql, ", ");
			
			/*
					uid, 
					name,
					email,
					first_name,
					last_name,
					third_party_id,
					sex,
					username,
					timezone
			*/	
			
				" FROM user
				WHERE uid = $oauth_uid";
		return $this->fb_fql_query($fql, $access_token);
		
	}
}
 
/* End of file library_facebook.php */
/* Location: ./application/libraries/library_facebook.php */
