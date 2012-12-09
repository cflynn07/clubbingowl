<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Generates sitemap for web-crawlers
 * 
 */
class Sitemap extends MY_Controller {
	
	/**
	 * constructor for controller, checks to see if controller methods were called via ajax
	 * 
	 * @return	null
	 */
	function __construct(){
		parent::__construct();
		
	}
	
	/**
	 * Routing method of controller, decides what private method to call based
	 * on 'method' parameter of post request
	 * 
	 * @return 	null
	 */
	function index(){
		
		header('Content-Type: text/xml');
		
		$this->load->model('model_app_data', 'app_data', true);
		$this->load->model('model_guest_lists', 'guest_lists', true);
		$this->load->model('model_team_guest_lists', 'team_guest_lists', true);
		
		$data['all_cities_venues'] = $this->app_data->retrieve_all_cities();
		$data['all_cities_promoters'] = $this->app_data->retrieve_all_cities(true);
		
	
		//retrieve all promoters
		$data['all_promoters'] = $this->app_data->retrieve_all_promoters();	
		foreach($data['all_promoters'] as &$promoter){
			var_dump($promoter);
			$promoter->guest_lists = $this->guest_lists->retrieve_day_guest_lists($promoter->up_id, false, $promoter->t_fan_page_id);
		}
		
		$data['all_venues'] = $this->app_data->retrieve_all_venues();
		foreach($data['all_venues'] as &$venue){
			$venue->guest_lists = $this->team_guest_lists->retrieve_all_guest_lists($venue->tv_id);
		}
		
		
		
		$data['vc_friends'] = $this->app_data->retrieve_all_users();
		$data['time'] = date('Y-m-d', time());
				
	//	Kint::dump($data); die();
				
		$output = $this->load->view('sitemap/view_sitemap', $data, true);
		
	//	$output = str_replace('encoding="UTF-8";', 'encoding="UTF-8"', $output);

		$this->output->set_output('<?xml version="1.0" encoding="UTF-8"?>' . $output);
		
	}
	
}

/* End of file sitemap.php */
/* Location: ./application/controllers/sitemap.php */