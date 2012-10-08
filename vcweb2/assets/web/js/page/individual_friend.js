if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	window.vc_page_scripts.individual_friend = function(){
		
		var users_oauth_uid = individual_friend_obj.users_oauth_uid, 
				friend_users_full_name = individual_friend_obj.users_full_name;
					
							
		//multi-dimensional 2D array of event types, bounded elements, and callbacks
		//to be iterated over when pagechange occurs via pushState
		var unload_items = [];
		var timeout_cancels = [];
		var custom_events_unbind = [];
		var facebook_callbacks = [];
		
		
		//load picture of THIS user
		var fb_callback = function(rows){
			
			for(var i in rows){
				jQuery('.pic_' + rows[i].uid).html('<img src="' + rows[i].pic_big + '" alt="' + rows[i].name + '" />');
			}
			
		}
		jQuery.fbUserLookup([users_oauth_uid], 'uid, name, pic_big', fb_callback);
		facebook_callbacks.push(fb_callback);
		
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
					vc_method: 'friend_retrieve'
		 		},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
								
					if(data.success){
													
						incrementor = 0;
						display_function(data.message);
						
					}else{
						
						incrementor++;
						var timeout = setTimeout(retrieve_function, 1000);
						timeout_cancels.push(timeout);
						
					}
				
				}
			});
		};
		
		if(window.vc_server_auth_session)
			retrieve_function();
		
		var display_function = function(data){
			
			var friend_content = jQuery('div#friend_content');
			
			//If not friend, display error message
			if(!data.vc_friend){
				jQuery('div#loading_indicator').remove();
				jQuery('p#friend_error').html('You must be Facebook Friends with ' + friend_users_full_name + ' to view their profile on VibeCompass.').css('display', 'block');
				return;
			}
						
			//render vibecompass friends
			var friends = data.vc_friend.vc_mates;
			for(var i in friends){
				var friend_html = new EJS({
				//	element: jQuery('div#ejs_friend_templates > div#ejs_vibecompass_friends').get(0)
					text: ejs_view_templates.friends_vibecompass_friends
				}).render(friends[i]);
				friend_content.find('ul#vibecompass_friends').append(friend_html);
			}
			
			
			//render activity feed
			var activity = data.vc_friend.activity_feed;
			for(var i in activity){
				
				console.log(activity[i]);
				
				switch(activity[i].un_notification_type){
					case 'join_vibecompass':
					
						var activity_html = new EJS({
						//	element: jQuery('div#ejs_friend_templates > div#ejs_recent_activity_join_vibecompass').get(0)
							text: ejs_view_templates.friends_recent_activity_join_vc
						}).render(activity[i]);
					
						break;
					
					case 'join_team_guest_list':
						
						var activity_html = new EJS({
						//	element: jQuery('div#ejs_friend_templates > div#ejs_recent_activity_join_tgl').get(0)
							text: ejs_view_templates.friends_recent_activity_join_tgl	
						}).render(activity[i]);

					
						break;
					
					case 'join_promoter_guest_list':
					
						var activity_html = new EJS({
						//	element: jQuery('div#ejs_friend_templates > div#ejs_recent_activity_join_pgl').get(0)
							text: ejs_view_templates.friends_recent_activity_join_pgl
						}).render(activity[i]);
										
						break;
				}
				
				friend_content.find('ul#recent_activity').append(activity_html);
			}
			
			//render mutual promoters
			
			
			//render promoters
			var promoters = data.vc_friend.vc_promoters;
			for(var i in promoters){
				var promoter_html = new EJS({
				//	element: jQuery('div#ejs_friend_templates > div#ejs_friend_promoters').get(0)
					text: ejs_view_templates.friends_promoters
				}).render(promoters[i]);
				friend_content.find('ul#user_promoters').append(promoter_html);
			}
			
			/*
			//render venues
			var venues = data.vc_friend.vc_venues;
			for(var i in venues){
				var venues_html = new EJS({
					element: jQuery('div#ejs_friend_templates > div#ejs_friend_venues').get(0)
				}).render(venues[i]);
				friend_content.find('ul#user_venues').append(venues_html);
			}
			*/
			
			jQuery('div#loading_indicator').remove();
			friend_content.fadeIn(300, function(){});
			
			
		}
		
		
		
		
		
		
		
		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			console.log('unbind_callback');
			console.log(unload_items);
			console.log(timeout_cancels);
			console.log(custom_events_unbind);
			console.log(facebook_callbacks);
			
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