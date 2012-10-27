if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.venues_home = function(){
		
		
		
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
							
							if(data.trigger_refresh){
								window.location.reload();
								return;
							}
							
							venue_items.retrieve_lock = false;
						
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
							vc_method: 'friend_venue_activity_retrieve'
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
				
		//		venue_items.generate_pop_charts(true);
				
				for(var i in data.tv_friends_pop){
					
					var tv_u_friends = jQuery('div#friends_' + i);
					if(data.tv_friends_pop[i].length > 0){
						
						
						
						var tdata = {
							tv_friends_pop: data.tv_friends_pop[i],
							user_friends: data.user_friends
						};
						
						tv_friends_html = new EJS({
					//		element: jQuery('div#ejs_venues_templates > div#friends_venues').get(0)
							text: ejs_view_templates.venues_friends_visited
						}).render(tdata);
						
						
						
						/*
						
						
						var tv_friends_html = '<p>' + data.tv_friends_pop[i].length + 'friend(s) have been here</p>';
						
						for(var k in data.tv_friends_pop[i]){
							
							var friend = data.user_friends[data.tv_friends_pop[i][k]];
							
							tv_friends_html += '<a style="display:inline-block;" href="' + window.module.Globals.prototype.front_link_base + 'friends/' + friend.third_party_id + '/" title="' + friend.name + '">';
							tv_friends_html += '<img class="thumbnail" style="width:20px; height:20px;" src="' + friend.pic_square + '" alt="' + friend.name + '">';
							tv_friends_html += '</a>';
						}
						
						*/
						
						
						
						
						tv_u_friends.find('img.loading_indicator').css('display', 'none');
						tv_u_friends.append(tv_friends_html);
						
					}else{
						tv_u_friends.html('');
					}
					
				}
				
			},
			generate_pop_charts: function(data){
				
				
				chart = new Highcharts.Chart({
		            chart: {
		                renderTo: 'graphs',
		                type: 'bar'
		            },
		            title: {
		                text: 'Historic World Population by Region'
		            },
		            subtitle: {
		                text: 'Source: Wikipedia.org'
		            },
		            xAxis: {
		                categories: ['Africa', 'America', 'Asia', 'Europe', 'Oceania'],
		                title: {
		                    text: null
		                }
		            },
		            yAxis: {
		                min: 0,
		                title: {
		                    text: 'Population (millions)',
		                    align: 'high'
		                }
		            },
		            tooltip: {
		                formatter: function() {
		                    return ''+
		                        this.series.name +': '+ this.y +' millions';
		                }
		            },
		            plotOptions: {
		                bar: {
		                    dataLabels: {
		                        enabled: true
		                    }
		                }
		            },
		            legend: {
		                layout: 'vertical',
		                align: 'right',
		                verticalAlign: 'top',
		                x: -100,
		                y: 100,
		                floating: true,
		                borderWidth: 1,
		                backgroundColor: '#FFFFFF',
		                shadow: true
		            },
		            credits: {
		                enabled: false
		            },
		            series: [{
		                name: 'Year 1800',
		                data: [107, 31, 635, 203, 2]
		            }, {
		                name: 'Year 1900',
		                data: [133, 156, 947, 408, 6]
		            }, {
		                name: 'Year 2008',
		                data: [973, 914, 4054, 732, 34]
		            }]
		        });
		        
				
			}
		}
		
		if(window.vc_server_auth_session)
			venue_items.retrieve_feed(false);
		
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
			
		});

	
	}
	
});