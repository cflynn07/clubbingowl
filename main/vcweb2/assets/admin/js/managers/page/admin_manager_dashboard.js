if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_dashboard = function(){
						
		var unbind_callbacks = [];		
			
		
	


		//announcement system
		var announcement_btn_callback = function(){
			
			
			jQuery('div#announcement_dialog').dialog({
				resizable: false,
				height:340,
				modal: true,
				buttons: {
					"Okay": function() {
						
						var _this = this;
						
						var message = jQuery('textarea#manager_announcement_textarea').val();
						if(message.length == 0){
							jQuery('p#manager_announcement_msg').html('Message can not be blank');
							return;
						}
						
						jQuery('div#announcement_dialog .loading_indicator').show();
						
						var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
						jQuery.ajax({
							url: window.location,
							type: 'post',
							data: {
								ci_csrf_token: cct,
								vc_method: 'announcement_create',
								message: message
							},
							cache: false,
							dataType: 'json',
							success: function(data, textStatus, jqXHR){
								
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
						
					},
					Cancel: function() {
						jQuery(_this).dialog('close');
					}
				}
			});
			return false;
		}
		jQuery('a#create_announcement_btn').bind('click', announcement_btn_callback);
	
	
	
		var team_managers = [];
		for(var i in window.page_obj.team_chat_members.managers){
			team_managers.push(window.page_obj.team_chat_members.managers[i].oauth_uid);
		}
		if(team_managers.length > 0){
			jQuery.fbUserLookup(team_managers, '', function(rows){
				
				for(var i in rows){
					
					jQuery('div#team_announcements div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '">');
					
				}
				
				jQuery('img#messages_loading_indicator').remove();
				jQuery('div#team_announcements').show();
				
			});
		}else{
			jQuery('img#messages_loading_indicator').remove();
			jQuery('div#team_announcements').show();
		}
			

















	
		
		var promoter_dash_global = {
			
			count: 0
			
		}
			
		var retrieve_function = function(){
			
			console.log('retrieve_function called');
			
			if(promoter_dash_global.count > 4){
				promoter_dash_global.count = 0;
				jQuery('div.team_stats_tabs img#loading_gif').remove();
				jQuery('div.team_stats_tabs div#tabs-1').html('<span style="color:red">We\'re sorry, something went wrong. We\'ll get it fixed as soon as possible, please try again in a few minutes.</span>').css('display', 'block');
				return;
			}
			
			//cross-site request forgery token, accessed from session cookie
			//requires jQuery cookie plugin
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
	
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: {'ci_csrf_token': cct,
						'status_check': true,
						'vc_method': 'stats_retrieve'},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
					if(data.success){
						
						var categories = [];
						var visits = [];
						var unique_visitors = [];
						
						if(typeof data.message.visits.length == undefined)
							return; //error
						
						var count = 0;
						for(key in data.message.visits){
							
						//	console.log(key);
							
							if(!(count % 2))
								categories.push(key.toString().substring(5, 10).replace('-', '/'));
							else
								categories.push(' ');
							
							count++;
							
							visits.push(data.message.visits[key]);
							unique_visitors.push(data.message.unique_visitors[key]);
						}
						console.log(categories);
						
						promoter_dash_global.count = 0;
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
						
					}else{
						
						promoter_dash_global.count = promoter_dash_global.count + 1;
						setTimeout(retrieve_function, 1000);
						
					}
					
				}
			});
			
		};		
		

		if(window.page_obj.team.team_completed_setup == '1')
			setTimeout(retrieve_function, 1000);
		
		fbEnsureInit(function(){
			
			if(! jQuery('div#pending_reservations table tbody tr.loading'))
				return;
			
			//var users = eval('<?= json_encode($users) ?>');
			var users = window.page_obj.users;
			
			if(users.length > 0){
				
				var fql = "SELECT uid, name, pic_square, pic_big, third_party_id FROM user WHERE ";
				for(var i = 0; i < users.length; i++){
					if(i == (users.length - 1)){
						fql += "uid = " + users[i];
					}else{
						fql += "uid = " + users[i] + " OR ";
					}
				}
				
				var query = FB.Data.query(fql);
				query.wait(function(rows){
					
														
					//populate divs with FB data
					for(var i = 0; i < rows.length; i++){
						
						jQuery('div#pending_reservations div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
						jQuery('div#pending_reservations div.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
						
					}
					
					jQuery('div#pending_reservations table tbody tr.loading').remove();
					jQuery('div#pending_reservations table tbody tr').each(function(){
						
						if(jQuery(this).attr('id') != 'table_row_tpl')
							jQuery(this).css('display', '');
							
					});
					
					zebraRows();
													
				});
				
			}
			
		});
		
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











		fbEnsureInit(function(){
			
		//	var users = eval('<?= json_encode($statistics->top_visitors) ?>');
			var users = window.page_obj.statistics.top_visitors;
			
			if(users.length == 0){
				jQuery('div#top_visitors').empty();
				return;
			}
			
			var fql = "SELECT uid, name, first_name, pic_square, pic_big, third_party_id FROM user WHERE ";
			for(var i = 0; i < users.length; i++){
				if(i == (users.length - 1)){
					fql += "uid = " + users[i];
				}else{
					fql += "uid = " + users[i] + " OR ";
				}
			}
			
			var query = FB.Data.query(fql);
			query.wait(function(rows){
				console.log(rows);
				
				jQuery('div#top_visitors').empty();
				
				for(var i = 0; i < rows.length; i++){
					
					if(rows[i].uid == window.page_obj.users_oauth_uid)
						continue;
					
					var html = '<div class="top_visitor ' + rows[i].uid + '">';
					html += '<img style="width:50px;height:50px;" src="' + rows[i].pic_square + '" alt="picture" />';
					html += '<a href="#" class="vc_name"><span class="uid">' + rows[i].uid + '</span>' + rows[i].first_name + '</a>';
					html += '</div>';
					jQuery('div#top_visitors').append(html);
				}
				
			});
		});



		fbEnsureInit(function(){
		//	var users = eval('<?= json_encode($statistics->recent_visitors) ?>');
			
			var users = window.page_obj.statistics.recent_visitors;
			
			if(users.length == 0){
				jQuery('div#recent_visitors').empty();
				return;
			}
			
			var fql = "SELECT uid, name, first_name, pic_square, pic_big, third_party_id FROM user WHERE ";
			for(var i = 0; i < users.length; i++){
				if(i == (users.length - 1)){
					fql += "uid = " + users[i];
				}else{
					fql += "uid = " + users[i] + " OR ";
				}
			}
			
			var query = FB.Data.query(fql);
			query.wait(function(rows){
				console.log(rows);
				
				jQuery('div#recent_visitors').empty();
				
				for(var i = 0; i < rows.length; i++){
					
					if(rows[i].uid == window.page_obj.users_oauth_uid)
						continue;
					
					var html = '<div class="recent_visitor ' + rows[i].uid + '">';
					html += '<img style="width:50px;height:50px;" src="' + rows[i].pic_square + '" alt="picture" />';
					html += '<a href="#" class="vc_name"><span class="uid">' + rows[i].uid + '</span>' + rows[i].first_name + '</a>';
					html += '</div>';
					jQuery('div#recent_visitors').append(html);
				}
				
			});
		});













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
			
			window.live_visitor_timeout_remove_queue = {};
			
			//fix box we care about
			jQuery('div#live_visitors').empty();
			
			var users = [];
			
			members.each(function(member){
				users.push(member.id);
			});
			
			if(users.length == 0)
				return;
			
			var fql = "SELECT uid, name, first_name, pic_square, pic_big, third_party_id FROM user WHERE ";
			for(var i = 0; i < users.length; i++){
				if(i == (users.length - 1)){
					fql += "uid = " + users[i];
				}else{
					fql += "uid = " + users[i] + " OR ";
				}
			}
			
			var query = FB.Data.query(fql);
			query.wait(function(rows){
				console.log(rows);
				
				for(var i = 0; i < rows.length; i++){
					
					if(rows[i].uid == window.page_obj.users_oauth_uid)
						continue;
					
					var html = '<div class="live_visitor ' + rows[i].uid + '">';
					html += '<img style="width:50px;height:50px;" src="' + rows[i].pic_square + '" alt="picture" />';
					html += '<a href="#" class="vc_name"><span class="uid">' + rows[i].uid + '</span>' + rows[i].first_name + '</a>';
					html += '</div>';
					jQuery('div#live_visitors').append(html);
				}
				
			});
		});
		
		team_user_presence.bind('pusher:member_added', function(member){
		  	
		  	if(window.live_visitor_timeout_remove_queue[member.id]){
		  		clearTimeout(window.live_visitor_timeout_remove_queue[member.id]);
		  	}
		  	
		  	//make sure not already in list
		  	if(jQuery('div#live_visitors').find('div.' + member.id).length > 0)
		  		return;
		  	
		  	var fql = "SELECT uid, name, first_name, pic_square, pic_big, third_party_id FROM user WHERE uid = " + member.id;
		  	console.log(fql);
		  	var query = FB.Data.query(fql);
			query.wait(function(rows){
				console.log(rows);
				
				for(var i = 0; i < rows.length; i++){
					var html = '<div class="live_visitor ' + rows[i].uid + '">';
					html += '<img src="' + rows[i].pic_square + '" alt="picture" />';
					html += '<a href="#" class="vc_name"><span class="uid">' + rows[i].uid + '</span>' + rows[i].first_name + '</a>';
					html += '</div>';
					jQuery('div#live_visitors').append(html);
				}
				
			});
			
		  	console.log('teamvisitors member_added');
			console.log(member)
							  					  	
		});
		
		team_user_presence.bind('pusher:member_removed', function(member){
		  	
		  	window.live_visitor_timeout_remove_queue[member.id] = setTimeout(function(){
		  		jQuery('div#live_visitors').find('div.' + member.id).remove();
		  	}, 1000 * 5);
		  	
		  	console.log('teamvisitors member_removed');
		  	console.log(member);
		  	
		});
		

		unbind_callbacks.push(function(){
			console.log('unbind callback pusher disconnect');
			team_user_presence.disconnect();
		});

















		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});