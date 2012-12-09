<div class="tabs ui-tabs ui-widget ui-widget-content ui-corner-all" style="width:1050px;">
	<div class="ui-widget-header">
		<span>Guest Lists</span>
		
		<ul style="" class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<?php foreach($team->team_venues as $key => $venue): ?>
				<?php
				
				
				
					//find total count of guest list reservations for all guest lists at venue
					$res_count = 0;
				//	foreach($venue->tv_gla as $gla){
					//	($gla->current_list) ? $res_count += count($gla->current_list->groups) : 0;
				//	}
				
				
				
				?>
				<li class="ui-state-default ui-corner-top"><a href="#tabs-<?= $key ?>"><?= $venue->team_venue_name ?> (<span class="team_gl_groups_count"><?= $res_count ?></span>)</a></li>
			<?php endforeach; ?>
		</ul>
		
		<select style="float:right;" class="venue_select">
			<?php foreach($team->team_venues as $key => $venue): ?>
				<?php
					//find total count of guest list reservations for all guest lists at venue
					$res_count = 0;
				//	foreach($venue->tv_gla as $gla){
					//	($gla->current_list) ? $res_count += count($gla->current_list->groups) : 0;
				//	}
				?>
				<option value="<?= $key ?>"><?= $venue->team_venue_name ?> (<span class="team_gl_groups_count"><?= $res_count ?></span>)</option>
			<?php endforeach; ?>
		</select>
		<span style="float:right;">Select Venue: </span>
		
	</div>
	
</div>