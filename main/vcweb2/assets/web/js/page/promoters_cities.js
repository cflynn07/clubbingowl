if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.promoters_cities = function(){

				
				
				
				
		var page_items = {
			retrieve_lock: false,
			items_iterator: false,
			retrieve_feed: function(request_first){
				
				if(page_items.retrieve_lock)
					return;
				page_items.retrieve_lock = true;
				
				
				
				
				
				
				var incrementor = 0;
				
				var retrieve_function = function(){
									
					if(incrementor > 4){
						incrementor = 0;
						return; //failed
					}
					
					//cross-site request forgery token, accessed from session cookie
					//requires jQuery cookie plugin
					var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
					
					jQuery.ajax({
						url: window.location,
						type: 'post',
						data: {
						 	ci_csrf_token: cct,
							vc_method: 'promoter_friends_retrieve',
							status_check: true
						},
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
							
							if(data.trigger_refresh){
								window.location.reload();
								return;
							}
							
							page_items.retrieve_lock = false;
						
							if(data.success){
											
								console.log('success');
								page_items.display(data.message);
								
							}else{
								
								incrementor++;
								var timeout = setTimeout(retrieve_function, 1000);
								
							}
						
						}
					});
				}
				
				
				
				//If this is an initial request, start job w/ server
				if(request_first){
					//cross-site request forgery token, accessed from session cookie
					//requires jQuery cookie plugin
					var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
					
					jQuery.ajax({
						url: window.location,
						type: 'post',
						data: {
							ci_csrf_token: cct,
							vc_method: 'promoter_friends_retrieve'
						},
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
							
							if(data.success)
								//start first check 1 second after
								var timeout = setTimeout(retrieve_function, 1000);

						}
					});
					
					
					
					
					return;
				}else{
					retrieve_function();
				}
				
			},
			display: function(data){
				
				if(!data.promoters_users_friends){
					return;
				}
				
				var user_ids = [];
				for(var i in data.promoters_users_friends){
					var up = data.promoters_users_friends[i];
					
					var promoter_friends_div = jQuery('div[data-up_id=' + i + ']');
														
					for(var k=0; k < up.length; k++){
						user_ids.push(up[k]);
						
						promoter_friends_div.append('<span data-oauth_uid=' + up[k] + '></span>');
						
					}
									
				}
			
			
				console.log(data.promoters_users_friends);
			
			
			
				jQuery.fbUserLookup(user_ids, 'uid, third_party_id, name, first_name, last_name, pic_square, pic_big', function(rows){
					console.log(rows);
					
					jQuery('img.loading_indicator').hide();
					
					for(var i in rows){
						
						var user = rows[i];
						jQuery('span[data-oauth_uid=' + user.uid + ']').html('<a class="ajaxify" href="' + window.module.Globals.prototype.front_link_base + 'friends/' + user.third_party_id + '/"><img src="' + user.pic_square + '" class="friend_img" style="" /></a>');
						
						
					}
					
					
				});
			
			}
			
			
			
			
			
			
			
		};
				
		if(window.vc_server_auth_session)
			page_items.retrieve_feed(false);
	
	
	
		var vc_login_callback = function(){
		
			var auth_content = jQuery('.auth_content');
			var unauth_content = jQuery('.unauth_content');
			
			console.log('vc_login');
			
			unauth_content.css('display', 'none');
			auth_content.css('display', 'block');
			
			page_items.retrieve_feed(true);
			
		};
		window.EventHandlerObject.addListener("vc_login", vc_login_callback);
		
		


		var vc_logout_callback = function(){
			
			var auth_content = jQuery('.auth_content');
			var unauth_content = jQuery('.unauth_content');
			
			console.log('vc_logout');
	
			unauth_content.css('display', 'block');
			auth_content.css('display', 'none');
	
			//clean up previously inserted content
			var auth_content = jQuery('div.auth_content');
			
			auth_content.find('img.loading_indicator').show();
			jQuery('.auth_clear_content').find('*').not('img.loading_indicator').contents().remove();
			
			
		};
		window.EventHandlerObject.addListener("vc_logout", vc_logout_callback);
	
	
		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			window.EventHandlerObject.removeListener('vc_logout', vc_logout_callback);
			window.EventHandlerObject.removeListener('vc_login', vc_login_callback);
					
			
		}
			
		
	}
	
});