(function(){
	
	window.EventHandlerObject.addListener('vc_logout', function(){
		jQuery('div#user_notifications').empty();
	});
	
	window.EventHandlerObject.addListener('vc_login', function(){
		
		window.vc_fetch_sticky_notifications();
		
	});
	
	
	
	window.vc_fetch_sticky_notifications = function(){
		
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		
		jQuery.ajax({
			url: '/ajax/notifications/',
			type: 'post',
			data: {
				 	ci_csrf_token: cct,
					vc_method: 'retrieve_all_sticky_notifications'
				  },
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				
				console.log(data);
				window.vc_sticky_notifications = data;
				window.vc_display_sticky_notifications();
									
			}
		});
		
		
	};
	
	
	
	
	
	window.vc_display_sticky_notifications = function(){
		jQuery(function(){
						
			jQuery('div#user_notifications a.notification_close').die();
			jQuery('div#user_notifications a.notification_close').live('click', function(e){
				
				e.preventDefault();
						
				var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
				
				jQuery.ajax({
					url: '/ajax/notifications/',
					type: 'post',
					data: {
						ci_csrf_token: cct,
						vc_method: 'sticky_notification_read',
						notification_id: jQuery(this).parents('div.notification').find('span.notification_id').html()
					},
					cache: false,
					dataType: 'json',
					success: function(data, textStatus, jqXHR){
						
					}
				});
				
				jQuery(this).parents('div.notification').fadeOut();
				
				return false;
			});
			
			var sticky_notifications = window.vc_sticky_notifications;
			
			if(sticky_notifications.length === 0)
				return;
		
	
		
			for(var i in sticky_notifications){
				
				console.log(sticky_notifications[i]);
					
				//if(typeof sticky_notifications[i].notif_type === 'undefined')
				//	continue;
				
				
				if(sticky_notifications[i].data){
					
					console.log('p1');
									
					//ensure id property is always set for view
					var data = jQuery.parseJSON(sticky_notifications[i].data);
					data.id = sticky_notifications[i].id;
					
					if(typeof data.promoter_name !== 'undefined'){
						data.notif_type = 'promoter';
					}else{
						data.notif_type = 'team';
					}
					
					console.log(data);
								
					var notification_html = new EJS({
					//	element: jQuery('div#ejs_global_notification_templates > div#request_response').get(0)
						text: ejs_view_templates.notifications_request_response
					}).render(data);
				}else{
					
					console.log('p2');
					
					var notification_html = new EJS({
					//	element: jQuery('div#ejs_global_notification_templates > div#request_response').get(0)
						text: ejs_view_templates.notifications_request_response
					}).render(sticky_notifications[i]);
				}
				
				console.log(notification_html);
				
				jQuery('div#user_notifications').append(notification_html);
				
			//	if(fadeIn)
			//		jQuery('div#user_notifications > div.notification').fadeIn(700, function(){
			//			jQuery(this).css('display', 'block');
			//		});
			//	else
					jQuery('div#user_notifications > div.notification').css('display', 'block');
					
				
			}
		});
	};
	
})();