<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function lang_key($string, $kv){
	
	
	$key_start = '<%=';
	$key_end = '%>';
	
	foreach($kv as $key => $value){
		
		$string = str_replace($key_start . $key . $key_end, $value, $string);
		
	}
	
   return $string;
   
}