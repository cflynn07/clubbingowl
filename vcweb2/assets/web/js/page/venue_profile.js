if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.venue_profile = function(promoter_full_name){


		//multi-dimensional 2D array of event types, bounded elements, and callbacks
		//to be iterated over when pagechange occurs via pushState
		var unload_items = [];
		var timeout_cancels = [];
		var custom_events_unbind = [];
		var facebook_callbacks = [];
				
				
				
				
				
				
		var venue_items = {
			retrieve_lock: false,
			items_iterator: false,
			retrieve_feed: function(request_first){
				
				if(venue_items.retrieve_lock)
					return;
				
				//while loading data, prevent this method from firing again
				venue_items.retrieve_lock = true;
				
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
							
							venue_items.retrieve_lock = false;
						
							if(data.success){
											
								console.log('success');
								venue_items.display(data.message);
								
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
							
							if(data.success)
								//start first check 1 second after
								var timeout = setTimeout(retrieve_function, 1000);
								timeout_cancels.push(timeout);
						}
					});
					
					return;
				}else{
					retrieve_function();
				}
				
			},
			display: function(data){
				
				var friends_div = jQuery('div#venue_user_friends_feed');
				friends_div.find('img.loading_indicator').css('display', 'none');
				
				var first_index = 0;
				for(var i in data.venue_user_friends){
					first_index = i;
					break;
				}
				
				var venue_friends = data.venue_user_friends[first_index];
				venue_friends = jQuery.randomize_array(venue_friends);
				for(var i in venue_friends){
					
					if(i > 14)
						break; //limit 15
					
					console.log(data.user_friends[venue_friends[i]]);
			
					var html = new EJS({
					//	element: jQuery('div#ejs_venue_profile_templates > div#friends_li').get(0)
						text: ejs_view_templates.venues_profile_friends_li
					}).render(data.user_friends[venue_friends[i]]);
					friends_div.find('ul#vc_friends').append(html);
					
				}
				
				var html = new EJS({
				//	element: jQuery('div#ejs_venue_profile_templates > div#friends_li_message').get(0)
					text: ejs_view_templates.venues_profile_friends_li_message
				}).render({count: venue_friends.length});
				
				friends_div.find('p#friends_count_msg').html(html);
				
				// -----------------------------------------------------------
				
				
				var feed_div = jQuery('div#venue_user_news_feed');
				feed_div.find('img.loading_indicator').css('display', 'none');
				
				var venue_news_feed = data.venue_user_news_feed;
				for(var i in venue_news_feed){
					
					var obj = {
						user_friend: data.user_friends[venue_news_feed[i].vibecompass_id],
						notification_data: jQuery.parseJSON(venue_news_feed[i].notification_data),
						notification: venue_news_feed[i]
					};
					
					console.log('obj');
					console.log(obj);
					
					var html = new EJS({
					//	element: jQuery('div#ejs_venue_profile_templates > div#news_feed_item').get(0)
						text: ejs_view_templates.venues_profile_news_feed_item
					}).render(obj);
					
					feed_div.find('ul#news_feed').append(html);
					
				}
			}
			
			
			
		};
				
		if(window.vc_server_auth_session)
			venue_items.retrieve_feed(false);
	
	
	
		var vc_login_callback = function(){
		
			var auth_content = jQuery('.auth_content');
			var unauth_content = jQuery('.unauth_content');
			
			console.log('vc_login');
			
			unauth_content.css('display', 'none');
			auth_content.css('display', 'block');
			
			venue_items.retrieve_feed(true);
			
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
			auth_content.find('img.loading_indicator').css('display', 'block');
			jQuery('.auth_clear_content').find('*').not('img.loading_indicator').contents().remove();
			
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