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

$base = '20_';

$cache_kill = true;


$config['cache_global_css'] 	= $base . '1321057555' . ((MODE == 'local' && $cache_kill) ? '_' . time() : '');
$config['cache_global_js'] 		= $base . '1321057555' . ((MODE == 'local' && $cache_kill) ? '_' . time() : '');



$file = '/home/dotcloud/current/custom.json';
if(file_exists($file)){
	$custom = json_decode(file_get_contents($file), true);

	
	$config['cache_global_css'] = $custom['deployment_unique_id'];
	$config['cache_global_js'] 	= $custom['deployment_unique_id'];
}

$config['cache_global_images'] 	= $base . '1321057401';