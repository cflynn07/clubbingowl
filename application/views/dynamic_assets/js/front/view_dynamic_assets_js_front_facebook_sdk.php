window.fbAsyncInit = function() {
    FB.init({appId: '<?= $central->facebook_app_id ?>',
    		status: true,
    		cookie: true,
             xfbml: true});
             
    //Detect if user is logged into facebook, update session accordingly
	FB.getLoginStatus(function(response) {
		
		console.log('getLoginStatus');
		console.log(response.authResponse);
				
		if(response.authResponse){
			
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
 			if(window.vc_server_auth_session == false){
    			window.module.VCAuth.prototype.session_login();
    			return;
    		}
			
      	}else{
      		      		
    		if(jQuery.cookies.get('vc_user')){
    			
    			//unauthenticated user with authenticated session, update server and unset session
    			window.module.VCAuth.prototype.session_logout();
    			return;
    			
    		}
    		
    		 //Server and client lost sync, correct. Server session = true, client session = false, update server
 			if(window.vc_server_auth_session == true){
    			window.module.VCAuth.prototype.session_logout();
    			return;
    		}
			
    		
    	}
		
	});
	
	FB.Event.subscribe('auth.login', function(response){
				
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
		
				
		if(FB.getAccessToken() == null){ 
						
			if(jQuery.cookies.get('vc_user')){
				//unauthenticated user with authenticated session, update server and unset session
				window.module.VCAuth.prototype.session_logout();
			}
				
		} 
		
	});
  
  	
  	//indicates facebook has completed loading
  	fbApiInit = true;
};
  
//used to load code within body after facebook init complete
function fbEnsureInit(callback){
	if(!window.fbApiInit){
		setTimeout(function() {fbEnsureInit(callback);}, 50);
	}else{
		if(callback){
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
  
window.EventHandlerObject.addListener("vc_login", function(){
	
	window.module.VCAuth.prototype.display_invitations();
	
});