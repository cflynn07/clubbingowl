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
	
	// Test some basic printing with Colors class
	//	echo $colors->getColoredString("Testing Colors class, this is purple string on yellow background.", 	"purple", 	"yellow") 		. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is blue string on light gray background.", 	"blue", 	"light_gray") 	. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is red string on black background.", 		"red", 		"black") 		. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is cyan string on green background.", 		"cyan", 	"green") 		. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is cyan string on default background.", 		"cyan") 					. "\n";
	//	echo $colors->getColoredString("Testing Colors class, this is default string on cyan background.", 		null, 		"cyan") 		. "\n";
	
	
	
	
	
	
	
	
	
	public function mandrill_test(){
		
		$this->load->library('library_bulk_email', '', 'library_bulk_email');
		
		$test_users = array(
			'Casey Flynn' => 'casey_flynn@cobarsystems.com',
			'Casey Flynn1' => 'casey_flynn@clubbingowl.com',
			'Casey Flynn2' => 'casey_flynn@cobarsystems.com',
			'Casey Flynn3' => 'casey_flynn@clubbingowl.com'
		);
		
				
		foreach($test_users as $key => $email){
			
			$this->library_bulk_email->add_queue(array(
				'html'		=> '<p>This is a test</p>',
				'text'		=> 'This is a test',
				'subject'	=> 'Email test from ClubbingOwl',
				'to_email'	=> $email, 
				'to_name'	=> $key,
			));
						
		}
		
		$this->library_bulk_email->flush_queue();
		
		echo 'complete' . PHP_EOL;			
		
	}
	
	
	
	
	
	
	
	
	
	public function run_billing(){
		
		
		$previous_month			= date('n', strtotime('-1 month'));
		$previous_month_year	= date('Y', strtotime('-1 month'));
		
		
	
		$this->db->select('*')
			->from('teams');
		$query = $this->db->get();
		$all_teams = $query->result();
		
	
		// Create new Colors class
		$colors = new Colors();
	 	$handle = fopen ("php://stdin","r");
	 	
		
	 	echo $colors->getColoredString("Hello, welcome to the ClubbingOwl billing experience. My name is Karl, I will help you bill your clients today.", 										'green', null) . PHP_EOL;
	 	echo $colors->getColoredString("We will start by billing all of the clients for the previous month of " . date('F', strtotime('-1 month')) . ' that have not already been invoiced, one at a time.', 	'purple', null) . PHP_EOL . PHP_EOL;
	 	
		
		echo $colors->getColoredString("You have " . count($all_teams) . " clients.", 	'brown', null) . PHP_EOL;
		echo $colors->getColoredString("First up:", 									'brown', null) . PHP_EOL;

		foreach($all_teams as $team){
			
			
			//see if this team was invoiced already for the previous month
			$this->db->select('*')
				->from('teams_invoices')
				->where(array(
					'invoice_month'		=> $previous_month,
					'invoice_year'		=> $previous_month_year,
					'team_fan_page_id'	=> $team->fan_page_id
				));
			$query = $this->db->get();
			$result = $query->row();
			
			
			echo $colors->getColoredString($team->name, 														'white', null) 	. PHP_EOL;
			echo $colors->getColoredString("\t" . "DATE BEGIN BILLING:" . "\t" . $team->date_begin_billing, 	'lightred', null) . PHP_EOL;
			
			
			
			if($result){
				echo $colors->getColoredString("\t" . "This team has already been invoiced for " . date('F', strtotime('-1 month')) . " ... skipping", 	'light_cyan', null) . PHP_EOL;
			}else{						
				$line 	= strtolower(fgets($handle));
				
				
				
				
				
				$month_days = cal_days_in_month(CAL_GREGORIAN, $previous_month, $previous_month_year);
				
				
				
				
			}
			
			
			
		}
	
		echo $colors->getColoredString("And that completes our billing cycle. Have a nice day.", 'green', null) . PHP_EOL;
				
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
	
	
	public function cron_all(){
		
		$filename = '/home/dotcloud/current/cronjobs.txt';
		
		//set up the crontab
		$fh = fopen($filename, 'w+');
		
		$txt = '';
		$txt .= 'TZ=America/New_York' . PHP_EOL;
		$txt .= '*/1 * * * * date > /home/dotcloud/current/date1.txt' . PHP_EOL;
		$txt .= 'TZ=America/Los_Angeles' . PHP_EOL;
		$txt .= '*/1 * * * * date > /home/dotcloud/current/date2.txt' . PHP_EOL;
		
		fwrite($fh, $txt);
		fclose($fh);
		
		exec('crontab ' . $filename);
		$this->all();
		
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
			
			
			$gearman_worker->addAbility('gearman_confirmation_email_team');	
			$gearman_worker->addAbility('gearman_confirmation_email_promoter');	
			
			
			//finds reviews of a user's friends for a given promoter
			$gearman_worker->addAbility('gearman_individual_promoter_friend_reviews');	
					
					
			
		//	if($arg1 == 'special_emails'){
				$gearman_worker->addAbility('gearman_email_friends_new_user');	
				$gearman_worker->addAbility('gearman_send_sms_mass_text_team_announcements');	
		//		$gearman_worker->addAbility('gearman_email_friends_gl_join');
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