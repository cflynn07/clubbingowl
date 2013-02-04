<?php 
	$page_obj = new stdClass;
	$page_obj->team_venues = $team_venues;
	$page_obj->init_users = $init_users;
	$page_obj->factor = 0.58;
	
	$factor = $page_obj->factor;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>


<div id="admin_managers_tables_wrapper">
	
	<div id="vlf_dialog" style="display:none;">
		<div id="vlf_dialog_floor">
		</div>
	</div>
	
	<h1>Table & Guest List Reservations</h1>
	
	<div id="tabs" style="display:none; margin-bottom:0px; height:auto !important; width:980px;">
		
		
		<div class="ui-widget-header" style="cursor:default;">
			<span></span>
			
			
			
			<div style="display: inline-block; float:right;">
				Select Venue: 
				<select class="venue_select">
					<?php foreach($team_venues as $key => $venue): ?>
						<option data-tv_id="<?= $venue->tv_id ?>" value="<?= $key ?>"><?= $venue->tv_name ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			
			
			
			<ul>
			<?php foreach($team_venues as $key => $venue): ?>
				<li><a href="#tabs-<?= $key ?>"><?= $venue->tv_name ?></a></li>
			<?php endforeach; ?>
			</ul>
			
			
		</div>

		<?php foreach($team_venues as $key => $venue): ?>
		<div class="top_lvl" id="tabs-<?= $key ?>">
			
			<div>
				
				<div class="full_width last">
					
					<div>
						
						<div style="float:left; display:inline-block;">
							<img 	style="border:1px solid #CCC;" src="<?= $central->s3_uploaded_images_base_url . 'venues/banners/' . $venue->tv_image . '_t.jpg' ?>" alt="<?= $venue->tv_name ?>"/>
						</div>
						
						<div style="float:right; display:inline-block; padding-top:5px;">
							<a 		style="margin-right:0;" href="<?= $central->manager_admin_link_base . 'settings_venues_edit_floorplan/' . $venue->tv_id . '/' ?>" class="ajaxify button_link btn-action">Edit Floorplan</a><br/><br/>	
							<a 		href="#" data-action="expand-collapse-all" class="button_link btn-action">Expand/Collapse</a>
						</div>
						
					</div>
					
					<div style="clear:both;"></div>
					
					
					<div data-tv_id="<?= $venue->tv_id ?>" class="tabs_tables tabs_tables_tv_id_<?= $venue->tv_id ?>">
						
						<div class="ui-widget-header">
							
							<span>
								<input type="text" class="table_datepicker" value="<?= date('l F j, Y', time()); ?>" />
								<img style="display:none;" class="loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
							</span>
							
							<ul>
								<li><a href="#tabs-<?= $venue->tv_id ?>-1">Table Reservations</a></li>
								<li><a href="#tabs-<?= $venue->tv_id ?>-2">Guest List & Table Reservations</a></li>
							</ul>
							
						</div>
									
						

						<div id="tabs-<?= $venue->tv_id ?>-1">
							
							<div data-clear-zone="" class="full_width last floorplan_wrapper"></div>
							
							<div class="full_width last table_reservations"></div>
							
						</div>
						
						
						
						
						
						<div id="tabs-<?= $venue->tv_id ?>-2">
														
							<div class="full_width last all_reservations"></div>
							
						</div>
						
						
						
						
						
						
					</div>
					
					<div style="clear:both;"></div>
					
				</div>
				
				<div style="clear:both;"></div>
				
			</div>
			
			
			
			
			<div style="clear:both; width:100%; border-bottom:1px dashed #CCC;"></div>
		
			<div style="margin-top:18px;" class="ui-widget">
				<div class="ui-widget-header">
					<span>All Upcoming Team Reservations</span>
				</div>			
				<div id="all_upcoming_reservations" class="full_width last" style="margin-bottom:40px;"></div>
			</div>
		
			
			
			
		</div>
		<?php endforeach; ?>
		
		<div style="clear:both;"></div>
		
	</div>
	
	
	
	
	
</div>