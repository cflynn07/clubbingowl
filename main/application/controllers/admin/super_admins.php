<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Super_admins extends MY_Controller {
		
	private $view_dir = 'admin/super_admins/';
	private $vc_user;
	
	/*
	 * class constructor, determines if user is properly authenticated, exits if false
	 * 
	 * @return	null
	 * */
	function __construct(){
		parent::__construct();
					
		/*
		 * Authenticates user and verifies they are logged in
		 * 	call these methods before any loading done
		 * */
		/*--------------------- Login Handler ------------------------*/
		$vc_user = json_decode($this->session->userdata('vc_user'));
		if(!isset($vc_user->super_admin)){
			$this->_logout();
			die();
		}
		if($this->uri->segment(3) == 'logout'){
			$this->_logout();
			die();
		}
		/*--------------------- End Login Handler --------------------*/
				
		$this->vc_user = $vc_user;		
				
		$this->load->vars('users_oauth_uid', $vc_user->oauth_uid);
		$this->load->vars('subg', 'super_admins');
		
	}
	
	/**
	 * /admin/$arg0/$arg1/$arg2/$arg3/$arg4/
	 * Control point, chooses private method to handle request based on URL
	 * Example:
	 * 		www.vibecompass.com/admin/super_admins/no_example
	 * 			- $arg0 = 'no_example'
	 * 			- $arg1 = ''
	 * 			- $arg2 = Limiter check, throws 404 for non-existant urls
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return 	null
	 * */
	public function index($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
		
		
		/*--------------------- AJAX Request Bypass Handler ---------------------*/
		//Note: ocupload is the name of the plugin used for one-click image uploading
			//it creates a hidden iframe which is used to submit an image without a page refresh
		if($this->input->is_ajax_request() || $this->input->post('ocupload')){
			
			//SPECIAL CASES:
			//we want these methods to fire but we don't want to force users to supply extra url segment
			if($arg0 == ''){
				$arg0 = 'dashboard';
			}
			 
			//check to see if method exists, throw error if false
			if(!method_exists($this, '_ajax_' . $arg0)){
				log_message('error', 'undefined method called via ajax: admin/super_admins->' . $arg0);
				die(json_encode(array('success' => false)));
			}
			
			call_user_func(array($this, '_ajax_' . $arg0), $arg0, $arg1, $arg2);
			return;
		}
		/*--------------------- End AJAX Request Bypass Handler ---------------------*/
		
		
		
		/*
		
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		
		
		
		
		
		
		if(isset($_ENV['CRED_FILE']))
			$string = file_get_contents($_ENV['CRED_FILE'], false);
		else 
			$string = false;
		
			
		# the file contains a JSON string, decode it and return an associative array
		if($string)
			$creds = json_decode($string);
		else {
			$creds = false;
		}
		
		echo '<pre>';
		$dir = FCPATH . 'vcweb2/assets/web/css/base.css';
		$contents = file_get_contents($dir);
		var_dump($contents);
		
		var_dump(FCPATH);
		var_dump($_ENV);
		var_dump($creds);
		
		*/
		
		
		/* ------------------------------- Prepare static asset urls ------------------------------- */	
		//Set in 'CONTROLLER METHOD ROUTING,' passed to the header view. Loads additional
		//javascript/css files+properties that are unique to specific pages loaded from this
		//controller
		$header_data['additional_admin_javascripts'] = array();
		$header_data['additional_admin_css'] = array();
		$header_data['additional_global_javascripts'] = array();
		$header_data['additional_global_css'] = array();
		//additional_js_properties are javascript variables defined in the global namespace, making them
			//available to code in included js files
		$header_data['additional_js_properties'] = array();
		/* ------------------------------- End prepare static asset urls ------------------------------- */
		
		# ----------------------------------------------------------------------------------- #
		#	BEGIN CONTROLLER METHOD ROUTING													  #
		# ----------------------------------------------------------------------------------- #	
		/*
		 * /
		 * Examples: 
		 * 		www.vibecompass.com/admin/super_admins/
		 * 
		 * 		$arg0 = ''
		 * 		$arg1 = ''
		 * 		$arg2 = ''
		 * */
		if($arg0 == ''){
			
			$arg0 = 'dashboard';			

			
		}
		/*
		 * /[function identifier: specific controller private method to invoke]/ --multiple
		 * Examples:
		 * 		www.vibecompass.com/admin/super_admins/logout/
		 * 
		 * 		$arg0 = 'guest_lists'
		 * 		$arg1 = ''
		 * 		$arg2 = ''
		 * */
		elseif($arg0 != '' && $arg1 == ''){
			
			switch($arg0){
				case 'settings':
					
					break;
				case 'logout':
					
					break;
				case 'impersonate':
					break;
				default:
					show_404();
					break;
			}
			
		}
		/*
		 * /[function identifier: specific controller private method to invoke]/[id specifier] --multiple
		 * Limiter Check
		 * 		www.vibecompass.com/admin/promoters/edit_guest_list/55
		 * 
		 * 		$arg0 = 'edit_guest_list'
		 * 		$arg1 = '55'
		 * 		$arg2 = ''
		 * */
		elseif($arg0 != '' && $arg1 != '' && $arg2 == ''){
			
			switch($arg0){
				default:
					show_404();
					break;
			}
			
		}
		/*
		 * Limiter check
		 * 
		 * */
		elseif($arg0 != '' && $arg1 != '' && $arg2 != ''){
			show_error('Invalid url', 404); //<-- there are no sections of the admin panel with 3 url segments
		}
		# ----------------------------------------------------------------------------------- #
		#	END CONTROLLER METHOD ROUTING													  #
		# ----------------------------------------------------------------------------------- #	
		
		/**
		 * 'header_data' is made globally available to all views so that the 'view_common_header'
		 * view can be included from the header files of all themes to properly include
		 * external css/js files and define global-namespace js variables
		 */
		$this->load->vars('header_data', $header_data);
		
		# ---------------- LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		
		//display header view		
		$this->load->view($this->view_dir . 'view_admin_header');
			
		//call 'body' function and include all arguments/url-segments
		call_user_func(array($this, '_' . $arg0), $arg0, $arg1, $arg2);
		
		//Display the footer view after the header/body views have been displayed
		$this->load->view($this->view_dir . '/view_admin_footer');
		
		# ---------------- END LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		
	}

	/*******************************************************************************************************************
	 * 	END CONTROLLER ENTRY POINT ROUTING FUNCTIONS
	 * 		Below functions called based on arguments to index function. They are responsible for rendering page content
	/ ******************************************************************************************************************/
	
	/**
	 * Display super_admin dashbaord page
	 *
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _dashboard($arg0 = '', $arg1 = '', $arg2 = ''){
		
		//retrieve database statistics:
		$this->load->model('model_app_data', 'app_data', true);
		
		$statistics = new stdClass;
//		$statistics->num_venues = $this->app_data->retrieve_num_venues();
		$statistics->num_promoters = $this->app_data->retrieve_num_promoters();
		$statistics->num_vc_users = $this->app_data->retrieve_num_vc_users();
		$statistics->joins_past_3_days = $this->app_data->retrieve_num_vc_users(array('since_date' => 3));
		$statistics->joins_past_7_days = $this->app_data->retrieve_num_vc_users(array('since_date' => 7));
		$statistics->joins_past_14_days = $this->app_data->retrieve_num_vc_users(array('since_date' => 14));
		
		$data['statistics'] = $statistics;
		
		$this->load->view($this->view_dir . 'dashboard/view_super_admin_dashboard', $data);
		
	}
	
	/**
	 * Display teams and settings page
	 *
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _settings($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->model('model_app_data', 'app_data', true);
		$data['teams_promoters_venues'] = $this->app_data->retrieve_all_teams_promoters_venues();
		
		$this->load->view($this->view_dir . 'settings/view_super_admin_settings', $data);
		
	}
	
	/**
	 * Display teams and settings page
	 *
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _impersonate($arg0 = '', $arg1 = '', $arg2 = ''){
		
		Kint::dump($this->vc_user);
		
		$this->load->model('model_app_data', 'app_data', true);
		$data['managers'] = $this->app_data->retrieve_all_managers();
		$data['promoters'] = $this->app_data->retrieve_all_promoters();
		$data['hosts'] = $this->app_data->retrieve_all_hosts();
		
			
		$this->load->view($this->view_dir . 'impersonate/view_super_admin_impersonate', $data);
		
	}
	
	/**
	 * Unsets session data to log out user
	 * 
	 * @return 	null
	 * */
	private function _logout(){
		redirect('/', 'refresh');
		return;
	}
	
	/*******************************************************************************************************************
	 * 	END CONTROLLER VIEW DISPLAY FUNCTIONS
	 * 		Below functions are called via AJAX and helpers
	/ ******************************************************************************************************************/

	/**
	 * Handles AJAX requests made from settings page, such as approving/banning promoters and teams
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _ajax_settings($arg0 = '', $arg1 = '', $arg2 = ''){
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'Invalid request')));
		}
		
		switch($vc_method){
			case 'approve_ban_team':
				
				$this->load->model('model_teams', 'teams', true);
				$this->teams->update_team($this->input->post('team_fan_page_id'), array('completed_setup' => ($this->input->post('completed_setup') == 'true') ? 1 : 0));
				
				if($this->input->post('completed_setup') == 'true')
					$this->teams->update_team_add_piwik($this->input->post('team_fan_page_id'));
				
				die(json_encode(array('success' => true)));
				
				break;
			case 'approve_ban_promoter':
				
				$vc_user = json_decode($this->session->userdata('vc_user'));
				
				$promoter_id = $this->input->post('promoter_id');
				$banned = $this->input->post('banned');
				
				if($banned == 'true')
					$banned = 1;
				else 
					$banned = 0;
				
				$data = array(
								'banned' => $banned
							);
							
				if($banned){
					$data['banned_time'] = time();
					$data['banned_by_user'] = $vc_user->oauth_uid;
				}
				
				$this->load->model('model_users_promoters', 'users_promoters', true);
				$this->users_promoters->update_promoter(array('promoter_id' => $promoter_id), $data);
				
				var_dump($this->db->last_query()); die();
				
				die(json_encode(array('success' => true)));
			
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'Unknown vc_method')));
				break;
		}
		
	}

	/**
	 * AJAX requests for impersonate
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _ajax_impersonate($arg0 = '', $arg1 = '', $arg2 = ''){
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'Invalid request')));
		}
		
		switch($vc_method){
			case 'impersonate':
								
				$type = $this->input->post('type');
				$oauth_uid = $this->input->post('oauth_uid');
				if($type == 'promoter'){

					$up_id 				= $this->input->post('up_id');
					$team_fan_page_id 	= $this->input->post('team_fan_page_id');
					$up_users_oauth_uid = $this->input->post('up_users_oauth_uid');
					
					
					$promoter = new stdClass;
					$promoter->up_id = $up_id;
					$promoter->t_fan_page_id = $team_fan_page_id;
					$promoter->up_users_oauth_uid = $up_users_oauth_uid;
					
		//			$this->vc_user->oauth_uid = $oauth_uid;
					$this->vc_user->promoter = $promoter;
					$this->session->set_userdata('vc_user', json_encode($this->vc_user));
					
				
				}elseif($type == 'manager'){
					
					$mt_id = $this->input->post('mt_id');
					$mt_users_oauth_uid = $this->input->post('mt_users_oauth_uid');
					$team_name = $this->input->post('team_name');
					$team_fan_page_id = $this->input->post('team_fan_page_id');
					$team_description = $this->input->post('team_description');
					$team_piwik_id_site = $this->input->post('team_piwik_id_site');
					$team_completed_setup = $this->input->post('team_completed_setup');
					$c_id = $this->input->post('c_id');
					$c_name = $this->input->post('c_name');
					$c_state = $this->input->post('c_state');
					
					$manager = new stdClass;
					$manager->mt_id		= $mt_id;
					$manager->mt_user_oauth_uid = $mt_users_oauth_uid;
					$manager->team_name = $team_name;
					$manager->team_fan_page_id = $team_fan_page_id;
					$manager->team_description = $team_description;
					$manager->team_piwik_id_site = $team_piwik_id_site;
					$manager->team_completed_setup = $team_completed_setup;
					$manager->c_id = $c_id;
					$manager->c_name = $c_name;
					$manager->c_state = $c_state;
					
		//			$this->vc_user->oauth_uid = $oauth_uid;
					$this->vc_user->manager = $manager;
					$this->session->set_userdata('vc_user', json_encode($this->vc_user));
					
				}
				
				die(json_encode(array('success' => true, 'message' => 'now impersonating ' . $oauth_uid)));
				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'Unknown vc_method')));
				break;
		}
		
	}
	
}

/* End of file super_admins.php */
/* Location: ./application/controllers/admin/super_admins.php */