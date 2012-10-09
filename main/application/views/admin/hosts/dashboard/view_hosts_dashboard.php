<h1><?= $team->team->team_name ?></h1>
<?php //Kint::dump($team); ?>

<div id="tabs" class="tabs ui-tabs ui-widget ui-widget-content ui-corner-all" style="width:1050px;">
	<div class="ui-widget-header">
		<span>Website Statistics</span>
		
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

	<?php foreach($team->team_venues as $key => $venue): ?>

		<div id="tabs-<?= $key ?>" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
			
			<div style="display:none;" class="tv_id"><?= $venue->team_venue_id ?></div>
			
			<h1><?= $venue->team_venue_name ?></h1>
			<img src="<?= $central->s3_uploaded_images_base_url ?>venues/banners/<?= $venue->team_venue_image ?>_t.jpg" alt="<?= $venue->team_venue_name ?>"/>
			
			<hr>
			
			<table class="normal tablesorter fullwidth guest_lists">
				<thead>
					<tr>
						<?php if(false): ?>
						<th style="display:none;">glr_id</th>
						<?php endif; ?>
						
						<th class="header sorttable_alpha">Head User</th>
						<th class="header sorttable_nosort">Image</th>
						<th class="header sorttable_nosort">Host Notes</th>
						<th class="header">Promoter</th>
						<th class="header">Guest List</th>
						<th class="header">Time Requested</th>
						<th class="header sorttable_nosort">Entourage</th>
						<th class="header">Check-In Status</th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
			
		</div>
	<?php endforeach; ?>

</div>

<script type="text/javascript">
jQuery(function(){

	//page initialization stuff...
	jQuery('div#tabs > div.ui-widget-header select.venue_select').bind('change', function(){
		jQuery('div#tabs').tabs('select', parseInt(jQuery(this).val()));
	});
	jQuery('div#tabs > div.ui-widget-header > ul').css('display', 'none');
	
	window.populate_list = function(tab_content, data){
		
		var oauth_uids = [];
		for(var i in data){
			
			if(data[i].reservation_type == 'promoter'){
				
				oauth_uids.push(data[i].pglr_user_oauth_uid);
				
				
				var entourage = [];
				for(var k in data[i].pglre){
					oauth_uids.push(data[i].pglre[k].oauth_uid);
					entourage.push(data[i].pglre[k].oauth_uid);
				}
				data[i].pglre = entourage;
				
				
				
				var dateObj = new Date(data[i].pglr_create_time * 1000);
				var timeString = (dateObj.getMonth()+1) + '/' + (dateObj.getDate()+1) + '/' + (dateObj.getFullYear());				
				data[i].create_time = timeString;
				
				var row_html = new EJS({
					element: jQuery('div#ejs_host_templates > div#reservation_row_promoter > table > tbody').get(0)
				}).render(data[i]);
								
			}else if(data[i].reservation_type == 'team'){
		
				oauth_uids.push(data[i].tglr_user_oauth_uid);
		
				
				var dateObj = new Date(data[i].tglr_create_time * 1000);
				var timeString = (dateObj.getMonth()+1) + '/' + (dateObj.getDate()+1) + '/' + (dateObj.getFullYear());				
				data[i].create_time = timeString;
		
				
				var row_html = new EJS({
					element: jQuery('div#ejs_host_templates > div#reservation_row_team > table > tbody').get(0)
				}).render(data[i]);
		
				
			}
			
			tab_content.find('tbody').append(row_html);
			
		}		
			
		jQuery.fbUserLookup(oauth_uids, 'uid, name, first_name, last_name, pic_square, pic', function(rows){
			
			for(var i in rows){
				jQuery('.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
			
			//	jQuery('.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
				jQuery('span.name_' + rows[i].uid).parents('td').html(rows[i].last_name + ', ' + rows[i].first_name);
				jQuery('span.name_' + rows[i].uid).parents('td').attr('sorttable_customkey', rows[i].last_name);
		
			}
			
		});
			
//		jQuery('tr.reservation').css('display', 'block');
		
	}
	
//	populate_list(jQuery('div#tabs div#tabs-0'), temp_data);


	var venue_gl_retrieve = function(tab_index){
		
		var tab_content = jQuery('div#tabs div#tabs-' + tab_index);
		var tv_id = tab_content.find('div.tv_id').html();
		
		//display a loading indicator
		var loading_indicator = '<tr class="loading"><td colspan="10"><img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." /></td></tr>';
		tab_content.find('table.guest_lists tbody').html(loading_indicator);
		
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		jQuery.ajax({
			url: window.location,
			type: 'post',
			data: {
				ci_csrf_token: cct,
				vc_method: 'venue_gl_retrieve',
				tv_id: tv_id,
			},
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				
				tab_content.find('table.guest_lists tbody').empty();
				
				//populate team_venue guest lists page
				populate_list(tab_content, data.message);
			}
		});
	
	
	};
	
	jQuery('div#tabs').tabs({
		show: function(event, ui){
			
			//clear present data
			
			//retrieve & show fresh data
			venue_gl_retrieve(ui.index);
			
		}
	});
	
	jQuery('span.checkin_group_pglr').live('click', function(){
		
		var tr = jQuery(this).parents('tr');
		var td = jQuery(this).parents('td');
		var pglr_id = jQuery.trim(tr.find('td.pglr_id').html());
		
		td.html('<img src="<?= $central->global_assets . 'images/ajax_round.gif' ?>" alt="loading..." />');
		
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		jQuery.ajax({
			url: window.location,
			type: 'post',
			data: {
				ci_csrf_token: cct,
				vc_method: 'checkin_event',
				type: 'pglr',
				glr_id: pglr_id,
			},
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				
				td.html('<span style="color:green;font-size:28px;">Yes</span>');
				
			}
		});
		return false;
	});
	
	jQuery('span.checkin_group_tglr').live('click', function(){
		
		var tr = jQuery(this).parents('tr');
		var td = jQuery(this).parents('td');
		var tglr_id = jQuery.trim(tr.find('td.tglr_id').html());
		
		td.html('<img src="<?= $central->global_assets . 'images/ajax_round.gif' ?>" alt="loading..." />');
		
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		jQuery.ajax({
			url: window.location,
			type: 'post',
			data: {
				ci_csrf_token: cct,
				vc_method: 'checkin_event',
				type: 'tglr',
				glr_id: tglr_id,
			},
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				
				td.html('<span style="color:green;font-size:28px;">Yes</span>');
				
			}
		});
		return false;
	});
	
	//assume page loads and shows tab 0
	venue_gl_retrieve(0);

	window.EventHandlerObject.addListener("pusher_init", function(){
				
		
		//bind to events
		team_chat_channel.bind('host_notification', function(data){
			
			if(!data.host_notification_type){
				return; //error
			}
			
			//jQuery('#test_box').prop('checked', !jQuery('#test_checkbox').prop('checked')).change();
			
			switch(data.host_notification_type){
				case 'new_reservation':
				
					break;
				case 'reservation_checkin':
				
					break;
				case 'reservation_notes':
				
					break;
				default:
					break; //error
			}
			
		});
		
	});

});
</script>





