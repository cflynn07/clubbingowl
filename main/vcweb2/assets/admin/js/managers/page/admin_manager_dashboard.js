if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_dashboard = function(){
						

			
		var Models 		= {};
		var Collections = {};
		var Views 		= {};

		Views.TeamAnnoucements = {
			el: '#team_announcements',
			team_managers: [],
			initialize: function(){
				
				this.render();
			},
			render: function(){
				
				var oauth_uids = [];
				
				//gather all uids
				this.$el.find('*[data-oauth_uid]').each(function(){
					
					var oauth_uid = jQuery(this).attr('data-oauth_uid');
					if(_.indexOf(oauth_uids, oauth_uid) == -1)
						oauth_uids.push(oauth_uid);
					
				});
				
				var _this = this;
				jQuery.fbUserLookup(oauth_uids, '', function(rows){
					
					for(var i in rows){
						_this.$el.find('.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="" />');
						_this.$el.find('.name_' + rows[i].uid).html(rows[i].name);												
					}
					
					_this.$el.find('#messages_loading_indicator').hide();
					_this.$el.find('#team_announcements_content').show();
					
				});
				
				
			},
			events: {
				'click *[data-action]': 'click_data_action'
			},
			click_data_action: function(e){
				
				e.preventDefault();
				
				var el 		= jQuery(e.currentTarget);
				var action 	= el.attr('data-action');
				switch(action){
					case 'create-announcement':
					
						if(!jQuery('div#announcement_dialog').hasClass('ui-dialog'))
							jQuery('div#announcement_dialog').dialog({
								resizable: 	false,
								height:		340,
								modal: 		true,
								buttons: [{
									text: 'Cancel',
									click: function(){
										
										
										
										jQuery(this).dialog('close');
									}
								},{
									text: 'Okay',
									'class': 'btn-confirm',
									click: function(){
										
										var _this = this;
										
										var message = jQuery('textarea#manager_announcement_textarea').val();
										if(message.length == 0){
											jQuery('p#manager_announcement_msg').html('Message can not be blank');
											return;
										}
										
										jQuery('div#announcement_dialog .loading_indicator').show();
										
										jQuery.background_ajax({
											data: {
												vc_method: 'announcement_create',
												message: 	message
											},
											success: function(data){
												jQuery('div#announcement_dialog .loading_indicator').hide();
												
												if(data.success){
													
													jQuery(_this).dialog('close').remove();
													jQuery('#primary_left .li_dashboard a').trigger('click');
													
												}else{
													
													if(data.message)
														jQuery('p#manager_announcement_msg').html(data.message);
													
												}
											}
										});
										
										jQuery(this).dialog('close');
									}
								}]
							});
				}
			}
		}; Views.TeamAnnoucements = Backbone.View.extend(Views.TeamAnnoucements);
		
		Views.TeamStatistics = {
			el: '#team_statistics',
			initialize: function(){
				
				
				if(window.page_obj.team.team_completed_setup == '1')
					this.render();
					
			},
			render: function(){
				
				var count = 0;
				var retrieve_function = function(){
					
					console.log('retrieve_function called');
					
					if(count > 4){
						count = 0;
						jQuery('div.team_stats_tabs img#loading_gif').remove();
						jQuery('div.team_stats_tabs div#tabs-1').html('<span style="color:red">We\'re sorry, something went wrong. We\'ll get it fixed as soon as possible, please try again in a few minutes.</span>').css('display', 'block');
						return;
					}
					
					jQuery.background_ajax({
						data: {
							vc_method: 		'stats_retrieve',
							status_check: 	true,
						},
						success: function(data){
							
							if(!data.success){
								count++;
								setTimeout(retrieve_function, 1000);
								return;
							}
							count = 0;
							
							
							var categories 		= [];
							var visits 			= [];
							var unique_visitors = [];
							
							if(typeof data.message.visits.length == undefined)
								return; //error
								
							var counter = 0;
							for(key in data.message.visits){
															
								if(!(counter % 2))
									categories.push(key.toString().substring(5, 10).replace('-', '/'));
								else
									categories.push(' ');
								
								counter++;
								
								visits.push(data.message.visits[key]);
								unique_visitors.push(data.message.unique_visitors[key]);
							}

							jQuery('img#loading_gif').remove();
							
							jQuery('div#tabs-1').css('display', 'block');
												
							visitors_chart = new Highcharts.Chart({
								chart: {
									renderTo: 'tabs-1',
									defaultSeriesType: 'area'
								},
								tooltip:{
									enabled: false
								},
								width: '100%',
								margin: [0, 0, 0, 0],
								xAxis: {
									categories: categories,
									tickmarkPlacement: 'on'
								},
								title: {
									text: ' '
								},
								yAxis: {
									title: {
										text: null
									}
								},
								series: [{
									name: 'Visits',
									data: visits
								},{
									name: 'Unique Visitors',
									data: unique_visitors
								}]
							});
							
						}
					});
				};
				
				retrieve_function();
				
				
				
				
				trailing_requests_chart = new Highcharts.Chart({
					chart: {
						renderTo: 'tabs-2',
						type: 'column',
						width: 1048
					},
					title: {
						text: ' '
					},
					margin: [0, 0, 0, 0],
					xAxis: {
						categories: window.page_obj.trailing_gl_requests_categories
					},
					yAxis: {
						title: {
							text: null
						}
					},
					tooltip:{
						enabled: false
					},
					legend: {
						layout: 'vertical',
						backgroundColor: '#FFFFFF',
						align: 'left',
						verticalAlign: 'top',
						x: 100,
						y: 70,
						floating: true,
						shadow: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.2,
							borderWidth: 0
						}
					},
						series: [{
						name: 'Requests',
						data: window.page_obj.trailing_gl_requests_values
			
					}]
				});

			}
		}; Views.TeamStatistics = Backbone.View.extend(Views.TeamStatistics);

		
		
		var views_team_announcements = new Views.TeamAnnoucements({});
		var views_team_statistics	 = new Views.TeamStatistics({});
		
		
		
		
		
		
		if(page_obj.statistics.top_visitors.length == 0){
			jQuery('div#top_visitors').empty();
		}else{
			jQuery.fbUserLookup(page_obj.statistics.top_visitors, '', function(rows){
				
				jQuery('div#top_visitors').empty();
				
				for(var i in rows){
					var user = rows[i];
					
					if(user.uid == window.page_obj.users_oauth_uid)
						continue;
					
					var html = '<div class="top_visitor ' + user.uid + '">';
					html += '<img style="width:50px;height:50px;" src="' + user.pic_square + '" alt="picture" />';
					html += '<a href="#" class="vc_name"><span class="uid">' + user.uid + '</span>' + user.first_name + '</a>';
					html += '</div>';
					jQuery('div#top_visitors').append(html);
				
				}
				
			});
		}
		
		
		if(page_obj.statistics.recent_visitors.length == 0){
			jQuery('div#recent_visitors').empty();
		}else{
			jQuery.fbUserLookup(page_obj.statistics.recent_visitors, '', function(rows){
				
				jQuery('div#recent_visitors').empty();
				
				for(var i in rows){
					var user = rows[i];
					
					if(user.uid == window.page_obj.users_oauth_uid)
						continue;
						
					var html = '<div class="recent_visitor ' + user.uid + '">';
					html += '<img style="width:50px;height:50px;" src="' + user.pic_square + '" alt="picture" />';
					html += '<a href="#" class="vc_name"><span class="uid">' + user.uid + '</span>' + user.first_name + '</a>';
					html += '</div>';
					jQuery('div#recent_visitors').append(html);
					
				}
				
			});
		}
		
		
		
		
		
		
		
		
		
		
		
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
		    
		    xhr.send('socket_id=' + encodeURIComponent(pusher.connection.socket_id) + '&channel_name=' + encodeURIComponent(self.name) + '&ci_csrf_token=' + csrf_token);
		};
		
		
		var pusher = new Pusher(window.module.Globals.prototype.pusher_api_key);
		var team_user_presence = pusher.subscribe('presence-teamvisitors-' + window.page_obj.team.team_fan_page_id);
		
		team_user_presence.bind('pusher:subscription_succeeded', function(members){
			
		
			//fix box we care about
			jQuery('div#live_visitors').empty();
			
			var users = [];
			
			members.each(function(member){
				users.push(member.id);
			});
			
			if(users.length == 0)
				return;
				
			jQuery.fbUserLookup(users, '', function(rows){
				
				for(var i in rows){
					
					var user = rows[i];
					
					if(user.uid == window.page_obj.users_oauth_uid)
						continue;
					
					var html = '<div data-user_oauth_uid="' + user.uid + '" class="live_visitor ' + user.uid + '">';
					html += '<img style="width:50px;height:50px;" src="' + user.pic_square + '" alt="picture" />';
					html += '<a href="#" class="vc_name"><span class="uid">' + user.uid + '</span>' + user.first_name + '</a>';
					html += '</div>';
					jQuery('div#live_visitors').append(html);
					
				}
				
			});
			
			
		});
		
		team_user_presence.bind('pusher:member_added', function(member){
		  	
		  	
		  	if(jQuery('div#live_visitors').find('div[data-user_oauth_uid="' + member.id + '"]').length == 0){
		  		
		  		jQuery.fbUserLookup([member.id], '', function(rows){
		  			
		  			for(var i in rows){
						
						var user = rows[i];
						
						if(user.uid == window.page_obj.users_oauth_uid)
							continue;
						
						var html = '<div data-user_oauth_uid="' + user.uid + '" class="live_visitor ' + user.uid + '">';
						html += '<img style="width:50px;height:50px;" src="' + user.pic_square + '" alt="picture" />';
						html += '<a href="#" class="vc_name"><span class="uid">' + user.uid + '</span>' + user.first_name + '</a>';
						html += '</div>';
						jQuery('div#live_visitors').append(html);
						
					}
		  			
		  		});
		  		
		  	}
		  	
		  	 	
		});
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			
		}
		
		

	}
	
});