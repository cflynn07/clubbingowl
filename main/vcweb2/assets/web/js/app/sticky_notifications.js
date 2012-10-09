window.vc_display_sticky_notifications = function(){
	
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
		
		jQuery(this).parents('div.notification').remove();
		
		return false;
	});
	
	
	var sticky_notifications = window.vc_sticky_notifications;
	
	if(window.vc_sticky_notifications.length === 0)
		return;
	
	window.VC_Global_Event_Callbacks.request_response(sticky_notifications, false);
	
	for(var i in sticky_notifications){
		window.VC_Global_Event_Callbacks.sticky_notifications.push(sticky_notifications[i]);
	}
		
};