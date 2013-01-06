if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_tables = function(){
		
		var globals = window.module.Globals.prototype;
		var datepicker;
		
		jQuery('div#tabs').tabs({}).css('display', 'block').resizable();
		jQuery('div#tabs div.tabs_tables').tabs();
				
		jQuery('div#tabs > div.ui-widget-header select.venue_select').bind('change', function(){
			
			jQuery('div#tabs').tabs('select', parseInt(jQuery(this).val()));	
						
			if(!window.page_obj.team_venues)
				return;

			var selected_tv_id = jQuery('select.venue_select option[value=' + jQuery(this).val() + ']').attr('data-tv_id');
			
			jQuery('input.table_datepicker').each(function(){
				if(jQuery(this).hasClass('hasDatepicker'))
					jQuery(this).datepicker('destroy');	
			});
				
			jQuery('div[data-clear-zone]').empty();
		
			
		
		
		
		
		
			var tv_display_module;
			for(var i in window.page_obj.team_venues){
				
				var venue 				= window.page_obj.team_venues[i];
				if(venue.tv_id != selected_tv_id)
					continue;
				
				tv_display_module 	= jQuery.extend(true, {}, globals.module_tables_display);
				tv_display_module
					.initialize({
						display_target: 	'#tabs-' + venue.tv_id + '-0',
						team_venue: 		venue,
						factor: 			0.5,
						options: {
							display_slider: true
						}
					});
								
				break;
	
			}
			
			
			
			
			
			datepicker = jQuery('div#tabs div[data-tv_id=' + selected_tv_id + '] input.table_datepicker').datepicker({
				dateFormat: 'DD MM d, yy',
				maxDate: '+6d',
				minDate: '-3y',
				defaultDate: new Date(),
				onSelect: function(dateText, inst){
										
					var iso_date = jQuery.datepicker.formatDate('yy-mm-dd', jQuery(this).datepicker('getDate'));
					tv_display_module.manual_date(iso_date);
					
					tv_display_module.refresh_table_layout(selected_tv_id, iso_date);
					jQuery('#displayed_layout_date').html(jQuery(this).val());
					
		       }
			});
			datepicker.datepicker('setDate', '0 days');			
				
				
				
				
				
					
		}).trigger('change');
		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			jQuery('div[data-clear-zone]').empty();
			
			jQuery('input.table_datepicker').each(function(){
				if(jQuery(this).hasClass('hasDatepicker'))
					jQuery(this).datepicker('destroy');	
			});
			
			jQuery('div#tabs > div.ui-widget-header select.venue_select').unbind('change');

		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		return;
		
		
		
		
		
		
		
		
		
		
		
		if(window.page_obj.team_venues)
			for(var i in window.page_obj.team_venues){
		
				var venue 				= window.page_obj.team_venues[i];
				var tv_display_module 	= jQuery.extend(true, {}, globals.module_tables_display);
				
				tv_display_module
					.initialize({
						display_target: 	'#tabs-' + venue.tv_id + '-0',
						team_venue: 		venue,
						factor: 			0.5,
						options: {
							display_slider: true
						}
					});
					

				
				break;
	
			}
		

		jQuery('div#tabs input.table_datepicker').datepicker({
			dateFormat: 'DD MM d, yy',
			maxDate: '+6d',
			minDate: '-3y',
			onSelect: function(dateText, inst){
									
				var iso_date = jQuery.datepicker.formatDate('yy-mm-dd', jQuery(this).datepicker('getDate'));

				for(var i in update_modules){
					
					
					
					var m = update_modules[i];
								
					m.module.refresh_table_layout(m.tv_id, iso_date);
				}
	                  
	       }
		});








				
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			

		}

		return;

















	
		var unbind_callbacks = [];		
					
		jQuery('.tabs').tabs();
		jQuery('img.tooltip').tooltip();



		jQuery('div#tabs').tabs().css('display', 'block');
		jQuery('div#tabs div.tabs_tables').tabs();
		
		jQuery('div#tabs > div.ui-widget-header select.venue_select').bind('change', function(){
			jQuery('div#tabs').tabs('select', parseInt(jQuery(this).val()));
		});
		
	
		fbEnsureInit(function(){
			
			//var users = <?= json_encode($init_users) ?>;
			var users = window.page_obj.init_users;
			
			if(users.length == 0)
				return;
			
			var fql = "SELECT uid, name, pic_square, pic_big, third_party_id FROM user WHERE ";
			for(var i = 0; i < users.length; i++){
				if(i == (users.length - 1)){
					fql += "uid = " + users[i];
				}else{
					fql += "uid = " + users[i] + " OR ";
				}
			}
			
			var query = FB.Data.query(fql);
			query.wait(function(rows){
						
				//populate divs with FB data
				for(var i = 0; i < rows.length; i++){
					
					jQuery('.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
					jQuery('.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
					
				}
												
			});
				
		});
		
		zebraRows();
	
		
		//--------- data change function -----------
		var data_update_function = function(data, tv_id, init_users, tabs_tables){
			
			if(data.length == 0){
				alert('Unknown error');
				return;
			}
			
			for (first in data) break; //gets first element
			data = data[first];
					
			//update venue layout and reserved tables
			var tv_tab = jQuery('div.tabs_tables_tv_id_' + tv_id);
			var tv_tab_venue_layout = tv_tab.find('div.vl');
	
			tv_tab_venue_layout.empty();
			
			//construct new floorplan... --------------------------------------------------------------------------------------------------------------------------------------
					
			//construct floorplan
			for (var i in data.venue_floorplan){
				
				var table_counter = 0;
				var vlf = '<div class="vlf">';
				
				vlf += '<div class="vlf_title">' + data.venue_floorplan[i].name + '</div>';
				vlf += '<div style="display:none;" class="vlf_id">' + i + '</div>';
				
				for(var j = 0; j < data.venue_floorplan[i].items.length; j++){
					vlf += '<div class="item ' + data.venue_floorplan[i].items[j].vlfi_item_type + '" style="top:' + Math.ceil(data.venue_floorplan[i].items[j].vlfi_pos_y * window.page_obj.factor) + 'px; left:' + Math.ceil(data.venue_floorplan[i].items[j].vlfi_pos_x * window.page_obj.factor) + 'px; width:' + Math.ceil(data.venue_floorplan[i].items[j].vlfi_width * window.page_obj.factor) + 'px; height:' + Math.ceil(data.venue_floorplan[i].items[j].vlfi_height * window.page_obj.factor) + 'px;">';
	
					vlf += '<span class="title">';
					
					vlf += '<div class="vlfi_id" style="display:none;">' + data.venue_floorplan[i].items[j].vlfi_id + '</div>';
					vlf += '<div class="vlfi_id_' + data.venue_floorplan[i].items[j].vlfi_id + '" style="display:none;">' + data.venue_floorplan[i].items[j].vlfi_id + '</div>';
					vlf += '<div class="pos_x" style="display:none;">' + data.venue_floorplan[i].items[j].vlfi_pos_x + '</div>';
					vlf += '<div class="pos_y" style="display:none;">' + data.venue_floorplan[i].items[j].vlfi_pos_y + '</div>';
					vlf += '<div class="width" style="display:none;">' + data.venue_floorplan[i].items[j].vlfi_width + '</div>';
					vlf += '<div class="height" style="display:none;">' + data.venue_floorplan[i].items[j].vlfi_height + '</div>';
					vlf += '<div class="itmCls" style="display:none;">' + data.venue_floorplan[i].items[j].vlfi_item_type + '</div>';
					
	
					switch(data.venue_floorplan[i].items[j].vlfi_item_type){
						case 'table':
							vlf += 'T-' + table_counter;
							table_counter++;
							break;
						case 'bar':
							vlf += '(B)';
							break;
						case 'stage':
							vlf += '(S)';
							break;
						case 'dancefloor':
							vlf += '(D)';
							break;
						case 'djbooth':
							vlf += '(DJ)';
							break;
						case 'bathroom':
							vlf += '(Br)';
							break;
						case 'stairs':
							vlf += '(St)';
							break;
					}
				
					vlf += '</span>';	
					
					if(data.venue_floorplan[i].items[j].vlfi_item_type == 'table'){
						vlf += '<div class="max_capacity">' + data.venue_floorplan[i].items[j].vlfit_capacity + '</div>';
						vlf += '<div class="vlfit_id">' + data.venue_floorplan[i].items[j].vlfit_id + '</div>';
						vlf += '<div style="display:none;" class="vlfit_id_' + data.venue_floorplan[i].items[j].vlfit_id + '">' + data.venue_floorplan[i].items[j].vlfit_id + '</div>';
					}
									
					vlf += '</div>';
				}
				vlf += '</div>';
							
				tv_tab_venue_layout.append(vlf);
				
			}
			
			//indicate reserved tables
			//TODO
			for(var y = 0; y < data.venue_reservations.length; y++){
				
				if(!data.venue_reservations[y].vlfit_id)
					continue;
				
				var table =  tv_tab_venue_layout.find('div.vlf div.vlfit_id_' + data.venue_reservations[y].vlfit_id).parents('div.table');
				table.addClass('reserved');
				
			}
			
			//construct new floorplan... --------------------------------------------------------------------------------------------------------------------------------------
			//add table reservations and all reservations... --------------------------------------------------------------------------------------------------------------------------------------
			
			var table_reservations = tv_tab_venue_layout.parents('div.top_lvl').find('div.table_reservations');
			var all_reservations = tv_tab_venue_layout.parents('div.top_lvl').find('div.all_reservations');
			
			table_reservations.find('table tbody tr').remove();
			all_reservations.find('table tbody tr').remove();
			
			var tr_html = '';	
			for(var n = 0; n < data.venue_reservations.length; n++){
				
				var reservation = data.venue_reservations[n];
							
				//add to all ----------------------------------------
				
				tr_html += '<tr style="display:none;">';
				tr_html += '	<td class="head_user_name"><span class="name_' + ((reservation.tglr_user_oauth_uid) ? reservation.tglr_user_oauth_uid : reservation.pglr_user_oauth_uid) + '"></span></td>';
				tr_html += '	<td class="head_user_picture"><div class="pic_square_' + ((reservation.tglr_user_oauth_uid) ? reservation.tglr_user_oauth_uid : reservation.pglr_user_oauth_uid) + '"></div></td>';
				tr_html += '	<td class="promoter">' + ((reservation.up_users_oauth_uid) ? '<img src="' + window.module.Globals.prototype.s3_uploaded_images_base_url + 'profile-pics/' + reservation.up_profile_image + '_t.jpg"><br><span class="name_' + reservation.up_users_oauth_uid + '"></span>' : ' - ') + '</td>';
				tr_html += '	<td class="guest_list">' + ((reservation.tgla_name) ? reservation.tgla_name : reservation.pgla_name) + '</td>';
				tr_html += '	<td class="user_messages">';
				tr_html += '		<table style="width:152px; text-wrap: unrestricted;" class="user_messages">';
				tr_html += '			<tbody>';
				tr_html += '				<tr><td class="message_header">Request Message:</td></tr>';
				tr_html += '				<tr>';
				tr_html += '					<td class="request_msg">' + ((typeof reservation.tglr_request_msg != 'undefined') ? ((reservation.tglr_request_msg.length > 0) ? reservation.tglr_request_msg : ' - ') : ((reservation.pglr_request_msg.length > 0) ? reservation.pglr_request_msg : ' - ')) + '</td>';
				tr_html += '				</tr>';
				tr_html += '				<tr><td class="message_header">Response Message:</td></tr>';
				tr_html += '				<tr>';
				tr_html += '					<td class="response_msg">' + ((typeof reservation.tglr_response_msg != 'undefined') ? ((reservation.tglr_response_msg.length > 0) ? reservation.tglr_response_msg : ' - ') : ((reservation.pglr_response_msg.length > 0) ? reservation.pglr_response_msg : ' - ')) + '</td>';
				tr_html += '				</tr>';
				tr_html += '				<tr><td class="message_header">Host Notes:</td></tr>';
				tr_html += '				<tr style="max-width:122px;">';
				tr_html += '					<td style="max-width:122px;" class="host_notes">' + ((typeof reservation.tglr_host_message != 'undefined') ? ((reservation.tglr_host_message.length > 0) ? reservation.tglr_host_message : ' - ') : ((reservation.pglr_host_message.length > 0) ? reservation.pglr_host_message : ' - ')) + '</td>';
				tr_html += '				</tr>';
				tr_html += '			</tbody>';
				tr_html += '		</table>';
				tr_html += '	</td>';
				tr_html += '	<td class="entourage">';
				
				if(reservation.entourage.length == 0){
					
					tr_html += '		<p>No Entourage</p>';
								
				}else{
					
					tr_html += '		<table class="entourage">';
					tr_html += '			<thead>';
					tr_html += '				<tr>';
					tr_html += '					<th>Name</th>';
					tr_html += '					<th>Picture</th>';
					tr_html += '				</tr>';
					tr_html += '			</thead>';
					tr_html += '			<tbody>';
	
					for(var j=0; j < reservation.entourage.length; j++){
						tr_html += '			<tr>';
						tr_html += '				<td><span class="name_' + reservation.entourage[j] + '"></span></td>';
						tr_html += '				<td><div class="pic_square_' + reservation.entourage[j] + '"></div></td>';
						tr_html += '			</tr>';
					}
					
					tr_html += '			</tbody>';
					tr_html += '		</table>';
					
				}
				
				tr_html += '	</td>';
				tr_html += '</tr>';
						
			}
			
			all_reservations.find('table tbody').append(tr_html);
			
			var tr_html = '';	
			for(var n = 0; n < data.venue_reservations.length; n++){
				
				var reservation = data.venue_reservations[n];
				
				if(!reservation.vlfit_id)
					continue;
							
				//add to all ----------------------------------------
				
				tr_html += '<tr style="display:none;">';
				tr_html += '	<td class="head_user_name"><span class="name_' + ((reservation.tglr_user_oauth_uid) ? reservation.tglr_user_oauth_uid : reservation.pglr_user_oauth_uid) + '"></span></td>';
				tr_html += '	<td class="head_user_picture"><div class="pic_square_' + ((reservation.tglr_user_oauth_uid) ? reservation.tglr_user_oauth_uid : reservation.pglr_user_oauth_uid) + '"></div></td>';
				tr_html += '	<td class="promoter">' + ((reservation.up_users_oauth_uid) ? '<img src="' + window.module.Globals.prototype.s3_uploaded_images_base_url + 'profile-pics/' + reservation.up_profile_image + '_t.jpg"><br><span class="name_' + reservation.up_users_oauth_uid + '"></span>' : ' - ') + '</td>';
				tr_html += '	<td class="guest_list">' + ((reservation.tgla_name) ? reservation.tgla_name : reservation.pgla_name) + '</td>';
				tr_html += '	<td class="user_messages">';
				tr_html += '		<table style="width:152px; text-wrap: unrestricted;" class="user_messages">';
				tr_html += '			<tbody>';
				tr_html += '				<tr><td class="message_header">Request Message:</td></tr>';
				tr_html += '				<tr>';
				tr_html += '					<td class="request_msg">' + ((typeof reservation.tglr_request_msg != 'undefined') ? ((reservation.tglr_request_msg.length > 0) ? reservation.tglr_request_msg : ' - ') : ((reservation.pglr_request_msg.length > 0) ? reservation.pglr_request_msg : ' - ')) + '</td>';
				tr_html += '				</tr>';
				tr_html += '				<tr><td class="message_header">Response Message:</td></tr>';
				tr_html += '				<tr>';
				tr_html += '					<td class="response_msg">' + ((typeof reservation.tglr_response_msg != 'undefined') ? ((reservation.tglr_response_msg.length > 0) ? reservation.tglr_response_msg : ' - ') : ((reservation.pglr_response_msg.length > 0) ? reservation.pglr_response_msg : ' - ')) + '</td>';
				tr_html += '				</tr>';
				tr_html += '				<tr><td class="message_header">Host Notes:</td></tr>';
				tr_html += '				<tr style="max-width:122px;">';
				tr_html += '					<td style="max-width:122px;" class="host_notes">' + ((typeof reservation.tglr_host_message != 'undefined') ? ((reservation.tglr_host_message.length > 0) ? reservation.tglr_host_message : ' - ') : ((reservation.pglr_host_message.length > 0) ? reservation.pglr_host_message : ' - ')) + '</td>';
				tr_html += '				</tr>';
				tr_html += '			</tbody>';
				tr_html += '		</table>';
				tr_html += '	</td>';
				
				tr_html += '	<td class="min_spend">$500</td>';
				tr_html += '	<td class="phone_number">1-(774)-573-4580</td>';
				
				tr_html += '	<td class="entourage">';
				
				if(reservation.entourage.length == 0){
					
					tr_html += '		<p>No Entourage</p>';
								
				}else{
					
					tr_html += '		<table class="entourage">';
					tr_html += '			<thead>';
					tr_html += '				<tr>';
					tr_html += '					<th>Name</th>';
					tr_html += '					<th>Picture</th>';
					tr_html += '				</tr>';
					tr_html += '			</thead>';
					tr_html += '			<tbody>';
	
					for(var j=0; j < reservation.entourage.length; j++){
						tr_html += '			<tr>';
						tr_html += '				<td><span class="name_' + reservation.entourage[j] + '"></span></td>';
						tr_html += '				<td><div class="pic_square_' + reservation.entourage[j] + '"></div></td>';
						tr_html += '			</tr>';
					}
					
					tr_html += '			</tbody>';
					tr_html += '		</table>';
					
				}
				
				tr_html += '	</td>';
				tr_html += '<td class="table"><div style="width:100px;height:100px;background:#000;"></div></td>';
				tr_html += '</tr>';
						
			}
			
			table_reservations.find('table tbody').append(tr_html);
			//add table reservations and all reservations... --------------------------------------------------------------------------------------------------------------------------------------
			
			
			
			var users = init_users;
			fbEnsureInit(function(){
						
				if(users.length == 0){
					tabs_tables.find('img.loading_indicator').css('display', 'none');
					all_reservations.find('table tbody tr').css('display', 'table-row');
					return;
				}
							
				var fql = "SELECT uid, name, pic_square, pic_big, third_party_id FROM user WHERE ";
				for(var i = 0; i < users.length; i++){
					if(i == (users.length - 1)){
						fql += "uid = " + users[i];
					}else{
						fql += "uid = " + users[i] + " OR ";
					}
				}
				
				var query = FB.Data.query(fql);
				query.wait(function(rows){
							
					//populate divs with FB data
					for(var i = 0; i < rows.length; i++){
						
						jQuery('.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
						jQuery('.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
						
					}
												
					tabs_tables.find('img.loading_indicator').css('display', 'none');
					all_reservations.find('table tbody tr').css('display', 'table-row');
					table_reservations.find('table tbody tr').css('display', 'table-row');
										
				});
							
			});
			
			window.zebraRows();
					
		};
		
		jQuery('div#tabs input.table_datepicker').datepicker({
			dateFormat: 'DD MM d, yy',
			maxDate: '+6d',
			minDate: '-3y',
			onSelect: function(dateText, inst){
				
				var tabs_tables = jQuery(this).parents('div.tabs_tables');
				tabs_tables.find('img.loading_indicator').css('display', 'inline-block');
				var tv_id = tabs_tables.find('div.tv_id').html();
				            
	            var dateObj = {
	            	currentYear: inst.selectedYear,
	            	currentMonth: (inst.selectedMonth + 1),
	            	currentDay: inst.selectedDay
	            }
	            
	            //cross-site request forgery token, accessed from session cookie
				//requires jQuery cookie plugin
				var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
				
				jQuery.ajax({
					url: window.location,
					type: 'post',
					data: {
						ci_csrf_token: cct,
						vc_method: 'date_reservations',
						dateObj: dateObj,
						tv_id: tv_id
					},
					cache: false,
					dataType: 'json',
					success: function(data, textStatus, jqXHR){
						
						if(data.success){
							
							data_update_function(data.message, tv_id, data.init_users, tabs_tables);
													
						}else{
							
							tabs_tables.find('img.loading_indicator').css('display', 'none');
							alert('An unknown error has occured, please contact support if this continues to happen.');
						
						}
						
					},
					failure: function(){
						
						tabs_tables.find('img.loading_indicator').css('display', 'none');
						alert('An unknown error has occured, please contact support if this continues to happen.');
						
					}
				});
	            
	                  
	       }
		});
		
		jQuery('div#tabs div.vlf').live('click', function(){
			
			var vlf_floor = jQuery('div#vlf_dialog div#vlf_dialog_floor');
			vlf_floor.empty();
			
			var vlf_id = jQuery(this).find('div.vlf_id').html();
			jQuery(this).find('div.item').each(function(){
				
				var reserved = '';
				if(jQuery(this).hasClass('reserved'))
					reserved = ' reserved';
				
				var vlfi_id = jQuery(this).find('div.vlfi_id').html();
				var x = jQuery(this).find('div.pos_x').html();
				var y = jQuery(this).find('div.pos_y').html();
				var width = jQuery(this).find('div.width').html();
				var height = jQuery(this).find('div.height').html();
				var itmCls = jQuery(this).find('div.itmCls').html();
				var title = jQuery(this).find('span.title').html();
				
				var html 	=  '<div class="item ' + itmCls + reserved + '" style="top:' + y + 'px; left:' + x + 'px; width:' + width + 'px; height:' + height + 'px;">';
				html 		+= '	<span class="title">' + title + '</span>';
				html 		+= ' 	<div class="vlfi_id">' + vlfi_id + '</div>';
				html 		+= '</div>';
				vlf_floor.append(html);
				
			});
			
			jQuery('div#vlf_dialog').dialog({
				height: 700,
				width: 900,
				modal: true,
				resizable: false,
				draggable: false,
				position: ['center', 'center'],
				title: 'Loading...'
			});
		});
	
	
	












		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});