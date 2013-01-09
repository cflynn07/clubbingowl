<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Managers extends MY_Controller {
		
	private $vc_user = null;
	private $view_dir = 'admin/managers/';
	
	private $header_html 	= '';
	private $body_html 		= '';
	private $footer_html 	= '';
	
	/*
	 * class constructor, determines if user is properly authenticated, exits if false
	 * 
	 * @return	null
	 * */
	function __construct(){
		parent::__construct();
		
		/**
		 * Authenticates user and verifies they are logged in
		 * 	call these methods before any loading done
		 */
		/*--------------------- Login Handler ------------------------*/
		$this->vc_user = $vc_user = json_decode($this->session->userdata('vc_user'));
		if(!isset($vc_user->manager)){
			$this->_login();
			die();
		}
		if($this->uri->segment(3) == 'logout'){
			$this->_logout();
			die();
		}
		/*--------------------- End Login Handler --------------------*/

		/* --------------------- Load manager library ------------------------ */
		$this->load->library('library_admin_managers');
		$this->library_admin_managers->initialize($vc_user->manager->mt_user_oauth_uid);
		/* --------------------- End Load manager library ------------------------ */
		
			
			
			
			
		//is team live?
		$this->db->select('t.live_status as t_live_status')
			->from('teams t')
			->where(array(
				't.fan_page_id'	=> $vc_user->manager->team_fan_page_id
			));
		$t_live_status = $this->db->get()->row()->t_live_status;
					
		$this->load->vars('mt_live_status', $t_live_status);

		if(!$t_live_status && strpos($_SERVER['REQUEST_URI'], '/admin/managers/settings_payment') !== 0){
			
			if($this->input->post('ajaxi{fy')){
				echo '<script type="text/javascript">window.location="/admin/managers/settings_payment/";</script>';
			}else{
				redirect('/admin/managers/settings_payment/', 301);
			}
			
			die();
		}
		
		
		
		
			
			
			
			
			
		if($this->input->post('vc_method') == 'user_stats_retrieve'){
			$this->_helper_retrieve_user_stats();
		}
			
		$this->load->vars('team_fan_page_id', 	$vc_user->manager->team_fan_page_id);
		$this->load->vars('users_oauth_uid', 	$vc_user->manager->mt_user_oauth_uid);
		$this->load->vars('subg', 'managers');



		$this->load->vars(array(
			'is_promoter' 	=> false,
			'is_manager' 	=> true,
			'is_host'		=> false
		));
		
		$this->load->vars('promoter_id', '');
		
		$this->load->model('model_team_messaging', 'team_messaging', true);
		$team_chat_members = $this->team_messaging->retrieve_team_members(array('teams_fan_page_id' => $vc_user->manager->team_fan_page_id));
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
				
			//	Kint::dump($this->vc_user);
				
		/* ------------------------- Force new managers into setup ------------------------- */
	
	//maybe we want to have first time setup for venue managers?
	
		//we want to capture all incoming requests that are not to /admin/ for promoters that have not gone through
		//the initial setup flow. Once at /admin/ promoters will be forced to read a setup dialog and answer fields
	
	//	if(!$this->library_promoters->promoter->completed_setup && ($this->uri->segment(3) != 'dashboard')){
	//		redirect('/admin/promoters/dashboard/', 'refresh');
	//		die();
	//	}
		/* ------------------------- End Force new managers into setup ------------------------- */
		
		
		
		/*--------------------- AJAX Request Bypass Handler ---------------------*/
		//Note: ocupload is the name of the plugin used for one-click image uploading
			//it creates a hidden iframe which is used to submit an image without a page refresh
		if(($this->input->is_ajax_request() && $this->input->post('ajaxify') === false ) || $this->input->post('ocupload')){
						
			//SPECIAL CASES:
			//we want these methods to fire but we don't want to force users to supply extra url segment
			if($arg0 == ''){
				$arg0 = 'dashboard';
			}
			
			//check to see if method exists, throw error if false
			if($this->input->post('ocupload')){
				
				if(!method_exists($this, '_ocupload_' . $arg0)){
					die(json_encode(array('success' => false)));
				}
			
			}elseif(!method_exists($this, '_ajax_' . $arg0)){
				die(json_encode(array('success' => false)));
			}
			
			if($this->input->post('ocupload')){
				call_user_func(array($this, '_ocupload_' . $arg0), $arg0, $arg1, $arg2);
			}else{
				call_user_func(array($this, '_ajax_' . $arg0), $arg0, $arg1, $arg2);
			}
			
			return;
		}
		/*--------------------- End AJAX Request Bypass Handler ---------------------*/

		
		
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
			
			$arg0 = 'dashboard';	

			
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
				case 'guest_lists':
					break;
				case 'promoters_guest_lists':
					break;
				case 'promoters_clients':
					break;
				case 'promoters_statistics':
					break;
				case 'tables':
					break;
				case 'clients':
					break;
				case 'reports_guest_lists':
					break;
				case 'reports_sales':
					break;
				case 'reports_clients':
					break;
				case 'marketing':
					break;
				case 'marketing_new':
					break;
				case 'settings_guest_lists':
					break;
				case 'settings_guest_lists_new':
					break;
				case 'settings_guest_lists_edit':
					break;
				case 'settings_payment':
					break;
				case 'settings_promoters':
					break;
				case 'settings_hosts':
					break;
				case 'settings_venues':
					break;
				case 'settings_venues_new':
					break;
				case 'manage_image':
					break;
				case 'statistics':
					break;
				case 'support':
					break;
				default:
					show_error('invalid url', 404);
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
				case 'settings_guest_lists_edit':
					break;
				case 'clients':
					break;
				case 'settings_venues_edit':
					$header_data['additional_global_javascripts'] = array(
																		'jquery.dumbformstate-1.01.js'
																		);
					break;
				case 'settings_venues_edit_floorplan':
				
					$header_data['additional_global_javascripts'] = array(
																		'jquery.price_format.1.7.min.js'
																		);
				
					$header_data['additional_admin_javascripts'] = array(
																		'jquery.ui.touch-punch.min.js'
																		);
				
					break;
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
		
		//call 'body' function and include all arguments/url-segments
		call_user_func(array($this, '_' . $arg0), $arg0, $arg1, $arg2);
		
		
		if(!$this->input->post('ajaxify')){

			$this->header_html = $this->load->view('admin/managers/view_admin_header', '', true);
						
			//Display the footer view after the header/body views have been displayed
			$this->footer_html = $this->load->view('admin/managers/view_admin_footer', '', true);
		
		}
		# ---------------- END LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		
		//Construct final output for browser
		$this->output->set_output($this->header_html . $this->body_html . $this->footer_html);
		
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
		
		
		
		
		
		
		if($this->library_admin_managers->team->team_completed_setup == '1'){
		
			$this->load->helper('run_gearman_job');
			$arguments = array('piwik_id_site' => $this->library_admin_managers->team->team_piwik_id_site);
			run_gearman_job('admin_manager_piwik_stats', $arguments);
			
		}
		
		$data = $this->_helper_retrieve_pending_requests();


		$this->body_html = $this->load->view($this->view_dir . 'dashboard/view_admin_dashboard', $data, true);
		
		
		
		
		
	}
	
	/**
	 * Displays all guest list join requests for generic guest lists
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _guest_lists($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
		
		
		
		list($users, $team_venues) = $this->_helper_manager_guest_lists_and_members();
		$data['users'] = $users;
		$data['team_venues'] = $team_venues;
		
		
		$this->body_html = $this->load->view($this->view_dir . 'guest_lists/view_managers_guest_lists', $data, true);
		
		
		
		
	}
	
	/**
	 * Displays current guest lists and information about each promoter
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _promoters_guest_lists($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->model('model_teams', 'teams', true);
		$data['promoters'] = $this->teams->retrieve_team_promoters($this->vc_user->manager->team_fan_page_id);
		
		
		
		
		
		
		
		$this->load->model('model_users_promoters', 'users_promoters', true);
		$users = array();
		
		//attach current guest list data to each promoter object
		foreach($data['promoters'] as &$promoter){
			
			//retrieve a list of all the guest lists a promoter has set up
			$weekly_guest_lists = $this->users_promoters->retrieve_promoter_guest_list_authorizations($promoter->up_id);
			
			$this->load->model('model_guest_lists', 'guest_lists', true);
			//for each guest list, find all groups associated with it
			foreach($weekly_guest_lists as &$gla){
				$gla->groups = $this->guest_lists->retrieve_single_guest_list_and_guest_list_members($gla->pgla_id, $gla->pgla_day);
			}
			
			//Sort array by upcoming dates
			foreach($weekly_guest_lists as $key => $value){
		//		$weekly_guest_lists[date('Y-m-d', strtotime(rtrim($value->pgla_day, 's')))] = $value;
		//		unset($weekly_guest_lists[$key]);
			}
			
		//	ksort($weekly_guest_lists);
		
			//attach to promoter object
			$promoter->weekly_guest_lists = $weekly_guest_lists;
			
			//record FBIDs of all users for later use
			foreach($weekly_guest_lists as $wgl){
				foreach($wgl->groups as $group){
					
			//		$users[] = $group->head_user;
			//		$users = array_merge($users, $group->entourage_users);
					
				}
			}
			
		}

	//	$users = array_unique($users);
	//	$users = array_values($users);
		$data['users'] = $users;
		
		
		

		
		
		
		$this->body_html = $this->load->view($this->view_dir . 'promoters/view_admin_manager_promoters_guest_lists', $data, true);
		
	}
	
	/**
	 * Displays client lists for each promoter and all promoters
	 *
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _promoters_clients($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->model('model_teams', 'teams', true);
		$data['promoters'] = $this->teams->retrieve_team_promoters($this->vc_user->manager->team_fan_page_id);
		
		$this->load->model('model_users_promoters', 'users_promoters', true);
		
		$users = array();
		foreach($data['promoters'] as &$promoter){
			
		//	$promoter->clients = $this->users_promoters->retrieve_promoter_clients_list($promoter->up_id, $this->vc_user->manager->team_fan_page_id);	
			$promoter->clients = $this->users_promoters->retrieve_promoter_clients_list_detailed($promoter->up_id);	
			
		//	foreach($promoter->clients as $client){
		//		$users[] = $client->pglr_user_oauth_uid;
		//	}
			
		}

		$data['users'] = $users;
		
		
		Kint::dump($data['promoters']);
		
		
		$this->body_html = $this->load->view($this->view_dir . 'promoters/view_admin_manager_promoters_clients', $data, true);
		
	}

	/**
	 * Displays generic statistics and graphs for each promoter on a manager's team
	 *
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _promoters_statistics($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->model('model_teams', 'teams', true);
		$data['promoters'] = $this->teams->retrieve_team_promoters($this->vc_user->manager->team_fan_page_id);
		
		$promoter_site_ids = array();
		foreach($data['promoters'] as $pro){
			
			if($pro->up_completed_setup != '1'){ continue; }
			
			$promoter_site_ids[$pro->up_id] = $pro->up_piwik_id_site;
			
		}
		
		if($promoter_site_ids){
			//start gearman job for retrieving promoter piwik stats
			$this->load->library('pearloader');
			$gearman_client = $this->pearloader->load('Net', 'Gearman', 'Client');
			
			# ------------------------------------------------------------- #
			#	Send guest list request to gearman as a background job		#
			# ------------------------------------------------------------- #				
			//add job to a task
			$gearman_task = $this->pearloader->load('Net', 'Gearman', 'Task', array('func' => 'gearman_admin_manager_promoter_piwik_stats',
																					'arg'  => array('promoter_site_ids' => $promoter_site_ids)));
			$gearman_task->type = Net_Gearman_Task::JOB_BACKGROUND;
			
			//add test to a set
			$gearman_set = $this->pearloader->load('Net', 'Gearman', 'Set');
			$gearman_set->addTask($gearman_task);
			 
			//launch that shit
			$gearman_client->runSet($gearman_set);
			# ------------------------------------------------------------- #
			#	END Send guest list request to gearman as a background job	#
			# ------------------------------------------------------------- #
		
			//Save background handle and server to user's session
			$gearman_admin_manager_promoter_piwik_stats = new stdClass;
			$gearman_admin_manager_promoter_piwik_stats->handle = $gearman_task->handle;
			$gearman_admin_manager_promoter_piwik_stats->server = $gearman_task->server;
			$this->session->set_userdata('gearman_admin_manager_promoter_piwik_stats', json_encode($gearman_admin_manager_promoter_piwik_stats));
		}

		
		$this->load->model('model_users_promoters', 'users_promoters', true);
		foreach($data['promoters'] as &$pro){
			
			$statistics = new stdClass;
			$statistics->num_clients = $this->users_promoters->retrieve_promoter_clients_list($pro->up_id, $this->vc_user->manager->team_fan_page_id, array('count' => true));
			$statistics->num_total_guest_list_reservations = $this->users_promoters->retrieve_num_guest_list_reservation_requests($pro->up_id, array('upcoming' => true));
			$statistics->num_upcoming_guest_list_reservations = $this->users_promoters->retrieve_num_guest_list_reservation_requests($pro->up_id);
			$statistics->trailing_weekly_guest_list_reservation_requests = $this->users_promoters->retrieve_trailing_weekly_guest_list_reservation_requests($pro->up_id);
			$pro->statistics = $statistics;
						
		}
		unset($pro);
		
		$this->body_html = $this->load->view($this->view_dir . 'promoters/view_admin_manager_promoters_statistics', $data, true);
		
	}
	
	/**
	 * Details all table requests at all venues for a team 
	 * 
	 *
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _tables($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
		
		
		list($init_users, $team_venues) = $this->_helper_venue_floorplan_retrieve_v2();
		
		$data['init_users'] = $init_users;
		$data['team_venues'] = $team_venues;

		$this->body_html = $this->load->view($this->view_dir . 'tables/view_admin_manager_tables', $data, true);
		
		
		
		
	}
	
	/**
	 * Display all clients that have reserved guest lists at this venue. Clients
	 * are users that do the actual reservation.
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _clients($arg0 = '', $arg1 = '', $arg2 = ''){
		

		$this->load->model('model_teams', 			'teams', 			true);
		$this->load->model('model_users_promoters', 'users_promoters', 	true);
		$this->load->model('model_users_managers', 	'users_managers', 	true);
		
		
		$promoters 	= $this->teams->retrieve_team_promoters($this->vc_user->manager->team_fan_page_id);
		$clients 	= array();
		
		
		
		//get a unique set of all promoter clients
		foreach($promoters as $pro){
			$pro_clients = $this->users_promoters->retrieve_promoter_clients_list_detailed($pro->up_id);
			
			foreach($pro_clients as $pc){
				
				$found = false;
				
				foreach($clients as $c){
				
					if($c->u_oauth_uid == $pc->u_oauth_uid){
						$found = true;
						break;
					}
					
				}
				
				if(!$found)
					$clients[] = $pc;
				
			}
			
		}
		
		$team_venues = $this->users_managers->retrieve_team_venues($this->vc_user->manager->team_fan_page_id);
		foreach($team_venues as $venue){
			
			$venue_clients = $this->users_managers->retrieve_venue_clients_detailed($venue->tv_id, $this->vc_user->manager->team_fan_page_id);
		
		
			foreach($venue_clients as $tc){
				
				$found = false;
				
				foreach($clients as $c){
				
					if($c->u_oauth_uid == $tc->u_oauth_uid){
						$found = true;
						break;
					}
					
				}
				
				if(!$found)
					$clients[] = $tc;
				
			}
		
		}
				
		
		
		//-------------------
		
		
		$data['clients'] = $clients;
		
		if($arg1){
			
			$client = false;
			foreach($data['clients'] as $cl){
				if($cl->u_oauth_uid == $arg1)
					$client = $cl;
			}
			
			
			//retrieve client notes

			$client_notes_team = $this->teams->retrieve_client_notes(array(
				'team_fan_page_id'	=> $this->vc_user->manager->team_fan_page_id,
				'client_oauth_uid'	=> $arg1
			));
			
			
			
			
			$users = array();
			foreach($client_notes_team as $cnt){
				$users[] = $cnt->user_oauth_uid;
			}
			$users = array_values($users);
			$users = array_unique($users);
			
			
			
			$my_client_notes = false;
			foreach($client_notes_team as $key => $cnt){
				if($cnt->user_oauth_uid == $this->vc_user->manager->mt_user_oauth_uid){
					$my_client_notes = $cnt;
					unset($client_notes_team[$key]);
					break;
				}
			}
			
			$page_data = new stdClass;
			$page_data->my_client_notes = $my_client_notes;
			$page_data->users = $users;
			$page_data->client_notes_team = $client_notes_team;
			
			
			
			
		
			$data['data']	= $page_data;
			$data['client'] = $client;
			$data['oauth_uid'] = strip_tags($arg1);
			$this->body_html = $this->load->view('admin/promoters/clients/view_clients_individual', $data, true);;
			
			return;
		}
		


		$this->body_html = $this->load->view('admin/promoters/clients/view_clients', $data, true);
		
		
		
		/*
		$this->load->model('model_users_managers', 'users_managers', true);
		
		$team_venues = $this->users_managers->retrieve_team_venues($this->vc_user->manager->team_fan_page_id);
		$users = array();
		foreach($team_venues as &$venue){
			
			$venue->clients = $this->users_managers->retrieve_venue_clients($venue->tv_id);
			foreach($venue->clients as $client){
				$users[] = $client->tglr_user_oauth_uid;
			}
			
		}
		
		$users = array_unique($users);
		$users = array_values($users);
		
		$data['team_venues'] = $team_venues;
		$data['users'] = $users;
		
		$this->body_html = $this->load->view($this->view_dir . 'clients/view_manager_clients', $data, true);
		*/
	}
	
	/**
	 * Reports on team and promoter guest lists
	 *
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null	 
	 */
	private function _reports_guest_lists($arg0 = '', $arg1 = '', $arg2 = ''){

		$this->load->model('model_users_managers', 'users_managers', true);
		$team_trailing_gl_requests = $this->users_managers->retrieve_trailing_weekly_guest_list_reservation_requests($this->vc_user->manager->team_fan_page_id);
		$data['team_trailing_gl_requests'] = $team_trailing_gl_requests;
		$team_trailing_gl_requests_percentage_attendance = $this->users_managers->retrieve_trailing_weekly_guest_list_reservation_requests_percentage_attendance($this->vc_user->manager->team_fan_page_id);
		$data['team_trailing_gl_requests_percentage_attendance'] = $team_trailing_gl_requests_percentage_attendance;


		$this->load->model('model_teams', 'teams', true);
		$data['promoters'] = $this->teams->retrieve_team_promoters($this->vc_user->manager->team_fan_page_id);
		
		
		$this->load->model('model_users_promoters', 'users_promoters', true);
		foreach($data['promoters'] as $key => &$pro){
			
			//if promoter hasn't completed setup, remove
			if($pro->up_completed_setup == '0' || $pro->up_completed_setup == 0){
				unset($data['promoters'][$key]);
				continue;
			}
			
			$statistics = new stdClass;
			$statistics->num_clients = $this->users_promoters->retrieve_promoter_clients_list($pro->up_id, $this->vc_user->manager->team_fan_page_id, array('count' => true));
			$statistics->num_total_guest_list_reservations = $this->users_promoters->retrieve_num_guest_list_reservation_requests($pro->up_id, array('upcoming' => true));
			$statistics->num_upcoming_guest_list_reservations = $this->users_promoters->retrieve_num_guest_list_reservation_requests($pro->up_id);
			$statistics->trailing_weekly_guest_list_reservation_requests = $this->users_promoters->retrieve_trailing_weekly_guest_list_reservation_requests($pro->up_id);
			$statistics->trailing_weekly_guest_list_reservation_requests_attendance = $this->users_promoters->retrieve_trailing_weekly_guest_list_reservation_requests_percentage_attendance($pro->up_id);
			
			$pro->statistics = $statistics;
						
		}
		unset($pro);
		
		
		$this->body_html = $this->load->view($this->view_dir . 'reports/view_manager_reports_guest_lists', $data, true);
		
	}
	
	/**
	 * Reports on team and promoter table sales
	 *
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null	 
	 */
	private function _reports_sales($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
		
	}

	/**
	 * Reports on all team and promoter clients
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _reports_clients($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
		
	}
	
	/**
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _marketing($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->model('model_email_marketing', 'email_marketing', true);
		
		$data = new stdClass;
		$data->campaigns 	= $this->email_marketing->retrieve_marketing_campaigns($this->library_admin_managers->team->team_fan_page_id);
		$data->clients 		= $this->email_marketing->retrieve_team_clients($this->library_admin_managers->team->team_fan_page_id);
		
		$this->body_html = $this->load->view($this->view_dir . 'marketing/view_manager_marketing', array('data' => $data), true);
	}
	
	/**
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _marketing_new($arg0 = '', $arg1 = '', $arg2 = ''){
		
	//	Kint::dump($this->library_admin_managers);
	//	Kint::dump($this->vc_user);
		
		$this->load->model('model_email_marketing', 'email_marketing', true);
		
		$data = new stdClass;
		$data->campaigns 	= $this->email_marketing->retrieve_marketing_campaigns($this->library_admin_managers->team->team_fan_page_id);
		$data->clients 		= $this->email_marketing->retrieve_team_clients($this->library_admin_managers->team->team_fan_page_id);
		
		$this->body_html = $this->load->view($this->view_dir . 'marketing/view_manager_marketing_new', array('data' => $data), true);
	}
	
	/**
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _ajax_marketing_new($arg0 = '', $arg1 = '', $arg2 = ''){
			
		$vc_method = $this->input->post('vc_method');
		
		switch($vc_method){
			case 'marketing_campaign_create':
				
				$campaign_title = $this->input->post('campaign_title');
				$campaign_content = $this->input->post('campaign_content');
				
				
				if(!$campaign_title || !$campaign_content)
					die(json_encode(array('success' => false)));
				
				
				$this->load->model('model_email_marketing', 'email_marketing', true);
				$result = $this->email_marketing->create_marketing_campaign(array(
					'team_fan_page_id'	=> $this->library_admin_managers->team->team_fan_page_id,
					'manager_oauth_uid'	=> $this->vc_user->manager->mt_user_oauth_uid,
					'campaign_title'	=> $campaign_title,
					'campaign_body'		=> $campaign_content,
					'send_time'			=> time()
				));
				
				
				
				if($result)
					die(json_encode(array('success' => true)));
				else 
					die(json_encode(array('success' => false)));
				
				
				
				break;
			default:
				die(json_encode(array('success' => false, 'message' => 'Unknown vc_method')));
				break;
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	private function _settings_payment($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->config('stripe');
		$this->load->model('model_teams', 'teams', true);
		
		
		//perform an upgrade check on a team to see if max number of promoters / hosts / managers exceeded
		$this->load->model('model_teams', 'teams', true);
		$this->teams->upgrade_check($this->vc_user->manager->team_fan_page_id);
		
		
		
		
		$this->db->select('t.last4 			as last4, 
						   t.card_type 		as type,
						   t.billing_tier 	as billing_tier')
			->from('teams t')
			->where(array(
				't.fan_page_id' => $this->vc_user->manager->team_fan_page_id
			));
		$data = $this->db->get()->row();
		$this->load->vars('card_data', $data);
		
		
		
		
		$this->body_html = $this->load->view($this->view_dir . 'settings/view_settings_payment', '', true);
		
	}
	private function _ajax_settings_payment($arg0 = '', $arg1 = '', $arg2 = ''){
	
		switch($this->input->post('vc_method')){
			case 'update_stripe_token':
			
				$this->load->library('library_payments', '', 'payments');
				$result = $this->payments->update_stripe_token(array(
					'team_fan_page_id'	=> $this->vc_user->manager->team_fan_page_id,
					'team_name'			=> $this->vc_user->manager->team_name,
					'token'				=> $this->input->post('token')
				));
			
			
			//	$this->payments->bill_manager(array(
			//		'managers_teams_id'	=> $this->vc_user->manager->mt_id
			//	));
				
				
				
				die(json_encode(array('success' => $result)));
			
			
			
				break;
			default:
				die(json_encode(array('success' => false)));
		}
				
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * Invite/Review/Delete promoters
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _settings_promoters($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->model('model_teams', 'teams', true);
		$data['promoters'] = $this->teams->retrieve_team_promoters($this->vc_user->manager->team_fan_page_id, array('completed_setup' => false));
		$data['inactive_promoters'] = $this->teams->retrieve_team_promoters($this->vc_user->manager->team_fan_page_id, array('banned_quit' => 'AND (pt.banned = 1 OR pt.quit = 1) '));
		$data['invitations'] = $this->teams->retrieve_all_team_invitations($this->library_admin_managers->team->team_fan_page_id);
		foreach($data['invitations'] as $key => $invite){
			
			$invitation_data = $invite->ui_invitation_data;
			if(strlen($invitation_data) == 0){
				continue; //legacy support before invitations had data
			}else{
				
				$invitation_data = json_decode($invitation_data);
				if(!isset($invitation_data->type)){
					//error?
					unset($data['invitations'][$key]);
					continue;
				}
				
				if($invitation_data->type != 'promoter'){
					unset($data['invitations'][$key]);
					continue;
				}
			
			}
			
		}
		
		$users = array();
		foreach($data['invitations'] as $invite){
			$users[] = $invite->ui_oauth_uid;
		}
		$users = array_unique($users);
		$users = array_values($users);
		$data['users'] = $users;
		
		//used for facebook request filtering
		$filter_uids = array();
		foreach($data['promoters'] as $pro){
			
			//Don't invite active promoters
			$filter_uids[] = $pro->u_oauth_uid;
			
		}
		foreach($data['invitations'] as $invite){
			
			//Don't reinvite users that haven't responded yet & haven't expired
			if($invite->ui_response == '0' && ($invite->ui_invitation_time + 432000) > time())
				$filter_uids[] = $invite->ui_oauth_uid;
				
		}
		$filter_uids = array_unique($filter_uids);
		$filter_uids = array_values($filter_uids);
		$data['filter_uids'] = $filter_uids;
		
		$this->body_html = $this->load->view($this->view_dir . 'settings/view_settings_promoters', $data, true);
		
	}

	/**
	 * Invite/Review/Delete hosts
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _settings_hosts($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->model('model_teams', 'teams', true);
		$data['hosts'] = $this->teams->retrieve_team_hosts($this->vc_user->manager->team_fan_page_id);
		$data['invitations'] = $this->teams->retrieve_all_team_invitations($this->vc_user->manager->team_fan_page_id, 'host');
		
		/*		
		foreach($data['invitations'] as $key => $invite){
			
			$invitation_data = $invite->ui_invitation_data;
			if(strlen($invitation_data) == 0){
				unset($data['invitations'][$key]);
				continue;
			}else{
				
				$invitation_data = json_decode($invitation_data);
				if(!isset($invitation_data->type)){
					//error?
					unset($data['invitations'][$key]);
					continue;
				}
				
				if($invitation_data->type != 'host'){
					unset($data['invitations'][$key]);
					continue;
				}
			
			}
			
		}
		*/
		
		$users = array();
		foreach($data['invitations'] as $invite){
			$users[] = $invite->ui_oauth_uid;
		}
		$users = array_unique($users);
		$users = array_values($users);
		$data['users'] = $users;
		
		//used for facebook request filtering
		$filter_uids = array();
		foreach($data['hosts'] as $host){
			
			//Don't invite active promoters
			$filter_uids[] = $host->th_users_oauth_uid;
			
		}
		foreach($data['invitations'] as $invite){
			
			//Don't reinvite users that haven't responded yet & haven't expired
			if($invite->ui_response == '0' && ($invite->ui_invitation_time + 432000) > time())
				$filter_uids[] = $invite->ui_oauth_uid;
				
		}
		$filter_uids = array_unique($filter_uids);
		$filter_uids = array_values($filter_uids);
		$data['filter_uids'] = $filter_uids;
		
	//	Kint::dump($data);
		
		$this->body_html = $this->load->view($this->view_dir . 'settings/view_settings_hosts', $data, true);
		
	}
















	private function _settings_guest_lists($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
		$this->load->model('model_users_managers', 'users_managers', true);
		$team_venues = $this->users_managers->retrieve_team_venues($this->vc_user->manager->team_fan_page_id);
		
		foreach($team_venues as &$tv){
			$tv_gla = $this->users_managers->retrieve_team_venue_guest_list_authorizations($tv->tv_id, $this->vc_user->manager->team_fan_page_id);
			$tv->tv_gla = $tv_gla;
		}

		$data['team_venues'] = $team_venues;		
		
		
		
		
		
		
		if($arg1){
			//edit a specific guest-list
			
			//do I own zis guest list
			
			$guest_list = false;
			foreach($team_venues as $tv){
				foreach($tv->tv_gla as $gla){
					if($gla->tgla_id == $arg1){
						
					}
				}
			}
			
			$this->session->delete_flashdata('manage_image');
			
			
			if(!$manage_image = $this->session->flashdata('manage_image')){
				//set this flash data so if user navigates to 'manage_image' it will allow 
				$manage_image = new stdClass;
				$manage_image->existing 	= false;
				$manage_image->type 		= 'guest_lists';
				$manage_image->live_image 	= false;
				$manage_image->return 		= 'settings_guest_lists_edit';
				$this->session->set_flashdata('manage_image', json_encode($manage_image));
			}else{
				$manage_image = json_decode($manage_image);
				$this->session->keep_flashdata('manage_image');
			}
						
			$data['manage_image'] = $manage_image;
			
			
			
			
			$this->body_html = $this->load->view($this->view_dir . 'manage_guest_lists/view_manage_guest_lists_edit', $data, true);
			
			
			return;
		}
				
				
				
				
				
		
		$this->body_html = $this->load->view($this->view_dir . 'manage_guest_lists/view_manage_guest_lists', $data, true);

	}
	private function _ajax_settings_guest_lists($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$vc_method = $this->input->post('vc_method');
		switch($vc_method){
			case 'set_deleted':
				
				$tgla_id		= $this->input->post('tgla_id');
				
				$this->db->where(array(
					'team_fan_page_id'	=> $this->vc_user->manager->team_fan_page_id,
					'id'				=> $tgla_id
				));
				$this->db->update('teams_guest_list_authorizations', array(
					'deactivated'	=> '1'
				));
				
				die(json_encode(array('success' => true)));
				
				
				break;
			case 'set_auto_approve':
				
				$auto_approve 	= ($this->input->post('auto_approve') == '1') ? '1' : '0';
				$tgla_id		= $this->input->post('tgla_id');
				
				$this->db->where(array(
					'team_fan_page_id'	=> $this->vc_user->manager->team_fan_page_id,
					'id'				=> $tgla_id
				));
				$this->db->update('teams_guest_list_authorizations', array(
					'auto_approve'	=> $auto_approve
				));
				
				die(json_encode(array('success' => true)));
				
				break;
		}
	
	}
	
	
	
	private function _settings_guest_lists_edit($arg0 = '', $arg1 = '', $arg2 = ''){
		
		//retrieve this guest list
		if(!$arg1){
			redirect('/admin/managers/settings_guest_lists/', 302);
			die();
		}
		
		//find out if this guest list belongs to this team.
		
		$this->db->select('tgla.id					as tgla_id,
							tgla.team_venue_id		as tgla_team_venue_id,
							tgla.day				as tgla_day,
							tgla.description		as tgla_description,
							tgla.min_age			as tgla_min_age,
							tgla.door_open			as tgla_door_open,
							tgla.door_close 		as tgla_door_close,
							tgla.regular_cover		as tgla_regular_cover,
							tgla.gl_cover			as tgla_gl_cover,
							tgla.name				as tgla_name,
							tgla.create_time		as tgla_create_time,
							tgla.deactivated		as tgla_deactivated,
							tgla.deactivated_time	as tgla_deactivated_time,
							tgla.additional_info_1 	as tgla_additional_info_1,
							tgla.additional_info_2 	as tgla_additional_info_2,
							tgla.additional_info_3 	as tgla_additional_info_3,
							tgla.auto_approve 		as tgla_auto_approve,
							
							tgla.image 				as tgla_image,
							tgla.x0					as tgla_x0,
							tgla.x1					as tgla_x1,
							tgla.y0					as tgla_y0,
							tgla.y1					as tgla_y1,
							
							tv.name 				as tv_name,
							tv.id					as tv_id,
							tv.description 			as tv_description,
							tv.street_address		as tv_street_address')
			->from('teams_guest_list_authorizations tgla')
			->join('team_venues tv', 'tgla.team_venue_id = tv.id')
			->where(array(
				'tgla.id'				=> $arg1,
				'tgla.deactivated'		=> 0,
				'tgla.team_fan_page_id' => $this->vc_user->manager->team_fan_page_id
			));
		$query = $this->db->get();
		$result = $query->row();
			
		if(!$result){
			redirect('/admin/managers/settings_guest_lists/', 302);
			die();
		}
						
		$data = array();
		$data['guest_list'] = $result;
		
		$this->body_html = $this->load->view($this->view_dir . 'manage_guest_lists/view_manage_guest_lists_edit', $data, true);
		
	}
	private function _ajax_settings_guest_lists_edit($arg0 = '', $arg1 = '', $arg2 = ''){
		
		//find out if this guest list belongs to this team.
		
		$this->db->select('tgla.id					as tgla_id,
							tgla.team_venue_id		as tgla_team_venue_id,
							tgla.day				as tgla_day,
							tgla.description		as tgla_description,
							tgla.min_age			as tgla_min_age,
							tgla.door_open			as tgla_door_open,
							tgla.door_close 		as tgla_door_close,
							tgla.regular_cover		as tgla_regular_cover,
							tgla.gl_cover			as tgla_gl_cover,
							tgla.name				as tgla_name,
							tgla.create_time		as tgla_create_time,
							tgla.deactivated		as tgla_deactivated,
							tgla.deactivated_time	as tgla_deactivated_time,
							tgla.additional_info_1 	as tgla_additional_info_1,
							tgla.additional_info_2 	as tgla_additional_info_2,
							tgla.additional_info_3 	as tgla_additional_info_3,
							tgla.auto_approve 		as tgla_auto_approve,
							
							tgla.image 				as tgla_image,
							tgla.x0					as tgla_x0,
							tgla.x1					as tgla_x1,
							tgla.y0					as tgla_y0,
							tgla.y1					as tgla_y1,
							
							tv.name 				as tv_name,
							tv.id					as tv_id,
							tv.description 			as tv_description,
							tv.street_address		as tv_street_address')
			->from('teams_guest_list_authorizations tgla')
			->join('team_venues tv', 'tgla.team_venue_id = tv.id')
			->where(array(
				'tgla.id'				=> $arg1,
				'tgla.deactivated'		=> 0,
				'tgla.team_fan_page_id' => $this->vc_user->manager->team_fan_page_id
			));
		$query = $this->db->get();
		$result = $query->row();
			
		if(!$result){
			die(json_encode(array('success' => false)));
		}
		
		
		
		
		
		$this->load->library('library_venues', '', 'library_venues');
		$this->load->library('library_image_upload', '', 'image_upload');
		$this->load->model('model_users_managers', 'users_managers', true);
		
		$gl_object = $this->input->post('gl_form');
		
		
		
		
		if(!isset($gl_object['guest_list_description']) || strlen($gl_object['guest_list_description']) < 10){
			die(json_encode(array('success' => false, 'message' => 'Please supply a guest list description')));
		}
		
		if(!isset($gl_object['guest_list_reg_cover']) || strlen($gl_object['guest_list_reg_cover']) == 0){
			die(json_encode(array('success' => false, 'message' => 'Please supply a regular cover charge')));
		}
		
		if(!isset($gl_object['guest_list_gl_cover']) || strlen($gl_object['guest_list_gl_cover']) == 0){
			die(json_encode(array('success' => false, 'message' => 'Please supply a guest list cover charge')));
		}
		
				
		
		foreach($gl_object as $key => $val){
			if(is_string($val)){
				$gl_object[$key] = strip_tags($val);
			}
		}
		
		
		
		//make image live			
		if($gl_object['image_data']['image'] != $result->tgla_image){
			$new_image_name = $this->image_upload->make_image_live('guest_lists', $gl_object['image_data']['image']);
		
			//crop image
			$image_data 		= new stdClass;
			$image_data->image 	= $new_image_name;
			$this->image_upload->image_crop($image_data, 'guest_lists', true, $gl_object['image_data'], true);
			$new_image_name 	= $this->image_upload->image_data['image'];
	
		}else{
			
			//crop image
			$image_data 		= new stdClass;
			$image_data->image 	= $result->tgla_image;
			$this->image_upload->image_crop($image_data, 'guest_lists', true, $gl_object['image_data'], true);
			$new_image_name 	= $this->image_upload->image_data['image'];
			
		}
		
		$this->db->where(array(
			'team_fan_page_id'	=> $this->vc_user->manager->team_fan_page_id,
			'id'				=> $arg1
		));
		$this->db->update('teams_guest_list_authorizations', array(
			'auto_approve'		=> isset($gl_object['guest_list_auto_approve']) ? 1 : 0,
			'description'		=> trim($gl_object['guest_list_description']),			
			'image'				=> $new_image_name,
			'x0'				=> $gl_object['image_data']['x0'],
			'y0'				=> $gl_object['image_data']['y0'],
			'x1'				=> $gl_object['image_data']['x1'],
			'y1'				=> $gl_object['image_data']['y1'],
			'min_age'			=> $gl_object['guest_list_min_age'],
			'door_open'			=> $gl_object['guest_list_open'],
			'door_close'		=> $gl_object['guest_list_close'],
			'regular_cover'		=> trim($gl_object['guest_list_reg_cover']),
			'gl_cover'			=> trim($gl_object['guest_list_gl_cover']),
			'additional_info_1'	=> trim($gl_object['guest_list_additional_info_1']),
			'additional_info_2'	=> trim($gl_object['guest_list_additional_info_2']),
			'additional_info_3'	=> trim($gl_object['guest_list_additional_info_3'])
		));
				
		die(json_encode(array('success' => true)));
				
	}
	private function _ocupload_settings_guest_lists_edit($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->library('library_image_upload', '', 'image_upload');
		
		
		//verifies image upload is acceptable, 
		//saves original + cropped versions to amazon s3, 
		//and updates promoter database
		if($this->image_upload->image_upload(array('type' 			=> 'venue', 
													'upload_type' 	=> 'guest_lists',
													'live_image' 	=> false,
													'image_data' 	=> false))){
				
			die(json_encode(array('success' => true,
									'image_data' => $this->image_upload->image_data)));
									
		}else{
			
			die(json_encode(array('success' => false,
									'message' => $this->image_upload->image_upload_error)));
									
		}
		
		
		
	}
	
	

	private function _settings_guest_lists_new($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->model('model_users_managers', 'users_managers', true);

		$data = array();
		$data['team_venues'] = $this->users_managers->retrieve_team_venues($this->vc_user->manager->team_fan_page_id);

		$this->body_html = $this->load->view($this->view_dir . 'manage_guest_lists/view_manage_guest_lists_new', $data, true);
		
	}
	private function _ajax_settings_guest_lists_new($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$vc_method = $this->input->post('vc_method');
		
		switch($vc_method){
			case 'new_team_guest_list':
				
				$this->load->library('library_venues', '', 'library_venues');
				$this->load->library('library_image_upload', '', 'image_upload');
				$this->load->model('model_users_managers', 'users_managers', true);
				
				$gl_object = $this->input->post('gl_form');
				
				$gl_object['guest_list_name'] = trim(preg_replace('/\s\s+/', ' ', $gl_object['guest_list_name']));
				
				if(preg_match('~[^a-z0-9 ]~i', $gl_object['guest_list_name'])){
					die(json_encode(array(
						'success' => false,
						'message' => 'List name must contain only alphanumeric characters'
					)));
				}
				
				if(!isset($gl_object['guest_list_name']) || strlen($gl_object['guest_list_name']) < 8 || strlen($gl_object['guest_list_name']) > 30){
					die(json_encode(array('success' => false, 'message' => 'Guest list name must be between 8 and 30 characters in length')));
				}
				
				if(!isset($gl_object['guest_list_description']) || strlen($gl_object['guest_list_description']) < 10){
					die(json_encode(array('success' => false, 'message' => 'Please supply a guest list description')));
				}
				
				if(!isset($gl_object['guest_list_reg_cover']) || strlen($gl_object['guest_list_reg_cover']) == 0){
					die(json_encode(array('success' => false, 'message' => 'Please supply a regular cover charge')));
				}
				
				if(!isset($gl_object['guest_list_gl_cover']) || strlen($gl_object['guest_list_gl_cover']) == 0){
					die(json_encode(array('success' => false, 'message' => 'Please supply a guest list cover charge')));
				}
				
				if(!isset($gl_object['image_data']) || $gl_object['image_data'] == 'false'){
					die(json_encode(array('success' => false, 'message' => 'Please supply an image to represent your guest list')));
				}
								
				$team_venues = $this->users_managers->retrieve_team_venues($this->vc_user->manager->team_fan_page_id);
				$found = false;
				foreach($team_venues as $tv){
					 if($tv->tv_id == $gl_object['guest_list_venue']){
					 	$found = true;
						 break;
					 }
				}
				
				if(!$found){
					die(json_encode(array('success' => false, 'message' => 'You are not authorized to promote this venue')));
				}
				
				
				
				//Does this venue already have a guest-list with this name?
				$existing_guest_list = $this->library_venues->retrieve_individual_guest_list(str_replace(' ', '_', $gl_object['guest_list_name']), $gl_object['guest_list_venue']);
				
				if($existing_guest_list){
					die(json_encode(array('success' => false, 'message' => 'A guest list with this name already exists')));
				}
				
				
				foreach($gl_object as $key => $val){
					if(is_string($val)){
						$gl_object[$key] = strip_tags($val);
					}
				}
				
				
				
				
				switch($gl_object['guest_list_weekday']){
					case 0:
						$weekday = 'mondays';
						break;
					case 1:
						$weekday = 'tuesdays';
						break;
					case 2:
						$weekday = 'wednesdays';
						break;
					case 3:
						$weekday = 'thursdays';
						break;
					case 4:
						$weekday = 'fridays';
						break;
					case 5:
						$weekday = 'saturdays';
						break;
					case 6:
						$weekday = 'sundays';
						break;
					default:
						die(json_encode(array('success' => false, 'message' => 'Invalid weekday')));
				}
				
				
				//make image live			
				$new_image_name = $this->image_upload->make_image_live('guest_lists', $gl_object['image_data']['image']);
				
				//crop image
				$image_data = new stdClass;
				$image_data->image = $new_image_name;
				$this->image_upload->image_crop($image_data, 'guest_lists', true, $gl_object['image_data'], true);
				$new_image_name = $this->image_upload->image_data['image'];
				
				
				$this->db->insert('teams_guest_list_authorizations', array(
					'team_fan_page_id'	=> $this->vc_user->manager->team_fan_page_id,
					'team_venue_id'		=> $gl_object['guest_list_venue'],
					'day'				=> $weekday,
					'name'				=> trim($gl_object['guest_list_name']),
					'create_time'		=> time(),
					'deactivated'		=> 0,
					'auto_approve'		=> isset($gl_object['guest_list_auto_approve']) ? 1 : 0,
					'description'		=> trim($gl_object['guest_list_description']),
					
					
					'image'				=> $new_image_name,
					'x0'				=> $gl_object['image_data']['x0'],
					'y0'				=> $gl_object['image_data']['y0'],
					'x1'				=> $gl_object['image_data']['x1'],
					'y1'				=> $gl_object['image_data']['y1'],
					
					
					'min_age'			=> $gl_object['guest_list_min_age'],
					'door_open'			=> $gl_object['guest_list_open'],
					'door_close'		=> $gl_object['guest_list_close'],
					'regular_cover'		=> trim($gl_object['guest_list_reg_cover']),
					'gl_cover'			=> trim($gl_object['guest_list_gl_cover']),
					'additional_info_1'	=> trim($gl_object['guest_list_additional_info_1']),
					'additional_info_2'	=> trim($gl_object['guest_list_additional_info_2']),
					'additional_info_3'	=> trim($gl_object['guest_list_additional_info_3'])
				));
				
				
				die(json_encode(array('success' => true)));
				
				break;
		}
		
	}
	private function _ocupload_settings_guest_lists_new($arg0 = '', $arg1 = '', $arg2 = ''){
		
				
		$this->load->library('library_image_upload', '', 'image_upload');
		
		
		//verifies image upload is acceptable, 
		//saves original + cropped versions to amazon s3, 
		//and updates promoter database
		if($this->image_upload->image_upload(array('type' => 'venue', 
													'upload_type' 	=> 'guest_lists',
													'live_image' 	=> false,
													'image_data' 	=> false))){
				
			die(json_encode(array('success' => true,
									'image_data' => $this->image_upload->image_data)));
									
		}else{
			
			die(json_encode(array('success' => false,
									'message' => $this->image_upload->image_upload_error)));
									
		}
		
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * Manage venues for your team
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return 	null
	 */
	private function _settings_venues($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$settings_venues = new stdClass;
		$settings_venues->team_venues = $this->library_admin_managers->retrieve_team_venues();
		
		$data['settings_venues'] = $settings_venues;
		
		$this->body_html = $this->load->view($this->view_dir . 'settings/view_settings_venues', $data, true);
		
	}
	
	/**
	 * Allows team managers to add a new venue
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return 	null
	 */
	private function _settings_venues_new($arg0 = '', $arg1 = '', $arg2 = ''){
		
		if(!$manage_image = $this->session->flashdata('manage_image')){
			//set this flash data so if user navigates to 'manage_image' it will allow
			$manage_image = new stdClass;
			$manage_image->existing = false;
			$manage_image->type = 'venues/banners';
			$manage_image->live_image = false;
			$manage_image->return = 'settings_venues_new';
			$this->session->set_flashdata('manage_image', json_encode($manage_image));
		}else{
			$manage_image = json_decode($manage_image);
			$this->session->keep_flashdata('manage_image');
		}
			
		$data['manage_image'] = $manage_image;
		
		$this->body_html = $this->load->view($this->view_dir . 'settings/view_settings_venues_new', $data, true);
		
	}
	
	/**
	 * Edit an existing venue's properties
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return 	null
	 */
	private function _settings_venues_edit($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
		$this->load->model('model_users_managers', 'users_managers', true);
		if(!$tv = $this->users_managers->retrieve_individual_team_venue($this->vc_user->manager->team_fan_page_id, $arg1)){
			redirect('/admin/managers/settings_venues/', 'refresh');
			die();
		}
		
		if(!$manage_image = $this->session->flashdata('manage_image')){
			//set this flash data so if user navigates to 'manage_image' it will allow
			$manage_image = new stdClass;
			$manage_image->existing = false;
			$manage_image->type = 'venues/banners';
			$manage_image->live_image = false;
			$manage_image->return = 'settings_venues_edit/' . $tv->id;
			$this->session->set_flashdata('manage_image', json_encode($manage_image));
		}else{
			$manage_image = json_decode($manage_image);
			$this->session->keep_flashdata('manage_image');
		}
		
	//	Kint::dump($manage_image);
		
		$data['manage_image'] = $manage_image;
		$data['tv'] = $tv;
		
		$this->body_html = $this->load->view($this->view_dir . 'settings/view_settings_venues_edit', $data, true);
		
	}
	
	/**
	 * Edit a venue floorplan
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return 	null
	 */
	private function _settings_venues_edit_floorplan($arg0 = '', $arg1 = '', $arg2 = ''){
		
	//	Kint::dump($arg0);
	//	Kint::dump($arg1); <--- venue_id
	//	Kint::dump($arg2);
			
		$this->load->model('model_teams', 'teams', true);
		$venue_floorplan = $this->teams->retrieve_venue_floorplan($arg1, $this->vc_user->manager->team_fan_page_id);
				
		if(count($venue_floorplan) === 0){
			//venue does not exist or belong to this team
			//redirect('/admin/managers/', 'location', 302);
			//echo '<script type="text/javascript">window.location = "/admin/managers/";</script>';
			die();
			return;
		}
		
		$venue_name = $venue_floorplan[0]->tv_name;
		
		$venue_floors = new stdClass;
		//iterate over all items to extract floors
		foreach($venue_floorplan as $key => $vlf){
			if(!isset($vlf->vlf_id))
				continue;
			
			if($vlf->vlf_deleted == 1)
				continue;
			
			if(!array_key_exists($vlf->vlf_id, $venue_floors)){
				
				$floor_object = new stdClass;
				$floor_object->items = array();
				$floor_object->name = $vlf->vlf_floor_name;
				
				$floor_id = $vlf->vlf_id;
				$venue_floors->$floor_id = $floor_object;
				
			}
		}
		
		//for each floor, extract the items
		foreach($venue_floors as $key => &$vf){
						
			foreach($venue_floorplan as $vlf){
				if($key == $vlf->vlf_id){
					//item is on THIS floor
					
					if($vlf->vlfi_id == NULL)
						continue;
					
					if($vlf->vlfi_deleted == 1)
						continue;
										
					$vf->items[] = $vlf;
					
				}
			}
			
		}
		
		
		$data['venue_floorplan'] = $venue_floors;
		$data['venue_name'] = $venue_name;
		
		$this->body_html = $this->load->view($this->view_dir . 'settings/view_settings_venues_edit_floorplan', $data, true);
		
	}

	/**
	 * Upload an image for a venue
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return 	null
	 */
	private function _manage_image($arg0 = '', $arg1 = '', $arg2 = ''){
		//check flashdata to make sure there is a flashdata object either from creating a new event or editing an existing one (this might get a little tricky)
		if(!$manage_image = $this->session->flashdata('manage_image')){
			redirect('/admin/managers/', 'refresh');
			die();
		}
		
		$manage_image = json_decode($manage_image);
		$this->session->keep_flashdata('manage_image');
		
		$data['manage_image'] = $manage_image;		
		$this->body_html = $this->load->view($this->view_dir . 'manage/view_manage_image', $data, true);
	}
	
	/**
	 * Contact support for help
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return 	null
	 */
	private function _support($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$data = array();
		
		$this->body_html = $this->load->view('admin/promoters/' . 'support/view_admin_promoter_support', $data, true);
		
		
	}
	
	/*******************************************************************************************************************
	 * 	END CONTROLLER VIEW DISPLAY FUNCTIONS
	 * 		Below functions are called via AJAX and helpers
	/ ******************************************************************************************************************/

	/**
	 * handles uploads of images (via background iframe) for events and guest lists
	 * 
	 * @return	null
	 * */
	private function _ocupload_manage_image($arg0 = '', $arg1 = '', $arg2 = ''){
		
		//check flashdata to make sure there is a flashdata object either from creating a new event or editing an existing one (this might get a little tricky)
		if(!$manage_image = $this->session->flashdata('manage_image'))
			die(json_encode(array('success' => false)));
		
		$this->session->keep_flashdata('manage_image');
		$manage_image = json_decode($manage_image);
				
		$this->load->library('library_image_upload', '', 'image_upload');
		
		
		//verifies image upload is acceptable, 
		//saves original + cropped versions to amazon s3, 
		//and updates promoter database
		if($this->image_upload->image_upload(array('type' => 'venue', 
													'upload_type' => $manage_image->type,
													'live_image' => $manage_image->live_image,
													'image_data' => (isset($manage_image->image_data)) ? $manage_image->image_data : false))){
		
			//add uploaded image data to session
			$manage_image->image_data = $this->image_upload->image_data;
			$this->session->set_flashdata('manage_image', json_encode($manage_image));
		
			die(json_encode(array('success' => true,
									'image_data' => $this->image_upload->image_data)));
									
		}else{
			
			die(json_encode(array('success' => false,
									'message' => $this->image_upload->image_upload_error)));
									
		}
		
	}	
	
	/**
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _ajax_dashboard($arg0 = '', $arg1 = '', $arg2 = ''){
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'invalid access attempt')));
		}
		
		switch($vc_method){
			case 'reservation_reassign':
				
				$this->_helper_reservation_reassign();
				
				break;
			case 'update_pending_requests':
			
				$result = $this->_helper_guest_list_approve_deny();
				die(json_encode($result));
				break;
				
			case 'find_tables':
				
				
				
				$data = $this->_helper_venue_floorplan_retrieve_v2();
				die(json_encode(array('success' => true, 'message' => array(
					'init_users' 	=> $data[0],
					'team_venues' 	=> $data[1]
				))));
				
				
				
				break;
			case 'retrieve_pending_requests':
			
			
			
				$data = $this->_helper_retrieve_pending_requests();
				die(json_encode(array('success' => true, 'message' => $data)));		
				
				
				
				break;
			case 'announcement_create':
				
				if(!$this->input->post('message')){
					die(json_encode(array('success' => false, 'message' => 'message required')));
				}
				
				$this->load->model('model_teams', 'teams', true);
				$this->teams->create_team_announcement(array(
					'manager_oauth_uid' 	=> $this->vc_user->manager->mt_user_oauth_uid,
					'message'				=> $this->input->post('message'),
					'team_fan_page_id'		=> $this->vc_user->manager->team_fan_page_id,
					'type'					=> 'regular'
				));
				die(json_encode(array('success' => true, 'message' => '')));
				
				
				break;
	
	//	No longer being used?
	//		case 'venue_floorplan_retrieve':		
	//			$result = $this->_helper_venue_floorplan_retrieve();
	//			die(json_encode($result));
	//			break;
	//		case 'team_guest_list_request_accept_deny':
	//			$result = $this->_helper_guest_list_approve_deny();
	//			die(json_encode($result));
	//			break;
	
	
			case 'stats_retrieve':
				
				$this->load->helper('check_gearman_job_complete');
				check_gearman_job_complete('admin_manager_piwik_stats');
							
				 				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'invalid access attempt - unknown vc_method')));
				break;
		}
		
	}
	
	/**
	 * Responds to ajax requests to visualize and modify guest lists
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _ajax_guest_lists($arg0 = '', $arg1 = '', $arg2 = ''){
			
		$this->load->library('pearloader');
		$gearman_client = $this->pearloader->load('Net', 'Gearman', 'Client');
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'invalid access attempt')));
		}
		
		switch($vc_method){
			
			
			case 'reservation_reassign':
				
				$this->_helper_reservation_reassign();
				
				break;
			
			
			
			
			case 'manual_add_final':


		
				$group = $this->input->post('group');
				if(!$group || !is_array($group))	
					die(json_encode(array('success' => false, 'message' => 'Missing group')));
				
				
				$head_user = false;
				$entourage = array();
				
				foreach($group as $client){

					if(!isset($client['head_user']))					
						die(json_encode(array('success' => false, 'message' => 'Incorrectly formatted data')));
					
					
					if($client['head_user'] == 'true'){
						
						if($head_user !== false){
							die(json_encode(array('success' => false, 'message' => 'Group can not have more than one head user.')));
						}
						
						$head_user = $client;
						
					}else{
						
						$entourage[] = $client;
						
					}
					
				}
				
				$this->load->model('model_team_guest_lists', 'team_guest_lists', true);
				$result = $this->team_guest_lists->create_team_guest_list_reservation(
					((isset($head_user['oauth_uid']) && $head_user['oauth_uid'] != 'null') ? $head_user['oauth_uid'] : NULL),
					$this->vc_user->manager->team_fan_page_id,
					$entourage,
					$this->input->post('tgla_id'),
					(($this->input->post('table_request') == '1') ? 1 : 0),
					false,
					'',
					'',
					'',
					'',
					false,
					true,
					$this->input->post('table_min_spend'),
					$head_user['name'],
					$this->input->post('vlfit_id')
				);
				die(json_encode($result));
				



				break;
		//	case 'manual_add_find_tables':
			
			
			
			case 'find_tables':		
					
					
					
					
					
				
				//data for displaying floorplans of venues
				list($init_users, $team_venues) = $this->_helper_venue_floorplan_retrieve_v2(array(
					'tv_id' => $this->input->post('tv_id'),
					'date' 	=> $this->input->post('date')
				));
				$data['init_users'] 	= $init_users;
				$data['team_venues'] 	= $team_venues;
				
				die(json_encode(array('success' => true, 'message' => $data)));			
				
				
				//find tables that are approved by this manager at this venue on this night
				
				break;
				
				
				
				
				
				
				
				
				
				
				
			case 'update_list_status':
			
			
			
			
			
			
				
				$tgla_id 	= $this->input->post('tgla_id');
				$status 	= $this->input->post('status');
				$status 	= strip_tags($status);
				
				if(!$status){
					die(json_encode(array('success' => false, 'message' => '')));
				}
				
				//should be checking if guest-list belongs to this promoter... too lazy
				$this->db->insert('guest_list_authorizations_statuses', array(
					'team_guest_list_authorizations_id'		=> $tgla_id,
					'status'								=> $status,
					'create_time'							=> time(),
					'users_oauth_uid'						=> $this->vc_user->manager->mt_user_oauth_uid
				));
				
				$return_obj = new stdClass;
				$return_obj->status = $status;
				$return_obj->human_date = date('l m/d/y h:i:s A', time());
				
				
				
				$this->load->helper('run_gearman_job');
				run_gearman_job('gearman_new_manager_gl_status', array(
					'manager_oauth_uid'			=> $this->vc_user->manager->mt_user_oauth_uid,
					'tgla_id'					=> $tgla_id,
					'status'					=> $status,
					'human_time'				=> $return_obj->human_date
				), false);
				
				
				
				die(json_encode(array('success' => true, 'message' => $return_obj)));
				
				
				
				
				
				break;
			case 'update_pending_requests':
			
				$result = $this->_helper_guest_list_approve_deny();
				die(json_encode($result));
				break;
				
				
			case 'venue_floorplan_retrieve':
	//			$result = $this->_helper_venue_floorplan_retrieve();
	//			die(json_encode($result));
	//			break;
				
				
				
			case 'retrieve_guest_lists':
				
				
				
				
				list($users, $team_venues) 			= $this->_helper_manager_guest_lists_and_members($this->input->post('tgla_id'), $this->input->post('tv_id'), $this->input->post('weeks_offset'));
				$return 							= new stdClass;
				$return->team_venues		 		= $team_venues;
				$return->users 						= $users;
				
				
				
				/**
				 
				 
		list($users, $team_venues) 	= $this->_helper_manager_guest_lists_and_members();
		$data['users'] 				= $users;
		$data['team_venues'] 		= $team_venues;
		
				 */
				
				die(json_encode(array('success' => true, 'message' => $return)));
				
				
				
				
				break;
				
				
				
		/*		
				
				
			case 'guest_lists_retrieve':
				
				if(!$admin_manager_guest_list = $this->session->userdata('admin_manager_guest_list'))
					die(json_encode(array('success' => false,
											'message' => 'No guest list retrieve request found')));	
													
				$admin_manager_guest_list = json_decode($admin_manager_guest_list);
				
				//check job status to see if it's completed
				$this->load->library('library_memcached', '', 'memcached');
				if($weekly_guest_list = $this->memcached->get($admin_manager_guest_list->handle)){
					//free memory from memcached
					$this->memcached->delete($admin_manager_guest_list->handle);	
					$this->session->unset_userdata('admin_manager_guest_list');				
					die($weekly_guest_list); //<-- already json in memcache
				}else{
					die(json_encode(array('success' => false)));
				}
				
				break;
			
			*/
							
			case 'update_reservation_host_notes':
				
				
				
				
				$host_message = strip_tags($this->input->post('host_message'));
				if(!$host_message)
					$host_message = '';
				
				$tglr_id = $this->input->post('tglr_id');
				if($tglr_id === FALSE)
					die(json_encode(array('success' => false)));
				
				$this->load->model('model_team_guest_lists', 'team_guest_lists', true);
				$result = $this->team_guest_lists->update_promoter_reservation_host_notes($host_message,
																							$tglr_id);
				die(json_encode($result));
				
				
				
				
				break;
			case 'list_manual_add':
				
				if($this->input->post('status_check')){
					//check to see if job complete
					
					if(!$gearman_manager_manual_add = $this->session->userdata('gearman_manager_manual_add'))
						die(json_encode(array('success' => false,
												'message' => 'No request found')));	
					
					$gearman_manager_manual_add = json_decode($gearman_manager_manual_add);
					
					//check job status to see if it's completed
					$this->load->library('library_memcached', '', 'memcached');					
					if($result = $this->memcached->get($gearman_manager_manual_add->handle)){
						//free memory from memcached
						$this->memcached->delete($gearman_manager_manual_add->handle);
						$this->session->unset_userdata('gearman_manager_manual_add');
						die($result); //<-- already json in memcache (slight inefficiency here...)
					}else{
						die(json_encode(array('success' => false)));
					}
					
				}else{
					//create new job
					
					if($vc_user = $this->session->userdata('vc_user')){
						
						//head user uid is required
						if(!$oauth_uids = $this->input->post('oauth_uids'))
							die(json_encode(array('success' => false)));
						
						//pgla id is required
						if(!$tgla_id = $this->input->post('tgla_id'))
							die(json_encode(array('success' => false)));
						
						$vc_user = json_decode($vc_user);
						//start gearman job for retrieving guest lists
						$this->load->library('pearloader');
						$gearman_client = $this->pearloader->load('Net', 'Gearman', 'Client');
						
						# ------------------------------------------------------------- #
						#	Send guest list request to gearman as a background job		#
						# ------------------------------------------------------------- #				
						//add job to a task
						$gearman_task = $this->pearloader->load('Net', 'Gearman', 'Task', array('func' => 'gearman_manager_manual_add',
																									'arg'  => array('user_oauth_uid' 	=> $vc_user->manager->mt_user_oauth_uid,
																													'fan_page_id'		=> $vc_user->manager->team_fan_page_id,
																													'access_token' 		=> $vc_user->access_token,
																													'tgla_id'			=> $tgla_id,
																													'oauth_uids' 		=> json_encode($oauth_uids))));
						$gearman_task->type = Net_Gearman_Task::JOB_BACKGROUND;
						
						//add test to a set
						$gearman_set = $this->pearloader->load('Net', 'Gearman', 'Set');
						$gearman_set->addTask($gearman_task);
						 
						//launch that shit
						$gearman_client->runSet($gearman_set);
						# ------------------------------------------------------------- #
						#	END Send guest list request to gearman as a background job	#
						# ------------------------------------------------------------- #
					
						//Save background handle and server to user's session
						$gearman_manager_manual_add = new stdClass;
						$gearman_manager_manual_add->handle = $gearman_task->handle;
						$gearman_manager_manual_add->server = $gearman_task->server;
						$this->session->set_userdata('gearman_manager_manual_add', json_encode($gearman_manager_manual_add));
						
						die(json_encode(array('success' => true)));
						
					}else{
						
						die(json_encode(array('success' => false, 'message' => 'User not authenticated.')));
						
					}
				}
				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'invalid access attempt - unknown vc_method')));
				break;
		}
		
	}
	
	/**
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _ajax_promoters_statistics($arg0 = '', $arg1 = '', $arg2 = ''){
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'invalid access attempt')));
		}
		
		switch($vc_method){
			case 'stats_retrieve':
				
				$this->load->helper('check_gearman_job_complete');
				check_gearman_job_complete('gearman_admin_manager_promoter_piwik_stats');
				
				
				/*
				
				if(!$gearman_admin_manager_promoter_piwik_stats = $this->session->userdata('gearman_admin_manager_promoter_piwik_stats'))
					die(json_encode(array('success' => false,
											'message' => 'No guest list retrieve request found')));	
													
				$gearman_admin_manager_promoter_piwik_stats = json_decode($gearman_admin_manager_promoter_piwik_stats);
				
				//check job status to see if it's completed
				$this->load->library('library_memcached', '', 'memcached');
				if($stats = $this->memcached->get($gearman_admin_manager_promoter_piwik_stats->handle)){
					//free memory from memcached
					$this->memcached->delete($gearman_admin_manager_promoter_piwik_stats->handle);
					$this->session->unset_userdata('gearman_admin_manager_promoter_piwik_stats');
					die($stats); //<-- already json in memcache
				}else{
					die(json_encode(array('success' => false)));
				}
				
				
				*/
				
				
				
				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'invalid access attempt - unknown vc_method')));
				break;
		}
		
	}

	/**
	 * Find all table and guest list reservations for all promtoers & team on a given night
	 *
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _ajax_tables($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
		if($this->input->post('vc_method') == 'reservation_reassign'){				
			$this->_helper_reservation_reassign();
			die();
		}
		if($this->input->post('vc_method') == 'find_tables'){
			
			$data = $this->_helper_venue_floorplan_retrieve_v2();
			die(json_encode(array('success' => true, 'message' => array(
				'init_users' 	=> $data[0],
				'team_venues' 	=> $data[1]
			))));
			
		}
				
				
				
				
				
		
		$tv_id = $this->input->post('tv_id');
		$date_obj = $this->input->post('dateObj');
				
		$month = $date_obj['currentMonth'];
		$day = $date_obj['currentDay'];
		$year = $date_obj['currentYear'];
		$venue_floorplan = false;

		$this->load->model('model_users_managers', 'users_managers', true);
		$this->load->model('model_teams', 'teams', true);
		
		$team_venues = $this->users_managers->retrieve_team_venues($this->vc_user->manager->team_fan_page_id);
		$init_users = array();
		foreach($team_venues as $key => &$venue){
			
			if($venue->tv_id != $tv_id){
				unset($team_venues[$key]);
				continue;
			}
			
			//------------------------------------- EXTRACT FLOORPLAN -----------------------------------------
			$venue_floorplan = $this->teams->retrieve_venue_floorplan($venue->tv_id, $this->vc_user->manager->team_fan_page_id);
			$venue_floors = new stdClass;
			
			//iterate over all items to extract floors
			foreach($venue_floorplan as $key => $vlf){
				if(!isset($vlf->vlf_id))
					continue;
				
				if($vlf->vlf_deleted == 1)
					continue;
				
				if(!array_key_exists($vlf->vlf_id, $venue_floors)){
					
					$floor_object = new stdClass;
					$floor_object->items = array();
					$floor_object->name = $vlf->vlf_floor_name;
					
					$floor_id = $vlf->vlf_id;
					$venue_floors->$floor_id = $floor_object;
					
				}
			}
			
			//for each floor, extract the items
			foreach($venue_floors as $key => &$vf){
							
				foreach($venue_floorplan as $vlf){
					if($key == $vlf->vlf_id){
						//item is on THIS floor
						
						if($vlf->vlfi_id == NULL)
							continue;
						
						if($vlf->vlfi_deleted == 1)
							continue;
											
						$vf->items[] = $vlf;
						
					}
				}
				
			}
			
			$venue->venue_floorplan = $venue_floors;
			
			$venue_reservations = $this->teams->retrieve_venue_floorplan_reservations($venue->tv_id,
																						$this->vc_user->manager->team_fan_page_id,
																						date('Y-m-d', strtotime("$month/$day/$year")));
			
			foreach($venue_reservations as $vr){
			
				if(isset($vr->tglr_user_oauth_uid))
					$init_users[] = $vr->tglr_user_oauth_uid;
				elseif(isset($vr->pglr_user_oauth_uid)){
					$init_users[] = $vr->pglr_user_oauth_uid;
					$init_users[] = $vr->up_users_oauth_uid;
				}
				
				if($vr->entourage)
					foreach($vr->entourage as $ent){
						$init_users[] = $ent;
					}
			}
			
			$venue->venue_reservations = $venue_reservations;			
			//------------------------------------- END EXTRACT FLOORPLAN -----------------------------------------
		}
		unset($venue);
		
		$init_users = array_unique($init_users);
		$init_users = array_values($init_users);
		
		if($team_venues)
			die(json_encode(array('success' => true, 'message' => $team_venues, 'init_users' => $init_users)));
		die(json_encode(array('success' => false, 'message' => 'Invalid tv_id')));
				
	}

	/**
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _ajax_clients($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->library('pearloader');
		$gearman_client = $this->pearloader->load('Net', 'Gearman', 'Client');
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'invalid access attempt')));
		}
		
		switch($vc_method){
			case 'update_client_notes':
				
				
				
				$this->load->model('model_teams', 'teams', true);
				$this->teams->update_client_notes(array(
					'users_oauth_uid'	=> $this->vc_user->manager->mt_user_oauth_uid,
					'client_oauth_uid'	=> $arg1,
					'team_fan_page_id'	=> $this->vc_user->manager->team_fan_page_id,
					'public_notes'		=> $this->input->post('public_notes'),
					'private_notes'		=> $this->input->post('private_notes')
				));
				
				$this->teams->create_team_announcement(array(
					'type'				=> 'json',
					'team_fan_page_id'	=> $this->vc_user->manager->team_fan_page_id,
					'message'			=> json_encode(array(
					
						'subtype'			=> 'new_client_notes',
						'client_oauth_uid'	=> $arg1,
						'public_notes'		=> $this->input->post('public_notes')
						
					)),
					'manager_oauth_uid'	=> $this->vc_user->manager->mt_user_oauth_uid
				));
				
				die(json_encode(array('success' => true)));
				
				
				
				break;
			case 'client_list_retrieve':
				
				if(!$admin_manager_client_list = $this->session->userdata('admin_manager_client_list'))
					die(json_encode(array('success' => false,
											'message' => 'No guest list retrieve request found')));	
													
				$admin_manager_client_list = json_decode($admin_manager_client_list);
				
				//check job status to see if it's completed
				$this->load->library('library_memcached', '', 'memcached');
				if($manager_client_list = $this->memcached->get($admin_manager_client_list->handle)){
					//free memory from memcached
					$this->memcached->delete($admin_manager_client_list->handle);
					$this->session->unset_userdata('admin_manager_client_list');
					die($manager_client_list); //<-- already json in memcache
				}else{
					die(json_encode(array('success' => false)));
				}
				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'invalid access attempt - unknown vc_method')));
				break;
		}
		
	}

	/**
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _ajax_marketing($arg0 = '', $arg1 = '', $arg2 = ''){
		
	}
	
	/**
	 * Allows managers to invite/delete promoters from their team
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _ajax_settings_promoters($arg0 = '', $arg1 = '', $arg2 = ''){
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'invalid access attempt')));
		}
		
		switch($vc_method){
			case 'invitation_create':
				
				$result = $this->library_admin_managers->invitation_create('promoter');
				die(json_encode($result));
				
				break;
			case 'promoter_delete':
				
				$result = $this->library_admin_managers->promoter_delete();
				die(json_encode($result));
				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'invalid access attempt - unknown vc_method')));
				break;
		}
		
	}
	
	/**
	 * Allows managers to invite/delete hosts from their team
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	null
	 */
	private function _ajax_settings_hosts($arg0 = '', $arg1 = '', $arg2 = ''){
				
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'invalid access attempt')));
		}
		
		switch($vc_method){
			case 'invitation_create':
				
				$result = $this->library_admin_managers->invitation_host_create();
				die(json_encode($result));
				
				break;
			case 'host_delete':
				
				$result = $this->library_admin_managers->host_delete();
				die(json_encode($result));
				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'invalid access attempt - unknown vc_method')));
				break;
		}
		
	}
	
	/**
	 * AJAX handler for venue creation page
	 * 
	 * @return	null
	 * */
	private function _ajax_settings_venues_new($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->session->keep_flashdata('manage_image');
			
		$vc_method = $this->input->post('vc_method');

		switch($vc_method){
			case 'manager_new_venue':
				
				$result = $this->library_admin_managers->create_new_team_venue();
				die(json_encode($result));
				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'unknown vc_method')));
				break;
		}

	}
	
	/**
	 * AJAX handler for venue creation page
	 * 
	 * @return	null
	 * */
	private function _ajax_settings_venues_edit($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->session->keep_flashdata('manage_image');
		
		$vc_method = $this->input->post('vc_method');

		switch($vc_method){
			case 'manager_edit_venue':
				
				$result = $this->library_admin_managers->edit_team_venue($arg1);
				die(json_encode($result));
				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'unknown vc_method')));
				break;
		}
		
		
		
		
		
		/*
		 * This 
		
		
		
		
		
		if(!$manage_image = $this->session->flashdata('manage_image')){
			//idk...
		}
		var_dump($manage_image);
		var_dump($this->input->post());
		die();
			
		$vc_method = $this->input->post('vc_method');

		switch($vc_method){
			case 'manager_new_venue':
				
				$result = $this->library_admin_managers->create_new_team_venue();
				die(json_encode($result));
				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'unknown vc_method')));
				break;
		}
		
		
		
		*/
		
		
		

	}
	
	/**
	 * Recieves JSON formatted object representing the layout of a venue's floors
	 * 
	 * @return	null
	 */
	private function _ajax_settings_venues_edit_floorplan($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
	//	Kint::dump($arg0);
	//	Kint::dump($arg1); <--- venue_id
	//	Kint::dump($arg2);
			
		$this->load->model('model_teams', 'teams', true);
		$venue_floorplan = $this->teams->retrieve_venue_floorplan($arg1, $this->vc_user->manager->team_fan_page_id);
		
		if(count((array)$venue_floorplan) === 0){
			//venue does not exist or belong to this team
			die(json_encode(array('success' => false,
									'message' => 'unauthorized venue')));
		}
		
		$tv_id = $venue_floorplan[0]->tv_id;
		
		
		
		
		$venue_layout = new stdClass;
		//iterate over all items to extract floors
		foreach($venue_floorplan as $key => $vlf){
			if(!isset($vlf->vlf_id))
				continue;
			
			if($vlf->vlf_deleted == 1)
				continue;
			
			if(!array_key_exists($vlf->vlf_id, $venue_layout)){
				
				$floor_object = new stdClass;
				$floor_object->items = array();
				$floor_object->name = $vlf->vlf_floor_name;
				
				$floor_id = $vlf->vlf_id;
				$venue_layout->$floor_id = $floor_object;
				
			}
		}
		
		//for each floor, extract the items
		foreach($venue_layout as $key => &$vf){
						
			foreach($venue_floorplan as $vlf){
				if($key == $vlf->vlf_id){
					//item is on THIS floor
					
					if($vlf->vlfi_id == NULL)
						continue;
					
					if($vlf->vlfi_deleted == 1)
						continue;
										
					$vf->items[] = $vlf;
					
				}
			}
			
		}
		
		
		
		
		
		
		$vc_method = $this->input->post('vc_method');
		
		switch($vc_method){
			case 'venue_save_layout':
//--------------------------------------------------- venue save layout ------------------------------------------------
				$venue_layout_post = $this->input->post('venue_layout');
				if($venue_layout_post === false)
					die(json_encode(array('success' => false,
											'message' => 'venue_layout required')));
				
				$venue_layout_post = json_decode($venue_layout_post);
				
				//construct array of all floors KNOWN
				$known_vlf_ids = array();
				foreach($venue_layout as $key => $vlf){
					$known_vlf_ids[] = $key;
				}
				
				foreach($venue_layout_post as $key => $vf_new){
					
					if($vf_new->vlf_id == false){
						//this is a new floor

						$vlf_id = $this->teams->create_team_venues_floors($vf_new->title, $tv_id);
												
						foreach($vf_new->items as $item){
							$vlfi_id = $this->teams->create_team_venues_floors_item($vlf_id,
																					$item->item_class,
																					$item->left,
																					$item->top,
																					$item->width,
																					$item->height,
																					isset($item->notes) ? $item->notes : '',
																					NULL);
							if($item->item_class == 'table'){
								$this->teams->create_team_venues_floors_item_table($vlfi_id, 
																					$item->title,
																					$item->monday_min,
																					$item->tuesday_min,
																					$item->wednesday_min,
																					$item->thursday_min,
																					$item->friday_min,
																					$item->saturday_min,
																					$item->sunday_min,
																					$item->max_capacity);
							}
						}
						
					}else{
						
						//remove vlfi_id from known_lfi_ids
						$temp = array_search($vf_new->vlf_id, $known_vlf_ids);
						if(isset($temp))
							unset($known_vlf_ids[$temp]);
												
						//this is an existing floor
						foreach($venue_layout as $key2 => $vlf){
							//make sure POST vlf_id matches one we know about

							if($vf_new->vlf_id == $key2){
								
								//update floor	
								$this->teams->update_team_venues_floors($key2, $vf_new->title); //TODO <--- update with floor name
																
								//Loop over uploaded floorplan items and create DB records for all new items
								foreach($vf_new->items 	as $item){
									
									if($item->vlfi_id == false){
										//this is a new item, create it
										
										$vlfi_id = $this->teams->create_team_venues_floors_item($key2,
																								$item->item_class,
																								$item->left,
																								$item->top,
																								$item->width,
																								$item->height,
																								NULL);
										if($item->item_class == 'table'){
											$this->teams->create_team_venues_floors_item_table($vlfi_id, 
																								$item->title,
																								$item->monday_min,
																								$item->tuesday_min,
																								$item->wednesday_min,
																								$item->thursday_min,
																								$item->friday_min,
																								$item->saturday_min,
																								$item->sunday_min,
																								$item->max_capacity);
										}
										
									}
									
								}
								
								
								//build array of all items that we know about on this floor (floorplan before update)
								$known_lfi_ids = array();
								foreach($vlf->items as $item){
									$known_lfi_ids[] = $item->vlfi_id;
								}
								
								//loop over each item in the NEW (uploaded) layout and if it matches an item in the old layout, update
								foreach($vf_new->items as $item){
									
									if(in_array($item->vlfi_id, $known_lfi_ids)){
										
										//update item
										$this->teams->update_team_venues_floors_item($item->vlfi_id,
																							$item->item_class,
																							$item->left,
																							$item->top,
																							$item->width,
																							$item->height,
																							NULL);
										
										//update table if item table
										if($item->item_class == 'table'){
											$this->teams->update_team_venues_floors_item_table(
																							$item->vlfi_id,
																							$item->title,
																							$item->monday_min,
																							$item->tuesday_min,
																							$item->wednesday_min,
																							$item->thursday_min,
																							$item->friday_min,
																							$item->saturday_min,
																							$item->sunday_min,
																							$item->max_capacity
																							);
										}
										
										//remove vlfi_id from known_lfi_ids
										$key = array_search($item->vlfi_id, $known_lfi_ids);
										if(isset($key))
											unset($known_lfi_ids[$key]);
										
									}
									
								}
								
								//logical delete all items in known_lfi_ids
								foreach($known_lfi_ids as $lfi_id){
									
									$this->teams->delete_team_venues_floors_item($lfi_id);
								
								}
								
								break;
							}
							
						}
						
					}
					
				}

				//delete all floors that we're not returned
				foreach($known_vlf_ids as $vlf_id){
					
					$this->teams->delete_team_venues_floors($vlf_id);
					
				}

				die(json_encode(array('success' => true)));
				break;
//--------------------------------------------------- end venue save layout ------------------------------------------------
		}
	}
	
	/**
	 * AJAX requests made from image manage page
	 * 
	 * @return 	null
	 */
	private function _ajax_manage_image($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$vc_method = $this->input->post('vc_method');
		
		//check flashdata to make sure there is a flashdata object either from creating a new event or editing an existing one (this might get a little tricky)
		if(!$manage_image = $this->session->flashdata('manage_image'))
			die(json_encode(array('success' => false)));
		
		$this->session->keep_flashdata('manage_image');
		$manage_image = json_decode($manage_image);
		
		if(!isset($manage_image->image_data)){
			die(json_encode(array('success' => false, 'message' => 'No image data found')));
		}
				
		$this->load->library('library_image_upload', '', 'image_upload');
						
		switch($vc_method){
			case 'crop_action':
				
				if($this->image_upload->image_crop($manage_image->image_data, $manage_image->type, $manage_image->live_image)){
					
					//add uploaded image data to session
					$manage_image->image_data = $this->image_upload->image_data;
					$this->session->set_flashdata('manage_image', json_encode($manage_image));
					
					die(json_encode(array('success' => true,
							'image_data' => $this->image_upload->image_data)));
				
				}else{
					
					die(json_encode(array('success' => false)));
				
				}
				
				break;
			case 'submit':
				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'unknown vc_method')));
				break;
		}
		
	}

	/**
	 * Contact support for help
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return 	null
	 */
	private function _ajax_support($arg0 = '', $arg1 = '', $arg2 = ''){
		
		die(json_encode(array('success' => false)));
		
	}
	
	
	
	
	
	private function _helper_reservation_reassign(){
		
		$pglr_id 	= $this->input->post('pglr_id');
		$tglr_id 	= $this->input->post('tglr_id');
		$vlfit_id 	= $this->input->post('vlfit_id');
		$iso_date	= $this->input->post('iso_date');
		$tv_id		= $this->input->post('tv_id');
		
		
		//check if table is already assigned
		list($init_users, $team_venues) 	= $this->_helper_venue_floorplan_retrieve_v2();
		if(!is_array($team_venues) || !count($team_venues))
			die(json_encode(array('success' => false)));
		
		
		$team_venue = array_shift(array_values($team_venues));
	//	$team_venue 			= $team_venues[0];
		
		
		$reservations_vlfit_ids = array();
		foreach($team_venue->venue_reservations as $res){
			$reservations_vlfit_ids[] = $res->vlfit_id;
		}
		
		if(in_array($vlfit_id, $reservations_vlfit_ids)){
			//table is already reserved
			die(json_encode(array('success' => false, 'message' => 'already reserved')));
		}
		
		
		
		if($tglr_id){
			


		
			$this->load->model('model_team_guest_lists', 'team_guest_lists', true);
			$response = $this->team_guest_lists->update_reservation_reassign(array(
				'tglr_id'			=> $tglr_id,
				'team_fan_page_id'	=> $this->vc_user->manager->team_fan_page_id,
				'vlfit_id'			=> $vlfit_id
			));
			
			
			
			
			die(json_encode(array('success' => true)));
			
			
		}elseif($pglr_id){
	
	
			$this->load->model('model_guest_lists', 'guest_lists', true);
			$this->guest_lists->update_reservation_reassign(array(
				'pglr_id'		=> $pglr_id,
				'vlfit_id'		=> $vlfit_id
			));
			
	
	
			die(json_encode(array('success' => true)));
	
	
		}else{
			die(json_encode(array('success' => false)));
		}
		
		
	}





	private function _helper_manager_guest_lists_and_members($tgla_id = false, $tv_id = false, $offset = false){
		
		
		
		
		
		
		
		$this->load->model('model_users_managers', 'users_managers', true);
		
		$team_venues = $this->users_managers->retrieve_team_venues($this->vc_user->manager->team_fan_page_id);
		
		$users = array();
		foreach($team_venues as $key => &$tv){
			
			
			//only grab tv we care about
			if($tv_id !== false){
				if($tv->tv_id !== $tv_id){
					unset($team_venues[$key]);
					continue;
				}
			}
			
			
			
			$tv_gla = $this->users_managers->retrieve_team_venue_guest_list_authorizations($tv->tv_id, $this->vc_user->manager->team_fan_page_id);
			foreach($tv_gla as $key2 => &$gla){
				
				//only grab tv we care about
				if($tgla_id !== false){
					if($gla->tgla_id !== $tgla_id){
						unset($tv_gla[$key2]);
						continue;
					}
				}
				
				
				
				if(!$offset){
					$gla->human_date 	= $gla->human_date = date('l m/d/y', strtotime(rtrim($gla->tgla_day, 's')));
					$gla->iso_date 		= date('Y-m-d', strtotime(rtrim($gla->tgla_day, 's')));
					$gla->current_week 	= true;
				}else{
					$gla->human_date 	= $gla->human_date = date('l m/d/y', strtotime('next ' . rtrim($gla->tgla_day, 's') . ' -' . $offset . ' weeks'));	
					$gla->iso_date 		= date('Y-m-d', strtotime('next ' . rtrim($gla->tgla_day, 's') . ' -' . $offset . ' weeks'));
					$gla->current_week 	= false;
				}
				
				if($offset == 0)
					$offset = false;
				
				
				
				
				
				
				
				$gla->current_list = $this->users_managers->retrieve_teams_guest_list_authorizations_current_guest_list($gla->tgla_id, $gla->iso_date);
				
					
					
			//	$gla->human_date 	= date('l m/d/y', strtotime(rtrim($gla->tgla_day, 's')));
			//	$gla->iso_date 		= date('Y-m-d', strtotime(rtrim($gla->tgla_day, 's')));
			//	$gla->current_week	= true;
				
				
				if($gla->current_list){
					$gla->current_list->groups = $this->users_managers->retrieve_teams_guest_list_members($gla->current_list->tgl_id);
					
					
					
					//add users to users array
					foreach($gla->current_list->groups as &$group){
						
						
						
						if(!$offset){
							$group->human_date 	= $gla->human_date = date('l m/d/y', strtotime(rtrim($gla->tgla_day, 's')));
							$group->iso_date 		= date('Y-m-d', strtotime(rtrim($gla->tgla_day, 's')));
							$group->current_week 	= true;
						}else{
							$group->human_date 	= $gla->human_date = date('l m/d/y', strtotime('next ' . rtrim($gla->tgla_day, 's') . ' -' . $offset . ' weeks'));	
							$group->iso_date 		= date('Y-m-d', strtotime('next ' . rtrim($gla->tgla_day, 's') . ' -' . $offset . ' weeks'));
							$group->current_week 	= false;
						}
						$group->request_type = 'team';
						
						
						
						
						
						$users[] = $group->tglr_user_oauth_uid;

						foreach($group->entourage as $ent_user){
							$users[] = $ent_user->tglre_oauth_uid;
						}
						
						
					}unset($group);
					
				}
			}
			
			$tv->tv_gla = $tv_gla;
			
		}
		
		$users = array_unique($users);
		$users = array_values($users);
		
		
		return array($users, $team_venues);
		
		
		
		
		
		
	}

	private function _helper_venue_floorplan_retrieve_v2(){	
		
		
		$this->load->model('model_users_managers', 'users_managers', true);
		$this->load->model('model_teams', 'teams', true);
		
		$team_venues = $this->users_managers->retrieve_team_venues($this->vc_user->manager->team_fan_page_id);
		
		
		//are we looking for just 1 tv?
		$tv_id 		= $this->input->post('tv_id');
		$iso_date 	= $this->input->post('iso_date');
		
		
		
		$init_users = array();
		
		
		
		
		
		
		
		
		
		
		
		
		//default date is today... lookup date of guest-list if pglr or tglr specified
		$lookup_date = date('Y-m-d', time());
		$pglr_id = $this->input->post('pglr_id');
		$tglr_id = $this->input->post('tglr_id');
		if($pglr_id){
			
			$this->db->select('pgl.date as date')
				->from('promoters_guest_lists pgl')
				->join('promoters_guest_lists_reservations pglr', 'pglr.promoter_guest_lists_id = pgl.id')
				->where(array(
					'pglr.id' => $pglr_id
				));
			$query = $this->db->get();
			$result = $query->row();
			if($result && isset($result->date)){
				$lookup_date = $result->date;
			}
			
		}
		if($tglr_id){
			
			$this->db->select('tgl.date as date')
				->from('teams_guest_lists tgl')
				->join('teams_guest_lists_reservations tglr', 'tglr.team_guest_list_id = tgl.id')
				->where(array(
					'tglr.id' => $tglr_id
				));
			$query = $this->db->get();
			$result = $query->row();
			if($result && isset($result->date)){
				$lookup_date = $result->date;
			}
			
		}
		if($iso_date){
			$lookup_date = $iso_date;
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		foreach($team_venues as $key => &$venue){
			
			
			if($tv_id){
				
				if($tv_id != $venue->tv_id){
					unset($team_venues[$key]);
					continue;
				}
				
			}
			
			$venue->floorplan_iso_date = $lookup_date;
			$venue->floorplan_human_date = date('l F j, Y', strtotime($lookup_date));
			
			//------------------------------------- EXTRACT FLOORPLAN -----------------------------------------

			$venue_floorplan = $this->teams->retrieve_venue_floorplan($venue->tv_id, $this->vc_user->manager->team_fan_page_id);
			$venue_floors = new stdClass;
			
			//iterate over all items to extract floors
			foreach($venue_floorplan as $key => $vlf){
				if(!isset($vlf->vlf_id))
					continue;
				
				if($vlf->vlf_deleted == 1)
					continue;
				
				if(!array_key_exists($vlf->vlf_id, $venue_floors)){
					
					$floor_object = new stdClass;
					$floor_object->items = array();
					$floor_object->name = $vlf->vlf_floor_name;
					
					$floor_id = $vlf->vlf_id;
					$venue_floors->$floor_id = $floor_object;
					
				}
			}unset($vlf);
			
			//for each floor, extract the items
			foreach($venue_floors as $key => &$vf){
							
				foreach($venue_floorplan as $vlf){
					if($key == $vlf->vlf_id){
						//item is on THIS floor
						
						if($vlf->vlfi_id == NULL)
							continue;
						
						if($vlf->vlfi_deleted == 1)
							continue;
											
						$vf->items[] = $vlf;
						
					}
				}unset($vlf);
				
			}unset($vf);
			
			$venue->venue_floorplan = $venue_floors;







			






			$venue_reservations = $this->teams->retrieve_venue_floorplan_reservations($venue->tv_id,
																						$this->vc_user->manager->team_fan_page_id,
																						$lookup_date);
			foreach($venue_reservations as $vr){
				
				if(isset($vr->tglr_user_oauth_uid))
					$init_users[] = $vr->tglr_user_oauth_uid;
				elseif(isset($vr->pglr_user_oauth_uid)){
					$init_users[] = $vr->pglr_user_oauth_uid;
					$init_users[] = $vr->up_users_oauth_uid;
				}
					
				
		//		if($vr->entourage)
		//			foreach($vr->entourage as $ent){
		//				$init_users[] = $ent;
		//			}
				
			}unset($vr);
			$venue->venue_reservations = $venue_reservations;
			
			
			
			
			
			
			
			$all_upcoming_reservations = $this->teams->retrieve_venue_floorplan_reservations($venue->tv_id,
																						$this->vc_user->manager->team_fan_page_id,
																						false);
																						
																						
			foreach($all_upcoming_reservations as $vr){
				
				if(isset($vr->tglr_user_oauth_uid))
					$init_users[] = $vr->tglr_user_oauth_uid;
				elseif(isset($vr->pglr_user_oauth_uid)){
					$init_users[] = $vr->pglr_user_oauth_uid;
					$init_users[] = $vr->up_users_oauth_uid;
				}
					
				
			//	if($vr->entourage)
			//		foreach($vr->entourage as $ent){
			//			$init_users[] = $ent;
			//		}
				
			}unset($vr);																	
																						
			
			$venue->venue_all_upcoming_reservations = $all_upcoming_reservations;
			
			//------------------------------------- END EXTRACT FLOORPLAN -----------------------------------------
		}unset($venue);
		
		
		
		$init_users = array_unique($init_users);
		$init_users = array_values($init_users);
		
		//$data['init_users'] = $init_users;
		//$data['team_venues'] = $team_venues;
		
		return array($init_users, $team_venues);
		
	}




	/**
	 * Helper function to retrieve the floorplan and table reservations on specific nights, 
	 * called from multiple endpoints
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return	object || false
	 */
/*	private function _helper_venue_floorplan_retrieve(){

		//'promoter' || 'manager'
		$request_type = $this->input->post('request_type');
		
		$glr_id = $this->input->post('glr_id');
		$tv_id = $this->input->post('tv_id');
				
		$this->load->model('model_teams', 'teams', true);
	
		$venue_floorplan = $this->teams->retrieve_venue_floorplan($tv_id, $this->vc_user->manager->team_fan_page_id);
		$venue_floors = new stdClass;
		
		//iterate over all items to extract floors
		foreach($venue_floorplan as $key => $vlf){
			if(!isset($vlf->vlf_id))
				continue;
			
			if($vlf->vlf_deleted == 1)
				continue;
			
			if(!array_key_exists($vlf->vlf_id, $venue_floors)){
				
				$floor_object = new stdClass;
				$floor_object->items = array();
				$floor_object->name = $vlf->vlf_floor_name;
				
				$floor_id = $vlf->vlf_id;
				$venue_floors->$floor_id = $floor_object;
				
			}
		}
		
		//for each floor, extract the items
		foreach($venue_floors as $key => &$vf){
						
			foreach($venue_floorplan as $vlf){
				if($key == $vlf->vlf_id){
					//item is on THIS floor
					
					if($vlf->vlfi_id == NULL)
						continue;
					
					if($vlf->vlfi_deleted == 1)
						continue;
										
					$vf->items[] = $vlf;
					
				}
			}
			
		}
		
			
		$pglr_id = false;
		$tglr_id = false;
		if($request_type == 'promoter')
			$pglr_id = $glr_id;
		else
			$tglr_id = $glr_id;
		
		$table_reservations = $this->teams->retrieve_venue_floorplan_reservations(false, 
																					$this->vc_user->manager->team_fan_page_id,
																					false,
																					$pglr_id,
																					$tglr_id);

		$response = new stdClass;
		$response->venue_floors = $venue_floors;
		$response->table_reservations = $table_reservations;
		
		return $response;
	}
*/

	private function _helper_retrieve_pending_requests(){
		
		
			
		$this->load->model('model_users_promoters', 'users_promoters', true);
		$this->load->model('model_guest_lists', 'guest_lists', true);
		$this->load->model('model_users_managers', 'users_managers', true);
		$this->load->model('model_teams', 'teams', true);
		
		
		
		
		$announcements = $this->teams->retrieve_team_announcements(array(
			'team_fan_page_id' => $this->vc_user->manager->team_fan_page_id
		));
		
			
		$trailing_gl_requests = $this->users_managers->retrieve_trailing_weekly_guest_list_reservation_requests($this->vc_user->manager->team_fan_page_id);
				
		
		$team_venues = $this->users_managers->retrieve_team_venues($this->vc_user->manager->team_fan_page_id);
		$total_clients = 0;
		
		
		$users = array();
		
		foreach($announcements as $an){
			if($an->type == 'json'){
				$message = json_decode($an->message);
				if(isset($message->client_oauth_uid)){
					$users[] = $message->client_oauth_uid;
				}
			}
		}
		$users = array_values($users);
		$users = array_unique($users);
	
		
		foreach($team_venues as &$venue){
			
			$venue->clients = $this->users_managers->retrieve_venue_clients($venue->tv_id, $this->vc_user->manager->team_fan_page_id);
			$total_clients += count($venue->clients);
			
			$venue->upcoming_guest_list_reservations = $this->users_managers->retrieve_team_venue_guest_list_reservations($venue->tv_id, true);
			$venue->all_time_guest_list_reservations = $this->users_managers->retrieve_team_venue_guest_list_reservations($venue->tv_id);
			
			
			//retrieve all guest list reservations (approved/unapproved) for each team
			$tv_gla = $this->users_managers->retrieve_team_venue_guest_list_authorizations($venue->tv_id, $this->vc_user->manager->team_fan_page_id);
			foreach($tv_gla as &$gla){
				$gla->current_list = $this->users_managers->retrieve_teams_guest_list_authorizations_current_guest_list($gla->tgla_id);
				
				$gla->human_date 	= date('l m/d/y', strtotime(rtrim($gla->tgla_day, 's')));
				$gla->iso_date 		= date('Y-m-d', strtotime(rtrim($gla->tgla_day, 's')));
				
				
				if($gla->current_list){
					$gla->current_list->groups = $this->users_managers->retrieve_teams_guest_list_members($gla->current_list->tgl_id);
					
					//add users to users array
					foreach($gla->current_list->groups as $group){
						$users[] = $group->tglr_user_oauth_uid;

						foreach($group->entourage as $ent_user){
							$users[] = $ent_user->tglre_oauth_uid;
						}
					}
					
				}
			}
			
			$venue->tv_gla = $tv_gla;
		}
		
		//------------------------------- promoter table requests -----------------------------------
		$promoters = $this->teams->retrieve_team_promoters($this->vc_user->manager->team_fan_page_id);
				
		
		$users2 = array();
		//attach current guest list data to each promoter object
		foreach($promoters as &$promoter){
			
			//retrieve a list of all the guest lists a promoter has set up
			$weekly_guest_lists = $this->users_promoters->retrieve_promoter_guest_list_authorizations($promoter->up_id);
			
			
			//for each guest list, find all groups associated with it
			foreach($weekly_guest_lists as &$gla){
				$gla->groups = $this->guest_lists->retrieve_single_guest_list_and_guest_list_members($gla->pgla_id, $gla->pgla_day);
			}
			
			
			//attach to promoter object
			$promoter->weekly_guest_lists = $weekly_guest_lists;
			
			//record FBIDs of all users for later use
			foreach($weekly_guest_lists as $wgl){
				foreach($wgl->groups as $group){
					
					$users2[] = $group->head_user;
					$users2 = array_merge($users2, $group->entourage_users);
					
				}
			}
			
		}unset($promoter);
		
//		Kint::dump($users);
//		Kint::dump($users2);
		

		
		$users = array_merge($users, $users2);
//		$users = array_unique($users);
		$users = array_values($users);
		
		
		
		
		
		
		
		
		
		$data['users'] = $users;
		$data['announcements'] = $announcements;
		//------------------------------- end promoter table requests -----------------------------------
		
		$statistics 						= new stdClass;
		$statistics->team_venues 			= $team_venues;
		$statistics->total_clients 			= $total_clients;
		$statistics->trailing_gl_requests 	= $trailing_gl_requests;
		$statistics->active_promoters 		= $this->teams->retrieve_team_promoters($this->vc_user->manager->team_fan_page_id, array('completed_setup' => false));
		$statistics->promoters 				= $promoters;
		
		//------ retrieve top visitors -------
		$promoters_pt_ids = array();
		foreach($promoters as $pro){
			$promoters_pt_ids[] = $pro->pt_id;
		}
		$top_visitors = $this->users_managers->retrieve_top_team_visitors($promoters_pt_ids, $this->vc_user->manager->team_fan_page_id);
		$top_visitors_uids = array();
		foreach($top_visitors as $tv){
			$top_visitors_uids[] = $tv->users_oauth_uid;
		}
		$statistics->top_visitors = $top_visitors_uids;
		//------ end retrieve top visitors -------
		
		//------ retrieve recent visitors --------
		$recent_visitors = $this->users_managers->retrieve_recent_team_visitors($promoters_pt_ids);
		$recent_visitors_uids = array();
		foreach($recent_visitors as $rv){
			$recent_visitors_uids[] = $rv->uv_users_oauth_uid;
		}
		$statistics->recent_visitors = $recent_visitors_uids;
		//------ end retrieve recent visitors -------
		
		
		
		
		
		
		
		
		
		
		
		
		$pending_requests = array();
		
		foreach($statistics->team_venues as $team_venue){
			foreach($team_venue->tv_gla as $tv_gla){
								
				if($tv_gla->current_list){
					
					foreach($tv_gla->current_list->groups as $group){
						
						$group->request_type = 'team';
						$group->human_date	 = $tv_gla->human_date;
						$group->iso_date	 = $tv_gla->iso_date;
						
						
						if($group->tglr_approved === '0' || $group->tglr_approved === 0)
							$pending_requests[] = $group;
						
					}
					
				}
				
			}
		}unset($team_venue);
		foreach($statistics->promoters as $promoter){
						
			foreach($promoter->weekly_guest_lists as $wgl){
								
				if($wgl->groups){
					foreach($wgl->groups as $group){
						
						$group->request_type = 'promoter';
						
						if($group->pglr_approved == '1' && $group->pglr_table_request == '1' && $group->pglr_manager_table_approved === '0')
							$pending_requests[] = $group;
							
					}
				}
			}
		}unset($promoter);
		
		$statistics->pending_requests = $pending_requests;
		
		
				
		
		
		
		
		
		
		
		
		$data['statistics'] = $statistics;
		
		
		return $data;
		
		
	}

	/**
	 * Approve a team guest list request, team table request, or a promoter table request
	 * 
	 * @return 	array
	 */
	private function _helper_guest_list_approve_deny(){
		

	//	return $this->input->post();
		
		//accept_deny
		//glr_id
		//message
		//request_type
		//table_request
		//vlfit_id
		
		$accept_deny 	= $this->input->post('action');
		$glr_id 		= $this->input->post('glr_id');
		$message 		= strip_tags($this->input->post('message'));
		$request_type 	= $this->input->post('request_type');
//		$table_request 	= $this->input->post('table_request');
		$vlfit_id 		= $this->input->post('vlfit_id');
		
		
		
		if($vlfit_id == 'false')
			$vlfit_id = false;
		
		
		
		if($accept_deny == 'approve'){
			$approve = true;
		}else{
			$approve = false;
		}
		
		
		
		
//		if($table_request == '1'){
			$table_request = true;
//		}else{
//			$table_request = false;
//		}
		
		
		
		
		if(!$message)
			$message = '';
		
		
		
		//Make sure submitted team_venue matches this manager's team_venues
		//TODO: This needs to be secured against spoofing
		if($request_type == 'team'){
			$this->load->model('model_team_guest_lists', 'team_guest_lists', true);
			$result = $this->team_guest_lists->update_team_guest_list_reservation_reject_or_approve($approve,
																							$message,
																							null,
																							$glr_id,
																							false,
																							$table_request,
																							$vlfit_id,
																							$this->vc_user->manager->team_fan_page_id);
		}else{
			
			
			$this->db->where(array(
				'id'						=> $glr_id,
				'approved' 					=> 1,
				'manager_table_approved' 	=> 0
			));
			if($approve){
				
				$this->db->update('promoters_guest_lists_reservations', array(
					'manager_table_approved' 				=> 1,
					'venues_layout_floors_items_table_id'	=> $vlfit_id
				));	
				
			}else{
				
				$this->db->update('promoters_guest_lists_reservations', array(
					'manager_table_approved' 				=> -1
				));
				
			}
			
			
			$result = true;
			
			
		/*	
			//promoter
			$this->load->model('model_guest_lists', 'guest_lists', true);
			$result = $this->guest_lists->update_promoter_guest_list_reservation_reject_or_approve($approve,
																							$message,
																							null,
					
		 * 			
		 * 																$this->input->post('tglr_id'));
		 */
		
		
		}





		return array('success' => $result,
								'message' => '');
	}

	/**
	 * Retrieve statistics about a user
	 * 
	 */
	private function _helper_retrieve_user_stats(){
		
		
		die(json_encode(array('success' => true)));
	}
	
	
	
	private function _helper_retrieve_floorplans_and_reservations($options){
		
		$this->load->model('model_users_managers', 'users_managers', true);
		$this->load->model('model_teams', 'teams', true);
		
		$team_venues = $this->users_managers->retrieve_team_venues($this->vc_user->manager->team_fan_page_id);
		$this->load->helper('retrieve_venue_floorplan');
		
		$init_users = array();
		foreach($team_venues as $key => &$venue){
			
			
			
			
			
			if(isset($options['tv_id']))
				if($venue->tv_id != $options['tv_id']){
					unset($team_venues[$key]);
					continue;
				}
			
			
			
			
			
			$venue_floorplan = retrieve_venue_floorplan(array(
				'tv_id' 							=> $venue->tv_id,
				'team_fan_page_id' 					=> $this->vc_user->manager->team_fan_page_id,
				'retrieve_approved_reservations'	=> false
			));
			$venue = (object)array_merge((array)$venue, (array)$venue_floorplan);
			$venue->venue_floorplan = (array)$venue->venue_floorplan;
			

			$all_upcoming_reservations = $this->teams->retrieve_venue_floorplan_reservations($venue->tv_id,
																								$this->vc_user->manager->team_fan_page_id,
																								false);
																								
			//get a list of all the users on this reservation																					
			foreach($all_upcoming_reservations as $vr){
				
				if(isset($vr->tglr_user_oauth_uid))
					$init_users[] = $vr->tglr_user_oauth_uid;
				elseif(isset($vr->pglr_user_oauth_uid)){
					$init_users[] = $vr->pglr_user_oauth_uid;
					$init_users[] = $vr->up_users_oauth_uid;
				}
				
				if($vr->entourage)
					foreach($vr->entourage as $ent){
						$init_users[] = $ent;
					}
				
			}unset($vr);																	
																						
			
			$venue->venue_all_upcoming_reservations = $all_upcoming_reservations;
			
			//------------------------------------- END EXTRACT FLOORPLAN -----------------------------------------
		
			
			
		}
		unset($venue);
		
		$init_users = array_unique($init_users);
		$init_users = array_values($init_users);
		
		return array($team_venues, $init_users);
		
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