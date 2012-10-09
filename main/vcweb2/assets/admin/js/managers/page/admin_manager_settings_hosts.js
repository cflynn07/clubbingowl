if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_settings_hosts = function(){
						
		var unbind_callbacks = [];		
				
					
					
					
					
					
					
					
					
					
					
					
					
					
		fbEnsureInit(function(){
			
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
					
					vc_fql_users = rows;
					console.log(rows);
					
					//populate divs with FB data
					for(var i = 0; i < rows.length; i++){
						
						jQuery('.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
						jQuery('.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
						
					}	
					
					jQuery('div#main_loading_indicator').remove();
					jQuery('table#host_invites').css('display', 'table');
									
				});
				
			}else{
				
				jQuery('div#main_loading_indicator').remove();
				jQuery('table#host_invites').css('display', 'table');
				
			}
	
			fb_operation_complete = true;
			
		});
		
		var hosts_settings_global = {
		//	invited_users: eval('<?= json_encode($filter_uids) ?>')
			invited_users: window.page_obj.filter_uids
		};
		
		jQuery(function(){
						
			jQuery('a#invite_hosts').bind('click', function(){
				
				FB.ui({
					method: 'apprequests',
					title: 'Invite friends to host for your team',
					message: 'Come host for ' + window.page_obj.team.team_name + ' with VibeCompass',
					data: 'TEST DATA',
					exclude_ids: hosts_settings_global.invited_users
				}, function(request){
					
					if(!request)
						return;
					
					for(var i=0; i < request.to.length; i++){
						hosts_settings_global.invited_users.push(request.to[i]);
					}
					
					//build FQL query
					if(request.to.length > 0){
						
						//cross-site request forgery token, accessed from session cookie
						//requires jQuery cookie plugin
						var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
						
						jQuery.ajax({
							url: window.location,
							type: 'post',
							data: {
							 	ci_csrf_token: cct,
								vc_method: 'invitation_create',
								users: request.to
					 		},
							cache: false,
							dataType: 'json',
							success: function(data, textStatus, jqXHR){
							
								if(data.success){
									
									var fql = 'SELECT pic_square, name, uid FROM user WHERE ';
									for(i = 0; i < request.to.length; i++){
										if(i == 0)
											fql += 'uid=' + request.to[i];
										else
											fql += ' OR uid=' + request.to[i];
									}
									
									fbEnsureInit(function(){
										//execute query and wait on results
										var query = FB.Data.query(fql);
										query.wait(function(rows) {
											
											var last_key = jQuery('table#host_invites tbody tr:last td:first').html();
											if(last_key == null){
												last_key = 0;
											}else{
												last_key = parseInt(last_key);
											}
																						
											var table_html = '';
											for(var i = 0; i < rows.length; i++){
												table_html += '<tr>';
												table_html += '<td>' + (last_key + i) + '</td>';
												table_html += '<td><img src="' + rows[i].pic_square + '" alt="profile_picture"></td>';
												table_html += '<td><a href="vc_name"><span style="display:none">' + rows[i].uid + '</span>' + rows[i].name + '</a></td>';
												table_html += '<td>Invited</td>';
												table_html += '<td>' + window.page_obj.date_time + '</td>';
												table_html += '</tr>';
											}
											jQuery('table#host_invites tbody').append(table_html);
											
										});
									});
	
								}
								
							}
						});
						
					
					}
				});
					
				return false;
			});
			
			jQuery('a.delete_host').live('click', function(){
				
				var host_id = jQuery(this).parents('td').find('span.uid').html();
				var tr = jQuery(this).parents('tr');
				
				jQuery('div#host_delete_dialog').dialog({
					resizable: false,
					height:140,
					modal: true,
					buttons: {
						"Delete": function(){
							
							//cross-site request forgery token, accessed from session cookie
							//requires jQuery cookie plugin
							var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
							
							jQuery.ajax({
								url: window.location,
								type: 'post',
								data: {
								 	ci_csrf_token: cct,
									vc_method: 'host_delete',
									host_id: host_id
						 		},
								cache: false,
								dataType: 'json',
								success: function(data, textStatus, jqXHR){
									
									if(data.success){
										
										tr.remove();
										jQuery('div#host_delete_dialog').dialog('close');
										window.location.reload(true);
										
									}else{
										
										alert('An unknown error has occured. Please try again later.');
									
									}
									
								}
							});
							
						},
						Cancel: function(){
							jQuery(this).dialog("close");
						}
					}
				});
				
				return false;
				
			});
			
		});
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
		var user_url_result = jQuery('div#user_url_result');
			
		var uid_update = function(uid){
			
			fbEnsureInit(function(){
				var fql = "SELECT uid, name, pic_square FROM user WHERE uid = " + uid;
				console.log(fql);
				var query = FB.Data.query(fql);
				query.wait(function(rows){
					
					console.log(rows);
					
					if(rows.length == 0){
						user_url_result.html('<p class="error">Facebook user not found</p>');
						return;
					}
					
					user_url_result.html('<img src="' + rows[0].pic_square + '"><span>' + rows[0].name + '</span><span class="uid">' + rows[0].uid + '</span>');
					jQuery('a#invite_manual').css('display', 'block');		
							
				});
			});
			
		};
		
		jQuery('input#user_url').bind('input', function(){
			
			jQuery('a#invite_manual').css('display', 'none');
			
			
			var profile_url = jQuery.trim(jQuery(this).val());
			if(profile_url.length == 0){
				
				user_url_result.html('<p class="error">Invalid URL</p>');
				
			}else{
				
				if(profile_url.indexOf('facebook.com/') == -1){
					user_url_result.html('<p class="error">Invalid URL</p>');
				}else{
					
					user_url_result.html('<img src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" />');
					
					if(profile_url.indexOf('&') != -1){
						profile_url = profile_url.substring(0, profile_url.indexOf('&')); //strip away get params
					}
					
					//attempt to extract uid
					if(profile_url.indexOf('/profile.php?id=') == -1){
						
						var search_string = 'facebook.com/';
						var uid = profile_url.substring(profile_url.indexOf(search_string) + search_string.length);
						
						console.log(uid);
						
						fbEnsureInit(function(){
							FB.api('/' + uid, function(result){
								
								console.log(result);
													
								if(result.id){
									uid_update(result.id);
								}
								
							});
						});								
						
					}else{
						
						var search_string = '/profile.php?id=';
						var uid = profile_url.substring(profile_url.indexOf(search_string) + search_string.length);
						
						uid_update(uid);
						
					}
					
					
				}
				
			}
			
		});
		
		
		jQuery('a#invite_manual').bind('click', function(){
			
			if(jQuery('div#user_url_result span.uid').length == 0){
				return; //no user to invite?
			}
			
			var invitees = new Array;
			invitees[0] = parseInt(jQuery('div#user_url_result span.uid').html());
			
			user_url_result.html('<img src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" />');		
			
			//cross-site request forgery token, accessed from session cookie
			//requires jQuery cookie plugin
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: {
				 	ci_csrf_token: cct,
					vc_method: 'invitation_create',
					users: invitees
		 		},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
				
					if(data.success){
						
						var fql = 'SELECT pic_square, name, uid FROM user WHERE uid = ' + invitees[0];
						
						fbEnsureInit(function(){
							//execute query and wait on results
							var query = FB.Data.query(fql);
							query.wait(function(rows) {
								
								var last_key = jQuery('table#promoter_invites tbody tr:last td:first').html();
								if(last_key == null){
									last_key = 0;
								}else{
									last_key = parseInt(last_key);
								}
																			
								var table_html = '';
								for(var i = 0; i < rows.length; i++){
									table_html += '<tr>';
									table_html += '<td><img src="' + rows[i].pic_square + '" alt="profile_picture"></td>';
									table_html += '<td><a href="vc_name"><span style="display:none">' + rows[i].uid + '</span>' + rows[i].name + '</a></td>';
									table_html += '<td>Invited</td>';
									table_html += '<td>' + window.page_obj.date_time + '</td>';
									table_html += '</tr>';
								}
								jQuery('table#promoter_invites tbody').prepend(table_html);
								
							});
						});
						
						jQuery('a#invite_manual').css('display', 'none');
						user_url_result.html('<p class="success">Invitation Successful!</p>');

					}else{
						
						jQuery('a#invite_manual').css('display', 'none');
						user_url_result.html('<p class="error">' + ((data.message) ? data.message : 'Unknown error') + '</p>');
						
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