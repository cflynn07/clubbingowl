if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	var already_connected = false;
	var	up_id = '';
	
	
	window.vc_page_scripts.promoter_pusher_presence_channels = function(){
				
		var pvars = window.promoter_pusher_presence_vars;
		
		var promoter_oauth_uid 	= pvars.up_users_oauth_uid,
			t_fan_page_id 		= pvars.t_fan_page_id, 
			up_id 				= pvars.up_id,
			pusher_api_key 		= pvars.pusher_api_key;			
			
		
		if(!window.vc_server_auth_session)
			return;
	
	
	
		if(already_connected && up_id == pvars.up_id)
			return;
		else{
			already_connected = true;
			up_id == pvars.up_id;
		}
		
		
		console.log('subscribed pusher presence channel');
	
		Pusher.channel_auth_endpoint = '/ajax/pusher_presence/';
	
		Pusher.authorizers.ajax = function(pusher, callback){
			var self = this, xhr;
	
		    if (Pusher.XHR) {
		      xhr = new Pusher.XHR();
		    } else {
		      xhr = (window.XMLHttpRequest ? new window.XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP"));
		    }
		
		    xhr.open("POST", Pusher.channel_auth_endpoint, true);
		    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
		    xhr.onreadystatechange = function() {
		      if (xhr.readyState == 4) {
		        if (xhr.status == 200) {
		          var data, parsed = false;
		
		          try {
		            data = JSON.parse(xhr.responseText);
		            parsed = true;
		          } catch (e) {
		            callback(true, 'JSON returned from webapp was invalid, yet status code was 200. Data was: ' + xhr.responseText);
		          }
		
		          if (parsed) { // prevents double execution.
		            callback(false, data);
		          }
		        } else {
		          Pusher.warn("Couldn't get auth info from your webapp", status);
		          callback(true, xhr.status);
		        }
		      }
		    };
		    
		    var csrf_token = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		    
		    xhr.send('socket_id=' + encodeURIComponent(pusher.connection.socket_id) + '&channel_name=' + encodeURIComponent(self.name) + '&ci_csrf_token=' + csrf_token + '&promoter_id=' + up_id);
		};
		
		
		
		var pusher = new Pusher(pusher_api_key);
		var presenceChannel = pusher.subscribe('presence-promotervisitors-' + promoter_oauth_uid);
		var presenceChannelTeam = pusher.subscribe('presence-teamvisitors-' + t_fan_page_id);
		
		window.kill_presence_channel = function(){
			
			console.log('unsubscribed promoter presence channels');
			
			pusher.unsubscribe('presence-promotervisitors-' + promoter_oauth_uid);
			pusher.unsubscribe('presence-teamvisitors-' + t_fan_page_id);
			
			already_connected = false;
			
		}
		
	}
	
});