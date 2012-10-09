if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_settings_venues_edit = function(){
						
		var unbind_callbacks = [];		
				
					
					
					
					
					
					
				
				
				
				
				
	//	venue_guest_lists = <?= json_encode($tv->tgla) ?>; //jQuery.cookies.get('venue_guest_lists') || [];
	
		var venue_guest_lists = window.page_obj.tv.tgla;
	
		// -----------------------------------------
		//quick conversion function for compatability...
		for(var i in venue_guest_lists){
			
			switch(venue_guest_lists[i].tgla_day){
				case 'mondays':
					venue_guest_lists[i].tgla_day = '0';
					break;
				case 'tuesdays':
					venue_guest_lists[i].tgla_day = '1';
					break;
				case 'wednesdays':
					venue_guest_lists[i].tgla_day = '2';
					break;
				case 'thursdays':
					venue_guest_lists[i].tgla_day = '3';
					break;
				case 'fridays':
					venue_guest_lists[i].tgla_day = '4';
					break;
				case 'saturdays':
					venue_guest_lists[i].tgla_day = '5';
					break;
				case 'sundays':
					venue_guest_lists[i].tgla_day = '6';
					break;
			}
			
		}
		// -----------------------------------------
		
		var build_guest_lists_table = function(){
			
			var vgl = jQuery('table#venue_guest_lists');
			
			var table_html = '';
			for(i in venue_guest_lists){
				
				var weekday;
				switch(venue_guest_lists[i].tgla_day){
					case '0':
						weekday = 'Mondays';
						break;
					case '1':
						weekday = 'Tuesdays';
						break;
					case '2':
						weekday = 'Wednesdays';
						break;
					case '3':
						weekday = 'Thursdays';
						break;
					case '4':
						weekday = 'Fridays';
						break;
					case '5':
						weekday = 'Saturdays';
						break;
					case '6':
						weekday = 'Sundays';
						break;
				}
				
				table_html += '<tr ' + ((i % 2 == 0) ? 'class="odd"' : '') + ' >';
				table_html += '	<td>' + venue_guest_lists[i].tgla_name + '</td>'; //
				table_html += '	<td>' + weekday + '</td>'; //weekday
				table_html += '	<td>' + venue_guest_lists[i].tgla_description + '</td>'; //description
				table_html += '	<td><input type="checkbox" class="iphone" name="list_auto_approve" ' + ((venue_guest_lists[i].tgla_auto_approve) ? 'checked="checked"' : '') + ' /></td>'; //auto-approve			 
				table_html += '	<td><span class="list_index" style="display:none;">' + i + '</span><span class="list_weekday" style="display:none;">' + venue_guest_lists[i].tgla_day + '</span><a href="#" class="delete_guest_list_button" title="Delete Guest List"><img src="' + window.module.Globals.prototype.admin_assets + 'images/icons/actions_small/Trash.png" alt=""></a>&nbsp;<a href="#" class="edit_guest_list_button" title="Edit Guest List"><img src="' + window.module.Globals.prototype.admin_assets + 'images/icons/actions_small/Pencil.png" alt=""></a></td>'; //actions
				table_html += '</tr>';
				
			}
			
			table_html += '<tr><td class="add_new">Add New Guest List</td></tr>';
			
			vgl.find('tbody').html(table_html);
			
			jQuery('.iphone').iphoneStyle();
		}
		
		build_guest_lists_table();
		
		jQuery('a.delete_guest_list_button').live('click', function(){
			
			var me = this;
			
			var delete_function = function(scope){
				
				var me = scope;
				var index = parseInt(jQuery(me).parent().find('span.list_index').html());
				var list_weekday = jQuery(me).parent().find('span.list_weekday').html();
				
				venue_guest_lists.splice(index, 1);
				jQuery.cookies.set('venue_guest_lists', venue_guest_lists);
				
				jQuery(me).parent().parent().remove();
				
				jQuery('div#venue_new_guest_list').find('form select[name = list_weekday] option[value = ' + list_weekday + ']').removeAttr('disabled');
			}
			
			jQuery('div#confirm_dialog p').html('Are you sure you want to delete this guest list?');
			jQuery('div#confirm_dialog').dialog({
				width: 300,
				height: 150,
				modal: true,
				title: 'Confirm Guest List Deletion',
				buttons: {
					'Confirm': function(){
						delete_function(me);
						jQuery(this).dialog('close');
					},
					'Cancel': function(){
						jQuery(this).dialog('close');
					}
				}
			});
			
			return false;
		});
		
		jQuery('table#venue_guest_lists td.add_new').live('click', function(){
			
			jQuery('div#venue_new_guest_list').dialog({
				width:	300,
				height:	470,
				modal:	true,
				title: 'Add Venue Guest List',
				buttons: {
					'Add Guest List' : function(){
						
						var message_box = jQuery(this).find('p.form_message');
						
						var guest_list = {
							name: 			jQuery(this).find('form input[name = list_name]').val(),
							weekday: 		jQuery(this).find('form select[name = list_weekday]').val(),
							description: 	jQuery(this).find('form textarea[name = list_description]').val(),
							auto_approve: 	(typeof jQuery(this).find('form input[name = list_auto_approve]').attr('checked') != 'undefined') ? true : false
						}
						
						if(guest_list.name.length < 5 || guest_list.name.length > 30){
							message_box.html('List name must be at least 5 characters and less than 30 characters');
							return;
						}
	
						var alpha_numeric = /^([a-zA-Z0-9 -]+)$/;
						if(!alpha_numeric.test(guest_list.name)){
							message_box.html('List name must only contain alpha-numeric charaters.');
							return;
						}
						
						if(guest_list.description.length == 0){
							message_box.html('Please enter a list description');
							return;
						}
						
						message_box.html('');
						
						venue_guest_lists.push(guest_list);
						jQuery.cookies.set('venue_guest_lists', venue_guest_lists);
						
						jQuery(this).find('form select[name = list_weekday] option[value = ' + guest_list.weekday + ']').attr('disabled', 'disabled');
						
						build_guest_lists_table();
						
						//clear form
						jQuery(this).find('form input[name = list_name]').val('');
						jQuery(this).find('form select[name = list_weekday]').val('');
						jQuery(this).find('form textarea[name = list_description]').val('');
						
						jQuery(this).dialog('close');
						
					},
					'Cancel' : function(){
						jQuery(this).dialog('close');
					}
				}
			});
			
		});
		
	//	jQuery('form#venue_new_form').dumbFormState();
		
		jQuery('input#submit_new_venue').bind('click', function(){
			
			jQuery('#ajax_loading').css('visibility', 'visible');
			jQuery('#ajax_complete').css('visibility', 'hidden');
			
			//cross-site request forgery token, accessed from session cookie
			//requires jQuery cookie plugin
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
			var data = {
				venue_name: jQuery('form#venue_new_form input[name = venue_name]').val(),
				venue_street_address: jQuery('form#venue_new_form input[name = venue_street_address]').val(),
				venue_city: jQuery('form#venue_new_form input[name = venue_city]').val(),
				venue_state: jQuery('form#venue_new_form select[name = venue_state]').val(),
				venue_zip: jQuery('form#venue_new_form input[name = venue_zip]').val(),
				venue_description: jQuery('form#venue_new_form textarea[name = venue_description]').val(),
				venue_guest_lists: venue_guest_lists,
				ci_csrf_token: cct,
				vc_method: 'manager_edit_venue'
			}
			
			
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: data,
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
					jQuery('#ajax_loading').css('visibility', 'hidden');
					jQuery('#ajax_complete').css('visibility', 'visible');
									
					if(data.success){
						
						jQuery.cookies.del('venue_guest_lists');
						jQuery('form#venue_new_form').dumbFormState('remove');
						window.location = window.module.Globals.prototype.front_link_base + 'admin/managers/settings_venues/';
					
					}else{
						
						jQuery('p#display_message').html(data.message);
					
					}
					
				}
			});
		
			return false;
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