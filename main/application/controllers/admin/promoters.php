<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Promoters extends MY_Controller {
	
	private $vc_user = null;
	private $view_dir = 'admin/promoters/';
	
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
		
		/***
		 * Authenticates user and verifies they are logged in
		 * 	call these methods before any loading done
		 */
		/*--------------------- Login Handler ------------------------*/
		$vc_user = json_decode($this->session->userdata('vc_user'));
		$this->vc_user = $vc_user;
		if(!isset($vc_user->promoter)){
			$this->_login();
			die();
		}
		if($this->uri->segment(3) == 'logout'){
			$this->_logout();
			die();
		}
		/*--------------------- End Login Handler --------------------*/
		

		
		/* --------------------- Load admin-promoter library ------------------------ */
		$this->load->library('library_promoters');
		$this->library_promoters->initialize(array('promoter_id' => $vc_user->promoter->up_id), true);
		/* --------------------- End Load admin-promoter library ------------------------ */
		
		
		if($this->input->post('vc_method') == 'user_stats_retrieve'){
			$this->_helper_retrieve_user_stats();
		}
		
	
		//if($this->library_promoters->promoter->)
		
		
		$this->load->vars('team_fan_page_id', $vc_user->promoter->t_fan_page_id);
		$this->load->vars('users_oauth_uid', $this->library_promoters->promoter->up_users_oauth_uid);
		$this->load->vars('subg', 'promoters');
		
		$this->load->vars(array(
			'is_promoter' 	=> true,
			'is_manager' 	=> false,
			'is_host'		=> false
		));
		
		$this->load->vars('promoter_id', $vc_user->promoter->up_id);
		
		$this->load->model('model_team_messaging', 'team_messaging', true);
		$team_chat_members = $this->team_messaging->retrieve_team_members(array('teams_fan_page_id' => $vc_user->promoter->t_fan_page_id));
		$this->load->vars('team_chat_members', $team_chat_members);

		
	}
	
	/**
	 * /admin/$arg0/$arg1/$arg2/
	 * Control point, chooses private method to handle request based on URL
	 * Example:
	 * 		www.vibecompass.com/admin/promoters/guest_lists_edit/55
	 * 			- $arg0 = 'guest_lists_edit'
	 * 			- $arg1 = '55'
	 * 			- $arg2 = Limiter check, throws 404 for non-existant urls
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return 	null
	 * */
	public function index($arg0 = '', $arg1 = '', $arg2 = ''){
		
		if(!isset($this->library_promoters->promoter->team) && ($this->uri->segment(3) !== false)){
			//promoter has either quit or been fired
			redirect('/admin/promoters/', 'refresh');
			die();
		}
			
		/* ------------------------- Force new promoters into setup ------------------------- */
		//we want to capture all incoming requests that are not to /admin/ for promoters that have not gone through
		//the initial setup flow. Once at /admin/ promoters will be forced to read a setup dialog and answer fields	
		if(!$this->library_promoters->promoter->up_completed_setup && ($this->uri->segment(3) !== false)){
			redirect('/admin/promoters/', 'refresh');
			die();
		}
		/* ------------------------- End Force new promoters into setup ------------------------- */
				
		/*--------------------- AJAX Request Bypass Handler ---------------------*/
		//Note: ocupload is the name of the plugin used for one-click image uploading
			//it creates a hidden iframe which is used to submit an image without a page refresh
		
		if($this->uri->segment(3) != 'mobile') 	//<--- special exception for mobile page, load all requests through primary method
		if(($this->input->is_ajax_request() && $this->input->post('ajaxify') === false ) || $this->input->post('ocupload')){
			
			//SPECIAL CASES:
			//we want these methods to fire but we don't want to force users to supply extra url segment
			if($arg0 == ''){
				$arg0 = 'dashboard';
			}
			
			if(!$this->library_promoters->promoter->team){
				$arg0 = 'no_team_dashboard';
			}elseif(!$this->library_promoters->promoter->up_completed_setup){
				$arg0 = 'setup_dashboard';
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
		 * 		www.vibecompass.com/admin/promoters/
		 * 
		 * 		$arg0 = ''
		 * 		$arg1 = ''
		 * 		$arg2 = ''
		 * */
		if($arg0 == ''){
			
			$arg0 = 'dashboard';
				
			if(!isset($this->library_promoters->promoter->team)){
				//promoter has either quit or been fired
				
				$arg0 = 'no_team_dashboard';
				
			}elseif(!$this->library_promoters->promoter->up_completed_setup){
				
				$arg0 = 'setup_dashboard';
				
			}else{

				
			}
			
		}
		/*
		 * /[function identifier: specific controller private method to invoke]/ --multiple
		 * Examples:
		 * 		www.vibecompass.com/admin/promoters/guest_lists/
		 * 
		 * 		$arg0 = 'guest_lists'
		 * 		$arg1 = ''
		 * 		$arg2 = ''
		 * */
		elseif($arg0 != '' && $arg1 == ''){
			
			switch($arg0){
				case 'guest_lists':

					
					break;
				case 'tables':
					
					break;
				case 'clients':
					
					break;
					
				
					
				case 'reports_guest_lists':
					
					break;
					
				/*
				case 'reports_sales':
					
					break;
				case 'reports_clients':
					
					break;
				 * 
				 * */
				
				
				case 'my_profile_img':
					
					break;
				case 'statistics':
				
					break;
				case 'manage_guest_lists':
					
					break;
				case 'manage_guest_lists_new':
					
					break;
				case 'manage_image':
		
					break;
				case 'my_profile':
					
					break;
				case 'support':
				
					break;
				case 'mobile':
					
					break;
				default:
					
					show_404('invalid_url');
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
				
				case 'clients':
					
					break;
				case 'manage_guest_lists_edit':
					
					break;
				case 'mobile':
					
					break;
				default:
					show_404('invalid url');
					break;
			}
			
		}
		/*
		 * Limiter check
		 * 
		 * */
		elseif($arg0 != '' && $arg1 != '' && $arg2 != ''){
			
			switch($arg0){
				case 'mobile':
					
					break;
				default:
					show_404('Invalid url');
			}
			
		}
		# ----------------------------------------------------------------------------------- #
		#	END CONTROLLER METHOD ROUTING													  #
		# ----------------------------------------------------------------------------------- #	
		
		
		# ---------------- LOAD HEADER, BODY, FOOTER VIEWS ---------- #
		
		//display header view		
		//show either setup-header or normal header
		
		//call 'body' function and include all arguments/url-segments
		call_user_func(array($this, '_' . $arg0), $arg0, $arg1, $arg2);
		
		
		if(!$this->input->post('ajaxify')){
			if(!$this->library_promoters->promoter->up_completed_setup || !isset($this->library_promoters->promoter->team))
				$this->header_html = $this->load->view('admin/promoters/setup/view_admin_setup_header', '', true);
			else
				if($arg0 != 'mobile')
				$this->header_html = $this->load->view('admin/promoters/view_admin_header', '', true);
				
					
			//Display the footer view after the header/body views have been displayed
			if($arg0 != 'mobile')
				$this->footer_html = $this->load->view('admin/promoters/view_admin_footer', '', true);
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
	 * Dashboard page
	 * Show promoter basic statistics
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 * */
	private function _dashboard($arg0 = '', $arg1 = '', $arg2 = ''){
		
		//force mobile devices onto mobile site
	//	$this->load->library('user_agent');
	//	if($this->agent->is_mobile()){
	//		redirect('/admin/promoters/mobile/', 'refresh');
	//		die();
	//	}


		$this->load->helper('run_gearman_job');
		$arguments = array('piwik_id_site' => $this->library_promoters->promoter->up_piwik_id_site);
		run_gearman_job('admin_promoter_piwik_stats', $arguments);
		
		
		
		$statistics = new stdClass;
		$statistics->num_clients 										= $this->library_promoters->retrieve_num_clients();
		$statistics->num_total_guest_list_reservations 					= $this->library_promoters->retrieve_num_guest_list_reservation_requests(false);
		$statistics->num_upcoming_guest_list_reservations 				= $this->library_promoters->retrieve_num_guest_list_reservation_requests();
		$statistics->trailing_weekly_guest_list_reservation_requests 	= $this->library_promoters->retrieve_trailing_weekly_guest_list_reservation_requests();
		
		
		
		//------- retrieve top profile visitors ---------
		$top_profile_views = $this->library_promoters->retrieve_top_profile_visitors();
		$top_profile_views_uids = array();
		foreach($top_profile_views as $tpv){
			$top_profile_views_uids[] = $tpv->users_oauth_uid;
		}
		$statistics->top_visitors = $top_profile_views_uids;
		//------- end retrieve top profile visitors --------
		
		
		//------- retrieve recent profile visitors --------
		$recent_profile_views = $this->library_promoters->retrieve_recent_profile_views();
		$recent_profile_views_uids = array();
		foreach($recent_profile_views as $rpv){
			$recent_profile_views_uids[] = $rpv->uv_users_oauth_uid;
		}
		$statistics->recent_visitors = $recent_profile_views_uids;
		// ------ end retrieve recent profile visitors -------
		

		
		list($weekly_guest_lists, $backbone_pending_reservations, $users) = $this->_helper_backbone_weekly_guest_lists();


		
		$statistics->backbone_pending_reservations 	= $backbone_pending_reservations;
		$statistics->pending_reservations_users 	= $users;
		$statistics->weekly_guest_lists 		= $weekly_guest_lists;
		$statistics->weekly_guest_lists_users 	= json_encode($users);
		//------- end retrieve promoter guest list reservations -------
		
		
		
		
		$data['statistics'] = $statistics;
		
		$this->load->model('model_teams', 'teams', true);
	
		$announcements = $this->teams->retrieve_team_announcements(array(
			'team_fan_page_id' => $this->library_promoters->promoter->team->t_fan_page_id
		));
		$data['announcements'] = $announcements;
		
		
		
		
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
		$data['users'] = $users;
		
		
		
		
		$this->body_html = $this->load->view($this->view_dir . 'dashboard/view_admin_dashboard', $data, true);
		
	}

	/**
	 * Handles signup flow for new promoters
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 */
	private function _setup_dashboard($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->helper('form'); //TODO: I should check if this is necessary anymore
		
		$this->body_html = $this->load->view($this->view_dir . 'setup/view_admin_setup_dashboard', '', true);
	}
	
	/**
	 * Display message to promoters who have either quit or been fired
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 */
	private function _no_team_dashboard($arg0 = '', $arg1 = '', $arg2 = ''){
				
		$this->body_html = $this->load->view($this->view_dir . 'no_team/view_admin_no_team', '', true);
	}
	
	/**
	 * Displays promoter's current/upcoming guest lists as well as the groups
	 * that have joined that guest list and the members of each group. Allows
	 * promoters to accept/deny groups.
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 */
	private function _guest_lists($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
				
		list($weekly_guest_lists, $users) = $this->_helper_promoter_guest_lists_and_members();
		$data['weekly_guest_lists'] = $weekly_guest_lists;
		$data['users'] 				= json_encode($users);
		$data['clients'] = $this->library_promoters->retrieve_promoter_clients_list();
		
		$this->body_html = $this->load->view('admin/promoters/guest_lists/view_guest_lists', $data, true);
		
	}
	
	/**
	 * Details the floorplans and any table reservations at all of a promoter's team_venues
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 */
	private function _tables($arg0 = '', $arg1 = '', $arg2 = ''){

		
		
		list($init_users, $team_venues) = $this->_helper_venue_floorplan_retrieve_v2();
		
		$data['init_users'] = $init_users;
		$data['team_venues'] = $team_venues;
		
		
		
		$this->body_html = $this->load->view($this->view_dir . 'tables/view_admin_promoter_tables', $data, true);
		
	}
	
	/**
	 * Displays a list of all facebook users who have ever been a 'client' of
	 * a given promoter. To qualify as a 'client' that facebook user must have
	 * requested to be included on a guest list or been a member of a group
	 * that was requested to be included on a guest list.
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 */
	private function _clients($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
		$this->load->model('model_users_promoters', 'users_promoters', true);
		$data['clients'] = $this->users_promoters->retrieve_promoter_clients_list_detailed($this->library_promoters->promoter->up_id);
		
		
		if($arg1){
			
			$client = false;
			foreach($data['clients'] as $cl){
				if($cl->u_oauth_uid == $arg1)
					$client = $cl;
			}
			
			$this->load->model('model_teams', 'teams', true);
			//retrieve client notes
			
			
			
			
			$client_notes_team = $this->teams->retrieve_client_notes(array(
				'team_fan_page_id'	=> $this->vc_user->promoter->t_fan_page_id,
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
				if($cnt->user_oauth_uid == $this->library_promoters->promoter->up_users_oauth_uid){
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

		
	}
	
	
	
	
	
	
	
	
	
	/**
	 * 
	 *
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 */
	private function _reports_guest_lists($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->helper('admin_report_guest_lists');
		$data = admin_report_guest_lists($this->vc_user->promoter->t_fan_page_id);
		
		$this->body_html = $this->load->view('admin/managers/' . 'reports/view_manager_reports_guest_lists', $data, true);
		
		
	}





	
	
	
	
	/**
	 * Reports statistical data about a promoter's guest list performance
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 */
//	private function _reports_guest_lists($arg0 = '', $arg1 = '', $arg2 = ''){
		
//	}
	
	/**
	 * Reports statistical information about a promoter's sales
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 */
//	private function _reports_sales($arg0 = '', $arg1 = '', $arg2 = ''){
		
//	}
	
	/**
	 * Reports statistical information about a promoter's clients
	 *
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 */
//	private function _reports_clients($arg0 = '', $arg1 = '', $arg2 = ''){
		
//	}
	
	
	
	
	
	
	
	
	
	
	/**
	 * Profile management page
	 * Allows promoters to view/update details of their user profiles
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 * */
	private function _my_profile($arg0 = '', $arg1 = '', $arg2 = ''){
				
		//add promoter's languages to promoter object
//		$data['languages'] = $this->library_promoters->retrieve_promoter_languages();
		
		
		$this->body_html = $this->load->view('admin/promoters/my_profile/view_admin_profile', '', true);
	}

	/**
	 * displays promoter's profile image and allows users to upload/crop new images
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 */
	private function _my_profile_img($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
		$this->body_html = $this->load->view('admin/promoters/my_profile/view_admin_profile_picture_crop', '', true);
	}

	
	/**
	 * Guest list management page
	 * Allows promoters to view/modify their guest lists and settings
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null
	 */
	private function _manage_guest_lists($arg0 = '', $arg1 = '', $arg2 = ''){

		//retrieve promoter's guest lists
		$this->load->model('model_users_promoters', 'users_promoters', true);
		$data['promoters_guest_lists'] = $this->users_promoters->retrieve_promoter_guest_list_authorizations($this->library_promoters->promoter->up_id);
		
	//	$data['promoters_guest_lists'] = $this->library_promoters->retrieve_promoter_guest_list_authorizations();
		
		$this->body_html = $this->load->view($this->view_dir . 'manage_guest_lists/view_manage_guest_lists', $data, true);
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * Edit the settings of an existing guest list
	 * 
	 * @return	null
	 */
	private function _manage_guest_lists_edit($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->model('model_guest_lists', 'guest_lists', true);
		$guest_list = $this->guest_lists->retrieve_pgla($this->library_promoters->promoter->up_id, $arg1);
		
		if(!$guest_list){
			redirect('/admin/promoters/manage_guest_lists/', 'refresh');
			die();
		}
		if(isset($guest_list->pgla_deactivated) && $guest_list->pgla_deactivated == 1){
			redirect('/admin/promoters/manage_guest_lists/', 'refresh');
			die();
		}
		
		
		$data['guest_list'] = $guest_list;
		$this->body_html = $this->load->view($this->view_dir . 'manage_guest_lists/view_manage_guest_lists_edit', $data, true);
				
	}
	/**
	 * 
	 */
	private function _ocupload_manage_guest_lists_edit($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->library('library_image_upload', '', 'image_upload');
			
		//verifies image upload is acceptable, 
		//saves original + cropped versions to amazon s3, 
		//and updates promoter database
		if($this->image_upload->image_upload(array('type' 			=> 'promoter', 
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
	 * Guest list management page
	 * Allows promoters to view/modify their guest lists and settings
	 * 
	 * @return	null
	 */
	private function _manage_guest_lists_new($arg0 = '', $arg1 = '', $arg2 = ''){
		
		
		/*
		if(!$manage_image = $this->session->flashdata('manage_image')){
			//set this flash data so if user navigates to 'manage_image' it will allow 
			$manage_image = new stdClass;
			$manage_image->existing = false;
			$manage_image->type = 'guest_lists';
			$manage_image->live_image = false;
			$manage_image->return = 'manage_guest_lists_new';
			$this->session->set_flashdata('manage_image', json_encode($manage_image));
		}else{
			$manage_image = json_decode($manage_image);
			$this->session->keep_flashdata('manage_image');
		}
			
		Kint::dump($manage_image);	
				
		$data['manage_image'] = $manage_image;
		*/
				
		//retrieve venues promoter is authorized to represent
		$data['promoter_team_venues'] = $this->library_promoters->retrieve_promoter_team_venues();
		
		$this->load->helper('form');
		$this->body_html = $this->load->view($this->view_dir . 'manage_guest_lists/view_manage_guest_lists_new', $data, true);
		
	}
	
	private function _ocupload_manage_guest_lists_new($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->library('library_image_upload', '', 'image_upload');
		
		//verifies image upload is acceptable, 
		//saves original + cropped versions to amazon s3, 
		//and updates promoter database
		if($this->image_upload->image_upload(array('type' 			=> 'promoter', 
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
	 * Handles image uploading and cropping for either a new or existing image
	 * 
	 * @return 	null
	 */
	private function _manage_image($arg0 = '', $arg1 = '', $arg2 = ''){
		
		//check flashdata to make sure there is a flashdata object either from creating a new event or editing an existing one (this might get a little tricky)
		if(!$manage_image = $this->session->flashdata('manage_image'))
			redirect('/admin/promoters/', 'location', 302);
		
		$this->session->keep_flashdata('manage_image');
					
		$data['manage_image'] = json_decode($manage_image);
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
		
		$this->body_html = $this->load->view($this->view_dir . 'support/view_admin_promoter_support', $data, true);
		
	}
	
	/**
	 * Mobile interface to promoter backend
	 * 
	 * @param	first URL segment
	 * @param	second URL segment
	 * @param	third URL segment
	 * @return 	null
	 */
	private function _mobile($arg0 = '', $arg1 = '', $arg2 = ''){
		
		//force non-mobile devices onto regular site
//		$this->load->library('user_agent');
//		if(!$this->agent->is_mobile()){
//			redirect('/admin/promoters/', 'refresh');
//			die();
//		}

	//	var_dump($arg0);	mobile
	//	var_dump($arg1);	pglr
	//	var_dump($arg2);	44
		
		
		$data['title'] = 'Promoter Mobile Interface - ClubbingOwl';
		
		
		//for root, just load regular view of page
		if($arg1 == ''){
			
			// ------------------- retrieve guest lists and guest list members ---------------------------------				
			//for each guest list, find all groups associated with it
			
			$this->load->model('model_guest_lists', 'guest_lists', true);
			$this->load->model('model_users_promoters', 'users_promoters', true);
			$weekly_guest_lists = $this->users_promoters->retrieve_promoter_guest_list_authorizations($this->library_promoters->promoter->up_id);
			
			foreach($weekly_guest_lists as &$gla){
				$gla->groups = $this->guest_lists->retrieve_single_guest_list_and_guest_list_members($gla->pgla_id, $gla->pgla_day);
			}
			
			//Need simple array of all FBID's of users for javascript client-side FQL query
			$users = array();
			foreach($weekly_guest_lists as $wgl){
				foreach($wgl->groups as $group){
					
					$users[] = $group->head_user;
					$users = array_merge($users, $group->entourage_users);
					
				}
			}
			$users = array_unique($users);
			$users = array_values($users);
			$data['weekly_guest_lists'] = $weekly_guest_lists;
			$data['users'] = json_encode($users);
									
			// ------------------- end retrieve guest lists and guest list members ---------------------------------
								
			$this->body_html = $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_header', $data, true);
			$this->body_html .= $this->load->view($this->view_dir . 'mobile/requests/view_promoters_mobile_requests', $data, true);
			$this->body_html .= $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_footer', $data, true);
	
		}else{
			
			switch($arg1){
				case 'guest_lists':
					
					$this->load->model('model_guest_lists', 'guest_lists', true);
					$this->load->model('model_users_promoters', 'users_promoters', true);
					$weekly_guest_lists = $this->users_promoters->retrieve_promoter_guest_list_authorizations($this->library_promoters->promoter->up_id);
										
					if($arg2 == ''){
						//showcase all guest lists
						
						// ------------------- retrieve guest lists and guest list members ---------------------------------				
						//for each guest list, find all groups associated with it
						foreach($weekly_guest_lists as &$gla){
							$gla->groups = $this->guest_lists->retrieve_single_guest_list_and_guest_list_members($gla->pgla_id, $gla->pgla_day);
						}
						
						//Need simple array of all FBID's of users for javascript client-side FQL query
						$users = array();
						foreach($weekly_guest_lists as $wgl){
							foreach($wgl->groups as $group){
								
								$users[] = $group->head_user;
								$users = array_merge($users, $group->entourage_users);
								
							}
						}
						$users = array_unique($users);
						$users = array_values($users);
						$data['weekly_guest_lists'] = $weekly_guest_lists;
						$data['users'] = json_encode($users);
												
						// ------------------- end retrieve guest lists and guest list members ---------------------------------
						
						
						$this->body_html = $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_header', $data, true);
						$this->body_html .= $this->load->view($this->view_dir . 'mobile/guest_lists/view_promoters_mobile_guest_lists', $data, true);
						$this->body_html .= $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_footer', $data, true);
												
					}else{
						//showcase a specific guest list
						
						foreach($weekly_guest_lists as $key => $gla){
							if($gla->pgla_id != $arg2){
								unset($weekly_guest_lists[$key]);
							}
						}
						if($weekly_guest_lists){
							
							$weekly_guest_lists = array_shift(array_values($weekly_guest_lists));
							$weekly_guest_lists->groups = $this->guest_lists->retrieve_single_guest_list_and_guest_list_members($weekly_guest_lists->pgla_id, $weekly_guest_lists->pgla_day);
							
							$users = array();
							foreach($weekly_guest_lists->groups as $group){
								$users[] = $group->head_user;
								$users = array_merge($users, $group->entourage_users);
							}
							
							$users = array_unique($users);
							$users = array_values($users);
							$data['guest_list'] = $weekly_guest_lists;
							$data['users'] = json_encode($users);
							
							$this->body_html = $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_header', $data, true);
							$this->body_html .= $this->load->view($this->view_dir . 'mobile/guest_lists/view_promoters_mobile_guest_lists_individual', $data, true);
							$this->body_html .= $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_footer', $data, true);
													
						}else{
							show_404('Promoter guest list does not exist');
							die();
						}
						
					}
										
					break;
				case 'tables':
					$this->body_html = $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_header', $data, true);
					$this->body_html .= $this->load->view($this->view_dir . 'mobile/tables/view_promoters_mobile_tables', $data, true);
					$this->body_html .= $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_footer', $data, true);		
					break;
				case 'chat':
					$this->body_html = $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_header', $data, true);
					$this->body_html .= $this->load->view($this->view_dir . 'mobile/chat/view_promoters_mobile_chat', $data, true);
					$this->body_html .= $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_footer', $data, true);		
					break;
				case 'pglr':
					
					if($arg2 == '')
						die(json_encode(array('success' => false)));
						
					
					$this->load->model('model_guest_lists', 'guest_lists', true);
					$this->load->model('model_users_promoters', 'users_promoters', true);
					$pglr = $this->guest_lists->retrieve_pglr($this->library_promoters->promoter->up_id, $arg2);
					
					if(!$pglr)
						die(json_encode(array('success' => false)));
					
					$data['pglr'] = $pglr;
					
					$users = array();
					$users[] = $pglr->pglr_user_oauth_uid;
					foreach($pglr->entourage as $user){
						$users[] = $user;
					}
					$data['users'] = json_encode($users);
					
					$this->body_html = $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_header', $data, true);
					$this->body_html .= $this->load->view($this->view_dir . 'mobile/pglr/view_pglr', $data, true);
					$this->body_html .= $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_footer', $data, true);	
					
					
									
					break;
				case 'options':
					$this->body_html = $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_header', $data, true);
					$this->body_html .= $this->load->view($this->view_dir . 'mobile/options/view_promoter_mobile_menu_options', $data, true);
					$this->body_html .= $this->load->view($this->view_dir . 'mobile/view_promoters_mobile_footer', $data, true);					
					break;
				default:
					show_404('Page not found');
					break;
					
			}
			
		}

	}
	
	/*******************************************************************************************************************
	 * 	END CONTROLLER VIEW DISPLAY FUNCTIONS
	 * 		Below functions are called via AJAX and helpers
	/ ******************************************************************************************************************/

	/**
	 * handles uploads of profile images via the ocupload jquery plugin, which allows for image uploading
	 * without a page refresh via a hidden iframe.
	 * 
	 * @return	null
	 * */
	private function _ocupload_my_profile_img($arg0 = '', $arg1 = '', $arg2 = ''){
		
		$this->load->library('library_image_upload', '', 'image_upload');
		
		//verifies image upload is acceptable, 
		//saves original + cropped versions to amazon s3, 
		//and updates promoter database
		if($this->image_upload->image_upload(array(
			'upload_type' => 'profile-pics'
		)))
			die(json_encode(array('success' => true,
									'image_data' => $this->image_upload->image_data)));
		else
			die(json_encode(array('success' => false,
									'message' => $this->image_upload->image_upload_error)));
	}
	
	/**
	 * handles uploads of profile images via the ocupload jquery plugin, which allows for image uploading
	 * without a page refresh via a hidden iframe.
	 * 
	 * @return	null
	 * */
	private function _ocupload_setup_dashboard($arg0 = '', $arg1 = '', $arg2 = ''){
				
		$this->load->library('library_image_upload', '', 'image_upload');
		
		//verifies image upload is acceptable, 
		//saves original + cropped versions to amazon s3, 
		//and updates promoter database
		if($this->image_upload->image_upload(array(
			'upload_type' => 'profile-pics'
		)))
			die(json_encode(array('success' => true,
									'image_data' => $this->image_upload->image_data)));
		else
			die(json_encode(array('success' => false,
									'message' => $this->image_upload->image_upload_error)));
	}
	
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
		if($this->image_upload->image_upload(array('type' 			=> 'event_guest_list',			//<-- event OR guest list 
													'upload_type' 	=> $manage_image->type,	//<-- specificies which (event or guest list)
													'live_image' 	=> $manage_image->live_image,
													'image_data' 	=> (isset($manage_image->image_data)) ? $manage_image->image_data : false))){
		
			//add uploaded image data to session
			$manage_image->image_data 	= $this->image_upload->image_data;
		//	$manage_image->existing 	= false;
		//	$manage_image->live_image 	= false;
			$this->session->set_flashdata('manage_image', json_encode($manage_image));
		
			die(json_encode(array('success' => true,
									'image_data' => $this->image_upload->image_data)));
									
		}else{
			
			die(json_encode(array('success' => false,
									'message' => $this->image_upload->image_upload_error)));
									
		}
		
	}	
	
	/**
	 * Handles all ajax requests for a promoter dashboard, this includes retrieving statistics from Piwik
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
	 * @return	null	 */
	private function _ajax_dashboard($arg0 = '', $arg1 = '', $arg2 = ''){
		
		if(!$vc_method = $this->input->post('vc_method')){
			die(json_encode(array('success' => false,
									'message' => 'invalid access attempt')));
		}
		
		switch($vc_method){
			case 'update_pending_requests':
				
				$this->_helper_respond_pending_request();
				
				break;
			case 'retrieve_pending_requests':
			
			
			
				list($weekly_guest_lists, $backbone_pending_reservations, $users) = $this->_helper_backbone_weekly_guest_lists();		
				
				$return = new stdClass;
				$return->reservations = $backbone_pending_reservations;
				$return->users = $users;
			
				die(json_encode(array('success' => true, 'message' => $return)));		
				
				
				break;
			case 'stats_retrieve':
				
				$this->load->helper('check_gearman_job_complete');
				check_gearman_job_complete('admin_promoter_piwik_stats');
								
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'invalid access attempt - unknown vc_method')));
				break;
		}
		
	}
	
	/**
	 * Sends guest list data to browser.
	 * Also allows promoter to approve/deny groups.
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
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
			
			case 'update_checkin_notify':
				
				$checked 	= $this->input->post('checked');
				$pglr_id 	= $this->input->post('pglr_id');	
				
				$this->db->where(array(
					'id'	=> $pglr_id
				))
				->update('promoters_guest_lists_reservations', array(
					'checkin_notify'	=> (($checked == 'true') ? 1 : 0)
				));
				
				die(json_encode(array('success' => true)));
								
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
				
				$this->load->model('model_guest_lists', 'guest_lists', true);
				$result = $this->guest_lists->create_new_promoter_guest_list_reservation(
					$this->input->post('pgla_id'),
					((isset($head_user['oauth_uid']) && $head_user['oauth_uid'] != 'null') ? $head_user['oauth_uid'] : NULL),
					$entourage,
					$this->input->post('up_id'),
					(($this->input->post('table_request') == 'true' || $this->input->post('table_request') == '1') ? 'true' : 'false'),
					false,
					'',
					'',
					'',
					'',
					$this->input->post('table_min_spend'), 	//table-min-spend,
					false,
					true,
					$head_user['name']
				);
				die(json_encode($result));
				



				break;
			case 'manual_add_find_tables':
					
				
				
				
				
				list($init_users, $team_venues) = $this->_helper_venue_floorplan_retrieve_v2();
				
				
				$data['init_users'] 	= $init_users;
				$data['team_venues'] 	= $team_venues;
				
				die(json_encode(array('success' => true, 'message' => $data)));			
				
				
				
				
				
				
				//find tables that are approved by this manager at this venue on this night
				
				break;
			case 'update_list_status':
			
			
			
			
			
			
				
				$pgla_id = $this->input->post('pgla_id');
				$status = $this->input->post('status');
				$status = strip_tags($status);
				
				if(!$status){
					die(json_encode(array('success' => false, 'message' => '')));
				}
				
				//should be checking if guest-list belongs to this promoter... too lazy
				$this->db->insert('guest_list_authorizations_statuses', array(
					'promoter_guest_list_authorizations_id'	=> $pgla_id,
					'status'								=> $status,
					'create_time'							=> time(),
					'users_oauth_uid'						=> $this->library_promoters->promoter->up_users_oauth_uid
				));
				
				$return_obj = new stdClass;
				$return_obj->status = $status;
				$return_obj->human_date = date('l m/d/y h:i:s A', time());
				
				$this->load->helper('run_gearman_job');
				run_gearman_job('gearman_new_promoter_gl_status', array(
					'team_fan_page_id'	=> $this->vc_user->promoter->t_fan_page_id,
					'up_id'				=> $this->vc_user->promoter->up_id,
					'pgla_id'			=> $pgla_id,
					'status'			=> $status,
					'human_time'		=> $return_obj->human_date
				), false);
				
				
				die(json_encode(array('success' => true, 'message' => $return_obj)));
				
				
				
				
				
				break;
			case 'retrieve_guest_lists':
				
				
				
				
				list($weekly_guest_lists, $users) 	= $this->_helper_promoter_guest_lists_and_members($this->input->post('pgla_id'), $this->input->post('weeks_offset'));
				$return 							= new stdClass;
				$return->weekly_guest_lists 		= $weekly_guest_lists;
				$return->users 						= $users;
				
				die(json_encode(array('success' => true, 'message' => $return)));
				
				
				
				
				break;
			case 'update_pending_requests':
				
				$this->_helper_respond_pending_request();
				
				break;
			/*
			case 'client_stats':
				$result = $this->library_promoters->retrieve_client_stats();
				die(json_encode($result));
				break;
			case 'retrieve_specific_week':
				$result = $this->library_promoters->retrieve_specific_week();
				die(json_encode($result));
				break;
			case 'promoter_list_manual_add':
				$result = $this->library_promoters->manual_list_add();
				die(json_encode($result));
				break;
			 * */
			case 'update_promoter_reservation_host_notes':
				
				$host_message = strip_tags($this->input->post('host_message'));
				if(!$host_message)
					$host_message = '';
				
				$pglr_id = $this->input->post('pglr_id');
				if($pglr_id === FALSE)
					die(json_encode(array('success' => false)));
				
				$result = $this->library_promoters->update_promoter_reservation_host_notes($pglr_id,
																						$host_message);
				die(json_encode($result));
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
		
		
		
		if($this->input->post('vc_method') == 'find_tables'){
			
			$data = $this->_helper_venue_floorplan_retrieve_v2();
			die(json_encode(array('success' => true, 'message' => array(
				'init_users' 	=> $data[0],
				'team_venues' 	=> $data[1]
			))));
			
		}
		
		
			
			
	}

	/**
	 * Sent promoter client list data to browser
	 * 
	 * @param	First URL segment
	 * @param	Second URL segment
	 * @param	Third URL segment
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
					'users_oauth_uid'	=> $this->library_promoters->promoter->up_users_oauth_uid,
					'client_oauth_uid'	=> $arg1,
					'team_fan_page_id'	=> $this->vc_user->promoter->t_fan_page_id,
					'public_notes'		=> $this->input->post('public_notes'),
					'private_notes'		=> $this->input->post('private_notes')
				));
				
				$this->teams->create_team_announcement(array(
					'type'				=> 'json',
					'team_fan_page_id'	=> $this->vc_user->promoter->t_fan_page_id,
					'message'			=> json_encode(array(
					
						'subtype'			=> 'new_client_notes',
						'client_oauth_uid'	=> $arg1,
						'public_notes'		=> $this->input->post('public_notes')
						
					)),
					'manager_oauth_uid'	=> $this->library_promoters->promoter->up_users_oauth_uid
				));
				
				die(json_encode(array('success' => true)));
				
				break;
			case 'client_list_retrieve':
				
				$this->load->helper('check_gearman_job_complete');
				check_gearman_job_complete('admin_promoter_client_list');
				
				
				
				
				/*
				if(!$admin_promoter_client_list = $this->session->userdata('admin_promoter_client_list'))
					die(json_encode(array('success' => false,
											'message' => 'No guest list retrieve request found')));	
													
				$admin_promoter_client_list = json_decode($admin_promoter_client_list);
				
				//check job status to see if it's completed
				$this->load->library('library_memcached', '', 'memcached');
				if($promoter_client_list = $this->memcached->get($admin_promoter_client_list->handle)){
					//free memory from memcached
					$this->memcached->delete($admin_promoter_client_list->handle);
					$this->session->unset_userdata('admin_promoter_client_list');
					die($promoter_client_list); //<-- already json in memcache
				}else{
					die(json_encode(array('success' => false)));
				}
				*/
				
				
				
				
				break;
			case 'client_stats':
				$result = $this->library_promoters->retrieve_client_stats();
				die(json_encode($result));
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'invalid access attempt - unknown vc_method')));
				break;
		}
		
	}
	
	/**
	 * Handles form input for my_profile page (form submitted via ajax request)
	 * 
	 * @return	null
	 * */
	private function _ajax_my_profile($arg0 = '', $arg1 = '', $arg2 = ''){
		
		/*array(5) { 
		 * ["public_identifier"]=> string(0) "" 	users_promoters
		 * ["languages"]=> string(1) "1" 			promoters_languages
		 * ["education"]=> string(1) "1" 			promoters_schools
		 * ["venues"]=> string(1) "8" 				promoters_venues
		 * ["biography"]=> string(8) " " }			users_promoters_profile
		 * */
		
		$this->load->library('form_validation');
		
		// form rules
		// ToDo: enhance form validation
		$this->form_validation->set_rules('public_identifier', 'User Name', 'trim');
	//	$this->form_validation->set_rules('languages', 'languages', 'trim');
	//	$this->form_validation->set_rules('education', 'education', 'trim');
	//	$this->form_validation->set_rules('venues', 'venues', 'trim');
		$this->form_validation->set_rules('sms_text_number', 'SMS/Text Number', 'trim|required');
		$this->form_validation->set_rules('biography', 'biography', 'trim|required');
		
		if($this->form_validation->run() == false){
			die(json_encode(array('success' => false,
									'message' => 'Please fill out all fields.')));
		}
		
		
		
		$sms_text_number = $this->input->post('sms_text_number');
		$sms_text_number = trim(preg_replace('/\D/', '', $sms_text_number));
		
		$this->load->model('model_users', 'users', true);
		if($this->users->retrieve_twilio_number($this->library_promoters->promoter->up_users_oauth_uid, $sms_text_number))
					die(json_encode(array('success' => false,
											'message' => 'SMS number is already taken. Please choose a different number.')));
		
		//first save public_identifier
			//note: need to implement validation callback to make sure not already taken and fits rules
		$this->load->model('model_users_promoters', 'users_promoters', true);
		$this->users_promoters->update_promoter(array('promoter_id' => $this->vc_user->promoter->up_id), 
													array('biography' => strip_tags($this->input->post('biography'))));
		
		$this->users->update_twilio_number($this->library_promoters->promoter->up_users_oauth_uid, $sms_text_number);
		
		die(json_encode(array('success' => true)));
	}

	/**
	 * Crop users profile picture to coordinates specified in crop form
	 * 
	 * @return	null
	 * */
	private function _ajax_my_profile_img($arg0 = '', $arg1 = '', $arg2 = ''){
		
		//crop operation
		$this->load->library('library_image_upload', '', 'image_upload');
		if($this->image_upload->profile_picture_crop())
			die(json_encode(array('success' => true,
									'image_data' => $this->image_upload->image_data)));
		else
			die(json_encode(array('success' => false)));
		
	}
	
	
	/**
	 * AJAX method requests from guest lists overview page
	 * 
	 * @return	null
	 * */
	private function _ajax_manage_guest_lists($arg0 = '', $arg1 = '', $arg2 = ''){

		$vc_method = $this->input->post('vc_method');

		switch($vc_method){
			case 'delete_guest_list':

				$result = $this->library_promoters->update_promoter_guest_list_set_deactivated();
				die(json_encode($result));
			
				break;
			case 'update_auto_approve':
				
				$result = $this->library_promoters->update_promoter_guest_list_set_auto_approve();
				die(json_encode($result));
				
				break;
			default:
				log_message('error', '_ajax_guest_lists post \'method\' does not exist: ' . $this->input->post('method'));
				die(json_encode(array('success' => false,
										'message' => 'method does not exist.')));
				break;
		}

	}
	
	/**
	 * AJAX update guest list settings
	 * 
	 * @return	null
	 * */
	private function _ajax_manage_guest_lists_edit($arg0 = '', $arg1 = '', $arg2 = ''){
			
		$vc_method = $this->input->post('vc_method');
		
		switch($vc_method){
			case 'promoter_edit_guest_list':
				
				$result = $this->library_promoters->edit_promoter_guest_list_authorization();
				die(json_encode($result));
				
				break;
			default:
				die(json_encode(array('success' => false)));
				break;
		}
		

	}
	
	/**
	 * AJAX method requests from guest lists creation page. 
	 * Handles form submission
	 * 
	 * @return	null
	 * */
	private function _ajax_manage_guest_lists_new($arg0 = '', $arg1 = '', $arg2 = ''){

		$this->session->keep_flashdata('manage_image');
		
		$vc_method = $this->input->post('vc_method');
		
		switch($vc_method){
			case 'promoter_new_guest_list':
				
				$result = $this->library_promoters->create_promoter_guest_list_authorization();
				die(json_encode($result));
				
				break;
			default:
				die(json_encode(array('success' => false,
										'message' => 'unknown vc_method')));
				break;
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
				
				if($this->image_upload->image_crop(
													$manage_image->image_data,
													$manage_image->type,
													$manage_image->live_image
													)){
					
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
	 * Handles form submission from first page of new promoter signup form
	 * 
	 * @param	not used
	 * @param	not used
	 * @return	null
	 */
	private function _ajax_setup_dashboard($arg0 = '', $arg1 = '', $arg2 = ''){
	//	error_reporting(E_ALL);
		
		$this->load->library('library_image_upload', '', 'image_upload');
		
		$vc_method = $this->input->post('vc_method');
		
		switch($vc_method){
			case 'crop_action':
				
				if($this->image_upload->profile_picture_crop())
					die(json_encode(array('success' => true,
											'image_data' => $this->image_upload->image_data)));
				else
					die(json_encode(array('success' => false)));
				
				break;
			case 'confirm_complete_action':
			
				//confirm this promoter has basic requirements to be considered 'completed_setup'
				$this->load->model('model_users_promoters', 'users_promoters', true);
				$this->load->model('model_users', 'users', true);
				
				/* ------- has promoter completed all requirements? ------ */
				$current_promoter = $this->library_promoters->promoter;
				if(isset($current_promoter->up_public_identifier) && strlen($current_promoter->up_public_identifier) > 0
					&& isset($current_promoter->up_biography) && strlen($current_promoter->up_biography) > 0
					&& isset($current_promoter->up_profile_image) && strlen($current_promoter->up_profile_image) > 0){
					
					$data = array(
								'completed_setup' => 1
								);
					$this->users_promoters->update_promoter(array('promoter_id' => $current_promoter->up_id), $data);
					
					//register promoter as a new site with piwik (API call, slow)
					$this->users_promoters->update_promoter_add_piwik($current_promoter->up_id);
					
					die(json_encode(array('success' => true))); 
						
				}else{
					
					die(json_encode(array('success' => false)));
					
				}
			
				break;
			default:
				$this->load->library('form_validation');
				$this->form_validation->set_rules('public_identifier', 'Public Identifier', 'trim|required');
				$this->form_validation->set_rules('sms_text_number', 'SMS/Text Number', 'trim|required');
				$this->form_validation->set_rules('biography', 'Personal Information', 'trim|required');
				
				
							
				//ToDo: complete form validation, public identifier checks etc.
				
				if($this->form_validation->run() == false){
					//well then they fucked up the form now didn't they...
					die(json_encode(array('success' => false,
											'message' => 'You must fill out all fields')));
				}
				
				//additional validation...
				$public_identifier = $this->input->post('public_identifier');
				//remove internal spaces from public identifier
				$public_identifier = trim(preg_replace('/\s\s+/', ' ', $public_identifier));
				//replace spaces in public identifier
				$public_identifier = str_replace(' ', '_', $public_identifier);
				
				
				$sms_text_number = $this->input->post('sms_text_number');
				$sms_text_number = trim(preg_replace('/\D/', '', $sms_text_number));
				
				
				$biography = $this->input->post('biography');
				//remove extra internal spaces from biography
				$biography = trim(preg_replace('/\s\s+/', ' ', $biography));
			
				
				if(preg_match('~[^a-z0-9_]~i', $public_identifier))
					die(json_encode(array('success' => false,
											'message' => 'Your public identifier may only contain alphanumeric characters')));
				
				if(strlen($public_identifier) < 5)
					die(json_encode(array('success' => false,
											'message' => 'Public identifier must be at least 5 characters')));
				
				if(strlen($public_identifier) > 30)
					die(json_encode(array('success' => false,
											'message' => 'Public identifier must be less than 30 characters')));
				
				//check to see if public identifier is already taken
				if($this->users_promoters->retrieve_promoter(array('promoter_public_identifier' => $public_identifier)))
					die(json_encode(array('success' => false,
											'message' => 'Public identifier is already taken')));
				
				
				
				
				
				//public id can't match a city url_identifier
				$this->db->select('id')
					->from('cities')
					->where(array(
						'url_identifier'	=> $public_identifier
					));
				$tres = $this->db->get()->row();
				if($tres){
					die(json_encode(array('success' => false,
											'message' => 'Invalid public identifier, please choose another name.')));
				}
				unset($tres);
				
				
				//reserved names
				if($public_identifier == 'cities' || $public_identifier == 'city'){
					die(json_encode(array('success' => false,
											'message' => 'Invalid public identifier, please choose another name.')));
				}
				
				
				
					
				if(strlen($sms_text_number) < 10)
					die(json_encode(array('success' => false,
											'message' => 'Please enter a valid phone number to recieve texts.')));
											
				if($this->users->retrieve_twilio_number($this->library_promoters->promoter->up_users_oauth_uid, $sms_text_number))
					die(json_encode(array('success' => false,
											'message' => 'SMS number is already taken. Please choose a different number.')));
					
				if(strlen($biography) < 100)
					die(json_encode(array('success' => false,
											'message' => 'Biography must be at least 100 characters.')));
						
				if(strlen($biography) > 2000)
					die(json_encode(array('success' => false,
											'message' => 'Biography must be less than 2000 characters')));
									
				$this->load->model('model_users_promoters', 'users_promoters', true);
				$data = array('public_identifier' => $public_identifier,
								'biography' => $biography);
				$this->users_promoters->update_promoter(array('promoter_id' => $this->vc_user->promoter->up_id), $data);
				
				$this->users->update_twilio_number($this->library_promoters->promoter->up_users_oauth_uid, $sms_text_number);
									
				//output success message
				die(json_encode(array('success' => true)));
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
	
	private function _helper_respond_pending_request(){
		
		$approve = ($this->input->post('action') == 'approve');
		$message = strip_tags($this->input->post('message'));
		if(!$message)
			$message = '';
		
		//Make sure submitted team_venue matches this manager's team_venues
		$this->load->model('model_guest_lists', 'guest_lists', true);
		$result = $this->guest_lists->update_promoter_guest_list_reservation_reject_or_approve($approve,
																								$this->library_promoters->promoter->up_id,
																								$this->input->post('pglr_id'),
																								$message);
		die(json_encode(array('success' => $result,
								'message' => '')));
								
	}
	
	/**
	 * Retrieve statistics about a user
	 * 
	 */
	private function _helper_retrieve_user_stats(){
		
		$user_oauth_uid = $this->input->post('vc_user');
		
		
		
		
		
		
		
		
		die(json_encode(array('success' => true, 
			'message' => array(
				'foo' => 'bar',
				'goo' => 'doll'
			))));
	}
	
	
	
	
	private function _helper_promoter_guest_lists_and_members($pgla_id = false, $offset = false){
		
		
		
		
		
		//retrieve a list of all the guest lists a promoter has set up
		$this->load->model('model_users_promoters', 'users_promoters', true);
		$weekly_guest_lists = $this->users_promoters->retrieve_promoter_guest_list_authorizations($this->library_promoters->promoter->up_id);
		
		
		
		$this->load->model('model_guest_lists', 'guest_lists', true);
		//for each guest list, find all groups associated with it
		foreach($weekly_guest_lists as $key => &$gla){
	
	
	
			//only grab the specific pgla_id we care about
			if($pgla_id){
				if($gla->pgla_id != $pgla_id){
					unset($weekly_guest_lists[$key]);
					continue;
				}
			}
			
			
	
			if(!$offset){
				$gla->human_date 	= $gla->human_date = date('l m/d/y', strtotime(rtrim($gla->pgla_day, 's')));
				$gla->iso_date 		= $gla->iso_date = date('Y-m-d', strtotime(rtrim($gla->pgla_day, 's')));
				$gla->current_week = true;
			}else{
				$gla->human_date 	= $gla->human_date = date('l m/d/y', strtotime('next ' . rtrim($gla->pgla_day, 's') . ' -' . $offset . ' weeks'));	
				$gla->iso_date 		= $gla->iso_date = date('Y-m-d', strtotime('next ' . rtrim($gla->pgla_day, 's') . ' -' . $offset . ' weeks'));	
				$gla->current_week = false;
			}
			
			if($offset == 0)
				$offset = false;
			
			$gla->groups = $this->guest_lists->retrieve_single_guest_list_and_guest_list_members($gla->pgla_id, $gla->pgla_day, $offset);
			
		}
		
		//Need simple array of all FBID's of users for javascript client-side FQL query
		$users = array();
	//	foreach($weekly_guest_lists as $wgl){
	//		foreach($wgl->groups as $group){
				
			//	$users[] = $group->head_user;
			//	$users = array_merge($users, $group->entourage_users);
				
	//			if($group->head_user !== null)
	//				$users[] = $group->head_user;
				
				
	//			foreach($group->entourage_users as $ent_user){
		//			if($ent_user->pglre_oauth_uid !== null)
		//				$users[] = $ent_user->pglre_oauth_uid;
		//		}
				
				
				
	//		}
	//	}
	//	$users = array_unique($users);
	//	$users = array_values($users);
		
	//	foreach($users as $key => $val){
	//		if(!is_numeric($val)){
	//			unset($users[$key]);
	//		}
	//	}
		
		
	//	if(($key = array_search(null, $users)) !== false) {
	//	    unset($users[$key]);
	//	}
		
		
		return array($weekly_guest_lists, $users);
		
	}
	
	
	
	private function _helper_backbone_weekly_guest_lists(){
		
		/*
		 * ----------------------------------
		 * TEMPLATE FOR GETTING PENDING RESERVATION REQUESTS
		 * */
		
		//------- retrieve promoter guest list reservations -------
		//retrieve a list of all the guest lists a promoter has set up 
		$this->load->model('model_users_promoters', 'users_promoters', true);
		$weekly_guest_lists = $this->users_promoters->retrieve_promoter_guest_list_authorizations($this->library_promoters->promoter->up_id);
				
		$backbone_pending_reservations = array();		
		$this->load->model('model_guest_lists', 'guest_lists', true);
		//for each guest list, find all groups associated with it
		for($i=0; $i < count($weekly_guest_lists); $i++){
				
			$gla = $weekly_guest_lists[$i];
			$gla->human_date 	= date('l m/d/y', strtotime(rtrim($gla->pgla_day, 's')));
			
			
			
			$groups = $this->guest_lists->retrieve_single_guest_list_and_guest_list_members($gla->pgla_id, $gla->pgla_day);
			
			foreach($groups as $g){
							
				$gla->human_phone_number = preg_replace('~(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$~', '$1-$2-$3', $g->u_phone_number);	
			
				$backbone_pending_reservations[] = (object)array_merge((array)$gla, (array)$g);
			}unset($g);
			
			$weekly_guest_lists[$i]->groups = $groups;
			
		}		
		
						
		//Need simple array of all FBID's of users for javascript client-side FQL query
		$users = array();
		foreach($weekly_guest_lists as $wgl){
			foreach($wgl->groups as $group){
				
			//	$users[] = $group->head_user;
			//	$users = array_merge($users, $group->entourage_users);
				
				if($group->head_user !== null)
					$users[] = $group->head_user;
				
				
				foreach($group->entourage_users as $ent_user){
					if($ent_user->pglre_oauth_uid !== null)
						$users[] = $ent_user->pglre_oauth_uid;
				}
				
				
				
			}
		}
		$users = array_unique($users);
		$users = array_values($users);
		
		foreach($users as $key => $val){
			if(!is_numeric($val)){
				unset($users[$key]);
			}
		}
		
		
		if(($key = array_search(null, $users)) !== false) {
		    unset($users[$key]);
		}
		
				
		/*
		 * ----------------------------------
		 * END TEMPLATE FOR GETTING PENDING RESERVATION REQUESTS
		 * */
		
		return array($weekly_guest_lists, $backbone_pending_reservations, $users);
		
	}
	
	
	
	
	
	
	
	private function _helper_venue_floorplan_retrieve_v2(){	
		
		
		$this->load->model('model_users_managers', 'users_managers', true);
		$this->load->model('model_teams', 'teams', true);
		
		$team_venues = $this->users_managers->retrieve_team_venues($this->vc_user->promoter->t_fan_page_id);
		
		
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

			$venue_floorplan = $this->teams->retrieve_venue_floorplan($venue->tv_id, $this->vc_user->promoter->t_fan_page_id);
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
																						$this->vc_user->promoter->t_fan_page_id,
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
																						$this->vc_user->promoter->t_fan_page_id,
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
	 * Unsets session data to log out user
	 * 
	 * @return 	null
	 * */
	private function _logout(){
		//$this->session->unset_userdata('admin_logged_in');
		redirect('/', 'refresh');
		return;
	}
	
	/**
	 * Displays login prompt to unathenticated users
	 * 
	 * @return 	null2	
	 * */
	private function _login(){
		redirect('/', 'refresh');
		return;
		//this method used to be far more complicated before facebook users were implemented
		
		
		/*--------------------- AJAX Login Request Handler ------------------------*/
		if($this->input->is_ajax_request()){
			
			$this->load->library('form_validation');
			
			// rules
			$this->form_validation->set_rules('username', 'User Name', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');
			
			if($this->form_validation->run() == FALSE){
			/*
				$errors = array(
						'username' => form_error('username'),
						'password' => form_error('password'));
			 * */
			
				$this->output
						->set_content_type('application/json')
    					->set_output(json_encode(array('success' => false)));
				return;
			}
			
			$this->load->model('model_users_promoters', 'promoters', true);
			if($this->promoters->p_valid_login($this->input->post('username'), 
												$this->input->post('password'))){
				$this->session->set_userdata('admin_logged_in',true);
				$this->output
						->set_content_type('application/json')
    					->set_output(json_encode(array('success' => true)));
				return;
			}else{
				$this->output
						->set_content_type('application/json')
    					->set_output(json_encode(array('success' => false)));
				return;
			}
		}
		/*--------------------- AJAX Login Request Handler ------------------------*/
		
		$this->load->helper('form');
		
		$data['header_javascripts'] = array('cufon-yui.js',
											'ColaborateLight_400.font.js',
											'easyTooltip.js',
											'easyTooltip.js',
											'visualize.jQuery.js',
											'iphone-style-checkboxes.js',
											'jquery.cleditor.min.js'
											//'js/custom.js'
											);
		
		//prepend s3 url to page-specific assets
		$this->load->helper('s3_prepend');
		s3_prepend($data['header_javascripts'], $null=null, 'admin_assets');
		
		
		$this->load->view('admin/promoters/view_admin_login', $data);
	}

}

/* End of file promoters.php */
/* Location: ./application/controllers/admin/promoters.php */