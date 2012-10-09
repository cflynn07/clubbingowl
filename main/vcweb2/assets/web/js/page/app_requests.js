if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.app_requests = function(){
		
		
		//multi-dimensional 2D array of event types, bounded elements, and callbacks
		//to be iterated over when pagechange occurs via pushState
		var unload_items = [];
		var timeout_cancels = [];
		var custom_events_unbind = [];
		var facebook_callbacks = [];
			
		var requests_items = {
			retrieve_lock: false,
			retrieve_requests: function(){
				
				if(requests_items.retrieve_lock)
					return;
					
				requests_items.retrieve_lock = true;
				
				var incrementor = 0;
				
				var retrieve_function = function(){
				
					if(incrementor > 4){
						incrementor = 0;
						return; //error has occured
					}
					
					//cross-site request forgery token, accessed from session cookie
					//requires jQuery cookie plugin
					var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
					
					jQuery.ajax({
						url: window.location,
						type: 'post',
						data: {
							 	ci_csrf_token: cct,
								vc_method: 'request_feed_retrieve',
								status_check: true
							  },
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
						
							if(data.success){
										
								requests_items.retrieve_lock = false;
								requests_items.display_requests(data.message);
								
							}else{
								
								incrementor++;
								var timeout = setTimeout(retrieve_function, 2500);
								timeout_cancels.push(timeout);
								
							}
						
						}
					});
					
				}
				
				var timeout = window.setTimeout(retrieve_function, 2500);
				timeout_cancels.push(timeout);
				
			},
			display_requests: function(requests){
										
				var requests_updates_ul = jQuery('ul#requests_updates_ul');
				var users = [];
				
				for(var i in requests){
					
					if(typeof requests[i].data == 'string'){
						requests[i].data = JSON.parse(requests[i].data);
					}
					
					if(requests[i].from && requests[i].from.id){
						users.push(requests[i].from.id);
					}
					
					switch(requests[i].data.type){
						case 0:
						
							if(requests[i].data.gl_type == 0){
								//promoter
								var html = new EJS({
									element: jQuery('div#ejs_requests_templates > div#add_promoter_gl').get(0)
								}).render(requests[i]);
							}else if(requests[i].data.gl_type == 1){
								//team
								var html = new EJS({
									element: jQuery('div#ejs_requests_templates > div#add_team_gl').get(0)
								}).render(requests[i]);
							}
							
							break;
						case 1:
							var html = new EJS({
								element: jQuery('div#ejs_requests_templates > div#invite').get(0)
							}).render(requests[i]);
							break;
						case 2:
							var html = new EJS({
								element: jQuery('div#ejs_requests_templates > div#promoter_add_gl_manual').get(0)
							}).render(requests[i]);
							break;
						case 3:
							var html = new EJS({
								element: jQuery('div#ejs_requests_templates > div#team_add_gl_manual').get(0)
							}).render(requests[i]);
							break;
						case 4:
							var html = new EJS({
								element: jQuery('div#ejs_requests_templates > div#manager_promoter_invite').get(0)
							}).render(requests[i]);
							break;
						case 5:
							var html = new EJS({
								element: jQuery('div#ejs_requests_templates > div#manager_host_invite').get(0)
							}).render(requests[i]);
							break;
						default:
							break;
					}
					
					requests_updates_ul.append(html);
					
				}
				
								
				var fb_callback = function(rows){
					
					for(var i in rows){
					
						jQuery('a.link_' + rows[i].uid).attr('href', '/friends/' + rows[i].third_party_id + '/');
						jQuery('div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="' + rows[i].name + '" />');
						
					}
					
					jQuery('#loading_indicator').css('display', 'none');
					requests_updates_ul.css('display', 'block');
					
				};
				jQuery.fbUserLookup(users, 'uid, pic_square, name, third_party_id', fb_callback);
				facebook_callbacks.push(fb_callback);
		
		
			}
		}
		
		
		
		
		requests_items.retrieve_requests();
		
		
		
		
		
		
		var vc_login_callback = function(){
				
			var auth_content = jQuery('.auth_content');
			var unauth_content = jQuery('.unauth_content');
			
			console.log('vc_login');
			
			//add name
			var vc_user = jQuery.cookies.get('vc_user');
			jQuery('span#fname_greeting').html(vc_user.first_name);
			
			unauth_content.css('display', 'none');
			auth_content.css('display', 'block');
				
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