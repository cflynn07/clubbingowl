if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_promoters_statistics = function(){
						
		var unbind_callbacks = [];		
			
		jQuery('.tabs').tabs();
		jQuery('img.tooltip').tooltip();		
		jQuery("div.datepicker").datepicker();










		jQuery('div#main_loading_indicator').remove();
		jQuery('div#tabs').css('display', 'block').tabs();
		
		
		
		
		
		var tabbsshow_callback = function(event, ui){
			console.log(jQuery('div#tabs-' + ui.index));
			jQuery('div#tabs-' + ui.index).width('+=1').width('-=1');
		}
		jQuery('div#tabs').live('tabsshow', tabbsshow_callback);
		unbind_callbacks.push(function(){
			jQuery('div#tabs').die('tabsshow', tabbsshow_callback);
		});
		
		
		
		
		var fb_operation_complete = false;
		
		var pop_data;
		
		(function(){
			
			var ensure_fb_operation_complete = function(callback) {
		        if(!fb_operation_complete) {
		            setTimeout(function() {ensure_fb_operation_complete(callback);}, 50);
		        } else {
		            if(callback) {
		                callback();
		            }
		        }
		    }
			
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
						vc_method: 'stats_retrieve'
			 		},
					cache: false,
					dataType: 'json',
					success: function(data, textStatus, jqXHR){
					
						console.log(data);
					
						if(data.success){
														
							incrementor = 0;
							//display_function(data.message);
							pop_data = data.message;
							ensure_fb_operation_complete(display_function);
							
						}else{
							
							incrementor++;
							setTimeout(retrieve_function, 2000);
							
						}
					
					}
				});
			};
			
			retrieve_function();
			
			var display_function = function(){
				
				console.log('display function called');
				
				var data = pop_data;
				
				for(up_id in data){
					
					var categories = [];
					var visits = [];
					var unique_visitors = [];
					
					var count = 0;
					for(key in data[up_id].visits){
												
						if(!(count % 2))
							categories.push(key.toString().substring(5, 10).replace('-', '/'));
						else
							categories.push(' ');
						
						count++;
						
						visits.push(data[up_id].visits[key]);
						unique_visitors.push(data[up_id].unique_visitors[key]);
					}
					
					console.log(categories);
					console.log(visits);
					console.log(unique_visitors);
					console.log('div#table_stats_up_' + up_id);
					
					jQuery('div#table_stats_up_' + up_id).html('').show();
					
					
					window['visitors_chart' + up_id] = new Highcharts.Chart({
						credits: {
							enabled: false
						},
						chart: {
							renderTo: 'table_stats_up_' + up_id,
							defaultSeriesType: 'area',
							//width: 719
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
					
			}
		})();
		
		fbEnsureInit(function(){
			
	
			fb_operation_complete = true;
			
			
		});





		jQuery('div#tabs > div.ui-widget-header select.promoter_select').bind('change', function(){
			jQuery('div#tabs').tabs('select', parseInt(jQuery(this).val()));
		});
		jQuery('div#tabs > div.ui-widget-header > ul').css('display', 'none');








		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});