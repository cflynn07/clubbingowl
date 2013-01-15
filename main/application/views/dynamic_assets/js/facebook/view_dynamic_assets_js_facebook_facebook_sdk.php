window.fbAsyncInit = function() {
    FB.init({appId: '<?= $central->facebook_app_id ?>',
    		status: true,
    		cookie: true,
             xfbml: true});
             
    //Detect if user is logged into facebook, update session accordingly
    window.test_login_status = function(){
		FB.getLoginStatus(function(response) {
			
			console.log('getLoginStatus');
			console.log(response);
					
			if(response.authResponse){
				
				console.log(jQuery.cookies.get('vc_user'));
				
				//we don't know any users, log this facebook user into vibecompass
				if(!jQuery.cookies.get('vc_user')){
					
					window.module.VCAuth.prototype.session_login();
					return;
					
				}
				
				//also log user in if known user to VC is different from current facebook user
				var vc_user = jQuery.cookies.get('vc_user');
				if(vc_user.vc_oauth_uid != response.authResponse.userID){
					
	    			//authenticated user unknown to server, update server and set session
	    			window.module.VCAuth.prototype.session_login();
	    			return;
	    			
	    		}
	 
	 			//Server and client lost sync, correct. Server session = false, client session = true, update server
	 			if(window.module.Globals.prototype.server_auth_session == false){
	    			window.module.VCAuth.prototype.session_login();
	    			return;
	    		}
	    		
	    		//trigger events that occur when vibecompass user completes login
				if(EventHandlerObject)
					EventHandlerObject.vc_login();
				
	      	}else{
	      		      		
	    		if(jQuery.cookies.get('vc_user')){
	    			
	    			//unauthenticated user with authenticated session, update server and unset session
	    			window.module.VCAuth.prototype.session_logout();
	    			return;
	    			
	    		}
	    		
	    		 //Server and client lost sync, correct. Server session = true, client session = false, update server
	 			if(window.module.Globals.prototype.server_auth_session == true){
	    			window.module.VCAuth.prototype.session_logout();
	    			return;
	    		}
				
	    		
	    	}
	    	
	    	
			
		});
		
		
	//	setTimeout(function(){
	//		window.test_login_status();
	//	}, 300);
	};	
	window.test_login_status();
	
	
	
	
	FB.Event.subscript('auth.statusChange', function(response){
		
		console.log('auth.statusChange');
		window.test_login_status();
	});
	
	
	FB.Event.subscribe('auth.login', function(response){
		
		console.log('auth.login');
		
		//we don't know any users, log this facebook user into vibecompass
		if(!jQuery.cookies.get('vc_user')){
			
			window.module.VCAuth.prototype.session_login();
			return;
			
		}
		
		//also log user in if known user to VC is different from current facebook user
		var vc_user = jQuery.cookies.get('vc_user');
		if(vc_user.vc_oauth_uid != response.authResponse.userID){
			
			//authenticated user unknown to server, update server and set session
			window.module.VCAuth.prototype.session_login();
			
		}
		
	});
	
	FB.Event.subscribe('auth.logout', function(response){
		
		console.log('auth.logout');
		
		if(FB.getAccessToken() == null){ 
						
			if(jQuery.cookies.get('vc_user')){
				//unauthenticated user with authenticated session, update server and unset session
				window.module.VCAuth.prototype.session_logout();
			}
				
		} 
		
	});
  
  	//resize canvas
  	
  	FB.Canvas.setAutoGrow(100);
  	
  //	FB.Canvas.setAutoResize()
  	//indicates facebook has completed loading
  	fbApiInit = true;
};
  
  //used to load code within body after facebook init complete
  function fbEnsureInit(callback) {
        if(!window.fbApiInit) {
            setTimeout(function() {fbEnsureInit(callback);}, 50);
        } else {
            if(callback) {
                callback();
            }
        }
    }
  
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());