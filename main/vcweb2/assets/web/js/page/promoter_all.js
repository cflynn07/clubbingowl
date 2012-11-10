if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.promoter_all = function(){


		jQuery('#add_as_friend').css({
			cursor: 'pointer'
		}).bind('click', function(){
			
			fbEnsureInit(function(){
				
				FB.ui({
				    method: 'friends.add',
				    id: 	window.vc_promoter_oauth
				}, function(param) {
					
				}); 
				
			});
			
		});
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		var vc_user = jQuery.cookies.get('vc_user');
		//store it...
		var promoter_user_key = 'pro_' + window.vc_promoter_oauth + '_user_' + vc_user.vc_oauth_uid + '_popularity';
		
		
		
		
		
		
					
				
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
							vc_method: 'promoter_friend_popularity_retrieve',
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
							vc_method: 'promoter_friend_popularity_retrieve'
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
				
				jQuery.jStorage.set(promoter_user_key, data);
				jQuery.jStorage.setTTL(promoter_user_key, (1000 * 60 * 10)); //10 minutes...
			
			}

		};
				
		if(window.vc_server_auth_session){
			
			var pop = jQuery.jStorage.get(promoter_user_key);
			
			if(pop){
				//we have it on file...
				page_items.display(pop);
				
			}else{
				//need to go get it...
				page_items.retrieve_feed(false);
				
			}
					
		}
	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
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
			
			var friend_add = jQuery('#add_as_friend');
			friend_add.unbind();	
			
			window.EventHandlerObject.removeListener('vc_logout', vc_logout_callback);
			window.EventHandlerObject.removeListener('vc_login', vc_login_callback);
			
			
		}
			
		
	}
	
});