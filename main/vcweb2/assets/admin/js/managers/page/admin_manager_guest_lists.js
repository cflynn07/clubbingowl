if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_guest_lists = function(){
						
		var unbind_callbacks = [];		
			
		













			
		var fb_operation_complete = false;
		
		jQuery('ul.sitemap li').bind('click', function(){
			
			jQuery(this).parents('ul.sitemap').find('li').css('font-weight', 'normal');
			jQuery(this).css('font-weight', 'bold');
	
			var tgla_id = jQuery(this).find('span.tgla_id').html();
			jQuery(this).parents('div.guest_list_content').find('div.list').css('display', 'none');
			jQuery(this).parents('div.guest_list_content').find('div.gl_status').css('display', 'none');
			
			jQuery('div#tgla_' + tgla_id).css('display', 'block');
			
			jQuery('div.gl_status_' + tgla_id).css('display', 'block');
						
		});
		
		fbEnsureInit(function(){
			
			//Display the first guest list by default
			var display_first = function(){
				jQuery('div#main_loading_indicator').remove();
				
				if(window.page_obj.team_venues.length > 0)
					jQuery('div#tabs').tabs().css('display', 'block');
					
				jQuery('ul.sitemap').each(function(){
					
					jQuery(this).children('li:first').css('font-weight', 'bold');
					var first_tgla_id = jQuery(this).children('li:first').find('span.tgla_id').html();
					
					jQuery('div#tgla_' + first_tgla_id).css('display', 'block');
					
					jQuery('div.gl_status_' + first_tgla_id).css('display', 'block');
					
				});
									
			};
			
		//	var users = eval('<?= json_encode($users) ?>');
			var users = window.page_obj.users;
			
			if(users.length > 0){
				
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
					
					console.log(rows);
					
					//populate divs with FB data
					for(var i = 0; i < rows.length; i++){
						
						jQuery('div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
						jQuery('div.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
						
					}	
					
					display_first();
									
				});
				
			}else{
				
				display_first();
				
			}
			
			zebraRows();
	
			fb_operation_complete = true;
			
		});























		jQuery('div#tabs > div.ui-widget-header select.venue_select').bind('change', function(){
			jQuery('div#tabs').tabs('select', parseInt(jQuery(this).val()));
		});
		jQuery('div#tabs > div.ui-widget-header > ul').css('display', 'none');
		
		if(!jQuery.isIpad())
			jQuery('table.guestlists > tbody > tr').hover(function(){
				jQuery(this).addClass('hovered');
			}, function(){
				jQuery(this).removeClass('hovered');
			});
		
		var host_notes_click = function(){
			var original = jQuery.trim(jQuery(this).find('span.original').html());
			
			jQuery(this).find('span.original').css('display', 'none');
			jQuery(this).find('div.edit').css('display', 'block');
			
			if(original != '<span style="font-weight: bold;">Edit Message</span>')
				jQuery(this).find('div.edit textarea').val(original);
								
			var characters = 160 - jQuery(this).find('div.edit textarea').val().length;
			jQuery(this).find('div.edit span.message_remaining').html("<strong>" + characters + "</strong> char remaining");
			
			jQuery(this).find('div.edit textarea').keyup(function(){
			    if(jQuery(this).val().length > 160){
			        jQuery(this).val(jQuery(this).val().substr(0, 160));
			    }
			        
			    var remaining = 160 - jQuery(this).val().length;
				jQuery(this).parent().parent().find('span.message_remaining').html("<strong>" + remaining + "</strong> char remaining");
			});
			
			jQuery(this).find('textarea').focus();
			jQuery(this).unbind('click');
			
		};
		
		
		
		
		jQuery('td.host_notes').live('click', host_notes_click);
		unbind_callbacks.push(function(){
			jQuery('td.host_notes').die('click', host_notes_click);
		});
		
		
		
		
		
		var submit_host_message = function(me){
			var new_text = jQuery.trim(jQuery(me).val());
			var parent = jQuery(me).parent().parent();
			
			if(parent.find('div.edit').css('display') != 'block')
				return;
			
			parent.find('div.edit').css('display', 'none');
			parent.find('span.original').css('display', 'none');
			parent.find('img.message_loading_indicator').css('display', 'block');
			
			var tglr_id = parent.parent().parent().parent().parent().parent().find('td.tglr_id').html();
				
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: {
					ci_csrf_token: cct,
					vc_method: 'update_reservation_host_notes',
					tglr_id: tglr_id,
					host_message: new_text
				},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
					if(data.success){
						if(new_text.length == 0){
							parent.find('span.original').html('<span style="font-weight: bold;">Edit Message</span>');
						}else{
							parent.find('span.original').html(new_text);
						}
						parent.find('span.original').css('display', 'block').focus();
						parent.find('img.message_loading_indicator').css('display', 'none');
						parent.live('click', host_notes_click);
						unbind_callbacks.push(function(){
							parent.die('click', host_notes_click);
						});
					}
					
				}
			});	
		};
		
		var textarea_callback = function(){
			submit_host_message(this);
		}
		jQuery('td.host_notes textarea').live('blur', textarea_callback);
		unbind_callbacks.push(function(){
			jQuery('td.host_notes textarea').die('blur', textarea_callback);
		});
		
		
		
		var keydown_callback = function(e){
			if(e.keyCode == 13){
				
				e.preventDefault();
				submit_host_message(this);
			}
		}
		jQuery('td.host_notes textarea').live('keydown', keydown_callback);
		unbind_callbacks.push(function(){
			jQuery('td.host_notes textarea').die('keydown', keydown_callback);
		});
		
		
	//	manual_add_exclude_ids = eval('<?= json_encode($users) ?>');
		manual_add_exclude_ids = window.page_obj.users;
	
		//------- fb friends invite ----------
		jQuery('td.facebook_gl_invite').bind('click', function(){
			
			var table_body = jQuery(this).parent().parent();
			var tgla_id = table_body.find('td.tgla_id').html();
			var tv_id = table_body.find('td.tv_id').html();
			var venue_name = table_body.find('td.venue_name').html();
			var date = table_body.find('td.date').html();
			
			fbEnsureInit(function(){
				
				FB.ui({
					
					method: 'apprequests',
					message: 'I\'ve added you to my guest list on VibeCompass',
					title: 'Add friends to your Guest List',
					max_recipients: 20,
				//	exclude_ids: manual_add_exclude_ids
					
				}, function(response){
					
					if(response == undefined)
						return;
					
					var users = response.to;
					
					if(users.length == 0)
						return;
					
					//exclude additional users
					for(var i=0; i < users.length; i++){
						manual_add_exclude_ids.push(users[i]);
					}
					
					table_body.find('tr.no_reservations').remove();
					table_body.find('tr:last').before('<tr class="waiting"><td colspan="7"><img src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" alt="loading..." /></td></tr>');
					
					//------ retrieve function --------
					var iterator = 0;
					var retrieve_function = function(){
						
						var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
						jQuery.ajax({
							url: window.location,
							type: 'post',
							data: {
									ci_csrf_token: cct,
									vc_method: 'list_manual_add',
									status_check: true
									},
							cache: false,
							dataType: 'json',
							success: function(data, textStatus, jqXHR){
								
								console.log(data);
								
								if(data.success){
									//job complete
									iterator = 0;
									
									if(!data.message){
										alert('Unknown error, Facebook indicates you are not friends with the users you added.');
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
										
										//add users to window.vc_fql_users
										for(var i=0; i < rows.length; i++){
											window.vc_fql_users.push(rows[i]);
										}
										
										console.log(rows);
												
										//populate divs with FB data
										for(var i = 0; i < rows.length; i++){
											
											var table_html = '<tr class="new_add_manual">';
											table_html += '<td class="request_type" style="display:none;">manager</td>';
											table_html += '<td class="table" style="display:none;">0</td>';
											table_html += '<td class="tglr_id hidden" style="display:none;">' + data.message[rows[i].uid].tglr_id + '</td>';
											table_html += '<td class="tglr_head_user hidden" style="display:none;">' + rows[i].uid + '</td>';
											table_html += '<td class="tv_id" style="display:none;">' + tv_id + '</td>';
											table_html += '<td class="venue_name" style="display:none;">' + venue_name + '</td>';
											table_html += '<td class="date" style="display:none;">' + date + '</td>';
											
											//fields not shown on GL page but shown on dash page
											table_html += '<td class="venue" style="display:none;">' + venue_name + '</td>'; //TODO <-- make sure matches venue html on dashboard page
											table_html += '<td class="promoter" style="display:none;"> - </td>';
											table_html += '<td class="min_spend" style="display:none;">$500</td>';
													
											table_html += '<td class="user_name visual"><span class="name_' + rows[i].uid + '"></span></td>';
											table_html += '<td class="user_pic visual"><div class="pic_square_' + rows[i].uid + '"></div></td>';
											table_html += '<td class="visual">';
											table_html += '	<table class="user_messages" style="width:152px; text-wrap: unrestricted;">';
											table_html += '		<tr><td class="message_header">Request Message:</td></tr>';
											table_html += '		<tr><td class="request_msg"> - </td></tr>';
											table_html += '		<tr><td class="message_header">Response Message:</td></tr>';
											table_html += '		<tr><td class="response_message"> - </td></tr>';
											table_html += '		<tr><td class="message_header">Host Notes:</td></tr>';
											table_html += '		<tr style="max-width:122px;">';
											table_html += '			<td class="host_notes" style="max-width:122px;">';
											table_html += '				<div class="edit" style="display:none;">';
											table_html += '					<textarea></textarea>';
											table_html += '					<br>';
											table_html += '					<span class="message_remaining"></span>';
											table_html += '				</div>';
											table_html += '				<span class="original">';
											table_html += '					<span style="font-weight: bold;">Edit Message</span>';
											table_html += '				</span>';
											table_html += '				<img class="message_loading_indicator" style="display:none;" src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" alt="loading..." />';
											table_html += '			</td>';
											table_html += '		</tr>';
											table_html += '	</table>';
											table_html += '</td>';
											
											table_html += '<td class="visual"><span style="color:red;">No</span></td>';
											table_html += '<td class="visual"><span style="color: green;">Approved</span></td>';
											table_html += '<td class="entourage visual" style="white-space:nowrap; width:244px;"><p>No Entourage</p></td>';
											table_html += '</tr>';
											
											jQuery(table_html).insertBefore(table_body.find('tr:last'));
											
											jQuery('div#lists_container div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
											jQuery('div#lists_container span.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
																							
										}
																					
										//tv_id
										var name = jQuery('div#tabs div.ui-widget-header div.' + tv_id).find('div.name').html();
										var tv_count = parseInt(jQuery('div#tabs div.ui-widget-header div.' + tv_id).find('div.count').html());
										tv_count += rows.length;
										jQuery('div#tabs div.ui-widget-header select.venue_select option.' + tv_id).html(name + ' (' + tv_count + ')');
										jQuery('div#tabs div.ui-widget-header div.' + tv_id).find('div.count').html(tv_count);
										
										//tgla_id
										var tgla_count = parseInt(jQuery('div#tabs ul.sitemap li.' + tgla_id + ' span.count_tgla_id').html());
										tgla_count += rows.length;
										jQuery('div#tabs ul.sitemap li.' + tgla_id + ' span.count_tgla_id').html(tgla_count);
										
										table_body.find('tr.waiting').remove();
										
										var offset = jQuery('tr.new_add_manual:last').offset();
										jQuery(document).scrollTop(offset.top - 50);
										
										jQuery('tr.new_add_manual td.visual').show('highlight', { color:'red' }, 1500, function(){
											jQuery('tr.new_add_manual').removeClass('new_add_manual');
											jQuery('tr.visual').removeClass('visual');
											
											window.zebraRows('table.guestlists > tbody > tr:odd', 'odd');
											
											if(!jQuery.isIpad())
												jQuery('table.guestlists > tbody > tr').hover(function(){
													jQuery(this).addClass('hovered');
												}, function(){
													jQuery(this).removeClass('hovered');
												});
										});
										
									});
									
								}else{
									//not complete
									
									iterator++;
									console.log(iterator);
									setTimeout(retrieve_function, 1000);
									
								}
								
							}
						});
					
					};
					//------ end retrieve function --------
					
					var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
					jQuery.ajax({
						url: window.location,
						type: 'post',
						data: {
							ci_csrf_token: cct,
							vc_method: 'list_manual_add',
							tgla_id: tgla_id,
							oauth_uids: users
						},
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
													
							window.setTimeout(retrieve_function, 1000);
							
						}
					});
					
				});
					
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