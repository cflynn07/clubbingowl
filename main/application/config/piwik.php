<?php defined('BASEPATH') OR exit('No direct script access allowed');




$production_piwik_base_url 			= 'http://piwik.' . SITE . '.com/piwik/';
$production_piwik_base_url_https 	= 'https://piwik.' . SITE . '.com/piwik/';
$production_piwik_token 			= '871f0bc93ac2e069826e654a429af83e';





	
$config['piwik_url'] = $production_piwik_base_url;

// HTTPS Base URL to the Piwik Install (not required)
$config['piwik_url_ssl'] = $production_piwik_base_url_https;

// Piwik API token, you can find this on the API page by going to the API link from the Piwik Dashboard
$config['token'] = $production_piwik_token;


// Piwik Site ID for the website you want to retrieve stats for
$config['site_id'] = 1;

// To turn geoip on, you will need to set to TRUE  and GeoLiteCity.dat will need to be in helpers/geoip
$config['geoip_on'] = FALSE;

// Controls whether piwik_tag helper function outputs tracking tag (for production, set to TRUE)
$config['tag_on'] = TRUE;
