<?php

//determine environment/variables
$string = (isset($_ENV['CRED_FILE'])) ? file_get_contents($_ENV['CRED_FILE'], false) : false;
if($string){
	//cloudcontrol
	define('DEPLOYMENT_ENV', 'cloudcontrol');
	define('TLD', 'com');
}else{
	//local
	define('DEPLOYMENT_ENV', 'local');
	define('TLD', 'dev');
}

//var_dump($_ENV);

//BEGIN HACK -----------------------------
if(DEPLOYMENT_ENV == 'cloudcontrol'){
	
	if(isset($_SERVER['HTTP_CF_VISITOR'])){
		$http_cf_visitor = json_decode($_SERVER['HTTP_CF_VISITOR']);
		if(isset($http_cf_visitor->scheme)){
			
			if($http_cf_visitor->scheme == 'https'){
				$_SERVER['HTTPS'] = 'on';
			}else{
				$_SERVER['HTTPS'] = 'off';
			}
			
		}else{
			$_SERVER['HTTPS'] = 'off';
		}
	}

}
//END HACK ---------------------------


//shut down any requests at www.vibecompass.com/index.php/.....
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
	
	$domain = "tinkerbay";
	
	foreach($allowed_hosts as $key => $val){
	//	$allowed_hosts[$key] = $allowed_hosts[$key] . '.vibecompass.' . TLD;
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
	
	//one exception, www.staticcompass.com/assets/
	if($_SERVER['HTTP_HOST'] == 'www.staticcompass.' . TLD){
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
	#force non-www to www.vibecompass.com
	#DEVELOPMENT URLS
	RewriteCond %{HTTP_HOST} !^www.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^ar.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^cs.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^de.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^el.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^es.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^fr.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^hi.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^it.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^iw.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^ja.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^ko.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^nl.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^no.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^pl.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^pt.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^ru.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^sv.vibecompass.dev$
	RewriteCond %{HTTP_HOST} !^zh.vibecompass.dev$
	
	#PRODUCTION URLS
	RewriteCond %{HTTP_HOST} !^www.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^ar.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^cs.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^de.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^el.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^es.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^fr.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^hi.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^it.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^iw.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^ja.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^ko.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^nl.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^no.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^pl.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^pt.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^ru.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^sv.vibecompass.com$
	RewriteCond %{HTTP_HOST} !^zh.vibecompass.com$

	RewriteRule ^(.*)$ http://www.vibecompass.com/$1 [R=301]
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

$local = 'development';
//$local = 'production';
$cloudcontrol = 'production';
//$cloudcontrol = 'development';


if(php_sapi_name() == 'cli') {
    $local = 'development';
	$cloudcontrol = 'development';
}

if(DEPLOYMENT_ENV == 'cloudcontrol'){
	define('ENVIRONMENT', $cloudcontrol);
}else{
	define('ENVIRONMENT', $local);
}



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
	
		case 'testing':
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