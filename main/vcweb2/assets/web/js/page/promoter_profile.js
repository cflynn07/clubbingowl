if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.promoter_profile = function(){
				
		//multi-dimensional 2D array of event types, bounded elements, and callbacks
		//to be iterated over when pagechange occurs via pushState
		var unload_items = [];
		var timeout_cancels = [];
		var custom_events_unbind = [];
		var facebook_callbacks = [];
						
		var promoter_items = {
			retrieve_lock: false,
			items_iterator: false,
			retrieve_feed: function(request_first){
				
				if(promoter_items.retrieve_lock)
					return;
				
				//while loading data, prevent this method from firing again
				promoter_items.retrieve_lock = true;
				
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
								vc_method: 'feed_retrieve',
								status_check: true
							  },
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
							
							promoter_items.retrieve_lock = false;
						
							if(data.success){
											
								console.log('success');
								promoter_items.display(data.message);
								
							}else{
								
								incrementor++;
								var timeout = setTimeout(retrieve_function, 1000);
								timeout_cancels.push(timeout);
								
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
							vc_method: 'feed_retrieve'
						},
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
							
							if(data.success){
								
								//start first check 1 second after
								var timeout = setTimeout(retrieve_function, 1000);
								timeout_cancels.push(timeout);
								
							}
							
						}
					});
					
					return;
				}else{
					retrieve_function();
				}
				
			},
			display: function(data){
				
				
				//display venue friends
				for(var i in data.promoter_venues_user_friends){
					
					var obj = data.promoter_venues_user_friends[i];
					
					var pv_u_friends = jQuery('p#user_friends_' + i);
										
					
					var tdata = {
						tv_friends_pop: obj,
						user_friends: data.user_friends
					};
					
					html = new EJS({
					//	element: jQuery('div#ejs_promoter_profile_templates > div#friends_venues').get(0)
						text: ejs_view_templates.promoters_profile_friends_venues
					}).render(tdata);
					
									
					pv_u_friends.html(html);
				
					jQuery('p#user_friends_' + i + ' > a').each(function(){
						
					});
					
				}
				
				
				var friend_activity_feed = jQuery('div#promoter_user_news_feed');
				friend_activity_feed.empty();
				for(var i in data.promoter_notifications){
					
					var obj = data.promoter_notifications[i];
					
					var ejs_object = {
						pic_square: 	data.user_friends[obj.un_vibecompass_id].pic_square,
						u_first_name: 	data.user_friends[obj.un_vibecompass_id].name,
						u_full_name: 	data.user_friends[obj.un_vibecompass_id].name,
						tv_name: 		obj.un_notification_data.tv_name,
						pgla_name: 		obj.un_notification_data.pgla_name,
						third_party_id: data.user_friends[obj.un_vibecompass_id].third_party_id
					};
					
					
					news_html = new EJS({
					//	element: jQuery('div#ejs_promoter_profile_templates > div#news_feed_item').get(0)
						text: ejs_view_templates.promoters_profile_news_feed_item
					}).render(ejs_object);
					
					friend_activity_feed.append(news_html);
					
				}
							
			}
		};
				
		if(window.vc_server_auth_session)
			promoter_items.retrieve_feed(false);
	
	
	
	
	
		var vc_login_callback = function(){
		
			var auth_content = jQuery('.auth_content');
			var unauth_content = jQuery('.unauth_content');
			
			console.log('vc_login');
			
			unauth_content.css('display', 'none');
			auth_content.css('display', 'block');
			
			promoter_items.retrieve_feed(true);
			
		};
		window.EventHandlerObject.addListener("vc_login", vc_login_callback);
		custom_events_unbind.push([
			'vc_login',
			window.EventHandlerObject,
			vc_login_callback
		]);
		
		
		
		
		
		
		
		var vc_logout_callback = function(){
			
			var auth_content = jQuery('.auth_content');
			var unauth_content = jQuery('.unauth_content');
			
			console.log('vc_logout');
	
			unauth_content.css('display', 'block');
			auth_content.css('display', 'none');
	
			//clean up previously inserted content
			var auth_content = jQuery('div.auth_content');
			auth_content.find('div.loading_indicator').css('display', 'block');
			auth_content.find('*').not('div.loading_indicator').contents().remove();
			
		};
		window.EventHandlerObject.addListener("vc_logout", vc_logout_callback);
		custom_events_unbind.push([
			'vc_logout',
			window.EventHandlerObject,
			vc_logout_callback
		]);
	
		
		
		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			console.log('unbind_callback');
			console.log(unload_items);
			console.log(timeout_cancels);
			console.log(custom_events_unbind);
			
			for(var i in unload_items){
				unload_items[i][1].unbind(unload_items[i][0], unload_items[i][2]);
			}
			
			for(var i in timeout_cancels){
				clearTimeout(timeout_cancels[i]);
			}
			
			for(var i in custom_events_unbind){
				custom_events_unbind[i][1].removeListener(custom_events_unbind[i][0], custom_events_unbind[i][2]);
			}
			
			for(var i in facebook_callbacks){
				facebook_callbacks[i] = function(){};
			}
			
		}
		
	
	
	
	}
	
});