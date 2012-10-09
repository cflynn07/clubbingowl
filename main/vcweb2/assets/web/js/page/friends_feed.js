if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};
	
jQuery(function(){
	window.vc_page_scripts.friends_feed = function(){
		
		//multi-dimensional 2D array of event types, bounded elements, and callbacks
		//to be iterated over when pagechange occurs via pushState
		var unload_items 			= [];
		var timeout_cancels 		= [];
		var custom_events_unbind	= [];
		var facebook_callbacks 		= [];
		
		
		if(typeof window.all_vc_friends === 'undefined')
			window.all_vc_friends = false;
		
		
		
		
		var vc_friends = {
			retrieve_feed_lock: false,
			items_iterator: 0,
			retrieve_feed: function(request_first){
							
				//show loading indicator
				if(jQuery('ul#vc_friends div#loading_indicator').length == 0)
					jQuery('ul#vc_friends').append('<div id="loading_indicator"><img src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" alt="loading..." /></div>');
							
				
				var friends_subset_handle = function(){
					
					var data = window.all_vc_friends.slice(vc_friends.items_iterator, vc_friends.items_iterator + 21);
					vc_friends.append_item_html(data);				
					vc_friends.items_iterator = vc_friends.items_iterator + data.length;
					
				};
				
				
				if(window.all_vc_friends === false){
					
					
			
					fbEnsureInit(function(){
						
						setTimeout(function(){
							var token 	= FB.getAccessToken();						
							var fql 	= "SELECT uid, name, pic, pic_square, is_app_user, third_party_id FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me()) ORDER BY is_app_user DESC";
							
							FB.api({
							    method: 'fql.query',
							    access_token : token,
							    query: fql
							}, function(data) {
								
							    console.log(data);
							    window.all_vc_friends = data;
							    friends_subset_handle();
							    
							});
						}, 2000);
						
						
					});
					
					
				}else{
					
					
					friends_subset_handle();
					
				}
				
				return;
				
				
				
				
				
				
				
				
							
							
							
							
				if(vc_friends.retrieve_feed_lock)
					return;
				
				//while loading data, prevent this method from firing again
				vc_friends.retrieve_feed_lock = true;
				
				
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
								vc_method: 'friend_feed_retrieve',
								status_check: true
							  },
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
										
							if(data.success){
															
								vc_friends.append_item_html(data.message);	
								incrementor = 0;
								
								vc_friends.items_iterator = vc_friends.items_iterator + data.message.length;
								
								if(data.message.length > 17)
									vc_friends.retrieve_feed_lock = false; //free to load more data
								//otherwise it stays locked
								
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
							vc_method: 'friend_feed_retrieve',
							iterator: vc_friends.items_iterator
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
			append_item_html: function(data){
				
				console.log('append_item_html');
				console.log(data);
				
				jQuery('ul#vc_friends div#loading_indicator').remove();
				
				for(var i in data){
					
					if(data[i].is_app_user){
						
						var friend_html = new EJS({
						//	element: jQuery('div#ejs_friends_templates > div#vc_friend').get(0)
							text: ejs_view_templates.friends_vc_friend
						}).render(data[i]);
						
					}else{
						
						var friend_html = new EJS({
						//	element: jQuery('div#ejs_friends_templates > div#non_vc_friend').get(0)
							text: ejs_view_templates.friends_nonvc_friend
						}).render(data[i]);
						
					}
					
					jQuery('ul#vc_friends').append(friend_html);
					
				}
		
			}	
		};
		
		
		
		
		
		
		
		
		if(window.vc_server_auth_session)
			vc_friends.retrieve_feed(false);
		else 
			vc_friends.retrieve_feed_lock = true;
			
		
		
		
		
		
		
		
		
		var vc_login_callback = function(){
			var friends_holder = jQuery('div#friends_holder');
			var unauth_content_holder = jQuery('div#unauth_content_holder');
			
			window.all_vc_friends = false;
			
			friends_holder.css('display', 'block');
			friends_holder.find('ul#vc_friends').empty();
			unauth_content_holder.css('display', 'none');
			
			vc_friends.retrieve_feed_lock = false;
			vc_friends.retrieve_feed(true);
			
			//show if not already showing
			if(friends_holder.css('display') == 'block'){
				
				unauth_content_holder.css('display', 'none');
				friends_holder.css('display', 'block');
			
			}
		};
		window.EventHandlerObject.addListener("vc_login", vc_login_callback);
		custom_events_unbind.push([
			'vc_login',
			window.EventHandlerObject,
			vc_login_callback
		]);
		
		
		var vc_logout_callback = function(){
			var friends_holder = jQuery('div#friends_holder');
			var unauth_content_holder = jQuery('div#unauth_content_holder');
			
			window.all_vc_friends = false;
			
			friends_holder.css('display', 'none');
			friends_holder.find('ul#vc_friends').empty();
			unauth_content_holder.css('display', 'block');
			
			//empty contents of notifications_holder
			friends_holder.html('');
			
			//prevent browser from trying to look up feed
			vc_friends.retrieve_feed_lock = true;
			vc_friends.items_iterator = false;
			
			//show if not already showing
			if(friends_holder.css('display') == 'block'){
				
				unauth_content_holder.css('display', 'block');
				friends_holder.css('display', 'none');
			
			}
		};
		window.EventHandlerObject.addListener("vc_logout", vc_logout_callback);
		custom_events_unbind.push([
			'vc_logout',
			window.EventHandlerObject,
			vc_logout_callback
		]);
		
				
				
		var scroll_callback = function() {
		   	if(jQuery(window).scrollTop() >= (jQuery(document).height() - jQuery(window).height() - 250)){
				vc_friends.retrieve_feed(true);
			}
		};	
		jQuery(window).scroll(scroll_callback);
		unload_items.push([
			'scroll',
			jQuery(window),
			scroll_callback
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