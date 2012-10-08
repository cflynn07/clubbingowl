
jQuery(function(){
	
	jQuery('a#vc_fb_logout').live('click', function(){
		
		fbEnsureInit(function(){
			FB.logout(function(response){
			
				//unauthenticated user with authenticated session, update server and unset session
			//	VCAuth.prototype.session_logout();
				
			});
		});
		
		return false;
	});
	
	jQuery('a.vc_fb_login').live('click', function(){
		
		FB.login(function(){
			
		},{
			scope: 'email,publish_stream'
		});
		
		return false;
	});
	
});

(function(exports) {

	//class constructor
	var VCAuth = function(){
				
	};
	
	VCAuth.prototype.count = 0;
	
	VCAuth.prototype.session_login_locked = false;
	
	/**
	 * call server to initialize or update user session
	 * 
	 * @param	string [fb access_token]
	 * @return	null
	 */
	VCAuth.prototype.session_login = function(){
		
		if(VCAuth.prototype.session_login_locked)
			return; //There is already a login attempt in progress
		
		VCAuth.prototype.session_login_locked = true;
		
		var tempDate = new Date();
		var opts = {
			domain: ((window.location.host.indexOf('.vibecompass.dev') != -1) ? '.vibecompass.dev' : '.vibecompass.com'),
			path: '/',
			expiresAt: new Date(tempDate.getTime() + 63113851900),
			secure: false
		};
		jQuery.cookies.setOptions(opts);
		
//		jQuery('nav#navigation > ul.menu > li.login > span').html('<img src="' + window.module.Globals.prototype.front_assets + 'images/loader.gif" alt="" />');
		
		//cross-site request forgery token, accessed from session cookie
		//requires jQuery cookie plugin
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		var access_token = FB.getAccessToken();

		//send request for login job to server
		jQuery.ajax({
			url: '/ajax/auth/',
			type: 'post',
			data: {
					ci_csrf_token: cct,
					access_token: access_token,
					vc_method: 'session_login'
				  },
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
								
				/* ---------------- check job status ----------------- */
				var login_process = function(data){
					VCAuth.prototype.count = 0;
					VCAuth.prototype.session_login_locked = false;
					window.module.Globals.prototype.server_auth_session = true;
					
					//set local session data
					var vc_user = {
						first_name: data.first_name,
						last_name: data.last_name,
						vc_oauth_uid: data.oauth_uid,
						vc_promoter: false,
						vc_manager: false,
						vc_super_admin: false,
						host: false
					};
					
					if(data.promoter)
						vc_user.vc_promoter = true;
						
					if(data.manager)
						vc_user.vc_manager = true;
						
					if(data.super_admin)
						vc_user.vc_super_admin = true;
						
					if(data.host)
						vc_user.host = true;
					
					if(data.invitations)
						vc_user.invitations = data.invitations;
					
					jQuery.cookies.set('vc_user', vc_user);
																									
					//update menu
					window.module.VCAuth.prototype.update_menu();
					
					//trigger events that occur when vibecompass user completes login
					if(typeof EventHandlerObject !== 'undefined')
						EventHandlerObject.vc_login();
					
					console.log('job completed successfully on attempt: ' + (VCAuth.prototype.count + 1));
				}
				
				// ---------------------------------------------------------------
				
				var status_check = function(){
					
					console.log('status_check called');
					
					if(VCAuth.prototype.count > 9){
						VCAuth.prototype.count = 0;
						VCAuth.prototype.session_login_locked = false;
						VCAuth.prototype.update_menu();
						//alert('An error has occured while logging you into VibeCompass, please try again in a few minutes.');
						return;
					}
					
					jQuery.ajax({
						url: '/ajax/auth/',
						type: 'post',
						data: {ci_csrf_token: cct,
								status_check: true,
								vc_method: 'session_login'},
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
							
							if(data.success){
							
								login_process(data);
								
							}else{
								
								VCAuth.prototype.count = VCAuth.prototype.count + 1;
								setTimeout(status_check, 1000);
								
							}
							
						}
					});
					
				};
				/* ---------------- check job status ----------------- */
								
				console.log(VCAuth.prototype.count);
				
				if(data.oauth_uid){
					//quick login
					login_process(data);
					return;
				}
				
				//start first check 1 second after login request job sent
				setTimeout(status_check, 1000);
				
			},
			failure: function(){
				//ToDo: improve message
				alert('AJAX Failure, server failed to respond.');
			}
			
		});
	};
	
	/**
	 * call server to unset user session
	 * 
	 * @return	null
	 */
	VCAuth.prototype.session_logout = function(){
		
		var tempDate = new Date();
		var opts = {
			domain: ((window.location.host.indexOf('.vibecompass.dev') != -1) ? '.vibecompass.dev' : '.vibecompass.com'),
			path: '/',
			expiresAt: new Date(tempDate.getTime() + 63113851900),
			secure: false
		};
		jQuery.cookies.setOptions(opts);
		
		//cross-site request forgery token, accessed from session cookie
		//requires jQuery cookie plugin
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		
		jQuery.ajax({
			url: '/ajax/auth/',
			type: 'post',
			data: {ci_csrf_token: cct,
					vc_method: 'session_logout'},
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				if(data.success){
					//unset local session data
					jQuery.cookies.del('vc_user');
					
					//update menu
					window.module.VCAuth.prototype.update_menu();
					
					//trigger events that occur when vibecompass user completes login
					if(EventHandlerObject)
						EventHandlerObject.vc_logout();
				}
			},
			failure: function(){
				//ToDo: improve message
				alert('AJAX Failure, server failed to respond');
			}
		});
	};
	
	
	/**
	 * Updates menu bar for a given state as defined in the user's cookies
	 * 
	 * @return	null
	 */
	VCAuth.prototype.update_menu = function(){
		
		var tempDate = new Date();
		var opts = {
			domain: ((window.location.host.indexOf('.vibecompass.dev') != -1) ? '.vibecompass.dev' : '.vibecompass.com'),
			path: '/',
			expiresAt: new Date(tempDate.getTime() + 63113851900),
			secure: false
		};
		jQuery.cookies.setOptions(opts);
		
		//Get DOM node
		var nav_ul = jQuery('div#user > div.center > ul');
	
		//Do we have a logged in user?
		if(jQuery.cookies.get('vc_user')){
			//yes - known user
			
			var vc_user = jQuery.cookies.get('vc_user');
					
			//get & cache vc_user picture
			if(vc_user.pic_square){
			//	jQuery('nav#navigation > ul.menu > li.login > span').html('<img style="width:30px; height:30px;" src="' + vc_user.pic_square + '" />');
			}
			fbEnsureInit(function(){
				var fql = "SELECT pic_square FROM user WHERE uid = " + vc_user.vc_oauth_uid;
				var query = FB.Data.query(fql);
				query.wait(function(data){
					
					if(data.length == 0)
						return; //error
					
					if(!vc_user.pic_square){
						vc_user.pic_square = data[0].pic_square;
						jQuery.cookies.set('vc_user', vc_user);
				//		jQuery('nav#navigation > ul.menu > li.login > span').html('<img style="width:30px; height:30px;" src="' + vc_user.pic_square + '" />');
					}else{
						if(vc_user.pic_square != data[0].pic_square){
							vc_user.pic_square = data[0].pic_square;
							jQuery.cookies.set('vc_user', vc_user);
				//			jQuery('nav#navigation > ul.menu > li.login > span').html('<img style="width:30px; height:30px;" src="' + vc_user.pic_square + '" />');
						}
					}
					
				});
			});
			
	//		jQuery('nav#navigation > ul.menu > li.login > span').attr('class', 'arrow-up');
			
			var menu_html = '<li>Welcome <strong>' + vc_user.first_name + '</strong></li><li><a target="_new" href="' + window.module.Globals.prototype.front_link_base + 'profile/">Profile</a></li>';
			
			//Construct optional menu options
			if(vc_user.vc_promoter)
				menu_html += '<li><a target="_new" href="' + window.module.Globals.prototype.front_link_base + 'admin/promoters/">Promoter Admin Area</a></li>';
			
			if(vc_user.vc_manager)
				menu_html += '<li><a target="_new" href="' + window.module.Globals.prototype.front_link_base + 'admin/managers/">Manager Admin Area</a></li>';
			
			if(vc_user.vc_super_admin)
				menu_html += '<li><a target="_new" href="' + window.module.Globals.prototype.front_link_base + 'admin/super_admins/">Super Admin Area</a></li>';

			if(vc_user.host)
				menu_html += '<li><a target="_new" href="' + window.module.Globals.prototype.front_link_base + 'admin/hosts/">Host Admin Area</a></li>';
		
//			menu_html += '<li><a id="vc_fb_logout" href="#">Logout</a></li>'
			
			nav_ul.html(menu_html);
	
			
		}else{
			//no - unknown user
			
			//append before existing menu contents
			nav_ul.html('<li><span></span><a href="#" class="vc_fb_login"><img src="' + window.module.Globals.prototype.front_assets + 'images/connect-small.png' + '" alt="Facebook Login" style="vertical-align:middle;"/></a></li>');
			
//			jQuery('nav#navigation > ul.menu > li.login > span').html('Login').removeClass('arrow-up');
			
		}
	}
	
	/* ---------------------- class helper methods ---------------------- */

	/* ---------------------- / class helper methods ---------------------- */
	
	exports.module = exports.module || {};
	exports.module.VCAuth = VCAuth;

})(window);