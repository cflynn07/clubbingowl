if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_settings_promoters = function(){
						
		var unbind_callbacks = [];		
				
					
					
					
					
					
					
								
				
		fbEnsureInit(function(){
						
			var users = window.page_obj.users;
			
			if(users.length > 0){
				
				jQuery.fbUserLookup(users, 'uid, name, pic_square, pic_big, third_party_id', function(rows){
				
					vc_fql_users = rows;
					console.log(rows);
					
					//populate divs with FB data
					for(var i = 0; i < rows.length; i++){
						
						jQuery('div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
						jQuery('div.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
						
					}	
					
					jQuery('div#main_loading_indicator').remove();
					jQuery('table#promoter_invites').css('display', 'table');
									
				});
				
			}else{
				
				jQuery('div#main_loading_indicator').remove();
				jQuery('table#promoter_invites').css('display', 'table');
				
			}
	
			fb_operation_complete = true;
			
		});
		
		var promoters_settings_global = {
			//invited_users: eval('<?= json_encode($filter_uids) ?>')
			invited_users: window.page_obj.filter_uids
		};
					
		jQuery('a#invite_promoters').bind('click', function(){
			
			FB.ui({
				method: 'apprequests',
				title: 'Invite friends to promote for your team',
				message: 'Come promote for ' + window.page_obj.team.team_name + ' with ClubbingOwl',
				data: 'TEST DATA'
			}, function(request){
				
				if(!request)
					return;
				
				for(var i=0; i < request.to.length; i++){
					promoters_settings_global.invited_users.push(request.to[i]);
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
								
								
								jQuery.fbUserLookup(request.to, 'pic_square, name, uid', function(rows){
								
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
							}
							
						}
					});
					
				
				}
			});
			
			return false;
		});
		
		jQuery('a.delete_promoter').live('click', function(){
			
			var promoter_id = jQuery(this).parent().find('span').html();
			var tr = jQuery(this).parent().parent();
			
			jQuery('div#promoter_delete_dialog').dialog({
				resizable: false,
				height: 180,
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
								vc_method: 'promoter_delete',
								promoter_id: promoter_id
					 		},
							cache: false,
							dataType: 'json',
							success: function(data, textStatus, jqXHR){
								
								if(data.success){
									
									tr.remove();
									jQuery('div#promoter_delete_dialog').dialog('close');
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
				
				
				
				
				
				
				
				
				
				
				
				
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		var user_url_result = jQuery('div#user_url_result');
		
		var uid_update = function(uid){
			
			fbEnsureInit(function(){
				
				jQuery.fbUserLookup([uid], 'uid, name, pic_square', function(rows){
				
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
		
		jQuery('input#user_url').bind('focus', function(){
			jQuery(this).val('');
		});
		
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
						
						
						fbEnsureInit(function(){							
							
							jQuery.fbUserLookup(invitees, 'pic_square, name, uid', function(rows){
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
								
							})
				
						});
						
						jQuery('a#invite_manual').css('display', 'none');
						user_url_result.html('<p class="success">Invitation Successful!</p>');

					}else{
						
						jQuery('a#invite_manual').css('display', 'none');
						user_url_result.html('<p class="error">Invitation already sent to this Facebook user</p>');
						
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