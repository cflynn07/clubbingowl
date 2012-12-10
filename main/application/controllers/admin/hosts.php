<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hosts extends MY_Controller {

	private $vc_user 	= null;
	private $host 		= null;
	private $view_dir 	= 'admin/hosts/';
	private $date		= null;
	
	
	public $header_html = '';
	public $body_html	= '';
	public $footer_html = '';
	
	
	/*
	 * class constructor, determines if user is properly authenticated, exits if false
	 * 
	 * @return	null
	 * */
	function __construct(){
		parent::__construct();
				
		//temporary!
//		error_reporting(E_ALL);
//		ini_set('display_errors', '1');
		
		/**
		 * Authenticates user and verifies they are logged in
		 * 	call these methods before any loading done
		 */
		/*--------------------- Login Handler ------------------------*/
		$this->vc_user = $vc_user = json_decode($this->session->userdata('vc_user'));
		if(!isset($vc_user->host)){
			$this->_login();
			die();
		}
		if($this->uri->segment(3) == 'logout'){
			$this->_logout();
			die();
		}
		/*--------------------- End Login Handler --------------------*/
		
		$this->load->model('model_users_hosts', 'users_hosts', true);
		$this->host = $this->users_hosts->retrieve_team_host($vc_user->oauth_uid);
				
				
				
		$this->load->vars(array(
			'is_promoter' 	=> false,
			'is_manager' 	=> false,
			'is_host'		=> true
		));

		$this->load->vars('team_fan_page_id', $this->host->th_teams_fan_page_id);
		$this->load->vars('users_oauth_uid', $vc_user->oauth_uid);
		$this->load->vars('subg', 'hosts');
		
		
		
		$this->load->model('model_team_messaging', 'team_messaging', true);
		$team_chat_members = $this->team_messaging->retrieve_team_members(array('teams_fan_page_id' => $this->host->th_teams_fan_page_id));
		$this->load->vars('team_chat_members', $team_chat_members);
		
	}
	
	/**
	 * /admin/$arg0/$arg1/$arg2/$arg3/$arg4/
	 * Control point, chooses private method to handle request based on URL
	 * Example:
	 * 		www.vibecompass.com/admin/venues/settings/
	 * 			- $arg0 = 'venues'
	 * 			- $arg1 = 'settings'
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
		if($this->input->is_ajax_request() && !$this->input->post('ajaxify')){
				
			$arg0 = 'dashboard';
			
			//check to see if method exists, throw error if false
			if(!method_exists($this, '_ajax_' . $arg0)){
				die(json_encode(array('success' => false)));
			}

			call_user_func(array($this, '_ajax_' . $arg0), $arg0, $arg1, $arg2);
			return;
		}
		/*--------------------- End AJAX Request Bypass Handler ---------------------*/
		
		
		
	//	$arg0 = 'dashboard';	
		
		
		# ----------------------------------------------------------------------------------- #
		#	BEGIN CONTROLLER METHOD ROUTING													  #
		# ----------------------------------------------------------------------------------- #	
		/*
		 * /
		 * Examples: 
		 * 		www.vibecompass.com/admin/managers/
		 * 
		 * 		$arg0 = ''
		 * 		$arg1 = ''
		 * 		$arg2 = ''
		 * */
		if($arg0 == ''){
			
			
			
			
			
		}
		/*
		 * /[function identifier: specific controller private method to invoke]/ --multiple
		 * Examples:
		 * 		www.vibecompass.com/admin/managers/settings/
		 * 
		 * 		$arg0 = 'settings'
		 * 		$arg1 = ''
		 * 		$arg2 = ''
		 * */
		elseif($arg0 != '' && $arg1 == ''){
						
			switch($arg0){
				
				default:
					//show_error('invalid url', 404);
					break;
					
			}
			
		}
		/*
		 * /[function identifier: specific controller private method to invoke]/[id specifier] --multiple
		 * Limiter Check
		 * 		www.vibecompass.com/admin/managers/promoters/fede_wild_child
		 * 
		 * 		$arg0 = 'promoters'
		 * 		$arg1 = 'fede_wild_child'
		 * 		$arg2 = ''
		 * */
		elseif($arg0 != '' && $arg1 != '' && $arg2 == ''){
			
			switch($arg0){
				
				default:
					show_error('invalid url', 404);
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
		

		# ---------------- LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		
		$method = 'dashboard';
		
		

		
			
			
			
		//call 'body' function and include all arguments/url-segments
		call_user_func(array($this, '_' . $method), $arg0, $arg1, $arg2);
		
		
		if(!$this->input->post('ajaxify')){

			$this->header_html = $this->load->view('admin/hosts/view_admin_header', '', true);
		
			//Display the footer view after the header/body views have been displayed
			$this->footer_html = $this->load->view('admin/hosts/view_admin_footer', '', true);
		
		}
		# ---------------- END LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		
		//Construct final output for browser
		$this->output->set_output($this->header_html . $this->body_html . $this->footer_html);
		
		
		# ---------------- END LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		
	}

	/*******************************************************************************************************************
	 * 	END CONTROLLER ENTRY POINT ROUTING FUNCTIONS
	 * 		Below functions called based on arguments to index function. They are responsible for rendering page content
	/ ******************************************************************************************************************/
	
	/**
	 * Dashboard page, show statistics related to team
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _dashboard($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->model('model_teams', 'teams', true);
		$data = new stdClass;
		
		if(preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $arg0, $parts)){		
		    $year  = $parts[1];
		    $month = $parts[2];
		    $day   = $parts[3];
			
			$this->date = $year . '-' . $month . '-' . $day;
					
		}else{
			
			$this->date = date('Y-m-d', time());
			if($this->input->post('ajaxify')){
				echo '<script type="text/javascript">window.location = "/admin/hosts/' . $this->date . '/";</script>';
				die();
			}else{
				redirect('/admin/hosts/' . $this->date . '/', 302);
				die();
			}
			
		}
		
		
		
		
		
		$data->current_date = date('Y-m-d', time());
		$this->load->vars('current_date', $data->current_date);
		$this->load->vars('active_date', $this->date);
				
				
				
				
		if($this->vc_user->host->th_banned == '1' || $this->vc_user->host->th_quit == '1'){
			$this->body_html = $this->load->view($this->view_dir . 'dashboard/view_hosts_dashboard_quit', 	'', 	true);
		}else{
			
			
			list($promoters, $team_venues, $backbone) = $this->_helper_retrieve_gl_data();
			$data->team 		= $this->teams->retrieve_team($this->vc_user->host->th_teams_fan_page_id);
			$data->promoters 	= $promoters;
			$data->team_venues 	= $team_venues;
			$data->backbone 	= $backbone;
			
			$this->body_html = $this->load->view($this->view_dir . 'dashboard/view_hosts_dashboard', 		array('data' => $data), 	true);
			
			
		}
		
		
		
		
		
	}
	
	/**
	 * 
	 */
	private function _helper_retrieve_gl_data($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$backbone = new stdClass;
		$backbone->selected_date = $this->date;
		$backbone->date = date('Y-m-d', time());
		
		$this->load->model('model_teams', 'teams', true);
		$this->load->model('model_users_managers', 'users_managers', true);
		$this->load->model('model_users_promoters', 'users_promoters', true);
		$this->load->model('model_guest_lists', 'guest_lists', true);
		
		$promoters 		= $this->teams->retrieve_team_promoters($this->vc_user->host->th_teams_fan_page_id);
		$team_venues 	= $this->users_managers->retrieve_team_venues($this->vc_user->host->th_teams_fan_page_id);
		
		
		
		
		
		
		
		
		
		
		
		//get all the promoter's guest lists and members + filter for the selected date (date in url)
		foreach($promoters as &$pro){
			//fetch guest lists		
			
			$weekly_guest_lists 	= $this->users_promoters->retrieve_promoter_guest_list_authorizations($pro->up_id);
			
			//for each guest list, find all groups associated with it
			foreach($weekly_guest_lists as $key => &$gla){
										
				$gla->human_date 	= $gla->human_date 	= date('l m/d/y', strtotime(rtrim($gla->pgla_day, 's')));
				$gla->iso_date 		= $gla->iso_date 	= date('Y-m-d', strtotime(rtrim($gla->pgla_day, 's')));
				
				//we only care about the GLA of today
				if($gla->iso_date != $this->date){
					unset($weekly_guest_lists[$key]);												
				}
				
				$gla->groups = $this->guest_lists->retrieve_single_guest_list_and_guest_list_members($gla->pgla_id, $gla->pgla_day);

			}
			
			$pro->weekly_guest_lists 	= $weekly_guest_lists;

		}
		
		
		
		
		
		
		
		
		
		
		
		
		$backbone->team_venues = $team_venues;
		
		return array($promoters, $team_venues, $backbone);
		
	}
	
	/*******************************************************************************************************************
	 * 	END CONTROLLER VIEW DISPLAY FUNCTIONS
	 * 		Below functions are called via AJAX and helpers
	/ ******************************************************************************************************************/

	
	/**
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _ajax_dashboard($arg0 = '', $arg1 = '', $arg2 = ''){
		
				
		$vc_method = $this->input->post('vc_method');
		if(!$vc_method){
			die(json_encode(array('success' => false, 'message' => 'vc_method required')));
		}
		
		switch($vc_method){
			case 'venue_gl_retrieve':
								
				$date = date('Y-m-d', time());
				$tv_id = $this->input->post('tv_id');
				if($tv_id === false)
					die(json_encode(array('success' => false)));
				
				$this->load->model('model_users_hosts', 'users_hosts', true);
				$result = $this->users_hosts->retrieve_venue_guest_lists($tv_id, $date, $this->vc_user->host->th_teams_fan_page_id);
				die(json_encode(array('success' => true, 
										'message' => $result)));
				
				break;
			case 'checkin_event':
				
				$type = $this->input->post('type');
				$glr_id = $this->input->post('glr_id');
				
				if($type == 'pglr'){

					$this->db->where(array('id' => $glr_id));
					$this->db->update('promoters_guest_lists_reservations', 
										array('checked_in' => 1, 
												'checked_in_time' => time(), 
												'checked_in_by_host' => $this->vc_user->oauth_uid));
										
				}else{
					
					$this->db->where(array('id' => $glr_id));
					$this->db->update('teams_guest_lists_reservations', 
										array('checked_in' => 1, 
												'checked_in_time' => time(), 
												'checked_in_by_host' => $this->vc_user->oauth_uid));
										
				}
				
				$message = $this->db->last_query();
				
				die(json_encode(array('success' => true, 'message' => $message)));
				
				break;
			default:
				break;
		}
		
	}
	


	/**
	 * Unsets session data to log out user
 	 *
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _logout(){
		//$this->session->unset_userdata('admin_logged_in');
		redirect('/', 'refresh');
		return;
	}
	
	/**
	 * Displays login prompt to unathenticated users
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _login(){
		redirect('/', 'refresh');
		return;
	}
}

/* End of file promoters.php */
/* Location: ./application/controllers/admin/promoters.php */