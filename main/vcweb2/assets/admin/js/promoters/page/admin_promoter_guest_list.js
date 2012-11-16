if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	
	window.vc_page_scripts.admin_promoter_guest_list = function(){
						
		var unbind_callbacks = [];

		var Models = {};
		var Collections = {};
		var Views = {};
		
		
		
		
		
		
		Models.Reservation = {
			initialize: function(){
				
			},
			defaults: {
				
			}
		};
		
		Collections.Reservations = {
			model: Models.Reservation,
			initialize: function(){
				
			}
		};








		Views.LeftMenu = {
			initialize: function(){
				
			},
			render: function(){
				
			},
			events: {
				
			}
		};
		
		Views.Status = {
			initialize: function(){
				
			},
			render: function(){
				
			},
			events: {
				
			}
		};
		
		Views.Reservation = {
			initialize: function(){
				
			},
			render: function(){
				
			},
			events: {
				
			}
		};
		
		Views.Reservations_Table = {
			initialize: function(){
				
			},
			render: function(){
				
			},
			events: {
				
			}
		};
		
		
		
		
		
		
		Models.Reservation 			= Backbone.Model.extend(Models.Reservation);
		Collections.Reservations 	= Backbone.Collection.extend(Collections.Reservations);
		Views.LeftMenu 				= Backbone.View.extend(Views.LeftMenu);
		Views.Status 				= Backbone.View.extend(Views.Status);
		Views.Reservation 			= Backbone.View.extend(Views.Reservation);
		Views.Reservations_Table 	= Backbone.View.extend(Views.Reservations_Table);
		













		jQuery('.tabs').tabs();
		jQuery('div.datepicker').datepicker();
		
		jQuery('input.guest_list_datepicker').datepicker({
			dateFormat: 'DD MM d, yy',
			maxDate: '+6d',
			minDate: '-3y',
			onSelect: function(dateText, inst){
				
				var dateObj = {
		        	currentYear: inst.selectedYear,
		        	currentMonth: inst.selectedMonth,
		        	currentDay: inst.selectedDay
		        }
		        
			}
		});
		
		
		
		
		
		
		
		
		
		
		
		
		
		var display_approve_deny_dialog = function(ui_element){
			
			var pglr_id = jQuery(ui_element).parent().parent().find('td.pglr_id').html();
			var pglr_head_user = jQuery(ui_element).parent().parent().find('td.pglr_head_user').html();
			
			var fql_user;
			//find head user in vc_fql_users
			for(var i = 0; i < vc_fql_users.length; i++){
				
				if(vc_fql_users[i].uid == pglr_head_user){
					fql_user = vc_fql_users[i];
					break;
				}
				
			}
					
			jQuery('div#dialog_actions img.pic_square').attr('src', fql_user.pic_square);
			jQuery('div#dialog_actions span.name').html(fql_user.name);
							
			jQuery('div#dialog_actions').dialog({
				title: 'Approve or Decline Request',
				height: 420,
				width: 320,
				modal: true,
				resizable: false,
				draggable: false,
				buttons: [{
					text: 'Approve',
					id: 'ui-approve-button',
					click: function(){
						app_dec_function(true);
					}
				},{
					text: 'Decline',
					click: function(){
						app_dec_function(false);
					}
				}]
			});
			
			var app_dec_function = function(approved){
			
				var element1 = jQuery(ui_element).parent().parent().find('table.user_messages td.response_message');
				
				jQuery('div#dialog_actions').find('div#dialog_actions_loading_indicator').css('display', 'block');
				
				//cross-site request forgery token, accessed from session cookie
				//requires jQuery cookie plugin
				var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
				var message = jQuery.trim(jQuery('div#dialog_actions textarea[name = message]').val());
				
				jQuery.ajax({
					url: window.location,
					type: 'post',
					data: {
							ci_csrf_token: cct,
							vc_method: 'list_request_app_dec',
							app_dec: approved,
							pglr_id: pglr_id,
							message: message
							},
					cache: false,
					dataType: 'json',
					success: function(data, textStatus, jqXHR){
						
						if(data.success){
							
							jQuery('div#dialog_actions').find('div#dialog_actions_loading_indicator').css('display', 'none');
							jQuery('div#dialog_actions').find('textarea').val('');
							
							jQuery(ui_element).replaceWith((approved) ? '<span style="color: green;">Approved</span>' : '<span style="color: red;">Declined</span>');
													
							if(message.length > 0)
								element1.html(message);
							
							jQuery('div#dialog_actions').dialog('close');
							
							
						}
						
					},
					failure: function(){
						console.log('failure');
					}
				});
				
			};
			
		};
		
		jQuery('span.app_dec_action').live('click', function(){
			display_approve_deny_dialog(this);
		});
		
		
		
		
		
		
		
		
		jQuery('ul.sitemap li').bind('click', function(){
		
			jQuery('ul.sitemap li').css('font-weight', 'normal');
			jQuery(this).css('font-weight', 'bold');
		
			var pgla_id = jQuery(this).find('span.pgla_id').html();
			jQuery('div#lists_container div.list').css('display', 'none');
			
			jQuery('div#pgla_' + pgla_id).css('display', 'block');
			
			//show relevant gl status box
			jQuery('div.gl_status').css('display', 'none');
			jQuery('div.gl_status_' + pgla_id).css('display', 'block');
						
		});
		
		
		
		
		
		
		
		
		
		
		
		
		fbEnsureInit(function(){
			
			console.log('okay fb init');
			console.log(window.page_obj.users);
			
		
			//Display the first guest list by default
			var display_first = function(){
				jQuery('div#loading_indicator').remove();
				jQuery('div#guest_list_content').css('display', 'block');
			
				//display first guest list by default
				var list1_id = jQuery('ul.sitemap li:first span.pgla_id').html();
				
				jQuery('ul.sitemap').children("li:first").css('font-weight', 'bold'); 
				
				jQuery('div#lists_container div.list').css('display', 'none');
				jQuery('div#pgla_' + list1_id).css('display', 'block');
				
				//show relevant gl status box
				jQuery('div.gl_status').css('display', 'none');
				jQuery('div.gl_status_' + list1_id).css('display', 'block');
			};
			
			//var users = eval('<?= $users ?>');
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
					
					vc_fql_users = rows;
					
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
			
		});
			
	//	manual_add_exclude_ids = eval('<?= $users ?>');
		manual_add_exclude_ids = window.page_obj.users;
		
		
		
		
		
		
		
		
		
		
		
		
		//------- fb friends invite ----------
		jQuery('td.facebook_gl_invite').bind('click', function(){
			
			var table_body = jQuery(this).parent().parent();
			var pgla_id = table_body.find('td.pgla_id').html();
					
			fbEnsureInit(function(){
				
				FB.ui({
					method: 'apprequests',
					message: 'I\'ve added you to my guest list on ClubbingOwl',
					title: 'Add friends to your Guest List',
					max_recipients: 20,
					exclude_ids: manual_add_exclude_ids
				}, function(response){
					
					if(typeof response === 'undefined')
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
								vc_method: 'promoter_list_manual_add',
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
										
																
										console.log(rows);
												
										//populate divs with FB data
										for(var i = 0; i < rows.length; i++){
											
											var table_html = '<tr class="new_add_manual">';
											table_html += '<td class="pglr_id hidden hidden" style="display:none">' + data.message[rows[i].uid].pglr_id + '</td>';
											table_html += '<td class="pglr_head_user hidden" style="display:none">' + rows[i].uid + '</td>';
								
											table_html += '<td><span class="name_' + rows[i].uid + '"></span></td>';
											table_html += '<td><div class="pic_square_' + rows[i].uid + '"></div></td>';
											
											
											table_html += '<td>';
											table_html += '<table class="user_messages" style="width:152px; text-wrap: unrestricted;">';
											table_html += '		<tr><td class="message_header">Request Message:</td></tr>';
											table_html += '		<tr><td> - </td></tr>';
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
											table_html += '</table>';
											table_html += '</td>';
											
											table_html += '<td><span style="color:red;">No</span></td>';
											table_html += '<td><span style="color: green;">Approved</span></td>';
											table_html += '<td style="white-space: nowrap; width:244px;"><p>No Entourage</p></td>';
											table_html += '</tr>';
											
											jQuery(table_html).insertBefore(table_body.find('tr:last'));
																	
											jQuery('div#lists_container div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
											jQuery('div#lists_container span.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
																					
										}
																			
										table_body.find('tr.waiting').remove();
										
										//increment count
										var count_res = parseInt(jQuery('ul.sitemap li.' + pgla_id + ' span.wgl_groups_count').html());
										count_res += rows.length;
										jQuery('ul.sitemap li.' + pgla_id + ' span.wgl_groups_count').html(count_res);
										
										var offset = jQuery('tr.new_add_manual:last').offset();
										jQuery(document).scrollTop(offset.top - 50);
										
										jQuery('tr.new_add_manual td').show('highlight', { color:'red' }, 1500, function(){
											jQuery('tr.new_add_manual').removeClass('new_add_manual');
											
											window.zebraRows('table.guestlists > tbody > tr:odd', 'odd');
											
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
							vc_method: 'promoter_list_manual_add',
							pgla_id: pgla_id,
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
		
		
		
		
		
		(function(){
			
			var characters = 160;
			jQuery("span#dialog_actions_message_remaining").html("You have <strong>" + characters + "</strong> characters remaining");
			
			jQuery("div#dialog_actions textarea[name = message]").keyup(function(){
			    if(jQuery(this).val().length > characters){
			        jQuery(this).val(jQuery(this).val().substr(0, characters));
			    }
			        
			    var remaining = characters - jQuery(this).val().length;
				jQuery("span#dialog_actions_message_remaining").html("You have <strong>" + remaining + "</strong> characters remaining");
			});
			
		})();
		
		
		
		
		
		
		
		
		
		
		
		jQuery('div#guest_list_content').bind('click', function(event){
			
			var target = jQuery(event.target);
			if(!target.is('li.host_notes'))
				return;
								
			var _this = event.target;
			var original = jQuery.trim(jQuery(_this).find('span.original').html());
			
			jQuery(_this).find('span.original').css('display', 'none');
			jQuery(_this).find('div.edit').css('display', 'block');
							
			if(original != '<span style="font-weight: bold;">Edit Message</span>')
				jQuery(_this).find('div.edit textarea').val(original);
								
			var characters = 160 - jQuery(_this).find('div.edit textarea').val().length;
			jQuery(_this).find('div.edit span.message_remaining').html("<strong>" + characters + "</strong> char remaining");
			
			jQuery(_this).find('div.edit textarea').keyup(function(){
			    if(jQuery(_this).val().length > 160){
			        jQuery(_this).val(jQuery(_this).val().substr(0, 160));
			    }
			        
			    var remaining = 160 - jQuery(_this).val().length;
				jQuery(_this).parent().parent().find('span.message_remaining').html("<strong>" + remaining + "</strong> char remaining");
			});
			
			jQuery(_this).find('div.edit textarea').focus();
			jQuery(_this).die('click');
			
		});
		
		
		
		
		
	
		var submit_host_message = function(me){
			var new_text = jQuery.trim(jQuery(me).val());
			var parent = jQuery(me).parent().parent()
			
			if(parent.find('div.edit').css('display') != 'block')
				return;
			
			parent.find('div.edit').css('display', 'none');
			parent.find('span.original').css('display', 'none');
			parent.find('img.message_loading_indicator').css('display', 'block');
			
			var pglr_id = parent.parent().parent().parent().parent().parent().find('td.pglr_id').html();
				
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: {
					ci_csrf_token: cct,
					vc_method: 'update_promoter_reservation_host_notes',
					pglr_id: pglr_id,
					host_message: new_text
				},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
					if(data.success){
						
						parent.find('div.edit textarea').unbind('blur');
						parent.find('div.edit textarea').unbind('keydown');							
						
						if(new_text.length == 0){
							parent.find('span.original').html('<span style="font-weight: bold;">Edit Message</span>');
						}else{
							parent.find('span.original').html(new_text);
						}
						parent.find('span.original').css('display', 'block').focus();
						parent.find('img.message_loading_indicator').css('display', 'none');
						parent.live('click', host_notes_click);
					}
					
				}
			});	
		};
		
		
		
		
		var callback1 = function(){
			submit_host_message(this);
		}
		jQuery('td.host_notes textarea').live('blur', callback1);
		unbind_callbacks.push(function(){
			console.log('unbind 1');
			jQuery('td.host_notes textarea').die('blur', callback1);
		});
		
		
		
		
		var callback2 = function(){
			if(e.keyCode == 13){
				
				e.preventDefault();
				submit_host_message(this);
			}
		}
		jQuery('td.host_notes textarea').live('keydown', callback2);
		unbind_callbacks.push(function(){
			console.log('unbind 2');
			jQuery('td.host_notes textarea').die('keydown', callback2);
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