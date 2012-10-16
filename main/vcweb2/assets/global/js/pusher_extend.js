(function(Pusher){
	
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
	    
	    xhr.send('socket_id=' + encodeURIComponent(pusher.connection.socket_id) + '&channel_name=' + encodeURIComponent(self.name) + '&ci_csrf_token=' + csrf_token);
	};
	
})(Pusher);
