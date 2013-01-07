if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};
	
jQuery(function(){
	
	
	window.vc_page_scripts.facebook_plugin_init = function(){
			

		// --------------------- facebook plugin object ---------------------
		var venue_items = {
			retrieve_lock: false,
			items_iterator: false,
			retrieve_feed: function(request_first){
				
				if(venue_items.retrieve_lock)
					return;
				
				//while loading data, prevent this method from firing again
				venue_items.retrieve_lock = true;
				
				var incrementor = 0;
				
				var retrieve_function = function(){
									
					if(incrementor > 4){
						incrementor = 0;
						return; //failed
					}
					
					//cross-site request forgery token, accessed from session cookie
					//requires jQuery cookie plugin
					var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
					
					jQuery.ajax({
						url: window.location,
						type: 'post',
						data: {
							 	ci_csrf_token: cct,
								vc_method: 'friend_venue_activity_retrieve',
								status_check: true
							  },
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
							
					//		venue_items.retrieve_lock = false;
						
							if(data.success){
											
								console.log('success');
								venue_items.display(data.message);
								
							}else{
								
								incrementor++;
								setTimeout(retrieve_function, 1000);
								
							}
						
						}
					});
				}
				
				//If this is an initial request, start job w/ server
				if(request_first){
					//cross-site request forgery token, accessed from session cookie
					//requires jQuery cookie plugin
					var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
					
					jQuery.ajax({
						url: window.location,
						type: 'post',
						data: {
							ci_csrf_token: cct,
							vc_method: 'friend_venue_activity_retrieve',
							team_fan_page_id: jQuery('div#fan_page_id').html()
						},
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
							
							if(data.success)
								//start first check 1 second after
								setTimeout(retrieve_function, 1000);
							
						}
					});
					
					return;
				}else{
					retrieve_function();
				}
				
			},
			display: function(data){
				
				console.log('success');
				console.log(data);			
				
				for(var i in data.tv_friends_pop){
					
					var tv_u_friends = jQuery('div#friends_' + i);
					if(data.tv_friends_pop[i].length > 0){
						
						var tv_friends_html = '<p>' + data.tv_friends_pop[i].length + 'friend(s) have been here</p>';
						
						for(var k in data.tv_friends_pop[i]){
							
							var friend = data.user_friends[data.tv_friends_pop[i][k]];
							
							tv_friends_html += '<a target="_new" style="display:inline-block;" href="<?= $central->front_link_base ?>friends/' + friend.third_party_id + '/" title="' + friend.name + '">';
							tv_friends_html += '<img class="thumbnail" style="width:20px; height:20px;" src="' + friend.pic_square + '" alt="' + friend.name + '"> ';
							tv_friends_html += '</a>';
						}
						
						tv_u_friends.find('img.loading_indicator').css('display', 'none');
						tv_u_friends.append(tv_friends_html);
						
					}else{
						tv_u_friends.html('<p>0 Friends have been here</p>');
					}
					
				}
				
			}
		};
		
		
		// --------------------- facebook plugin object ---------------------
		
		
		
		
		// --------------------- page setup ------------------------------
		jQuery('a.gl_join').live('click', function(e){
			
			var tgla_id = jQuery(this).parent().find('span.tgla_id').html();
			var tv_id = jQuery(this).parent().find('span.tv_id').html();
			var content = jQuery('div#guest_list_content');
			var content_holder = jQuery('div#guest_list_content_temp_holder');
			
			
			//retrieve tgla html from server
			//cross-site request forgery token, accessed from session cookie
		
			//requires jQuery cookie plugin
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: {
				 	ci_csrf_token: 	cct,
					vc_method: 		'tgla_html_retrieve',
					tgla_id: 		tgla_id,
					tv_id: 			tv_id
				},
				cache: false,
			//	dataType: 'json',
				success: function(data, textStatus, jqXHR){
											
					//temp store content in holder
					content_holder.html(content.html());
					content.html(data);
					
					
					
					if(window.vc_page_scripts.facebook_plugin_gl_init)
						window.vc_page_scripts.facebook_plugin_gl_init();
					
					//Scroll facebook canvas
					var section_guestlist = jQuery('section#guestlist').offset();
					var y = section_guestlist.top;
					FB.Canvas.getPageInfo(function(pageInfo){
				            jQuery({y: pageInfo.scrollTop}).animate(
				                {y: y},
				                {duration: 1000, step: function(offset){
				                    FB.Canvas.scrollTo(0, offset);
				                }
				            });
				    });
				
				}
			});
			
			e.preventDefault();
			return false;
		});
		// --------------------- page setup ------------------------------
		
		
		
		
		// --------------------- initial load venue friend data ---------------------
		var vc_user = jQuery.cookies.get('vc_user');
		if(vc_user){
			venue_items.retrieve_feed(false);
			venue_items.retrieve_lock = true;
		}
		// --------------------- end initial load venue friend data ---------------------
		
		
		
		
		
		
		// --------------------- bind login/logout events ---------------------
		window.EventHandlerObject.addListener("vc_login", function(){
			
			var auth_content = jQuery('.auth_content');
			var unauth_content = jQuery('.unauth_content');
			
			console.log('vc_login');
			
			unauth_content.css('display', 'none');
			auth_content.css('display', 'block');
			
			venue_items.retrieve_feed(true);
			
		});
		
		window.EventHandlerObject.addListener("vc_logout", function(){
			
			var auth_content = jQuery('.auth_content');
			var unauth_content = jQuery('.unauth_content');
			
			console.log('vc_logout');
	
			unauth_content.css('display', 'block');
			auth_content.css('display', 'none');
	
			//clean up previously inserted content
			var auth_content = jQuery('div.auth_content');
			auth_content.find('img.loading_indicator').css('display', 'block');
			auth_content.find('*').not('img.loading_indicator').contents().remove();
			
			venue_items.retrieve_lock = false;
			
		});
		// --------------------- end bind login/logout events ---------------------
		
		
	};
	
	
	
	
	
	
	// ------------------------------------------------------------------------------------------------
	// ------------------------------------------------------------------------------------------------
	// ------------------------------------------------------------------------------------------------
	
	
	
	
	
	window.vc_page_scripts.facebook_plugin_gl_init = function(){
		
		
		jQuery('select#guestlist-confirmation-carrier').hide();
		
		
		setTimeout(function(){
			jQuery('div#accordion').effect('pulsate', {
				distance: 15,
				times: 2
			}, 300);	
		}, 300);
		
		
		var publish_stream = false;
		
		fbEnsureInit(function(){
			
			//find out if FB share permission enabled
			FB.api('/me/permissions', function (response){

				if(typeof response.data == 'object' && typeof response.data[0] !== 'undefined'){
				    var ps = response.data[0].publish_stream;
				    console.log(response);
				    if(typeof ps === 'undefined' || ps === 0){
				    	publish_stream = false;
				    }else{
				    	publish_stream = true;
				    }
			    }
			    
			});
			
		});
		
	
		var tgla_id 	= gl_obj.tgla_id;
		var tv_id		= gl_obj.tv_id;
		var tgla_name	= gl_obj.tgla_name;
		var tv_name 	= gl_obj.tv_name;


		var loading_indicator 	= '<div id="loading_indicator"><img src="' + window.module.Globals.prototype.global_assets + 'images/ajax.gif" alt="loading..." /></div>';
		var recipients_table 	= jQuery('div#facebook-guest-list-friends');
		var accordion 			= jQuery('div#accordion');





	 /**
	   * Guestlist Info/Join
	   */
	  (function($) {
	
	    var $guestlist = $("#guestlist");
	    var $buttons = $guestlist.find('.action a').not('.inactive');
	    var $table = $guestlist.find('.guestlist-table');
	    var $form = $guestlist.find('.guestlist-form');
	    var $confirmationText = $("#guestlist-confirmation-text");
	    var $cancel = $("#guestlist-form-cancel");
	
	
		//setup
		accordion.accordion({
			icons: false,
			clearStyle: true
		});
	
	
	    function showDetails() {
	      $table.animate({ 'opacity' : 0 }, 500, function() {
	        $(this).hide();
	        $form.css({ 'opacity' : 0, 'display': 'block' }).animate({ 'opacity' : 1 }, 500);
	      });
	      
	    }
	
	    
	    
	    
	    
	    
	    
	     $buttons.on('click', function(e) {
		      showDetails();
		    });
		    
		
		    $confirmationText.on('change', function(e) {
		    	
		    	if(jQuery(this).is(':checked')){
		    		$(this).parent().find('.confirmation-details').slideDown();
		    	}else{
		    		$(this).parent().find('.confirmation-details').slideUp();
		    	}
		    	
		    	
		   //   $(this).parent().find('.confirmation-details').slideToggle();
		    });
		    
		    
		    
		    
		    
		
			//input mask on phone number -------
			jQuery('input#guestlist-confirmation-phone').mask('(999)-999-9999');
			var vc_user = jQuery.cookies.get('vc_user');
			if(vc_user && vc_user.users_phone_number){
				jQuery('input#guestlist-confirmation-phone').val(vc_user.users_phone_number);
				if(!jQuery('#guestlist-confirmation-text').is(':checked')){
					jQuery('#guestlist-confirmation-text').trigger('click');
				}
			}
			
			
			
			
			jQuery('select#guest-list-table-price-selection').bind('change', function(){
				var spend = jQuery(this).val();
				jQuery('div.vlf div.reserved').removeClass('reserved');
				jQuery('div.vlf .price_' + spend).each(function(){
					jQuery(this).parents('div.table').addClass('reserved');
				});
			});
			
			
			
			
			
			
			
			
			
			
			
			
			jQuery('input#guestlist-table-request').bind('change', function(){
						
				jQuery('select#guest-list-table-price-selection').trigger('change');
				
				if(jQuery(this).is(':checked')){
					
					jQuery('#price_opt_hide').slideDown();
					
					$confirmationText.attr('checked', 'checked');
					$confirmationText.attr('disabled', 'disabled');
					if(!jQuery('div.confirmation-details').is(':visible')){
						jQuery('div.confirmation-details').slideDown();
					}
					
				}else{
					
					$confirmationText.removeAttr('disabled');
					jQuery('#price_opt_hide').slideUp();
					
				}
				
			});
			
			
			
			
			
			
			
			
			
			
			var return_cancel = function(){
				var content = jQuery('div#guest_list_content');
				var content_holder = jQuery('div#guest_list_content_temp_holder');
				
				content.html(content_holder.html());
				content_holder.html('');
		
				var y = 0;
				FB.Canvas.getPageInfo(function(pageInfo){
		            jQuery({y: pageInfo.scrollTop}).animate(
		                {y: y},
		                {duration: 1000, step: function(offset){
		                    FB.Canvas.scrollTo(0, offset);
		                }
		            });
			    });
			}
			
			jQuery('#guestlist-form-cancel').bind('click', function(){			
				jQuery('#dialog-confirm').dialog({
					resizable: false,
					height:210,
					modal: true,
					buttons: {
						"Okay": function() {
							
							jQuery(this).dialog('close');
						//	jQuery('a#back_trigger_link').trigger('click');
							
							return_cancel();
							
						},
						Cancel: function() {
							jQuery(this).dialog('close');
						}
					}
				});
			});
		
		
			jQuery('.return_cancel').live('click', return_cancel);
		
		
			jQuery('.guestlist-button:not(#guestlist-form-submit)').bind('click', function(e){
		
				
				var i = accordion.accordion('option', 'active');
				i++;		
				accordion.accordion('option', 'active', i);
						
				e.preventDefault();
				return false;
				
			});
	
	  })(jQuery);
		
		
		
		
		
		
		
		
		
		
		
		
		//multi-dimensional 2D array of event types, bounded elements, and callbacks
		//to be iterated over when pagechange occurs via pushState
		var unload_items = [];
		var timeout_cancels = [];
		var custom_events_unbind = [];
		var facebook_callbacks = [];
		
		
		
		
		
		
		
		
		
		window.teams_guest_list = {
			invitees: []
		}
		
		
		
		
		
		//Friend invite callback
		var click_callback_1 = function(){
			
			var vc_user = jQuery.cookies.get('vc_user');
			if(!vc_user){
				alert('Not logged in.');
				return false;
			}
			
			if(jQuery.fb_root_position)
				jQuery.fb_root_position();
			
			//tracking data
			var data = {
				type: 0,
				gl_type: 1,
				tgla_id: tgla_id,
				tv_id: tv_id
			};
			
			
			FB.ui({
				method: 'apprequests',
				title: 'Add your friends to your entourage!',
				message: vc_user.first_name + ' has added you to their entourage on the guest list "' + tgla_name + '" at ' + tv_name + ' with ClubbingOwl!',
				data: JSON.stringify(data),
				exclude_ids: teams_guest_list.invitees
			}, function(request){
				
				if(!request)
					return;
			
				if(request.to.length > 0){
					
					if(teams_guest_list.invitees.length === 0)
							recipients_table.empty();
					
					recipients_table.append(loading_indicator);
					
					//build FQL query
					var fql = 'select uid, pic_square, name from user where ';
					for(i = 0; i < request.to.length; i++){
						if(i == 0)
							fql += 'uid=' + request.to[i];
						else
							fql += ' OR uid=' + request.to[i];
					}
					
					//execute query and wait on results
					var query = FB.Data.query(fql);
					query.wait(function(rows){
						
						//construct the html to display the guest list members
						
						recipients_table.find('div#loading_indicator').remove();
						
						jQuery('span#facebook-gl-friends-count').html('(' + rows.length + ')');
						
						for(var i in rows){
							recipients_table.append('<div><img class="friend_pic" src="' + rows[i].pic_square + '" /><span class="friend_name">' + rows[i].name + '</span></div>')
							teams_guest_list.invitees.push(rows[i].uid);
						}
						
					});
					
				}
				
			});
			return false;
			
		};
		jQuery('a.facebook-invite').bind('click', click_callback_1);
		unload_items.push([
			'click',
			jQuery('a.facebook-invite'),
			click_callback_1
		]);
		
		
		
		
		
		//Guest-list submit callback
		var click_callback_2 = function(){
			
			
			
			
			
			
			
			
			var vc_user = jQuery.cookies.get('vc_user');
			if(!vc_user){
				alert('Error, you are not logged in to ClubbingOwl.');
				return false;	
			}
			
				
				
				
				
				
			var p_messages = jQuery('p#messages');
			var submit_btn = jQuery(this);
			var loading = jQuery('img#submit_loading');
			
			
			
			
			
			
			
			
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
			var data = {
				ci_csrf_token: 		cct,
				vc_method: 			'team_guest_list_join_request',
				gl_id: 				tgla_id,
				entourage: 			teams_guest_list.invitees,
				table_request: 		(jQuery('input#guestlist-table-request').attr('checked') == undefined) ? false : true,
				
				
				table_min_spend: 	jQuery('select#guest-list-table-price-selection').val(),
				
			
				facebook_share: 	(jQuery('input#guestlist-share').attr('checked') == undefined) ? false : true,
				request_message: 	jQuery('textarea#guestlist-message').val(),
				text_message: 		(jQuery('input#guestlist-confirmation-text').attr('checked') == undefined) ? false : true,
				phone_number: 		jQuery('input#guestlist-confirmation-phone').val(),
				phone_carrier: 		jQuery('select#guestlist-confirmation-carrier').val()
			}
			
			//phone number check
			if(data.text_message == 1){
					
				if(data.phone_number.length == 0){
					p_messages.html('You must enter your cellphone number to recieve a confirmation text!');
					return false;
				}
				
				if(data.phone_number.length < 13){
					p_messages.html('Please enter a valid phone number.');
					return false;
				}
							
			//	if(data.phone_carrier == 'invalid'){
			//		p_messages.html('You must select your phone carrier.');
			//		return false;
			//	}
				
				p_messages.html('');
				
			}
			
			
			
			
			submit_btn.css('display', 'none');
			loading.css('display', 'block');
			
			
			
			var ajax_submit = function(){
				jQuery.ajax({
					url: window.location,
					type: 'post',
					data: data,
					cache: false,
					dataType: 'json',
					success: function(data, textStatus, jqXHR){
						
						
						
						var vc_user = jQuery.cookies.get('vc_user');
						if(_this_data.phone_number)
							vc_user.users_phone_number = _this_data.phone_number.replace(/[^0-9]+/g, '');
						jQuery.cookies.set('vc_user', vc_user);
						console.log(_this_data.phone_number);
						
						
						
						if(data.success){
							
							loading.css('display', 'none');
							jQuery('div#accordion').fadeOut(700, function(){
								jQuery('div#accordion_replace_msg').fadeIn(700, function(){
									
									
									fbEnsureInit(function(){
										FB.XFBML.parse();
									});
									jQuery(this).append('<br><br><a href="#" class="return_cancel" >Return</a>');
										
								});
							});
							
													
						}else if(data.message){
							
							p_messages.css('color', 'red');
							p_messages.html(data.message);
							
						}
						
					}
				});
			};
			
			
	
	
			if(data.facebook_share){
				if(publish_stream){
					ajax_submit();
				}else{
					FB.login(function(response){
				    		
			    		FB.api('/me/permissions', function(response){
			    			
			    			 var publish_stream = response.data[0].publish_stream;
			    			  if(typeof publish_stream === 'undefined' || publish_stream === 0)
			    			  		data.facebook_share = false;
			    			  
			    			  ajax_submit();
			    			 
			    		});
			    			
			    	}, {scope: 'email,publish_stream'});
				}
			}else{
				ajax_submit();
			}
			
			
			return false;	
			
		};
		jQuery('input#guestlist-form-submit').bind('click', click_callback_2);
		unload_items.push([
			'click',
			jQuery('input#guestlist-form-submit'),
			click_callback_2
		]);
		
		
		
		
		
		
		//textarea -------
		var characters = 160;
		var span = jQuery('section#guestlist p.characters span.count');
		span.html(characters);
	
		var keyup_callback_1 = function(){
		    if(jQuery(this).val().length > characters){
		        jQuery(this).val(jQuery(this).val().substr(0, characters));
		    }
		        
		    var remaining = characters - jQuery(this).val().length;		
			span.html(remaining);
			
		};
		jQuery("textarea#guestlist-message").keyup(keyup_callback_1);
		unload_items.push([
			'keyup',
			jQuery("textarea#guestlist-message"),
			keyup_callback_1
		]);
		
		
		
		
		//input mask on phone number -------
		jQuery('input#guestlist-confirmation-phone').mask('(999)-999-9999');
		
		var rm_func = function(){
			jQuery('textarea#guestlist-message').val('').trigger('keyup');
			jQuery('input#guestlist-table-request').removeAttr('checked');
			jQuery('input#guestlist-share').attr('checked', 'checked');
			
			jQuery('input#guestlist-confirmation-text').removeAttr('checked');
			jQuery('input#guestlist-confirmation-phone').val('');
			jQuery('select#guestlist-confirmation-carrier').val('invalid');
			jQuery('div#facebook-guest-list-friends').html('No one yet.');
			jQuery('p#messages').html('');
			
			window.teams_guest_list.invitees = [];
		};
		
		
		
		
		
		
		
		var vc_login_callback = function(){
			
			jQuery('input#guestlist-confirmation-phone').val('');
			var vc_user = jQuery.cookies.get('vc_user');
			if(vc_user && vc_user.users_phone_number){
				jQuery('input#guestlist-confirmation-phone').val(vc_user.users_phone_number);
				if(!jQuery('#guestlist-confirmation-text').is(':checked')){
					jQuery('#guestlist-confirmation-text').trigger('click');
				}
			}
			
			
			jQuery('div#unavailable_overlay').css('display', 'none');
			rm_func();
		};
		window.EventHandlerObject.addListener("vc_login", vc_login_callback);
		custom_events_unbind.push([
			'vc_login',
			window.EventHandlerObject,
			vc_login_callback
		]);
		
		
		
		
		
			
		var vc_logout_callback = function(){
			jQuery('div#unavailable_overlay').css('display', 'block');
			rm_func();
		};
		window.EventHandlerObject.addListener("vc_logout", vc_logout_callback);
		custom_events_unbind.push([
			'vc_logout',
			window.EventHandlerObject,
			vc_logout_callback
		]);
		
		
		
		
		
		
		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			console.log('unbind_callback');
			console.log(unload_items);
			console.log(timeout_cancels);
			console.log(custom_events_unbind);
			
			for(var i in unload_items){
				unload_items[i][1].unbind(unload_items[i][0], unload_items[i][2]);
			}
			
			for(var i in timeout_cancels){
				clearTimeout(timeout_cancels[i]);
			}
			
			for(var i in custom_events_unbind){
				custom_events_unbind[i][1].removeListener(custom_events_unbind[i][0], custom_events_unbind[i][2]);
			}
			
			for(var i in facebook_callbacks){
				facebook_callbacks[i] = function(){};
			}
			
		}
				

	}
	
	
	
});