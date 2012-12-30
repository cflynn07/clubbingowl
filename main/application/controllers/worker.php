<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Colors {
		private $foreground_colors = array();
		private $background_colors = array();
 
		public function __construct() {
			// Set up shell colors
		$this->foreground_colors['black'] = '0;30';
		$this->foreground_colors['dark_gray'] = '1;30';
		$this->foreground_colors['blue'] = '0;34';
		$this->foreground_colors['light_blue'] = '1;34';
		$this->foreground_colors['green'] = '0;32';
		$this->foreground_colors['light_green'] = '1;32';
		$this->foreground_colors['cyan'] = '0;36';
		$this->foreground_colors['light_cyan'] = '1;36';
		$this->foreground_colors['red'] = '0;31';
		$this->foreground_colors['light_red'] = '1;31';
		$this->foreground_colors['purple'] = '0;35';
		$this->foreground_colors['light_purple'] = '1;35';
		$this->foreground_colors['brown'] = '0;33';
		$this->foreground_colors['yellow'] = '1;33';
		$this->foreground_colors['light_gray'] = '0;37';
		$this->foreground_colors['white'] = '1;37';
 
			$this->background_colors['black'] = '40';
		$this->background_colors['red'] = '41';
		$this->background_colors['green'] = '42';
		$this->background_colors['yellow'] = '43';
		$this->background_colors['blue'] = '44';
		$this->background_colors['magenta'] = '45';
		$this->background_colors['cyan'] = '46';
		$this->background_colors['light_gray'] = '47';
		}
 
		// Returns colored string
	public function getColoredString($string, $foreground_color = null, $background_color = null) {
		$colored_string = "";
 
			// Check if given foreground color found
		if (isset($this->foreground_colors[$foreground_color])) {
			$colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
		}
		// Check if given background color found
		if (isset($this->background_colors[$background_color])) {
			$colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
			}
 
			// Add string and end coloring
		$colored_string .=  $string . "\033[0m";
 
			return $colored_string;
		}
 
		// Returns all foreground color names
		public function getForegroundColors() {
			return array_keys($this->foreground_colors);
		}
 
		// Returns all background color names
	public function getBackgroundColors() {
		return array_keys($this->background_colors);
	}
}









class Worker extends CI_Controller {
	
