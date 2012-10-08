<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

//testing, local dev env connect to remote db

if(DEPLOYMENT_ENV == 'cloudcontrol'){
	$active_group = 'cctrl';
}else{
	$active_group = 'local';
}
 
$active_record = TRUE;

# read the credentials file
$string = (isset($_ENV['CRED_FILE'])) ? file_get_contents($_ENV['CRED_FILE'], false) : false;
$active_group = 'local';

if($string){
	
	# the file contains a JSON string, decode it and return an associative array
	$creds = json_decode($string, true);
	$active_group = 'cctrl';
	
	// cloudcontrol 
//	$db['cctrl']['hostname'] = $creds['MYSQLS']['MYSQLS_HOSTNAME'];
//	$db['cctrl']['username'] = $creds['MYSQLS']['MYSQLS_USERNAME'];
//	$db['cctrl']['password'] = $creds['MYSQLS']['MYSQLS_PASSWORD'];
//	$db['cctrl']['database'] = $creds['MYSQLS']['MYSQLS_DATABASE'];
	$db['cctrl']['hostname'] = $creds['MYSQLD']['MYSQLD_HOST'];
	$db['cctrl']['username'] = $creds['MYSQLD']['MYSQLD_USER'];
	$db['cctrl']['password'] = $creds['MYSQLD']['MYSQLD_PASSWORD'];
	$db['cctrl']['database'] = $creds['MYSQLD']['MYSQLD_DATABASE'];
	
	$db['cctrl']['dbdriver'] = 'mysql';
	$db['cctrl']['dbprefix'] = '';
	$db['cctrl']['pconnect'] = FALSE;
	$db['cctrl']['db_debug'] = FALSE;
	$db['cctrl']['cache_on'] = FALSE;
	$db['cctrl']['cachedir'] = '';
	$db['cctrl']['char_set'] = 'utf8';
	$db['cctrl']['dbcollat'] = 'utf8_general_ci';
	$db['cctrl']['swap_pre'] = '';
	$db['cctrl']['autoinit'] = TRUE;
	$db['cctrl']['stricton'] = FALSE;
	
}

// local
$db['local']['hostname'] = '127.0.0.1';
$db['local']['username'] = 'root';
$db['local']['password'] = '';
//$db['local']['database'] = 'backup2_live_05.19.2012';
$db['local']['database'] = 'backup';
$db['local']['dbdriver'] = 'mysql';
$db['local']['dbprefix'] = '';
$db['local']['pconnect'] = FALSE;
$db['local']['db_debug'] = FALSE;
$db['local']['cache_on'] = FALSE;
$db['local']['cachedir'] = '';
$db['local']['char_set'] = 'utf8';
$db['local']['dbcollat'] = 'utf8_general_ci';
$db['local']['swap_pre'] = '';
$db['local']['autoinit'] = TRUE;
$db['local']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */