<?php

define('SITE', 'clubbingowl');
define('ASSETS_SITE', 'clubbingowl'); //staticowl

$dotcloud_environment = '/home/dotcloud/environment.json';
define('DOTCLOUD_JSON', $dotcloud_environment);
if(file_exists(DOTCLOUD_JSON)){
	//we're on dotcloud...
	
	$dotcloud_environment = json_decode(file_get_contents($dotcloud_environment), true);
	if(isset($dotcloud_environment['DOTCLOUD_PROJECT']) && $dotcloud_environment['DOTCLOUD_PROJECT'] == 'coproduction'){
		define('MODE', 'production');
		define('TLD', 'com');
	}else{
		define('MODE', 'staging');
		define('TLD', 'staging');
	}
	
}else{
	//we're on local...
	
	define('MODE', 'local');
	define('TLD', 'dev');
	
}
unset($dotcloud_environment);





//temporary access control
if(FALSE && MODE != 'local'){
	
	if(isset($_GET['token_value'])){
		setcookie('token_value', 
					$_GET['token_value'],
					time()+60*60*24*30,
					'/',
					(SITE . '.' . TLD),
					false,
					false);
		
		header('Location: http://www.' . SITE . '.' . TLD . '/');
		die();
	}
	
	
	if(php_sapi_name() != 'cli')
		if(!isset($_COOKIE['token_value'])){
			die();
		}else{
			if($_COOKIE['token_value'] != 'v49y49fgs068y33nwfg90'){
				die();	
			}
			error_reporting(E_ALL);
		}
}







//Casey Flynn Added 5/6/2012
//Hack for compatability. SSL enabled with CloudFlare -- does not reach server (USER ---ssl---- CF -------- HOST)
//BEGIN HACK -----------------------------
if(isset($_SERVER['HTTP_CF_VISITOR'])){
	
	$http_cf_visitor = json_decode($_SERVER['HTTP_CF_VISITOR']);
	if(isset($http_cf_visitor->scheme))
		$_SERVER['HTTPS'] = ($http_cf_visitor->scheme == 'https') ? 'on' : 'off';
	unset($http_cf_visitor);
}
//END HACK ---------------------------

//assume always https for staging
if(MODE == 'staging'){
	$_SERVER['HTTPS'] = 'on';
}


//force https globally
if(php_sapi_name() !== 'cli'){
	/*	
	if(strpos($_SERVER['REQUEST_URI'], '/facebook') !== 0 && strpos($_SERVER['REQUEST_URI'], '/plugin') !== 0){
		if(strtolower($_SERVER['HTTPS']) != 'on'){
			$base_url = 'https';
		    $base_url .= '://'. $_SERVER['HTTP_HOST'];
		// 	$base_url .= '/';
			
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: ' . $base_url . (($_SERVER['REQUEST_URI'] == '/') ? '' : $_SERVER['REQUEST_URI']));
			die();
		}
	}
	 * */
}	



//shut down any requests at www.clubbingowl.com/index.php/.....
if(php_sapi_name() !== 'cli')
	if(strpos($_SERVER['REQUEST_URI'], '/index.php') === 0){
			
		$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
	    $base_url .= '://'. $_SERVER['HTTP_HOST'];
	    $base_url .= isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80' ? ( ':'.$_SERVER['SERVER_PORT'] ) : '';
	 	$base_url .= '/';
		
		header('HTTP/1.1 301 Moved Permanently');
		if(strpos($_SERVER['REQUEST_URI'], '/index.php/') === 0)
			header('Location: ' . $base_url . str_replace('/index.php/', '', $_SERVER['REQUEST_URI']));
		else
			header('Location: ' . $base_url);
		
		die();
	}









//shut down any requests at www.clubbingowl.com/index.php/.....
if(isset($_SERVER['REQUEST_URI']) && isset($_SERVER['HTTP_HOST'])){
		
	$perform_redirect = false;

	$redirect_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
    $redirect_url .= '://';

	$allowed_hosts = array(
		'www',			//English
		'es',			//Spanish
		'it',			//Italian
		'de',			//German
		'ja'			//Japanese
	);
	
	$domain = "clubbingowl";
	
	foreach($allowed_hosts as $key => $val){
		$allowed_hosts[$key] = $allowed_hosts[$key] . '.' . $domain . '.' . TLD;
	}
	
	if(!in_array($_SERVER['HTTP_HOST'], $allowed_hosts)){
		$perform_redirect = true;
		$redirect_url .= 'www.' . $domain . '.' . TLD . '/';
	}else{
		$redirect_url .= $_SERVER['HTTP_HOST'] . '/';
	}
		
	if(strpos($_SERVER['REQUEST_URI'], '/index.php') === 0){
			
		$perform_redirect = true;	
		$redirect_url .= str_replace('/index.php/', '', $_SERVER['REQUEST_URI']);
		
	}else{
				
		$redirect_url .= ltrim($_SERVER['REQUEST_URI'], '/');
		
	}
	
	
	//http://www.staticowl.dev/assets/js?g=base&cache=20_1321057554_1349920359
	//one exception, www.staticowl.com/assets/
	if($_SERVER['HTTP_HOST'] == 'www.' . ASSETS_SITE . '.' . TLD){
		if(strpos($_SERVER['REQUEST_URI'], '/assets') === 0)
			$perform_redirect = false;
		else
			$perform_redirect = true;
	}else{
		
		if(strpos($_SERVER['REQUEST_URI'], '/assets') === 0){
			$perform_redirect = true;
			$redirect_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
    		$redirect_url .= '://www.' . $domain . '.' . TLD . '/';
		}
		
	}



	if($perform_redirect){		
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: ' . $redirect_url);
		die();
	}
	
}





/**
 * 
	#force non-www to www.clubbingowl.com
	#DEVELOPMENT URLS
	RewriteCond %{HTTP_HOST} !^www.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^ar.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^cs.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^de.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^el.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^es.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^fr.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^hi.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^it.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^iw.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^ja.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^ko.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^nl.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^no.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^pl.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^pt.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^ru.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^sv.clubbingowl.dev$
	RewriteCond %{HTTP_HOST} !^zh.clubbingowl.dev$
	
	#PRODUCTION URLS
	RewriteCond %{HTTP_HOST} !^www.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^ar.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^cs.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^de.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^el.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^es.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^fr.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^hi.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^it.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^iw.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^ja.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^ko.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^nl.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^no.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^pl.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^pt.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^ru.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^sv.clubbingowl.com$
	RewriteCond %{HTTP_HOST} !^zh.clubbingowl.com$

	RewriteRule ^(.*)$ http://www.clubbingowl.com/$1 [R=301]
 */



//run this entire website on east-coast time. Might be an issue as we move across the country.
date_default_timezone_set('America/New_York');
//date_default_//timezone_set('America/Los_Angeles');


/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */

$local_mode = 'development';
$staging_mode = 'development';
//$production_mode = 'development';

switch(MODE){
	case 'local':
		define('ENVIRONMENT', (isset($local_mode)) 		? $local_mode 		: 'production');
		break;
	case 'staging':
		define('ENVIRONMENT', (isset($staging_mode)) 	? $staging_mode 	: 'production');
		break;
	case 'production':
		define('ENVIRONMENT', (isset($production_mode)) ? $production_mode 	: 'production');
		break;
}
unset($local_mode);
unset($staging_mode);
unset($production_mode);


 
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */

if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development':
			error_reporting(E_ALL);
			ini_set('display_errors', '1');
		break;
	
		case 'staging':
		case 'production':
			error_reporting(0);
		break;

		default:
			exit('The application environment is not set correctly.');
	}
}


/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same  directory
 * as this file.
 *
 */
	$system_path = 'system';

/*
 *---------------------------------------------------------------
 * APPLICATION FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * folder then the default one you can set its name here. The folder
 * can also be renamed or relocated anywhere on your server.  If
 * you do, use a full server path. For more info please see the user guide:
 * http://codeigniter.com/user_guide/general/managing_apps.html
 * 
 * NO TRAILING SLASH!
 *
 */
	$application_folder = 'application';

/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here.  For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *
 * IMPORTANT:  If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller.  Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *
 * Un-comment the $routing array below to use this feature
 *
 */
	// The directory name, relative to the "controllers" folder.  Leave blank
	// if your controller is not in a sub-folder within the "controllers" folder
	// $routing['directory'] = '';

	// The controller class file name.  Example:  Mycontroller.php
	// $routing['controller'] = '';

	// The controller function you wish to be called.
	// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *
 * Un-comment the $assign_to_config array below to use this feature
 *
 */
	// $assign_to_config['name_of_config_item'] = 'value of config item';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}

	// ensure there's a trailing slash
	$system_path = rtrim($system_path, '/').'/';

	// Is the system path correct?
	if ( ! is_dir($system_path))
	{
		exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// The PHP file extension
	define('EXT', '.php');

	// Path to the system folder
	define('BASEPATH', str_replace("\\", "/", $system_path));

	// Path to the front controller (this file)
	define('FCPATH', str_replace(SELF, '', __FILE__));

	// Name of the "system folder"
	define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));


	// The path to the "application" folder
	if (is_dir($application_folder))
	{
		define('APPPATH', $application_folder.'/');
	}
	else
	{
		if ( ! is_dir(BASEPATH.$application_folder.'/'))
		{
			exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);
		}

		define('APPPATH', BASEPATH.$application_folder.'/');
	}

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
 

require_once BASEPATH.'core/CodeIgniter'.EXT;

/* End of file index.php */
/* Location: ./index.php */