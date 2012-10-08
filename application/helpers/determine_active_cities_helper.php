<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function determine_active_cities(){
    $CI =& get_instance();
	
	$CI->load->model('model_app_data', 'app_data', true);
	$active_cities = $CI->app_data->retrieve_active_cities();
	$CI->load->vars('active_cities', $active_cities);
	
	$active_promoter_cities = $CI->app_data->retrieve_active_cities(true);
	$CI->load->vars('active_promoter_cities', $active_promoter_cities);
    
}