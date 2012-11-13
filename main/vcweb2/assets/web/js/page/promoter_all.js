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
		
		
		
		
		
		jQuery('.star').rating();
		jQuery('a#reviews_explain').bind('click', function(e){
			e.preventDefault();
			
			jQuery('div#modal_reviews_explain').dialog({
				modal: true,
				width: 400,
				height: 400,
				resizable: false
			});
			
			return false;
		});
		
		

		
		
					
				
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
								
				var template = window.ejs_view_templates.promoters_profile_friend_reviews;
				var html = new EJS({
					text: template
				}).render(data);
				
				
				jQuery('div#pro_user_reviews').html(html);
				jQuery('.star').rating();
				
				jQuery('div.rating > div.stars > div.stars-on').css({
					width: ((data.average / 5) * 100) + '%'
				});
				
			}

		};
				
				
				
		if(window.vc_server_auth_session){
			if(window.u_up_pop !== false){
				page_items.display(jQuery.parseJSON(window.u_up_pop));
				delete window.u_up_pop;
			}else{
				//go fetch...
				page_items.retrieve_feed(false);
			}	
					
		}
	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		var vc_login_callback = function(){
		
			var auth_content = jQuery('.auth_content');
			var unauth_content = jQuery('.unauth_content');
			
			console.log('vc_login');
			
			unauth_content.hide();
			auth_content.show();
			
			page_items.retrieve_feed(true);
			
		};
		window.EventHandlerObject.addListener("vc_login", vc_login_callback);
		
		


		var vc_logout_callback = function(){
			
			delete window.u_up_pop;
			
			var auth_content = jQuery('.auth_content');
			var unauth_content = jQuery('.unauth_content');
			
			console.log('vc_logout');
	
			unauth_content.show();
			auth_content.hide();
	
			//clean up previously inserted content			
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
			
			delete window.u_up_pop;
			
			jQuery('div#modal_reviews_explain').dialog('close').remove();
			
		}
			
		
	}
	
});