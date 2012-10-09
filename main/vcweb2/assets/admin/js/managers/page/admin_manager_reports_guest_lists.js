if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_reports_guest_lists = function(){
						
		var unbind_callbacks = [];		
			
		
		
		
		
		
		
		
		
		
		
		
		
		
		combo_chart = new Highcharts.Chart({
			chart: {
				renderTo: 'combo_chart_guest_lists'
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
					html: 'Total reservations',
					style: {
						left: '40px',
						top: '8px',
						color: 'black'
					}
				}]
			},
			series: window.page_obj.series_array
		});
		
		
		
		//------------------------------------------------------------
		
			
		gl_attendance_percentage_time = new Highcharts.Chart({
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
		
		
		
		
		
		
		
		
		









		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});