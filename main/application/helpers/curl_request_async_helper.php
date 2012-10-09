<?php if(! defined('BASEPATH') ) exit('No direct script access allowed');

// $type must equal 'GET' or 'POST'
  function curl_request_async($url, $params, $type='POST')
  {
  	
//	echo $url . PHP_EOL;
//	echo $params . PHP_EOL;
	
	/*
      foreach ($params as $key => &$val) {
        if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
      }
      $post_string = implode('&', $post_params);
	*/
	
		$post_string = $params;
	
      $parts=parse_url($url);

      $fp = fsockopen($parts['host'],
          isset($parts['port'])?$parts['port']:80,
          $errno, $errstr, 30);

      // Data goes in the path for a GET request
      if('GET' == $type) $parts['path'] .= '?'.$post_string;

      $out = "$type ".$parts['path']." HTTP/1.1\r\n";
      $out.= "Host: ".$parts['host']."\r\n";
      $out.= "Content-Type: application/json\r\n";
	 // curl_setopt( $ch, CURLOPT_HTTPHEADER, array ( "Content-Type: application/json" ) );
    
      $out.= "Content-Length: ".strlen($post_string)."\r\n";
      $out.= "Connection: Close\r\n\r\n";
      // Data goes in the request body for a POST request
      if ('POST' == $type && isset($post_string)) $out.= $post_string;

		var_dump($out);

      fwrite($fp, $out);
      fclose($fp);
	  
	  
	  
	  
	  
	  
	  
	  
	  
  }