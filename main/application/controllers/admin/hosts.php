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
		$data->team 				= $this->teams->retrieve_team($this->vc_user->host->th_teams_fan_page_id);
		$data->team_venues 			= $this->_helper_venue_floorplan_retrieve_v2();
		$data->checkin_categories 	= $this->_helper_retrieve_team_checkin_categories();
	
	
	

		$this->body_html = $this->load->view($this->view_dir . 'dashboard/view_hosts_dashboard', 		array('data' => $data), 	true);
				
		
	}
	
	
	
	
	/**
	 * 
	 */
	private function _helper_retrieve_team_checkin_categories(){
		
		$this->db->select('*')
			->from('host_checkins_categories')
			->where(array(
				'hcc_team_fan_page_id'	=> $this->vc_user->host->th_teams_fan_page_id,
				'hcc_deleted'			=> 0
			))
			->order_by('hcc_amount', 'desc');
			
			
		$query = $this->db->get();
		
		return $query->result();
		
	}
	
	
	
	
	/**
	 * 
	 */
	private function _helper_venue_floorplan_retrieve_v2(){
				
		$this->load->model('model_users_managers', 	'users_managers', 	true);
		$this->load->model('model_teams', 			'teams', 			true);
		
		
		
		
		
		
		
		
		$team_venues = $this->users_managers->retrieve_team_venues($this->vc_user->host->th_teams_fan_page_id);
		
		
		//are we looking for just 1 tv?
		$tv_id 		= $this->input->post('tv_id');
		$iso_date 	= $this->input->post('iso_date');
		
			
		
		
		
		
		
		
		
		
		
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

			$venue_floorplan = $this->teams->retrieve_venue_floorplan($venue->tv_id, $this->vc_user->host->th_teams_fan_page_id);
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
																						$this->vc_user->host->th_teams_fan_page_id,
																						$lookup_date);
			$venue->venue_reservations = $venue_reservations;
			
			
			
			
			
			
			
		//	$all_upcoming_reservations = $this->teams->retrieve_venue_floorplan_reservations($venue->tv_id,
		//																				$this->vc_user->host->th_teams_fan_page_id,
		//																				false);
		//	$venue->venue_all_upcoming_reservations = $all_upcoming_reservations;
			
			//------------------------------------- END EXTRACT FLOORPLAN -----------------------------------------
		}unset($venue);
		
		return $team_venues;		
		
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
			case 'find_tables':
							
				$data = $this->_helper_venue_floorplan_retrieve_v2();
				die(json_encode(array('success' => true, 'message' => array(
					'init_users' 			=> array(),
					'team_venues' 			=> $data,
					'checkin_categories'	=> $this->_helper_retrieve_team_checkin_categories()
				))));
				
				break;
			case 'checkin_event':
				
				
				$post = (object)$this->input->post();
								
				$pusher_checkin_broadcast = function($event, $found_reservation, $socket_id){
					
					$found_reservation->event = $event;
					$this->load->library('Pusher', '', 'pusher');
					$this->pusher->trigger('presence-' . $this->vc_user->host->th_teams_fan_page_id, 'host_emit', $found_reservation, $socket_id);
					
				};
				
				
				 
				//--------------------------------------------------------------------------------------------------------
				//	now find the damn reservation that we're dealing with and determine if it's already checked in or not
				//--------------------------------------------------------------------------------------------------------					
				$team_venues 		= $this->_helper_venue_floorplan_retrieve_v2();
				$all_reservations 	= array();
				foreach($team_venues as $venue){
					foreach($venue->venue_reservations as $reservation){
						$all_reservations[] = $reservation;
					}
				}
									
								
								
				if($post->user_type == 'head_user'){
					
											
					//only check surface of reservations
					foreach($all_reservations as $reservation){
						
						if($post->list_type == 'team'){
															
							if(!isset($reservation->tglr_id))
								continue;
							
							if($reservation->tglr_id == $post->tglr_id){
								$found_reservation = $reservation;
								break;
							}
							
						}elseif($post->list_type == 'promoter'){
							
							if(!isset($reservation->pglr_id))
								continue;
															
							if($reservation->pglr_id == $post->pglr_id){
								$found_reservation = $reservation;
								break;
							}
							
						}
						
					}
					
					
				}elseif($post->user_type == 'entourage'){
					
					//check entourages of each reservation
					$temp_found = false;
					foreach($all_reservations as $reservation){
						foreach($reservation->entourage as $ent_member){
							
							if($post->list_type == 'team'){
							
								if(!isset($ent_member->tglre_id))
									continue;
							
								if($post->tglre_id == $ent_member->tglre_id){
									$found_reservation = $ent_member;
									$temp_found = true;
									break;
								}	
							
							}elseif($post->list_type == 'promoter'){
								
								if(!isset($ent_member->pglre_id))
									continue;
								
								if($post->pglre_id == $ent_member->pglre_id){
									$found_reservation = $ent_member;
									$temp_found = true;
									break;
								}	
								
							}
							
						}
						
						if($temp_found)
							break;
						
					}
					unset($temp_found);
					
				}
								
				if(!isset($found_reservation))
					die(json_encode(array('success' => false)));
				
				if($post->checked == 'true')
					if($found_reservation->hc_id != NULL){
						die(json_encode(array('success' => false)));
					}
				
				//--------------------------------------------------------------------------------------------------------
				
				if($post->checked == 'false'){
					//delete hcd & return
					
					$this->db->delete('host_checkins_data', array('hcd_id' 					=> $found_reservation->hcd_id, 
																	'hcd_team_fan_page_id' 	=> $this->vc_user->host->th_teams_fan_page_id));
					
					
					$pusher_checkin_broadcast('check_out', $found_reservation, $post->socket_id);
					
					
					die(json_encode(array('success' => true)));
										
				}
				
				
				if($post->checked == 'true'){
					//create hcd
					
					//create host_checkins_data record first
					$this->db->insert('host_checkins_data', array(
						'hcd_host_oauth_uid' 	=> $this->vc_user->oauth_uid,
						'hcd_checkin_time'		=> time(),
						'hcd_checkin_amount'	=> $post->category_value,
						'hcd_checkin_category'	=> $post->category,
						'hcd_notes'				=> '',
						'hcd_additional_guests'	=> 0,
						'hcd_team_fan_page_id'	=> $this->vc_user->host->th_teams_fan_page_id
					));
					$hcd_id = $this->db->insert_id();
					
					
					//create join records
					if($post->list_type == 'team'){
						
						if($post->user_type == 'head_user'){
							
							if($found_reservation->tglr_user_oauth_uid == NULL){
								//just link this guy and be done
								
								$this->db->insert('host_checkins', array(
									'hcd_id'		=> $hcd_id,
									'hc_tglr_id' 	=> $found_reservation->tglr_id
								));
								
								$pusher_checkin_broadcast('check_in', $found_reservation, $post->socket_id);
															
								die(json_encode(array('success' => true)));
								
							}else{
								//regular user -- link and link all entourage occurances
								
								//first link this guy
								$this->db->insert('host_checkins', array(
									'hcd_id'		=> $hcd_id,
									'hc_tglr_id' 	=> $found_reservation->tglr_id
								));
								
								$pusher_checkin_broadcast('check_in', $found_reservation, $post->socket_id);
								
								//check entourages of each reservation
								foreach($all_reservations as $reservation){
									foreach($reservation->entourage as $ent_member){
																				
										if($ent_member->oauth_uid != $found_reservation->tglr_user_oauth_uid)
											continue;
										
										//add a join record for this guy
										$this->db->insert('host_checkins', array(
											'hcd_id'		=> $hcd_id,
											'hc_tglre_id' 	=> $ent_member->tglre_id
										));
										
									}
								}
								
								die(json_encode(array('success' => true)));
								
							}
							
						}elseif($post->user_type == 'entourage'){

							if($found_reservation->tglre_oauth_uid == NULL){
								//just link this guy and be done
								
								$this->db->insert('host_checkins', array(
									'hcd_id'		=> $hcd_id,
									'hc_tglre_id' 	=> $found_reservation->tglre_id
								));
								
								$pusher_checkin_broadcast('check_in', $found_reservation, $post->socket_id);
															
								die(json_encode(array('success' => true)));
								
							}else{
								
								//check this instance in
								$this->db->insert('host_checkins', array(
									'hcd_id'		=> $hcd_id,
									'hc_tglre_id' 	=> $found_reservation->tglre_id
								));
								
								$pusher_checkin_broadcast('check_in', $found_reservation, $post->socket_id);
								
								//scan all other entourages
								//check entourages of each reservation
								foreach($all_reservations as $reservation){
									foreach($reservation->entourage as $ent_member){
																				
										if($ent_member->oauth_uid != $found_reservation->tglre_oauth_uid)
											continue;
										
										if($ent_member->tglre_id == $found_reservation->tglre_id)
											continue;
										
										//add a join record for this guy
										$this->db->insert('host_checkins', array(
											'hcd_id'		=> $hcd_id,
											'hc_tglre_id' 	=> $ent_member->tglre_id
										));
										
									}
								}
								
								die(json_encode(array('success' => true)));
							}
							
						}
				
					}elseif($post->list_type == 'promoter'){
						
						if($post->user_type == 'head_user'){
							
							if($found_reservation->pglr_user_oauth_uid == NULL){
								//just link this guy and be done
								
								$this->db->insert('host_checkins', array(
									'hcd_id'		=> $hcd_id,
									'hc_pglr_id' 	=> $found_reservation->pglr_id
								));
								
								$pusher_checkin_broadcast('check_in', $found_reservation, $post->socket_id);						
															
								die(json_encode(array('success' => true)));
								
							}else{
								//regular user -- link and link all entourage occurances
								
								//first link this guy
								$this->db->insert('host_checkins', array(
									'hcd_id'		=> $hcd_id,
									'hc_pglr_id' 	=> $found_reservation->pglr_id
								));
								
								$pusher_checkin_broadcast('check_in', $found_reservation, $post->socket_id);
								
								//check entourages of each reservation
								foreach($all_reservations as $reservation){
									foreach($reservation->entourage as $ent_member){
																				
										if($ent_member->oauth_uid != $found_reservation->pglr_user_oauth_uid)
											continue;
										
										//add a join record for this guy
										$this->db->insert('host_checkins', array(
											'hcd_id'		=> $hcd_id,
											'hc_pglre_id' 	=> $ent_member->pglre_id
										));
										
									}
								}
								
								die(json_encode(array('success' => true)));
								
							}
							
						}elseif($post->user_type == 'entourage'){
														
							if($found_reservation->pglre_oauth_uid == NULL){
								//just link this guy and be done
								
								$this->db->insert('host_checkins', array(
									'hcd_id'		=> $hcd_id,
									'hc_pglre_id' 	=> $found_reservation->pglre_id
								));
								
								$pusher_checkin_broadcast('check_in', $found_reservation, $post->socket_id);
															
								die(json_encode(array('success' => true)));
								
							}else{
								
								//check this instance in
								$this->db->insert('host_checkins', array(
									'hcd_id'		=> $hcd_id,
									'hc_pglre_id' 	=> $found_reservation->pglre_id
								));
								
								$pusher_checkin_broadcast('check_in', $found_reservation, $post->socket_id);
								
								//scan all other entourages
								//check entourages of each reservation
								foreach($all_reservations as $reservation){
									foreach($reservation->entourage as $ent_member){
																				
										if($ent_member->oauth_uid != $found_reservation->pglre_oauth_uid)
											continue;
										
										if($ent_member->pglre_id == $found_reservation->pglre_id)
											continue;
										
										//add a join record for this guy
										$this->db->insert('host_checkins', array(
											'hcd_id'		=> $hcd_id,
											'hc_pglre_id' 	=> $ent_member->pglre_id
										));
										
									}
								}
								
								die(json_encode(array('success' => true)));
								
							}

						}

					}




				
				
				
				}
				
				
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