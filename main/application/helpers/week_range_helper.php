<?php if(! defined('BASEPATH') ) exit('No direct script access allowed');

if(!function_exists('week_range')){
	
	/*
	 * helper function used to determine the start and end dates of this week.
	 * returns array with two indexes, start date and end date.
	 * 
	 * Ex: 2011-08-21, 2011-08-27
	 * (week start and end dates, inclusive)
	 * 
	 * Sunday <---> Saturday == complete week
	 * 
	 * @return	array 
	 * */
	function week_range(){
		$human_start = date('Y-m-d', mktime(1, 0, 0, date('m'), date('d') - date('w'), date('Y')));
		$human_end = date('Y-m-d', mktime(1, 0, 0, date('m'), date('d') + 6 - date('w'), date('Y')));
		
		//have to add +7 epoch_end to get midnight on sunday, otherwise strtotime will give midnight saturday
		$epoch_start = strtotime($human_start);
		$epoch_end = strtotime(date('Y-m-d', mktime(1, 0, 0, date('m'), date('d') + 7 - date('w'), date('Y'))));
		
		return array('human_start' => $human_start, 
						'human_end' => $human_end,
						'epoch_start' => $epoch_start,
						'epoch_end' => $epoch_end);
	}
}

/* End of file week_range_helper.php */
/* Location: ./application/helpers/week_range_helper.php */