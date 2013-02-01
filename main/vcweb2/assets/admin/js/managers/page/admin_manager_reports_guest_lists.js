if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_reports_guest_lists = function(){
						
		var unbind_callbacks = [];
		var EVT = window.ejs_view_templates_admin_managers;
		
		
			
		jQuery('#reports_wrapper').tabs().show();
		
		
		
		
		combo_chart = new Highcharts.Chart({
			credits: {
				enabled: false
			},
			
			chart: {
				renderTo: 'combo_chart_guest_lists',
				width: 950
			},
			title: {
				text: 'Guest List Reservations'
			},
			xAxis: {
			//	categories: ['Apples', 'Oranges', 'Pears', 'Bananas', 'Plums']
			//	categories: <?= json_encode(array_keys($team_trailing_gl_requests)) ?>
				categories: window.page_obj.team_trailing_gl_requests_keys
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Reservations'
				}
			},
			tooltip: {
				formatter: function() {
					var s;
					s = 'Reservations: ' + this.y;
					return s;
				}
			},
			labels: {
				items: [{
					html: 'Total Reservations',
					style: {
						left: 	'40px',
						top: 	'8px',
						color: 	'black'
					}
				}]
			},
			series: window.page_obj.series_array
		});
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		var Models 		= {};
		var Collections = {};
		var Views 		= {};
		
		var Events = _.extend({}, Backbone.Events);
		
		
		Models.TeamVenue = Backbone.Model.extend({
			initialize: function(){},
			defaults: {}
		});
		Collections.TeamVenues = Backbone.Collection.extend({
			model: Models.TeamVenue,
			initialize: function(){}
		});
		
		
		
		Models.Promoter = Backbone.Model.extend({
			initialize: function(){},
			defaults: {}
		});
		Collections.Promoters = Backbone.Collection.extend({
			model: Models.Promoter,
			initialize: function(){}
		});
		
		
				
		Views.GuestListsOptions = Backbone.View.extend({
			el: '#filter_options',
			className: 'ui-widget',
			initialize: function(){
				
				this.render();
			},
			render: function(){
				
				var _this = this;
				var template = EVT['reports/ejs_guest_lists_options'];
				var html = new EJS({
					text: template
				}).render({
					team_venues: this.options.teamVenues.toJSON(),
					promoters: 	 this.options.promoters.toJSON()
				});
				
				this.$el.html(html);
				
				//initialize datepickers
				this.$el.find('input[name=start_date]').datepicker({
					defaultDate: 	'-1w',
					changeMonth: 	true,
					numberOfMonths: 1,
					onClose: function(selectedDate){
						_this.$el.find('input[name=end_date]').datepicker('option', 'minDate', selectedDate);
					}
				});
				this.$el.find('input[name=end_date]').datepicker({
					defaultDate: 	'+0d',
					changeMonth: 	true,
					numberOfMonths: 1,
					onClose: function(selectedDate){
						_this.$el.find('input[name=start_date]').datepicker('option', 'maxDate', selectedDate);
					}
				});
				this.$el.find('input[name=start_date]').datepicker('setDate', '-1w');
				this.$el.find('input[name=end_date]').datepicker('setDate', '0');
				
				
				
				this.$el.addClass('ui-widget');
				
			},
			events: {
				'change input': 'change_input'	
			},
			
			change_input: function(){
				
				Events.trigger('fetch_start');
				
				var promoters = [];
				var venues	  = [];

				
				jQuery('input[data-tv_id]:checked').each(function(){
					venues.push(jQuery(this).attr('data-tv_id'));
				});
				jQuery('input[data-up_id]:checked').each(function(){
					promoters.push(jQuery(this).attr('data-up_id'));
				});
				
				
				var selected_props = {
					vc_method: 	'gl_report_update_filter',
					start_date: jQuery.datepicker.formatDate('yy-mm-dd', this.$el.find('input[name=start_date]').datepicker('getDate')),
					end_date:	jQuery.datepicker.formatDate('yy-mm-dd', this.$el.find('input[name=end_date]').datepicker('getDate')),
					promoters:	promoters,
					venues:		venues
				}
				
				jQuery.background_ajax({
					data: selected_props,
					success: function(data){
						Events.trigger('fetch_finish');
					}
				});
				
			}
			
		});
		
		Views.GuestListsSummary = Backbone.View.extend({
			el: '#data_summary',
			initialize: function(){ 
				
				var _this = this;
				Events.on('fetch_start', function(){
					_this.$el.css({
						opacity: 0.4
					});
				});
				
				Events.on('fetch_finish', function(){
					_this.render();
				});
				
				this.render(); 
			},
			render: function(){
				
				var _this = this;
				var template = EVT['reports/ejs_guest_lists_summary'];
				var html = new EJS({
					text: template
				}).render({});
				
				this.$el.html(html).css({
					opacity: 1
				});
				
				this.$el.addClass('ui-widget');
				
			},
			events: {}
		});
		
		Views.GuestListsDetail = Backbone.View.extend({
			el: '#data_detail',
			initialize: function(){ 
				
				var _this = this;
				Events.on('fetch_start', function(){
					_this.$el.css({
						opacity: 0.4
					});
				});
				
				Events.on('fetch_finish', function(){
					_this.render();
				});
				
				this.render(); 
			
			},
			render: function(){
				
				var _this = this;
				var template = EVT['reports/ejs_guest_lists_details'];
				var html = new EJS({
					text: template
				}).render({});
				
				this.$el.html(html).css({
					opacity: 1
				});
				
				this.$el.addClass('ui-widget');
								
			},
			events: {}
		});
		
		
		var teamVenues, promoters, guestListOptions;
		var initialize_report = function(){
			
			teamVenues		 = new Collections.TeamVenues(window.page_obj.team_venues);
			promoters		 = new Collections.Promoters(_.values(window.page_obj.promoters));
			
			guestListOptions = new Views.GuestListsOptions({
				teamVenues: teamVenues,
				promoters:	promoters
			});
			
			guestListSummary = new Views.GuestListsSummary();
			guestListDetail  = new Views.GuestListsDetail();
		
		}
		
		initialize_report();
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//------------------------------------------------------------
	/*	
			
		gl_attendance_percentage_time = new Highcharts.Chart({
			credits: {
				enabled: false
			},
			chart: {
				renderTo: 'gl_attendance_percentage_time',
				type: 'column'
			},
			title: {
				text: 'Percent Guest List Attendance'
			},
			xAxis: {
				
			//	categories: <?= json_encode(array_keys($team_trailing_gl_requests)) ?>
				categories: window.page_obj.team_trailing_gl_requests_keys
			
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Guest List Attendance Percentage'
				}
			},
			tooltip: {
				formatter: function() {
					return ''+
						this.series.name +': '+ this.y +' ('+ Math.round(this.percentage) +'%)';
				}
			},
			plotOptions: {
				column: {
					stacking: 'percent'
				}
			},
			
			series: [{
				name: 'Did Not Attend',
			//	data: <?= json_encode($did_not_attend_time) ?>,
				data: window.page_obj.did_note_attend_time,
				color: 'rgb(154, 50, 49)'
			},{
				name: 'Attended',
			//	data: <?= json_encode($attended_time) ?>,
				data: window.page_obj.attended_time,
				color: 'rgb(118, 152, 56)'
			}]
		});
		
		
		
		
		
		//------------------------------------------------------------
		
		gl_attendance_percentage_team_promoters = new Highcharts.Chart({
			credits: {
				enabled: false
			},
			chart: {
				renderTo: 'gl_attendance_percentage_promoter',
				type: 'column'
			},
			title: {
				text: 'Percent Guest List Attendance'
			},
			xAxis: {
			//	categories: <?= json_encode($cats) ?>
				categories: window.page_obj.cats
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Guest List Attendance Percentage'
				}
			},
			tooltip: {
				formatter: function() {
					return ''+
						this.series.name +': '+ this.y +' ('+ Math.round(this.percentage) +'%)';
				}
			},
			plotOptions: {
				column: {
					stacking: 'percent'
				}
			},
			series: [{
				name: 'Did Not Attend',
		//		data: <?= json_encode($did_not_attend_array) ?>,
				data: window.page_obj.did_not_attend_array,
				color: 'rgb(154, 50, 49)'
			},{
				name: 'Attended',
		//		data: <?= json_encode($attended_array) ?>,
				data: window.page_obj.attended_array,
				color: 'rgb(118, 152, 56)'
			}]
		});
		
		
		
		
		
		
		
		*/
		









		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});