    public function __construct() {
    	parent::__construct();
		//force only access from command line
        if(php_sapi_name() !== 'cli') {
            show_404();
        }
		
		error_reporting(E_ALL);
	//	error_reporting(E_ALL);
		ini_set('display_errors', '1');
		
    }
	
	
	public function run_billing(){
		
		
		
		
		
		
		
		
		
		// Create new Colors class
		$colors = new Colors();
	 
		// Test some basic printing with Colors class
	//	echo $colors->getColoredString("Testing Colors class, this is purple string on yellow background.", 	"purple", 	"yellow") 		. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is blue string on light gray background.", 	"blue", 	"light_gray") 	. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is red string on black background.", 		"red", 		"black") 		. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is cyan string on green background.", 		"cyan", 	"green") 		. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is cyan string on default background.", 		"cyan") 					. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is default string on cyan background.", 		null, 		"cyan") 		. "\n";
	
	
	
		echo $colors->getColoredString("Testing Colors class, this is green string on default background.", 		'green', 		null) 		. PHP_EOL;
		$handle = fopen ("php://stdin","r");
		
		echo 'Do you like apples? ';
		$line 	= fgets($handle);
		
		echo $colors->getColoredString($line, 'green', null) . PHP_EOL;
		
		
		
		
		
	}
	
	
	/**
	 * Email a user's vibecompass friends when they join VibeCompass or join a guest-list
	 * 
	 */
	public function worker_email_vc_user_friends($oauth_uid = "504405294"){
		
		
		$this->db->where(array('oauth_uid' => $oauth_uid));
		$query = $this->db->get('users');
		$result = $query->row();
		
		
		
		
		/*	
		$this->load->helper('run_gearman_job');
		$arguments = array(
			'user_oauth_uid'	=> $result->oauth_uid,
			'access_token'		=> $result->access_token
		);
		
		run_gearman_job('gearman_email_friends_new_user', $arguments, false);
		
		*/
		
		
		
		
		
		//user just logged in, notify friends
		$this->load->helper('run_gearman_job');
		$arguments = array('access_token' => $result->access_token,
								'user_oauth_uid' => $result->oauth_uid);
		run_gearman_job('gearman_vc_user_notify_friends_online', $arguments, false);
		
		return;
				
	}
	
	
	
	
	public function test(){
	
		echo 'brazin test' . PHP_EOL;
	
		
		
		return;
	
		$this->load->library('Cloudcontrol', '', 'cloudcontrol');
	    $email = 'cflynn@ccs.neu.edu';
	    $password = 'w3smsnicq34';
	    $this->cloudcontrol->auth($email, $password);
	    $this->cloudcontrol->worker_create('vibecompass', 'default', 'index.php', 'worker all');
		
		echo 'done!' . PHP_EOL;		
				
	}
	
	
	
	
	/**
	 * Worker that performs all tasks, mostly used for development/simplicity
	 * 
	 */
	public function all($arg1 = ''){
		
		try{
 			
			echo 'all worker starting' . PHP_EOL;
		
			$this->load->library('pearloader');
			$gearman_worker = $this->pearloader->load('Net', 'Gearman', 'Worker');	
						
			$gearman_worker->addAbility('facebook_user_authenticate');
						
			$gearman_worker->addAbility('guest_list_text_message');
			$gearman_worker->addAbility('guest_list_share_facebook');
			$gearman_worker->addAbility('retrieve_facebook_app_requests');
			
		//	if(MODE == 'local'){
				$gearman_worker->addAbility('admin_promoter_piwik_stats');
				$gearman_worker->addAbility('admin_manager_piwik_stats');
		//	}
			
			$gearman_worker->addAbility('news_feed_retrieve');
			$gearman_worker->addAbility('friend_feed_retrieve');
			$gearman_worker->addAbility('friend_retrieve');
			$gearman_worker->addAbility('gearman_individual_promoter_friend_activity');			
			$gearman_worker->addAbility('gearman_admin_manager_promoter_piwik_stats');			
			$gearman_worker->addAbility('gearman_promoter_manual_add');			
			$gearman_worker->addAbility('gearman_manager_manual_add');			
			$gearman_worker->addAbility('gearman_vc_user_notify_friends_online');		
			$gearman_worker->addAbility('gearman_email_friends_new_user');				
			$gearman_worker->addAbility('gearman_retrieve_friend_venues_activity');	
			$gearman_worker->addAbility('gearman_individual_venue_friend_activity');	
			$gearman_worker->addAbility('gearman_send_emails');	
			$gearman_worker->addAbility('gearman_send_sms_notification');	
			
			
			//finds friends that are associated with a group of promoters
			$gearman_worker->addAbility('gearman_promoter_friend_activity');
			
			
			//promoter & team guest list status	
			$gearman_worker->addAbility('gearman_new_promoter_gl_status');	
			$gearman_worker->addAbility('gearman_new_manager_gl_status');	
			
			
			//finds reviews of a user's friends for a given promoter
			$gearman_worker->addAbility('gearman_individual_promoter_friend_reviews');	
					
					
			
		//	if($arg1 == 'special_emails'){
				$gearman_worker->addAbility('gearman_email_friends_new_user');	
				$gearman_worker->addAbility('gearman_send_sms_mass_text_team_announcements');	
				$gearman_worker->addAbility('gearman_email_friends_gl_join');
		//	}
			
			
			
			
		    $gearman_worker->beginWork();
			
		}catch(Net_Gearman_Exception $e){
		   	
		//	var_dump($e); //die();
			echo 'Exception...';
			
		}
	}
}

/* End of file worker.php */
/* Location: ./application/controllers/worker.php */