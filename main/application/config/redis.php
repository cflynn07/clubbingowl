<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Config for the CodeIgniter Redis library
 *
 * @see ../libraries/Redis.php
 */

 // Connection details
if(MODE == 'production' || MODE == 'staging'){
	$dotcloud 		= json_decode(file_get_contents(DOTCLOUD_JSON), true);
	
	
	
	
	$config['redis_host'] 		= (isset($dotcloud['DOTCLOUD_DATA_REDIS_HOST'])) 		? $dotcloud['DOTCLOUD_DATA_REDIS_HOST'] 	: '';		// IP address or host
//	$config['redis_host'] 		= $dotcloud['DOTCLOUD_DATA_REDIS_LOGIN'] . ':' . $dotcloud['DOTCLOUD_DATA_REDIS_PASSWORD'] . '@' . $dotcloud['DOTCLOUD_DATA_REDIS_HOST'];
	$config['redis_port'] 		= (isset($dotcloud['DOTCLOUD_DATA_REDIS_PORT'])) 		? $dotcloud['DOTCLOUD_DATA_REDIS_PORT'] 	: '';		// Default Redis port is 6379
	$config['redis_password'] 	= (isset($dotcloud['DOTCLOUD_DATA_REDIS_PASSWORD'])) 	? $dotcloud['DOTCLOUD_DATA_REDIS_PASSWORD'] : '';		// Can be left empty when the server does not require AUTH
//	$config['redis_password'] 	= '';
	
}else{
	
	$config['redis_host'] 		= 'localhost';		// IP address or host
	$config['redis_port'] 		= '6379';			// Default Redis port is 6379
	$config['redis_password'] 	= '';				// Can be left empty when the server does not require AUTH
	
}
 

unset($dotcloud);