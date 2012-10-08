<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| ASSET CACHE VERSIONING
| -------------------------------------------------------------------
| This file provides version-identifiers (unix-timestamps) for
| different asset groups. VibeCompass uses a CDN (sort of - AWS S3)
| for delivering static content. These static-content files are sent
| with infinite expiry headers, and the only way to force browsers to
| download the latest version of these files is to alter the filename.
| 
| Hence, we give each group of files a folder with a version number.
| Ex: /1313447011/css/filename.css
| There are six groups of versioned assets. Each group shares a common
| version number
|
| - Admin
| 	- css
|	- js
|	- images
|
| - Web
|	- css
|	- js
|	- images
|
| - Global
|	- css
|	- js
|	- images
|
| It probably wouldn't be feasible to track each asset individually.
| That's why we're doing it this way.
|
| -------------------------------------------------------------------
| Version Numbers
| -------------------------------------------------------------------
| */

$base = '69_';




$config['cache_global_css'] 		= $base . '1321057759';
$config['cache_global_js'] 			= $base . '1321057359';










$config['cache_admin_css'] 			= $base . '1321057401';
$config['cache_admin_js'] 			= $base . '1321057401';
$config['cache_admin_images']		= $base . '1321057401';

$config['cache_karma_css'] 			= $base . '1321057401';
$config['cache_karma_js'] 			= $base . '1321057402';
$config['cache_karma_images'] 		= $base . '1321057401';

$config['cache_front_css'] 			= $base . '1321057455';
$config['cache_front_js'] 			= $base . '1321057407';
$config['cache_front_images'] 		= $base . '1321057401';


$config['cache_global_images'] 		= $base . '1321057401';

$config['cache_facebook_css'] 		= $base . '1321057401';
$config['cache_facebook_js'] 		= $base . '1321057402';
$config['cache_facebook_images'] 	= $base . '1321057401';





/*
| -------------------------------------------------------------------
| PAGE CACHE CONTROL
| -------------------------------------------------------------------
|
| Maintains separate groups of cached pages throughout the site. The
| idea here is to provide one location in the code where you can modify
| the caching of various groups of files throughout the site to assist
| with temporarily bringing the site down in the future for upgrades and
| maintenance. 
|
| An upgrade plan would presumably involve setting all of the cached 
| pages to a 0, waiting until the cached versions on all users browsers
| expire, and then bringing down the site. This process will ensure users
| see a temporary 'Down for maintenance, be back soon' page instead of 
| a cached version of the existing website.
| */

$config['cache_group_a'] = '';
$config['cache_group_b'] = '';
$config['cache_group_c'] = '';
$config['cache_group_d'] = '';
