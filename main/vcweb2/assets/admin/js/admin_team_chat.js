jQuery(function() {(function(exports) {

		window.team_chat_object = {

			admin_title_base : document.title,
			window_focus : true,
			url_segment_2 : window.location.pathname.split('/')[2],
			pusher : 							false,
			vc_team_chat_users : 				false,
			team_chat_channel : 				false,
			admin_last_message_uid : 			false,
			adminChatUserTypingNotification : 	false,
			
			
			
			
			pageslide_open : function() {

				if(jQuery('div#team_chatbox_header'))
					jQuery('div#team_chatbox_header').css('display', 'none');

				jQuery.pageslide({
					href : '#team_chat',
					modal : true
				});

				jQuery('div#team_chat div.team_chat_messages').scrollTop(999999);





				var vc_user = jQuery.cookies.get('vc_user');
				var vc_admin_user = vc_user.vc_admin_user;
				if(vc_admin_user) {
					vc_admin_user.chat_open = true;
					vc_admin_user.unread = 0;
					vc_user.vc_admin_user = vc_admin_user;
					jQuery.cookies.set('vc_user', vc_user);
					jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').html('');
				}

				document.title = team_chat_object.admin_title_base;

			},
			
			
			
			
			
			
			init_pagesslide_open_close : function() {
				var vc_user = jQuery.cookies.get('vc_user');
				var vc_admin_user = vc_user.vc_admin_user;

				console.log('pageslideOpen');
				console.log(vc_admin_user);

				if(vc_admin_user != undefined) {
					if(vc_admin_user.chat_open != undefined && vc_admin_user.chat_open) {
						team_chat_object.pageslide_open();
					}
				}

				if(jQuery('div#pageslide').css('display') == 'none')
					jQuery('div#team_chatbox_header').css('display', 'block');
					
			},
			
			init_tc_unread_msg : function() {
				var vc_user = jQuery.cookies.get('vc_user');
				var vc_admin_user = vc_user.vc_admin_user;

				if(vc_admin_user && vc_admin_user.unread)
					jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').html('(' + vc_admin_user.unread + ')');
			},
			
			
			
			



			// --------------------------------------------------------------------------------------------------------------
			//	Team chat init
			// --------------------------------------------------------------------------------------------------------------
			subscription_success_init_chat : function(members) {

				var admin_last_message_uid 			= team_chat_object.admin_last_message_uid;
				var adminChatUserTypingNotification = team_chat_object.adminChatUserTypingNotification;
				var team_chat_channel 				= team_chat_object.team_chat_channel;
				var vc_team_chat_users 				= team_chat_object.vc_team_chat_users;

				console.log('subscription_succeeded');

				members.each(function(member) {
					var span = jQuery('div#team_chat div.team_chat_users span.chat_name_' + member.id);
					span.find('img.status').remove();
					span.append('<img src="' + window.module.Globals.prototype.admin_assets + 'images/green_dot.png" alt="" class="status online" />');
				});
				//indicate which users are online

				//retrieve chat messages and add to feed

				//cross-site request forgery token, accessed from session cookie
				//requires jQuery cookie plugin
				var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';

				jQuery.ajax({
					url : '/ajax/admin_messages/',
					type : 'post',
					data : {
						ci_csrf_token : cct,
						admin_panel : team_chat_object.url_segment_2, //'<?= $this->uri->segment(2) ?>',
						vc_method : 'read'
					},
					cache : false,
					dataType : 'json',
					success : function(data, textStatus, jqXHR) {


						var team_chat_channel 



						var message_html = '';
						admin_last_message_uid = 0;

						if(data.messages.length > 0) {

							for(var i = 0; i < data.messages.length; i++) {

								if(data.messages[i].m_users_oauth_uid != admin_last_message_uid) {
									admin_last_message_uid = data.messages[i].m_users_oauth_uid;

									if(i > 0) {
										message_html += '<div style="clear:both"></div>';
										message_html += '</div>';
										message_html += '</div>';
									}
									message_html += '<div class="chat_message">';
									message_html += '<div class="pic chat_pic_square_' + data.messages[i].m_users_oauth_uid + '"></div>';
									message_html += '<div class="first_name chat_first_name_' + data.messages[i].m_users_oauth_uid + '"></div>';
									message_html += '<div class="message_wrapper">';
									message_html += '<div class="message_content">' + data.messages[i].m_message_content + '</div>';

								} else {
									message_html += '<div class="message_content">' + data.messages[i].m_message_content + '</div>';

								}

							}
						}

						jQuery('div#team_chat div.team_chat_messages').html(message_html);
						setTimeout(function() {
							jQuery('div#team_chat div.team_chat_messages').scrollTop(999999);
						}, 40);
						//populate divs with FB data
						for(var i = 0; i < vc_team_chat_users.length; i++) {

							jQuery('div#team_chat div.team_chat_messages div.chat_pic_square_' + vc_team_chat_users[i].uid).html('<img src="' + vc_team_chat_users[i].pic_square + '" alt="picture" />');
							jQuery('div#team_chat div.team_chat_messages div.chat_first_name_' + vc_team_chat_users[i].uid).html('<div class="vc_name"><span style="display: none;">' + vc_team_chat_users[i].uid + '</span>' + vc_team_chat_users[i].first_name + '</div>');
							jQuery('div#team_chat div.team_chat_messages span.chat_name_' + vc_team_chat_users[i].uid).html('<div class="vc_name"><span style="display: none;">' + vc_team_chat_users[i].uid + '</span>' + vc_team_chat_users[i].name + '</div>');

						}
						admin_chat_inactive_users = data.chat_inactives;
						for(var i = 0; i < data.chat_inactives.length; i++) {
							jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + data.chat_inactives[i].ci_users_oauth_uid + ' img.status').remove();
							jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + data.chat_inactives[i].ci_users_oauth_uid).append('<img src="' + window.module.Globals.prototype.admin_assets + 'images/orange_dot.png" alt="" class="status away" />');
						}






						team_chat_object.team_chat_channel.bind('pusher:member_added', function(member) {

							console.log('member_added');
							console.log(member);

							if(window['user_timeout_' + member.id]) {
								clearTimeout(window['user_timeout_' + member.id]);
								window['user_timeout_' + member.id] = false;
								return;
							}

							var indicator = jQuery('div#team_chat div.team_chat_users span.chat_name_' + member.oauth_uid);
							indicator.each(function() {
								jQuery(this).parent().find('img.chat_activity').css('display', 'none');
							});
							var span = jQuery('div#team_chat div.team_chat_users span.chat_name_' + member.id);

							span.find('img.status').remove();
							span.append('<img src="' + window.module.Globals.prototype.admin_assets + 'images/green_dot.png" alt="" class="status online" />');

							var user;
							for(var i = 0; i < vc_team_chat_users.length; i++) {
								if(vc_team_chat_users[i].uid == member.id) {
									user = vc_team_chat_users[i];
									break;
								}
							}

							jQuery("div#notification_container").notify("create", {
								icon : '<img src="' + user.pic_square + '" alt="' + user.name + '" />',
								title : user.name,
								color : 'green',
								text : user.name + ' has signed into team chat.'
							}, {
								speed : 1000
							});

							if(!team_chat_object.window_focus && adminAlertSound)
								adminAlertSound.play();
						});







						team_chat_object.team_chat_channel.bind('pusher:member_removed', function(member) {

							console.log('member_removed');
							console.log(member);

							window['user_timeout_' + member.id] = setTimeout(function() {
								var span = jQuery('div#team_chat div.team_chat_users span.chat_name_' + member.id);
								span.find('img.status').remove();
								window['user_timeout_' + member.id] = false;

								var user;
								for(var i = 0; i < vc_team_chat_users.length; i++) {
									if(vc_team_chat_users[i].uid == member.id) {
										user = vc_team_chat_users[i];
										break;
									}
								}

								jQuery("div#notification_container").notify("create", {
									icon : '<img src="' + user.pic_square + '" alt="' + user.name + '" />',
									title : user.name,
									color : 'red',
									text : user.name + ' has signed out of team chat.'
								}, {
									speed : 1000
								});

							}, (1000 * 10));

						});




						team_chat_object.team_chat_channel.bind('new', function(message){
							
							console.log('new');
							console.log(message);
								
							var user;
							for(var i=0; i < team_chat_object.vc_team_chat_users.length; i++){
								if(team_chat_object.vc_team_chat_users[i].uid == message.oauth_uid){
									user = team_chat_object.vc_team_chat_users[i];
									break;
								}
							}
							
							var indicator = jQuery('div#team_chat div.team_chat_users span.chat_name_' + message.oauth_uid);
							indicator.each(function(){
								jQuery(this).parent().find('img.chat_activity').css('display', 'none');
							});
							
							if(team_chat_object.admin_last_message_uid == message.oauth_uid){
										
								jQuery('div#team_chat').each(function(){
									jQuery(this).find('div.team_chat_messages div.chat_message:last').find('div.message_wrapper').append('<div class="message_content">' + message.message + '</div>');
								});
							
							
							}else{
										
								team_chat_object.admin_last_message_uid = message.oauth_uid;
								
								jQuery('div#team_chat div.team_chat_messages').each(function(){
									jQuery(this).append('<div class="chat_message"><div class="pic pic_square_' + message.oauth_uid + '"><img src="' + user.pic_square + '" alt="picture" /></div><div class="first_name chat_first_name_' + message.oauth_uid + '"><div class="vc_name"><span style="display: none;">' + message.oauth_uid + '</span>' + user.first_name + '</div></div><div class="message_wrapper"><div class="message_content">' + message.message + '</div></div></div>');
								});
							
							}
							
							jQuery('div#team_chat div.team_chat_messages').scrollTop(999999);
							
							//if the message belongs to THIS user, ignore
							if(message.oauth_uid == window.module.Globals.prototype.user_oauth_uid)
								return;
							
							
							//increment unread if pageslide closed
							if(jQuery('div#pageslide').css('display') == 'none' || !window_focus){
								
								var vc_user = jQuery.cookies.get('vc_user');
								var vc_admin_user = vc_user.vc_admin_user;
								
								if(vc_admin_user){
									
									if(!vc_admin_user.unread)
										vc_admin_user.unread = 0;
									
									vc_admin_user.unread++;
									jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').html('(' + vc_admin_user.unread + ')');
												
									vc_user.vc_admin_user = vc_admin_user;
									jQuery.cookies.set('vc_user', vc_user);
								
								}else{
									
									vc_admin_user = {};
									vc_admin_user.unread = 1;
									jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').html('(' + vc_admin_user.unread + ')');
									
									vc_user.vc_admin_user = vc_admin_user;
									jQuery.cookies.set('vc_user', vc_user);
				
								}
								
								if(!team_chat_object.window_focus && adminAlertSound)
									adminAlertSound.play();
								
							}
								
						});




						team_chat_object.team_chat_channel.bind('user_chat_activity', function(member) {

							console.log('user_chat_activity');
							console.log(member);

							if(member.oauth_uid == window.module.Globals.prototype.user_oauth_uid)
								return;

							var indicator = jQuery('div#team_chat div.team_chat_users span.chat_name_' + member.oauth_uid).parent().find('img.chat_activity');

							console.log(indicator);

							if(member.chat_activity == 'true') {
								indicator.each(function() {
									jQuery(this).css('display', 'inline-block');
								});
							} else {
								indicator.each(function() {
									jQuery(this).css('display', 'none');
								});
							}

						});

						team_chat_object.team_chat_channel.bind('member_inactive', function(member) {

							jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + member.id + ' img.status').remove();
							jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + member.id).append('<img src="' + window.module.Globals.prototype.admin_assets + 'images/orange_dot.png" alt="" class="status away" />');

						});

						team_chat_object.team_chat_channel.bind('member_active', function(member) {

							jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + member.id + ' img.status').remove();
							jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + member.id).append('<img src="' + window.module.Globals.prototype.admin_assets + 'images/green_dot.png" alt="" class="status online" />');

						});
					}
				});
				adminChatUserTypingNotification = false;
				jQuery('div#team_chat div.team_chat_input textarea').bind('keydown', function(e) {

					if(e.keyCode == 13) {
						e.preventDefault();
						adminChatUserTypingNotification = false;

						var message = jQuery(this).val();
						jQuery(this).val('');
						message = jQuery.trim(message);

						if(message.length == 0)
							return;

						var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';

						jQuery.ajax({
							url : '/ajax/admin_messages/',
							type : 'post',
							data : {
								ci_csrf_token : cct,
								vc_method : 'new',
								admin_panel : team_chat_object.url_segment_2, //'<?= $this->uri->segment(2) ?>',
								message : message
							},
							cache : false,
							dataType : 'json',
							success : function(data, textStatus, jqXHR) {
								console.log(data);
							}
						});

					} else {

						if(!adminChatUserTypingNotification) {

							var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
							jQuery.ajax({
								url : '/ajax/admin_messages/',
								type : 'post',
								data : {
									ci_csrf_token : cct,
									vc_method : 'chat_activity',
									admin_panel : team_chat_object.url_segment_2, //'<?= $this->uri->segment(2) ?>',
									chat_activity : true
								},
								cache : false,
								dataType : 'json',
								success : function(data, textStatus, jqXHR) {
									console.log(data);
								}
							});
							adminChatUserTypingNotification = true;
						}

					}

				});
			},
			// --------------------------------------------------------------------------------------------------------------







			// --------------------------------------------------------------------------------------------------------------
			//	Start initializing pusher
			// --------------------------------------------------------------------------------------------------------------
			pusher_init : function() {




				var pusher 						= team_chat_object.pusher;
				var vc_team_chat_users 			= team_chat_object.vc_team_chat_users;
				var team_chat_channel 			= team_chat_object.team_chat_channel;
				
				
				
				
				Pusher.channel_auth_endpoint 	= '/ajax/admin_messages/';

				//--------------------------------------- OVERRIDE PUSHER AUTH AJAX REQUEST FOR CI_CSRF_TOKEN -----------------------------------------------
				Pusher.authorizers.ajax = function(pusher, callback) {
					var self = this, xhr;

					if(Pusher.XHR) {
						xhr = new Pusher.XHR();
					} else {
						xhr = (window.XMLHttpRequest ? new window.XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP"));
					}

					xhr.open("POST", Pusher.channel_auth_endpoint, true);
					xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
					xhr.onreadystatechange = function() {
						if(xhr.readyState == 4) {
							if(xhr.status == 200) {
								var data, parsed = false;

								try {
									data = JSON.parse(xhr.responseText);
									parsed = true;
								} catch (e) {
									callback(true, 'JSON returned from webapp was invalid, yet status code was 200. Data was: ' + xhr.responseText);
								}

								if(parsed) {// prevents double execution.
									callback(false, data);
								}
							} else {
								Pusher.warn("Couldn't get auth info from your webapp", status);
								callback(true, xhr.status);
							}
						}
					};
					var csrf_token = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
					var admin_panel = team_chat_object.url_segment_2;
					// '<?= $this->uri->segment(2) ?>';

					xhr.send('socket_id=' + encodeURIComponent(pusher.connection.socket_id) + '&channel_name=' + encodeURIComponent(self.name) + '&ci_csrf_token=' + csrf_token + '&admin_panel=' + admin_panel);
				};
				//--------------------------------------- OVERRIDE PUSHER AUTH AJAX REQUEST FOR CI_CSRF_TOKEN -----------------------------------------------
				
				team_chat_object.pusher = new Pusher(window.module.Globals.prototype.pusher_api_key);
				team_chat_object.team_chat_channel 		= team_chat_object.pusher.subscribe('presence-' + window.vc_tc_obj.team_fan_page_id);
				team_chat_object.individual_channel 	= team_chat_object.pusher.subscribe('private-' + window.module.Globals.prototype.user_oauth_uid);
				
				team_chat_object.team_chat_channel.bind('pusher:subscription_succeeded', function(members) {
					
					var users = eval(window.vc_tc_obj.users);
					jQuery.fbUserLookup(users, 'uid, name, first_name, pic_square, pic_big', function(rows) {
	
						team_chat_object.vc_team_chat_users = rows;
	
						//populate divs with FB data
						for(var i = 0; i < rows.length; i++) {
							jQuery('div#team_chat div.chat_pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
							jQuery('div#team_chat span.chat_name_' + rows[i].uid).html('<div class="vc_name"><span style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</div>');
						}
						
						team_chat_object.subscription_success_init_chat(members);
						EventHandlerObject.fire('team_chat_init');
					
					});	
					
				});
				
				
			}
			// --------------------------------------------------------------------------------------------------------------

		};





		// --------------------------------------------------------------------------------------------------------------
		//	Manage focus/defocus operations
		// --------------------------------------------------------------------------------------------------------------
		jQuery(window).focus(function() {
			team_chat_object.window_focus = true;

			//If chat slide is open... do the following
			if(jQuery('div#pageslide').css('display') == 'block') {

				var vc_user = jQuery.cookies.get('vc_user');
				var vc_admin_user = vc_user.vc_admin_user;
				if(vc_admin_user) {
					vc_admin_user.chat_open = true;
					vc_admin_user.unread = 0;
					vc_user.vc_admin_user = vc_admin_user;
					jQuery.cookies.set('vc_user', vc_user);
					jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').html('');
				}

				document.title = team_chat_object.admin_title_base;

			}

		}).blur(function() {
			team_chat_object.window_focus = false;
			console.log(team_chat_object.window_focus);

		});
		// --------------------------------------------------------------------------------------------------------------




		// --------------------------------------------------------------------------------------------------------------
		//	Manage sound effects
		// --------------------------------------------------------------------------------------------------------------

		//must load from root domain
		soundManager.url = window.location.protocol + '//' + window.location.host + '/vcweb2/assets/global/swf/soundmanager/soundmanager2.swf';
		soundManager.onready(function() {
			// SM2 has loaded - now you can create and play sounds!
			adminAlertSound = soundManager.createSound({
				id : 'adminAlert',
				url : window.module.Globals.prototype.global_assets + 'audio/alert.mp3'
			});
		});
		// --------------------------------------------------------------------------------------------------------------





		// --------------------------------------------------------------------------------------------------------------
		//	Idle timeout
		// --------------------------------------------------------------------------------------------------------------
		(function() {
			idleTime = 0;

			var timerIncrement = function() {
				idleTime = idleTime + 1;
				if(idleTime > 2) {// 3 minutes
					//alert inactive

					var ajax_request = function() {

						//cross-site request forgery token, accessed from session cookie
						//requires jQuery cookie plugin
						var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';

						jQuery.ajax({
							url : '/ajax/admin_messages/',
							type : 'post',
							data : {
								ci_csrf_token : cct,
								admin_panel : team_chat_object.url_segment_2, // '<?= $this->uri->segment(2) ?>',
								vc_method : 'alert_inactive'
							},
							cache : false,
							dataType : 'json',
							success : function(data, textStatus, jqXHR) {

							}
						});
					};
					var vc_user = jQuery.cookies.get('vc_user');
					var vc_admin_user = vc_user.vc_admin_user;

					if(vc_admin_user) {

						if(vc_admin_user.active == undefined || vc_admin_user.active) {
							ajax_request();
							vc_admin_user.active = false;
						}

					} else {

						ajax_request();
						vc_admin_user = {};
						vc_admin_user.active = false;

					}

					vc_user.vc_admin_user = vc_admin_user;
					jQuery.cookies.set('vc_user', vc_user);

				}
			}
			var modeActive = function() {
				idleTime = 0;

				var vc_user = jQuery.cookies.get('vc_user');
				var vc_admin_user = vc_user.vc_admin_user;

				if(vc_admin_user && (vc_admin_user.active != undefined) && !vc_admin_user.active) {
					//alert active

					vc_admin_user.active = true;
					vc_user.vc_admin_user = vc_admin_user;
					jQuery.cookies.set('vc_user', vc_user);

					//cross-site request forgery token, accessed from session cookie
					//requires jQuery cookie plugin
					var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';

					jQuery.ajax({
						url : '/ajax/admin_messages/',
						type : 'post',
						data : {
							ci_csrf_token : cct,
							admin_panel : team_chat_object.url_segment_2, //'<?= $this->uri->segment(2) ?>',
							vc_method : 'alert_active'
						},
						cache : false,
						dataType : 'json',
						success : function(data, textStatus, jqXHR) {

						}
					});

				}

			}
			//Increment the idle time counter every minute.
			var idleInterval = setInterval(timerIncrement, 60000);
			// 1 minute

			//Zero the idle timer on mouse movement.
			jQuery(document).mousemove(function(e) {
				modeActive();
			});
			jQuery(document).keypress(function(e) {
				modeActive();
			});
		})();
		// --------------------------------------------------------------------------------------------------------------

		// --------------------------------------------------------------------------------------------------------------
		//	Cycling function, changes title bar and other effects
		// --------------------------------------------------------------------------------------------------------------
		(function() {

			var count_cycle = 0;
			var cycle_notify = function() {

				var vc_user = jQuery.cookies.get('vc_user');
				var vc_admin_user = vc_user.vc_admin_user;

				if(vc_admin_user && vc_admin_user.unread && vc_admin_user.unread > 0) {

					if(count_cycle % 2) {

						jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').css('color', 'white');
						document.title = team_chat_object.admin_title_base + ' (' + vc_admin_user.unread + ')';

					} else {

						jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').css('color', 'red');
						document.title = 'New Message!';

					}

				}
				count_cycle++;
				if(count_cycle > 1000)
					count_cycle = 0;

				setTimeout(cycle_notify, 1000);
			};
			cycle_notify();

			jQuery('div#notification_container').notify();

		})();
		// --------------------------------------------------------------------------------------------------------------

		// --------------------------------------------------------------------------------------------------------------
		//	Set up basic properties of Chat modal
		// --------------------------------------------------------------------------------------------------------------
		(function() {

			window.globalScrollOffset = 0;
			jQuery(document).bind('scroll', function() {
				window.globalScrollOffset = jQuery(this).scrollTop();
			});

			jQuery('div#team_chat textarea').val('Start Typing...').css({
				color : '#333',
				'background-color' : 'rgba(0,0,0,.8)'
			});

			jQuery('div#team_chat textarea').live('focus', function(e) {

				if(jQuery(this).css('color') == 'rgb(51, 51, 51)')
					jQuery(this).val('').css('color', '#FFF');

				var scroll_offset = window.globalScrollOffset;

				document.ontouchmove = function(e2) {
					e2.preventDefault();
				}
				if(jQuery.isIpad())
					setTimeout(function() {

						var orientation = Math.abs(window.orientation) == 90 ? 'landscape' : 'portrait';

						jQuery(document).scrollTop(scroll_offset);

						if(orientation == 'landscape')
							jQuery('div#pageslide').css('bottom', '59%');
						else
							jQuery('div#pageslide').css('bottom', '33%');

					}, 1);
			});

			jQuery('div#team_chat textarea').live('blur', function() {

				document.ontouchmove = function(e) {
					return;
				}
				if(jQuery(this).val() == '')
					jQuery(this).val('Start Typing...').css('color', '#333');

				jQuery('div#pageslide').css('bottom', '0');

				if(window.adminChatUserTypingNotification) {
					var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
					jQuery.ajax({
						url : '/ajax/admin_messages/',
						type : 'post',
						data : {
							ci_csrf_token : cct,
							vc_method : 'chat_activity',
							admin_panel : team_chat_channel.url_segment_2, //'<?= $this->uri->segment(2) ?>',
							chat_activity : false
						},
						cache : false,
						dataType : 'json',
						success : function(data, textStatus, jqXHR) {
							console.log(data);
						}
					});

					team_chat_object.adminChatUserTypingNotification = false;
				}

			});
			
			
			
			var close_pageslide = jQuery.pageslide.close;
			jQuery.pageslide.close = function() {

				jQuery('div#team_chatbox_header').css('display', 'block');
				close_pageslide();

				var vc_user = jQuery.cookies.get('vc_user');
				var vc_admin_user = vc_user.vc_admin_user;

				if(vc_admin_user) {

					vc_admin_user.chat_open = false;
					vc_user.vc_admin_user = vc_admin_user;
					jQuery.cookies.set('vc_user', vc_user);
				}

			}



			jQuery('div#team_chatbox_header').live('click', team_chat_object.pageslide_open);

			jQuery('div#team_chat div#team_chatbox_header_tab_close').bind('click', function() {
				jQuery.pageslide.close();
				return false;
			});
		})();
		// --------------------------------------------------------------------------------------------------------------

		//and away we go...
		team_chat_object.pusher_init();
		team_chat_object.init_tc_unread_msg();
		team_chat_object.init_pagesslide_open_close();
		
		
//		jQuery('#team_chatbox_header').bind('click', function(){
//			jQuery('#team_chatbox_header_tab').trigger('click');
//		});

	})(window);
});
