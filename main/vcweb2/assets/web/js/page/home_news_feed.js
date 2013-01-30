if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.home_news_feed = function(){

		//multi-dimensional 2D array of event types, bounded elements, and callbacks
		//to be iterated over when pagechange occurs via pushState
		var unload_items = [];
		var timeout_cancels = [];
		var custom_events_unbind = [];
		var facebook_callbacks = [];

		
		//lock position of graphs on right side of page
		var lock_graphs_callback = function(){
													
			var header 						= jQuery('header#header');
			var news 						= jQuery('section#news');
			var news_side_content 			= jQuery('section#news div#side_data_content');
			var side_data_content_tracker 	= jQuery('section#news div#side_data_content_tracker');
			var news_feed_side_data			= jQuery('section#news div#news_feed_side_data');
	
			if(jQuery(this).width() > 991){
				
				if((side_data_content_tracker.offset().top - (header.offset().top + header.height()) <= 30)){
				
					news_side_content.addClass('fixed_side');
						
				}else{
					
					news_side_content.removeClass('fixed_side');
					
				}
				
			}
			
			if(jQuery(this).width() > 767 && jQuery(this).width() <= 991){
				
				if(side_data_content_tracker.offset().top - (jQuery(this).scrollTop()) <= 30){
					
					news_side_content.addClass('fixed_side');
						
				}else{
					
					news_side_content.removeClass('fixed_side');
					
				}
				
			}
						
		};
		jQuery(window).scroll(lock_graphs_callback);
		unload_items.push([
			'scroll',
			jQuery(window),
			lock_graphs_callback
		]);
		
		jQuery(window).resize(lock_graphs_callback);
		unload_items.push([
			'resize',
			jQuery(window),
			lock_graphs_callback
		]);
		lock_graphs_callback();
		
		
		
		var news_feed_items = {
			retrieve_feed_lock: false,
			items_iterator: false,
			retrieve_feed: function(request_first){							
							
				if(news_feed_items.retrieve_feed_lock)
					return;
								
				//while loading data, prevent this method from firing again
				news_feed_items.retrieve_feed_lock = true;
				
				//show loading indicator
				if(jQuery('div#notifications_holder div#loading_indicator').length == 0)
					jQuery('div#notifications_holder section#news').append('<div id="loading_indicator"><img src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" alt="loading..." /></div>');
				
				var incrementor = 0;
				
				var retrieve_function = function(){
									
					if(incrementor > 4){
						incrementor = 0;
						return; //failed
					}
					
					
					jQuery.background_ajax({
						data: {
							vc_method: 'news_feed_retrieve',
							status_check: true
						},
						success: function(data){
							
							if(data.trigger_refresh){
								window.location.reload();
								return;
							}
							
							if(data.success){
															
								news_feed_items.append_item_html(data);	
								incrementor = 0;
								
								if(data.message.data.length > 7)
									news_feed_items.retrieve_feed_lock = false; //free to load more data
								//otherwise it stays locked
								
							}else{
								
								
								
								incrementor++;
								var timeout = setTimeout(retrieve_function, 1000);
								timeout_cancels.push(timeout);
								
							}
							
							
						}
					});
					
					
					/*
					//cross-site request forgery token, accessed from session cookie
					//requires jQuery cookie plugin
					var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
					
					jQuery.ajax({
						url: window.location.href,
						type: 'post',
						data: {
							 	ci_csrf_token: cct,
								vc_method: 'news_feed_retrieve',
								status_check: true
							  },
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
											
							if(data.success){
															
								news_feed_items.append_item_html(data);	
								incrementor = 0;
								
								if(data.message.data.length > 7)
									news_feed_items.retrieve_feed_lock = false; //free to load more data
								//otherwise it stays locked
								
							}else{
								
								incrementor++;
								var timeout = setTimeout(retrieve_function, 1000);
								timeout_cancels.push(timeout);
								
							}
						
						}
					});
					
					*/
					
				}
				
				//If this is an initial request, start job w/ server
				if(request_first){
					
					
					jQuery.background_ajax({
						data: {
							vc_method: 'news_feed_retrieve',
							iterator: 	news_feed_items.items_iterator
						},
						success: function(data){
							
							
							if(data.success){
								//start first check 1 second after
								var timeout = setTimeout(retrieve_function, 1000);
								timeout_cancels.push(timeout);
							}
							
							
						}
					});
					
					
					
					/*
					
					
					//cross-site request forgery token, accessed from session cookie
					//requires jQuery cookie plugin
					var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
					
					jQuery.ajax({
						url: window.location,
						type: 'post',
						data: {
							ci_csrf_token: cct,
							vc_method: 'news_feed_retrieve',
							iterator: news_feed_items.items_iterator
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
					
					*/
					
					
					
					
					return;
				}else{
					retrieve_function();
				}
				
			},
			append_item_html: function(data){
				
				//data.message.iterator_position;
				//data.message.data
			
				jQuery('div#notifications_holder section#news div#loading_indicator').remove();
			
				if(!data.message.iterator_position && !data.message.data.length){
					//no friend activity
					
					var html = new EJS({
						text: window.ejs_view_templates.primary_no_friends
					}).render({});
					
					jQuery('section#news').append(html);
					return;
				}
				
				
				
				
				if(data.message && data.message.user_friends_vc_obj_pop)
					news_feed_items.generate_pop_charts(data.message.user_friends_vc_obj_pop);
				
				list_data = data.message.data;
				var news_html;
							
				for(i in list_data){
					
					var news_item = list_data[i];
					
					var guest_list_data = jQuery.parseJSON(news_item.un_notification_data);
					var pic_square = data.message.user_friends_pics[news_item.un_vibecompass_id];
					jQuery.extend(news_item, {
						guest_list_data: guest_list_data,
						pic_square: pic_square
					});
									
					switch(news_item.un_notification_type){
						case 'join_promoter_guest_list':
												
							news_html = new EJS({
							//	element: jQuery('div#ejs_home_templates > div#join_promoter_guest_list').get(0)
								text: ejs_view_templates.primary_join_promoter_gl
							}).render(news_item);
							news_html = jQuery(news_html);
							
							console.log(news_html);
							
							news_html.find('a').addClass('ajaxify_t3');
														
							break;
						case 'join_team_guest_list':
						
							console.log('join_team_guest_list');
							console.log(news_item);
							
							news_html = new EJS({
							//	element: jQuery('div#ejs_home_templates > div#join_team_guest_list').get(0)
								text: ejs_view_templates.primary_join_team_gl
							}).render(news_item);
							news_html = jQuery(news_html);
							news_html.find('a').addClass('ajaxify_t3');
						
							break;
						case 'join_vibecompass':
						
						
							news_html = new EJS({
							//	element: jQuery('div#ejs_home_templates > div#join_vibecompass').get(0)
								text: ejs_view_templates.primary_join_vibecompass
							}).render(news_item);
						
							var pic_square = data.message.user_friends_pics[news_item.un_vibecompass_id];
						
							break;
						case 'promoter_new_gl_status':
							
							console.log('promoter_new_gl_status ====== ');
							
							news_item.un_notification_data = jQuery.parseJSON(news_item.un_notification_data);

							news_html = new EJS({
								text: ejs_view_templates.primary_new_promoter_gl_status
							}).render(news_item);
						//	news_html = jQuery(news_html);
						//	news_html.find('a').addClass('ajaxify_t3');
							
							
							
							
							break;
						case 'team_new_gl_status':
							
							console.log('team_new_gl_status ====== ');
							
							news_item.un_notification_data = jQuery.parseJSON(news_item.un_notification_data);
							console.log(news_item);

							news_html = new EJS({
								text: ejs_view_templates.primary_new_team_gl_status
							}).render(news_item);
						//	news_html = jQuery(news_html);
						//	news_html.find('a').addClass('ajaxify_t3');
						
						
							
							
							break;
						default:
							break;
					}

					jQuery('div#notifications_holder ul.updates').append(news_html);
					
				}
							
				//lastly, save iterator for additional data requests
				news_feed_items.items_iterator = data.message.iterator_position;
								
			},
			generate_pop_charts: function(data){
				
				var div = jQuery('div#side_data_content');
				
				//clear out any data that might be existing
				div.find('#trending_gl > ul').empty();
								
				if(data.length === 0){
				//	div.find('#trending_gl > ul').html(jQuery('div#ejs_home_templates > div#pop_guestlist_empty').html());

					jQuery('div#news_feed_side_data').hide();
					jQuery('ul.updates').css({
						width: '100%'
					});
				
				}
				
				
				
				for(var i in data){
					
					var html = new EJS({
				//		element: jQuery('div#ejs_home_templates > div#pop_guestlist').get(0)
						text: ejs_view_templates.primary_pop_guestlist
					}).render(data[i]);
					html = jQuery(html);
					html.find('a').addClass('ajaxify_t3');
					
					console.log(html);
					
					div.find('#trending_gl > ul').append(html);
					
				}
				
				div.css('display', 'block');
				jQuery(window).trigger('scroll');
			}
		};
		
					
					
					
		if(window.vc_server_auth_session)
			news_feed_items.retrieve_feed(false);
		else
			news_feed_items.retrieve_feed_lock = true;





		var vc_login_callback = function(){
				
				
			//temporary		
			jQuery('a.nav_link[title=Venues]').trigger('click');
			return;			
					
					
						
			var notifications_holder 	= jQuery('div#notifications_holder');
			var unauth_content_holder 	= jQuery('div#unauth_content_holder');
			
			//show if not already showing
			if(unauth_content_holder.css('display') == 'block'){
				
				unauth_content_holder.css('display', 'none');
				notifications_holder.css('display', 'block');
				
				news_feed_items.retrieve_feed_lock = false;
				news_feed_items.retrieve_feed(true); //retrieve first
				
			}
			
		};
		window.EventHandlerObject.addListener("vc_login", vc_login_callback);
		custom_events_unbind.push([
			'vc_login',
			window.EventHandlerObject,
			vc_login_callback
		]);
		
		
		
		
		var vc_logout_callback = function(){
			var notifications_holder = jQuery('div#notifications_holder');
			var unauth_content_holder = jQuery('div#unauth_content_holder');
						
			//empty contents of notifications_holder
			notifications_holder.find('section#news ul.updates').empty();
			
			//prevent browser from trying to look up feed
			news_feed_items.retrieve_feed_lock = true;
			news_feed_items.items_iterator = false;
			
			//show if not already showing
			if(notifications_holder.css('display') == 'block'){
				
				unauth_content_holder.css('display', 'block');
				notifications_holder.css('display', 'none');
			
			}
		}
		window.EventHandlerObject.addListener("vc_logout", vc_logout_callback);
		custom_events_unbind.push([
			'vc_logout',
			window.EventHandlerObject,
			vc_logout_callback
		]);
		
		
		
		
		
		
		var scroll_callback = function() {
		   	if(jQuery(window).scrollTop() >= (jQuery(document).height() - jQuery(window).height() - 250)){
		
				news_feed_items.retrieve_feed(true);
		
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