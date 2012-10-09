<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

/*  
 * pre_system
    Called very early during system execution. Only the benchmark and hooks class have been loaded at this point. No routing or other processes have happened.
    
 * pre_controller
    Called immediately prior to any of your controllers being called. All base classes, routing, and security checks have been done.
    
 * post_controller_constructor
    Called immediately after your controller is instantiated, but prior to any method calls happening.
    
 * post_controller
    Called immediately after your controller is fully executed.
    
 * display_override
    Overrides the _display() function, used to send the finalized page to the web browser at the end of system execution. This permits you to use your own display methodology. Note that you will need to reference the CI superobject with $this->CI =& get_instance() and then the finalized data will be available by calling $this->CI->output->get_output()
    
 * cache_override
    Enables you to call your own function instead of the _display_cache() function in the output class. This permits you to use your own cache display mechanism.
    
 * post_system
    Called after the final rendered page is sent to the browser, at the end of system execution after the finalized data is sent to the browser.
 * 
 * */

$hook['pre_controller'][] = array(
	'class' => 'Pear_hook',
	'function' => 'index',
	'filename' => 'pear_hook.php',
	'filepath' => 'hooks'
);


if(isset($_SERVER['REQUEST_URI']) 
	&& strpos($_SERVER['REQUEST_URI'], '/admin/hosts') !== 0
//	&& strpos($_SERVER['REQUEST_URI'], '/plugin') !== 0
	&& strpos($_SERVER['REQUEST_URI'], '/sitemap.xml') !== 0 ){
//	&& strpos($_SERVER['REQUEST_URI'], '/facebook') !== 0){

	if(ENVIRONMENT == 'production'){
		// compress output
		$hook['display_override'][] = array(
			'class' => '',
			'function' => 'compress',
			'filename' => 'compress.php',
			'filepath' => 'hooks'
		);	
	}

		
}

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */