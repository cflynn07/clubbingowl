<?php
	$page_obj = new stdClass;
	$page_obj->team_trailing_gl_requests_keys 	= array_keys($team_trailing_gl_requests);
	$page_obj->team_trailing_gl_requests_values = array_values($team_trailing_gl_requests);
	
	
	$page_obj->team_venues = $team_venues;
	
	
	
	
	// ----------------------------------------- CHART 1 -----------------------------------------
	//generate series data
	$team_series_obj = new stdClass;
	$team_series_obj->type = 'column';
	$team_series_obj->name = 'Team';
	$team_series_obj->data = $page_obj->team_trailing_gl_requests_values;
	
	$series_array[] = $team_series_obj;
	
	foreach($promoters as $pro){
		
		$promoter_series_obj = new stdClass;
		$promoter_series_obj->type = 'column';
		$promoter_series_obj->name = $pro->u_full_name;
		$promoter_series_obj->data = array_values($pro->statistics->trailing_weekly_guest_list_reservation_requests);
		$series_array[] = $promoter_series_obj;
		
	}
	
	//attach average
	$average = array();
	foreach(array_values($team_trailing_gl_requests) as $key => $week){
		$average[$key] = $week;
	}
	foreach($promoters as $pro){
		foreach(array_values($pro->statistics->trailing_weekly_guest_list_reservation_requests) as $key2 => $week2){
			$average[$key2] += $week2;
		}
	}
	foreach($average as &$value){
		$value = $value / (count($promoters) + 1); //+1 for the TEAM
	}unset($value);
	
	$average_series_obj = new stdClass;
	$average_series_obj->type = 'spline';
	$average_series_obj->name = 'Average';
	$average_series_obj->data = $average;
	$series_array[] = $average_series_obj;
	
	
	//pie object
	$team_sum = 0;
	foreach($team_trailing_gl_requests as $val){
		$team_sum += $val;
	}
	
	$promoters_sum = array();
	foreach($promoters as $pro){
		$pro_sum = 0;
		foreach($pro->statistics->trailing_weekly_guest_list_reservation_requests as $week2){
			$pro_sum += $week2;
		}
		$promoters_sum[$pro->u_full_name] = $pro_sum;
	}
	
	$pie_series_obj = new stdClass;
	$pie_series_obj->type = 'pie';
	$pie_series_obj->name = 'Total Reservations';
	$pie_series_obj->data = array();
	
	$team_pie_obj = new stdClass;
	$team_pie_obj->name = 'Team';
	$team_pie_obj->y = $team_sum;
	$team_pie_obj->center = array(100, 80);
	$team_pie_obj->size = 100;
	$team_pie_obj->showInLegend = false;
	
	$temp = new stdClass;
	$temp->enabled = false;
	$team_pie_obj->dataLabels = $temp;
	
	$pie_series_obj->data[] = $team_pie_obj;
	
	foreach($promoters_sum as $key => $count){
		$pie_obj = new stdClass;
		$pie_obj->name = $key;
		$pie_obj->y = $count;
		$pie_series_obj->data[] = $pie_obj;
	}
	
	$page_obj->series_array = $series_array;
	
	// ----------------------------------------- END CHART 1 -----------------------------------------
	
	
	
	
	
	
	
	
	
	
	
	/*
	
	
	
	
	// ----------------------------------------- CHART 2 -----------------------------------------
	$attended_time = array();
	$did_not_attend_time = array();
	foreach(array_values($team_trailing_gl_requests_percentage_attendance) as $key => $value){
		$attended_time[$key] = 0;
		$did_not_attend_time[$key] = 0;
	}
	
	foreach(array_values($team_trailing_gl_requests_percentage_attendance) as $key => $value){
		$attended_time[$key] += intval($value->attended);
		$did_not_attend_time[$key] += intval($value->did_not_attend);
	}
	
	foreach($promoters as $pro){
		
		foreach(array_values($pro->statistics->trailing_weekly_guest_list_reservation_requests_attendance) as $key => $value){
			
			$attended_time[$key] += intval($value->attended);
			$did_not_attend_time[$key] += intval($value->did_not_attend);
			
		}
		
	}
	
	$page_obj->did_not_attend_time = $did_not_attend_time;
	$page_obj->attended_time = $attended_time;
	
	// ----------------------------------------- END CHART 2 -----------------------------------------
	
	
	// ----------------------------------------- CHART 3 -----------------------------------------
	
	$cats = array();
	$cats[] = 'Team';
	foreach($promoters as $pro){
		$cats[] = $pro->u_full_name;
	}
	$page_obj->cats = $cats;
	
	
	$team_attended = 0;
	$team_did_not_attend = 0;
	foreach($team_trailing_gl_requests_percentage_attendance as $key => $value){
		$team_attended += $value->attended;
		$team_did_not_attend += $value->did_not_attend;
	}
	
	
	$attended_array = array();
	$attended_array[] = $team_attended;
	
	$did_not_attend_array = array();
	$did_not_attend_array[] = $team_did_not_attend;
	
	foreach($promoters as $pro){
		
		$pro_attended = 0;
		$pro_did_not_attend = 0;
		foreach($pro->statistics->trailing_weekly_guest_list_reservation_requests_attendance as $key => $value){
			
			$pro_attended += $value->attended;
			$pro_did_not_attend += $value->did_not_attend;
			
		}
		
		$attended_array[] = $pro_attended;
		$did_not_attend_array[] = $pro_did_not_attend;
		
	}
	
	$page_obj->did_not_attend_array = $did_not_attend_array;
	$page_obj->attended_array = $attended_array;
	
	// ----------------------------------------- END CHART 3 -----------------------------------------
	*/
	
	
	
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>

<h1>Guest List Reports</h1>


<div id="reports_wrapper" class="full_width last tabs" style="width:980px; display:none;">
	
	  <div class="ui-widget-header">
		<span>Reports</span>
		<ul>
			<li><a href="#tabs-1">Check In</a></li>
			<li><a href="#tabs-2">Reservations</a></li>
		</ul>
	  </div>


	  <div id="tabs-1">
	  	<?= $this->load->view('admin/managers/reports/view_manager_reports_check_in', '', true); ?>
	  </div>
	  <div id="tabs-2">
	  	<div id="combo_chart_guest_lists" class="ui-widget-content"></div>
	  </div>
	  
	  
</div>












































			<?php if(false): ?>
			
			<div class="full_width last" style="width:980px;">
				
				<h3>
					Weekly Guest List Booking
					<img class="info_icon tooltip" title="Breakdown of which promoters or team-guest-lists are driving client reservations." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
				</h3>
				
				<div class="ui-widget full_width last">
					
					<div class="ui-widget-header">
						<span>Weekly Guest List Booking</span>
					</div>
					
						
					</div>
					
				</div>
				
				<?php if(false): ?>
				
				
				
				<hr>
				<br>
				
				<h3>
					Guest List Attendance Percentage by Time
					<img class="info_icon tooltip" title="Percentages of booked clients that actually show up." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
				</h3>
				
				
				<div class="ui-widget full_width last">
					
					<div class="ui-widget-header">
						<span>Guest List Attendance Percentage</span>
					</div>
					
					<div id="gl_attendance_percentage_time" class="ui-widget-content">
						
					</div>
					
				</div>
				
				<div style="clear:both;"></div>
				
				<hr>
				<br>
				
				<h3>
					Guest List Attendance Percentage by Promoters & Team
					<img class="info_icon tooltip" title="Percentages of booked clients that actually show up." src="<?= $central->admin_assets . 'images/icons/small_icons_2/Info.png'?>" alt="info" />
				</h3>
				
				
				<div class="ui-widget full_width last">
					
					<div class="ui-widget-header">
						<span>Guest List Attendance Percentage</span>
					</div>
					
					<div id="gl_attendance_percentage_promoter" class="ui-widget-content">
						
					</div>
					
				</div>
				
				
				<?php endif; ?>
				
				<div style="clear:both;"></div>
			</div>
			<?php endif; ?>

