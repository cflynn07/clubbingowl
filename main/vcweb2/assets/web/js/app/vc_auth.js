jQuery(function(){
	
	jQuery('a#vc_fb_logout').live('click', function(){
		
		fbEnsureInit(function(){
			FB.logout(function(response){
			
				//unauthenticated user with authenticated session, update server and unset session
				//VCAuth.prototype.session_logout();
				
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
		
		
		jQuery('nav#navigation > ul.menu > li.login > span').html('<img src="' + window.module.Globals.prototype.front_assets + 'images/loader.gif" alt="" />');
		
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
				
				jQuery('nav#navigation > ul.menu > li.login > div#login-drop').css('display', 'none');
				
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
					
					if(data.users_phone_number){
						vc_user.users_phone_number = data.users_phone_number;
					}
					
					jQuery.cookies.set('vc_user', vc_user);
					
					VCAuth.prototype.display_invitations();
						
					window.vc_server_auth_session = true;										
																
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
						jQuery('nav#navigation > ul.menu > li.login > div#login-drop').removeAttr('style');
						//alert('An error has occured while logging you into ClubbingOwl, please try again in a few minutes.');
						return;
					}
					
					jQuery.ajax({
						url: '/ajax/auth/',
						type: 'post',
						data: {
							ci_csrf_token: 	cct,
							status_check: 	true,
							vc_method: 		'session_login'
						},
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
		
		//cross-site request forgery token, accessed from session cookie
		//requires jQuery cookie plugin
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		
		jQuery.ajax({
			url: '/ajax/auth/',
			type: 'post',
			data: {
				ci_csrf_token: cct,
				vc_method: 'session_logout'
			},
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
	
	
	
	
	
	
	
	var lock_scroll_top = function(){
		var resize_scroll_callback = function(){
													
			var header 	= jQuery('header#header');
			var user 	= jQuery('div#user');
			var spacer 	= jQuery('div#spacer');
			
			
			
			
			top_offset = 0;
			if(user.css('display') == 'block')
				top_offset += 35;
						
			if(jQuery(this).width() <= 991){
				top_offset += header.find('nav#navigation').height();
			}
					
					
					
			if((top_offset - jQuery(this).scrollTop()) < 1){
				//lock to top
				
				
				if(jQuery(this).width() > 991){
					
					if(header.css('position') == 'relative'){
						
						header.css({
							position:	'fixed',
							top:		'0px',
							width:		'100%',
							'z-index':	99998
						});
						spacer.css({
							'height': header.height() + 'px'
						});
						
					}
					
				}else{
					
					if(header.css('position') == 'relative'){
					
						header.find('nav#navigation').css({
							position:	'fixed',
							top:		'0px',
							width:		'100%',
							'z-index':	99998
						});
						spacer.css({
							'height': header.height() + 'px'
						});
						
					}
					
				}
				
			}else{
				//allow to scroll down
				
				header.removeAttr('style');
				header.find('nav#navigation').removeAttr('style');
				spacer.removeAttr('style');
				
			}		
						
						
		/*				
						
						//991
			if(jQuery(this).width() > 991 && (top_offset - jQuery(this).scrollTop()) < 1){
				//lock to top
				
				
				if(header.css('position') == 'relative'){
					
					header.css({
						position:	'fixed',
						top:		'0px',
						width:		'100%',
						'z-index':	99998
					});
					spacer.css({
						'height': header.height() + 'px'
					});
					
				}
				
			}else{
				//allow to scroll down
				
				header.removeAttr('style');
				spacer.removeAttr('style');
				
			}
						
		*/				
						
						
						
						
		};
		
		EventHandlerObject.addListener('vc_login', function(){ 
			jQuery.superScroll();
		});
		
		jQuery('ul.menu li.authenticated').live('click', function(){
			jQuery.superScroll();
		});
		jQuery(window).scroll(resize_scroll_callback);
		jQuery(window).resize(resize_scroll_callback);
		
		jQuery(window).bind('touchmove', resize_scroll_callback);
		
		var timeout_func = function(){
			resize_scroll_callback();
			window.setTimeout(timeout_func, 50);
		};
		
	}
	
	
	
	
	
	
	
	/**
	 * Updates menu bar for a given state as defined in the user's cookies
	 * 
	 * @return	null
	 */
	VCAuth.prototype.update_menu = function(){
		
		//Get DOM node
		var nav_ul = jQuery('div#user > div.center > ul');
	
		//Do we have a logged in user?
		if(jQuery.cookies.get('vc_user')){
			//yes - known user
			
			jQuery('nav#navigation > ul.menu > li.login').addClass('authenticated');
			
			var vc_user = jQuery.cookies.get('vc_user');
			jQuery('nav#navigation > ul.menu > li.login > div#login-drop').css('display', 'none');
					
			//get & cache vc_user picture
			if(vc_user.pic_square){
				jQuery('nav#navigation > ul.menu > li.login > span').html('<img style="width:30px; height:30px;" src="' + vc_user.pic_square + '" />');
			}
			fbEnsureInit(function(){
												
				var fql = "SELECT pic_square FROM user WHERE uid = me()";// + vc_user.vc_oauth_uid;
				FB.api({
					method: 'fql.query',
					query: fql
				}, function(data){
					if(data.length == 0)
						return; //error
					
					if(!vc_user.pic_square){
						vc_user.pic_square = data[0].pic_square;
						jQuery.cookies.set('vc_user', vc_user);
						jQuery('nav#navigation > ul.menu > li.login > span').html('<img style="width:30px; height:30px;" src="' + vc_user.pic_square + '" />');
					}else{
						if(vc_user.pic_square != data[0].pic_square){
							vc_user.pic_square = data[0].pic_square;
							jQuery.cookies.set('vc_user', vc_user);
							jQuery('nav#navigation > ul.menu > li.login > span').html('<img style="width:30px; height:30px;" src="' + vc_user.pic_square + '" />');
						}
					}
				});
				
				
				/*
				var query = FB.Data.query(fql);
				query.wait(function(data){
					
					if(data.length == 0)
						return; //error
					
					if(!vc_user.pic_square){
						vc_user.pic_square = data[0].pic_square;
						jQuery.cookies.set('vc_user', vc_user);
						jQuery('nav#navigation > ul.menu > li.login > span').html('<img style="width:30px; height:30px;" src="' + vc_user.pic_square + '" />');
					}else{
						if(vc_user.pic_square != data[0].pic_square){
							vc_user.pic_square = data[0].pic_square;
							jQuery.cookies.set('vc_user', vc_user);
							jQuery('nav#navigation > ul.menu > li.login > span').html('<img style="width:30px; height:30px;" src="' + vc_user.pic_square + '" />');
						}
					}
					
				});
				*/
				
			});
			
			jQuery('nav#navigation > ul.menu > li.login > span').attr('class', 'arrow-up');
			
			/*
			//convert to be compatable with EJS view
			if(!vc_user.host)
				vc_user.host = false;
				
			if(!vc_user.vc_promoter)
				vc_user.vc_promoter = false;
				
			if(!vc_user.vc_manager)
				vc_user.vc_manager = false;
				
			if(!vc_user.vc_super_admin)
				vc_user.vc_super_admin = false;
			*/
			
			menu_html = new EJS({
			//	element: jQuery('div#ejs_global_templates > div#ejs_user_view').get(0)
				text: ejs_view_templates.user_menu
			}).render(vc_user);
			
			
			/*
			var menu_html = '<li>Welcome <strong>' + vc_user.first_name + '</strong></li><li><a href="' + window.module.Globals.prototype.front_link_base + 'profile/">Profile</a></li>';
			
			//Construct optional menu options
			if(vc_user.vc_promoter)
				menu_html += '<li><a href="' + window.module.Globals.prototype.front_link_base + 'admin/promoters/">Promoter Admin Area</a></li>';
			
			if(vc_user.vc_manager)
				menu_html += '<li><a href="' + window.module.Globals.prototype.front_link_base + 'admin/managers/">Manager Admin Area</a></li>';
			
			if(vc_user.vc_super_admin)
				menu_html += '<li><a href="' + window.module.Globals.prototype.front_link_base + 'admin/super_admins/">Super Admin Area</a></li>';

			if(vc_user.host)
				menu_html += '<li><a href="' + window.module.Globals.prototype.front_link_base + 'admin/hosts/">Host Admin Area</a></li>';
				
			if(vc_user.invitations){
				menu_html += '<li><a id="user_invitations_href" href="#">Invitations</a></li>';
				
				jQuery('a#user_invitations_href').live('click', function(){
					
					VCAuth.prototype.display_invitations();
					
					return false;
				});
			}
			
			
			menu_html += '<li><a id="vc_fb_logout" href="#">Logout</a></li>'
			
			*/
			
			nav_ul.html(menu_html);
			
			
			//quick hack...
			if(!vc_user.invitations){
				jQuery('a#user_invitations_href').parents('li').remove();
				delete vc_user.invitations;
			}else{
				if(typeof vc_user.invitations == 'object' || typeof vc_user.invitations == 'array'){
					var show = false;
					for(var i in vc_user.invitations){
						
						if(vc_user.invitations[i]){
							show = true;
							break;
						}
						
					}
					if(!show){
						jQuery('a#user_invitations_href').parents('li').remove();
						delete vc_user.invitations;
					}else{
						//show flashing indicator
						
						var timeout_callback = function(){
							if(jQuery('a#user_invitations_href').css('color') == 'rgb(255, 0, 0)'){
								jQuery('a#user_invitations_href').css('color', 'white');
							}else{
								jQuery('a#user_invitations_href').css('color', 'red');
							}
							setTimeout(timeout_callback, 1000);
						}
						timeout_callback();
						
					}
				}
			}
			jQuery.cookies.set('vc_user', vc_user);
			
			
			
			jQuery('a#user_invitations_href').live('click', function(){
					
				VCAuth.prototype.display_invitations();
				
				return false;
			});
			
			if(jQuery('div#user').css('display') == 'none'){
				//was hidden, show animation
				
				jQuery(window).trigger('scroll');
				
			//	jQuery('div#user').fadeIn(800, function(){});
				jQuery('div#user').show('slide', {
					direction: 'right'
				}, 500);
			
				jQuery(window).trigger('scroll');
				
			}else{
				//don't show animation
				jQuery('div#user').css('display', 'block');
				
			}
			
		}else{
			//no - unknown user
			
			//append before existing menu contents
	//		nav_ul.html('<a href="#" onclick="FB.login(function(){},{scope: \'email,publish_stream\'}); return false;"><img src="' + window.module.Globals.prototype.front_assets + 'images/fb-signup-button.png' + '" alt="Facebook Login" style="margin-top:-2px" /></a>');
			
			jQuery('nav#navigation > ul.menu > li.login.authenticated').removeClass('authenticated');
			
			jQuery('div#user').css('display', 'none');
			jQuery('nav#navigation > ul.menu > li.login > div#login-drop').css('display', '');
					
			jQuery('nav#navigation > ul.menu > li.login > span').html('Login').removeClass('arrow-up');
			
		}
		
		lock_scroll_top();
		
	}
	
	/**
	 * Displays invitations to a user from promotion teams
	 * 
	 * @return	null
	 */
	VCAuth.prototype.display_invitations = function(){
				
		var vc_user = jQuery.cookies.get('vc_user');
		
		if(!vc_user || !vc_user.invitations)
			return;
		
		var invitations_dialog = jQuery('div#invitations_dialog');
		
		var invitations_html = '';
		for(var i = 0; i < vc_user.invitations.length; i++){

			var date = new Date((parseInt(vc_user.invitations[i].ui_invitation_time) + 432000) * 1000);
			invitations_html += '<tr>';
			invitations_html += '<td><div class="loading_indicator"><img src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" alt="loading..." /></div><div class="pic_square_' + vc_user.invitations[i].ui_manager_oauth_uid + '"></div><div class="name_' + vc_user.invitations[i].ui_manager_oauth_uid + '"></div></td>';
			invitations_html += '<td>' + vc_user.invitations[i].t_name + '</td>';
			invitations_html += '<td>' + date.toLocaleDateString() + ' ' + date.toLocaleTimeString() + '</td>';
			invitations_html += '<td class="type" style="text-transform: capitalize;">' + vc_user.invitations[i].ui_invitation_type + '</td>';
			invitations_html += '<td class="actions"><span class="ui_i" style="display:none">' + i + '</span><span class="ui_id" style="display:none;">' + vc_user.invitations[i].ui_id + '</span><span class="actions_holder"><span class="respond_action" style="color:green;">Accept</span> | <span class="respond_action" style="color:red;">Decline</span></span></td>';
			invitations_html += '</tr>';
						
		}
		
		invitations_dialog.find('table tbody').html(invitations_html);
		
		var uids = [];
		for(var i = 0; i < vc_user.invitations.length; i++){
			
			if(_.indexOf(uids, vc_user.invitations[i].ui_manager_oauth_uid) == -1)
				uids.push(vc_user.invitations[i].ui_manager_oauth_uid);
			
		}

		jQuery.fbUserLookup(uids, 'uid, name, pic_square', function(rows){
			
			invitations_dialog.find('div.loading_indicator').remove();
			
			for(var i = 0; i < rows.length; i++){
				invitations_dialog.find('div.name_' + rows[i].uid).html(rows[i].name);
				invitations_dialog.find('div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="profile picture" />');
			}
			
		});
		
		
		
		
		var ajaxing_dont_touch_me = false;	
		jQuery('div#invitations_dialog td.actions span.respond_action').css('cursor', 'pointer').bind('click', function(){

			if(ajaxing_dont_touch_me)
				return false;
				ajaxing_dont_touch_me = true;

			var response 	= jQuery(this).html().toLowerCase();
			var ui_id 		= jQuery(this).parents('td').find('span.ui_id').html();
			var ui_i 		= parseInt(jQuery(this).parents('td').find('span.ui_i').html());
			var tr 			= jQuery(this).parents('tr');
			
			
			jQuery(this).parents('span.actions_holder').html('<img src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" />');
		

			//cross-site request forgery token, accessed from session cookie
			//requires jQuery cookie plugin
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
			//send request for login job to server
			jQuery.ajax({
				url: '/ajax/invitations/',
				type: 'post',
				data: {
						ci_csrf_token: cct,
						vc_method: 'invitation_response',
						ui_id: ui_id,
						response: response
					  },
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					console.log(data);
					
					ajaxing_dont_touch_me = false;
					
					if(data.success){
					
						var vc_user = jQuery.cookies.get('vc_user');
					
						switch(response){
							case 'accept':
																
								delete vc_user.invitations[ui_i];
								
								var type = tr.parents('tr').find('td.type').html();		
								
								
								
								if(type == 'promoter'){
									
									vc_user.vc_promoter = true;
									
								}else if(type == 'host'){
									
									vc_user.host = true;
									
								}
								
								console.log('---------');
								console.log(tr);
								console.log(tr.parents('tr'));
														
								tr.html('<td colspan="5">Congratulations! Click on <b>Promoter/Host Admin Area</b> in the upper right hand corner to get started with your new team! :)</td>');
								
								tr.parents('tbody').find('tr').each(function(){
									//remove all other invitations of same type
									
									if(jQuery(this).find('td.type').html == type){
										
										//find i
										var del_i = parseInt(jQuery(this).find('td.actions').find('span.ui_i').html());
									//	delete vc_user.invitations[del_i];
										vc_user.invitations.splice(del_i,1); //<-- remove index
										jQuery(this).remove();
										
									}
									
								});
								
								
								break;
							case 'decline':
							
								//remove invitation from vc_user.invitations
								vc_user.invitations.splice(ui_i, 1);
								tr.remove();
								
								if(vc_user.invitations.length == 0){
									delete vc_user.invitations;
									jQuery('div#invitations_dialog').dialog('close');
								}
								
								break;
							default:
								break;
						}
						
						if(vc_user.invitations)
						if(vc_user.invitations.length == 0)
							delete vc_user.invitations;
						
						jQuery.cookies.set('vc_user', vc_user);
						VCAuth.prototype.update_menu();
						
					}else{
						
						alert(data.message);
					
					}
					
				}
			});
					
		});
		
		invitations_dialog.dialog({
			title: 'Invitations',
			modal: true,
			width: 700,
			height: 'auto',
			resizable: false,
			movable: false
		});
		
	}
	
	/* ---------------------- class helper methods ---------------------- */

	/* ---------------------- / class helper methods ---------------------- */
	
	exports.module = exports.module || {};
	exports.module.VCAuth = VCAuth;

})(window);