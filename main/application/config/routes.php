<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] 	= "primary";
$route['404_override'] 			= 'error';

/*
 * Allow all url sections to be passed into index() as arguments, index will be 'control' function of 
 * class and will call private methods based on input of said arguments
 * */

//admin area related routes:
$route['admin/promoters/(:any)'] 	= 'admin/promoters/index/$1';
$route['admin/managers/(:any)'] 	= 'admin/managers/index/$1';
$route['admin/hosts/(:any)'] 		= 'admin/hosts/index/$1';
$route['admin/super_admins/(:any)'] = 'admin/super_admins/index/$1';
$route['admin'] 					= 'error';


$route['promoters/(:any)'] 	= 'promoters/index/$1';
$route['friends/(:any)'] 	= 'friends/index/$1';
$route['venues/(:any)'] 	= 'venues/index/$1';
$route['profile/(:any)'] 	= 'profile/index/$1';
$route['primary/(:any)'] 	= 'primary/index/$1';
$route['corp/(:any)'] 		= 'corp/index/$1';
$route['twilio/(:any)'] 	= 'twilio/index/$1';

# ---------- facebook tab routes --------- #
$route['facebook/(:any)'] 	= 'facebook/primary/index/$1';
$route['plugin/(:any)'] 	= 'plugin/index/$1';

# ---------- non-display routes (should only ever be called by ajax) ---------- #
$route['ajax/auth/(:any)'] 				= 'ajax/auth/index/$1';
$route['ajax/auto_suggest/(:any)'] 		= 'ajax/auto_suggest/index/$1';
$route['ajax/facebook/(:any)'] 			= 'ajax/facebook/index/$1';
$route['ajax/admin_messages/(:any)'] 	= 'ajax/admin_messages/index/$1';
$route['ajax/admin_client_data/(:any)'] = 'ajax/admin_client_data/index/$1';
$route['ajax/pusher_presence/(:any)'] 	= 'ajax/pusher_presence/index/$1';
$route['ajax/notifications/(:any)'] 	= 'ajax/notifications/index/$1';



# ----------- sitemap url route -------------- #
$route['sitemap.xml'] = 'sitemap/index';
/* End of file routes.php */
/* Location: ./application/config/routes.php */