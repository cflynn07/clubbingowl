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










if(MODE == 'production' || MODE == 'staging'){
	$active_group 	= 'cloud';
	$dotcloud 		= json_decode(file_get_contents(DOTCLOUD_JSON), true);
}else{
	$active_group 	= 'local';
}


$active_record = TRUE;

	//DOTCLOUD_DB_MYSQL_PASSWORD
// cloud 
$db['cloud']['hostname'] = (isset($dotcloud['DOTCLOUD_DB_MYSQL_HOST'])) 	? $dotcloud['DOTCLOUD_DB_MYSQL_HOST'] 		: '';
$db['cloud']['username'] = (isset($dotcloud['DOTCLOUD_DB_MYSQL_LOGIN'])) 	? $dotcloud['DOTCLOUD_DB_MYSQL_LOGIN'] 		: '';					//'application';
$db['cloud']['password'] = (isset($dotcloud['DOTCLOUD_DB_MYSQL_PASSWORD'])) ? $dotcloud['DOTCLOUD_DB_MYSQL_PASSWORD'] 	: '';					//'chepufraCheDagu3p8Ch';
$db['cloud']['database'] =  MODE;  //staging or production
//$db['cloud']['database'] = 'cobarsystems_' . MODE;  //staging or production
$db['cloud']['dbdriver'] = 'mysqli';
$db['cloud']['dbprefix'] = '';
$db['cloud']['pconnect'] = FALSE;
$db['cloud']['port']	 = (isset($dotcloud['DOTCLOUD_DB_MYSQL_PORT'])) 	? $dotcloud['DOTCLOUD_DB_MYSQL_PORT'] 		: ''; 
$db['cloud']['db_debug'] = FALSE;
$db['cloud']['cache_on'] = FALSE;
$db['cloud']['cachedir'] = '';
$db['cloud']['char_set'] = 'utf8';
$db['cloud']['dbcollat'] = 'utf8_general_ci';
$db['cloud']['swap_pre'] = '';
$db['cloud']['autoinit'] = TRUE;
$db['cloud']['stricton'] = FALSE;


// local
$db['local']['hostname'] = 'localhost';
$db['local']['username'] = 'root';
$db['local']['password'] = 'root';
$db['local']['database'] = 'clubbingowl_development';
$db['local']['dbdriver'] = 'mysqli';
$db['local']['dbprefix'] = '';
$db['local']['pconnect'] = FALSE;
$db['local']['port']	 = 8889; 
$db['local']['db_debug'] = FALSE;
$db['local']['cache_on'] = FALSE;
$db['local']['cachedir'] = '';
$db['local']['char_set'] = 'utf8';
$db['local']['dbcollat'] = 'utf8_general_ci';
$db['local']['swap_pre'] = '';
$db['local']['autoinit'] = TRUE;
$db['local']['stricton'] = FALSE;

unset($dotcloud);

/* End of file database.php */
/* Location: ./application/config/database.php */