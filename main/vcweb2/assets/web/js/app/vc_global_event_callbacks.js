window.VC_Global_Event_Callbacks = {
	request_response: function(sticky_notifications, fadeIn){
		
		if(typeof fadeIn === 'undefined')
			fadeIn = true;
		
		for(var i in sticky_notifications){
			
			if(typeof sticky_notifications[i].notif_type === 'undefined')
				continue;
			
			
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
			
			jQuery('div#user_notifications').append(notification_html);
			
			if(fadeIn)
				jQuery('div#user_notifications > div.notification').fadeIn(700, function(){
					jQuery(this).css('display', 'block');
				});
			else
				jQuery('div#user_notifications > div.notification').css('display', 'block');
				
			
		}
		
	},
	sticky_notifications: []
};

//DELETE notifications when user logs out
window.EventHandlerObject.addListener('vc_logout', function(){
	jQuery('div#user_notifications').empty();
});

window.EventHandlerObject.addListener('vc_login', function(){
	
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
				
			window.VC_Global_Event_Callbacks.request_response(data, true);
				
		}
	});
	
});

jQuery.extend({
	
	randomize_array: function(arr){
		for(var j, x, i = arr.length; i; j = parseInt(Math.random() * i), x = arr[--i], arr[i] = arr[j], arr[j] = x);
		return arr;
	},
	
	superScroll: function(){
		
		if(jQuery(window).scrollTop() > 0)
			jQuery('html, body').animate({
			    scrollTop: 0
			}, 1000);
		
	},
    isIpad: function(){
        return navigator.userAgent.match(/ipad/i) != null;
    },
    /**
     * FQL query to retrieve user info for given uids
     */
    fbUserLookup: function(users, fields, callback){
    	fbEnsureInit(function(){
    		
    		if(users.length > 0){
    			
    			if(fields.length == 0)
    				fields = "uid, name, first_name, last_name, pic_square, pic_big";
    			
    			var fql = "SELECT " + fields + " FROM user WHERE ";
				for(var i = 0; i < users.length; i++){
					if(i == (users.length - 1)){
						fql += "uid = " + users[i];
					}else{
						fql += "uid = " + users[i] + " OR ";
					}
				}
				
				var query = FB.Data.query(fql);
    			query.wait(function(rows){
    				callback(rows);
    			});
    			
    		}else{
    			
    			callback([]);
    			
    		}
    		
    	});
    },
    fb_root_position: function(){
    	return;
    	var height = jQuery(window).height();
    	
//    	top:50%;  
//  left:50%;  
//  margin:-100px 0 0 -100px;  
    	
    	
    	
    	var style_html 	=  '<style type="text/css">';
    	style_html 		+= '	#fb-root{';
    	style_html 		+= '		position: fixed;';
    	style_html 		+= '		top: 50%;';
    	style_html 		+= '		left: 50%;';
    	style_html 		+= '	}';
    	style_html 		+= '</style>';
    	
    	//inject the css
    	jQuery('div#fb_style_inject').html(style_html);
    	
    }
});
	
	//Prevents browser from making http requests if you just specify a value for src in an img tag
	EjsView.prototype.image_insert = function(url, opts){
		var html = '<img src="' + url + '" ';
		
		if(opts)
			for(var key in opts)
				html += key + '="' + opts[key] + '" ';
				
		html += ' />';
		return html;
	}
	
	//Inserting links into inline text, helpful for translations
	EjsView.prototype.inline_link = function(path, text, opts){
		
		if(path.indexOf('http://') === 0 || path.indexOf('https://') === 0){
			var html = '<a href="' + path + '/" ';
		}else{
			var html = '<a href="' + window.location.protocol + '//' + window.location.host + '/' + path + '/" ';
		}
		
		if(opts)
			for(var key in opts)
				html += key + '="' + opts[key] + '" ';
		
		html += '>' + text + '</a>';
		return html;
	};
	
	//Convert public identifiers to url acceptable links
	EjsView.prototype.pi_link_convert = function(pid){
		
		return pid.replace(/\s+/g, '_').toLowerCase();
		
	};