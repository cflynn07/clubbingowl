<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function friends_promoters_correlate($friends, $promoters){
    $CI =& get_instance();
	
	
	
	return true;
	
	
	
	$CI->load->model('model_app_data', 'app_data', true);
	$active_cities = $CI->app_data->retrieve_active_cities();
	$CI->load->vars('active_cities', $active_cities);
	
	$active_promoter_cities = $CI->app_data->retrieve_active_cities(true);
	$CI->load->vars('active_promoter_cities', $active_promoter_cities);
    
}