<div id="ejs_host_templates" style="display:none;">
	
	<div id="reservation_row_promoter">
		<table>
			<tbody>
			<tr>
				
				<td style="display:none" class="pglr_id">
					[%= pglr_id %]
				</td>
				
				<td style="font-size:18px;">
					<span class="name_[%=pglr_user_oauth_uid%]"></span>
				</td>
				
				<td><div class="pic_square_[%=pglr_user_oauth_uid%]"></div></td>
				<td class="host_message">[%= pglr_host_message %]</td>
				<td style="font-size:18px;">[%= u_full_name %]</td>
				<td>[%= pgla_name %]</td>
				<td>[%= create_time %]</td>
				<td>
					-
				</td>
				<td>
					[% if(pglr_checked_in == 1){ %]
						
						<span style="color:green;font-size:28px;">Yes</span>
						
					[% }else{ %]
						
						<span class="checkin_group_pglr" style="color:red;cursor:pointer;text-decoration:underline;font-size:28px;">No</span>
						
					[% } %]
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	
	<div id="reservation_row_team">
		<table>
			<tbody>
			<tr class="reservation reservation_[%=tglr_id%]">
				
				<td style="display:none" class="pglr_id">
					[%= tglr_id %]
				</td>
				
				<td style="font-size:18px;">
					<span class="name_[%=tglr_user_oauth_uid%]"></span>
				</td>
				<td><div class="pic_square_[%=tglr_user_oauth_uid%]"></div></td>
				<td class="host_message">[%=tglr_host_message%]</td>
				<td> - </td>
				<td>[%= tgla_name %]</td>
				<td>[%= tglr_create_time %]</td>
				<td>
					-
				</td>
				<td>
					[% if(tglr_checked_in == 1){ %]
						
						<span style="color:green;font-size:28px;">Yes</span>
						
					[% }else{ %]
						
						<span class="checkin_group_tglr" style="color:red;cursor:pointer;text-decoration:underline;font-size:28px;">No</span>
						
					[% } %]
				</td>
				<td>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	
</div>
