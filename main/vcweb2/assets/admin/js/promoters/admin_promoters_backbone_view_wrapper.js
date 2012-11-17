jQuery(function(){
	
	var Models = {};
	var Collections = {};
	var Views = {};
	
	Views.AdminWrapper = {
		el: '#bgwrap',
		initialize: function(){
			console.log('Views.AdminWrapper.initialize()');
			this.$el.find('#menu ul ul').hide();			
			
		},
		ajaxify_change: function(){
			/**
			 * Here the contents of #primary_right will have been replaced
			 */
			var el_pr = this.$el.find('#primary_right');
			
			Cufon.replace('h1, h2, h5, .notification strong', { hover: 'true' });
			el_pr.find('.tablesorter').tablesorter();
			el_pr.find('.iphone').iphoneStyle();
			el_pr.find('.tooltip').easyTooltip({
				xOffset: -60,
				yOffset: 70
			}); // Tooltips! 
			el_pr.find('.tabs').tabs();
			el_pr.find('.datepicker').datepicker();
			
			return this;
		},
		render: function(){
			return this;
		},
		events: {
			'click #menu ul li': 						'events_click_menu_item',
			'hover #primary_right .notification': 		'events_hover_notification',
			'click #primary_right .notification span': 	'events_click_notification_span'
		},
		events_click_menu_item: function(e){
					
			var el = jQuery(e.currentTarget);
			if(el.hasClass('current')){
				if(el.find('ul')){
					el.find('ul').slideToggle('fast');
				}
				return;
			}else{
				
				if(el.find('ul')){
					
					this.$el.find('#primary_left .current').removeClass('current');
					el.toggleClass('current');
					el.find('ul').slideToggle('fast');
					
				}else{
					
					
				//	el.find('ul').slideToggle('fast');
				
				}
				
			}
						
		},
		events_hover_notification: function(e){
			
			var el = jQuery(e.currentTarget);
			if(e.type == 'mouseover'){
				el.css({
					cursor: 'pointer'
				});
			}else if(e.type == 'mouseout'){
				el.css({
					cursor: 'auto'
				});
			}
			
		},
		events_click_notification_span: function(e){
			var el = jQuery(e.currentTarget);
			el.parents('.notification').fadeOut(800);
		}
	}; Views.AdminWrapper = Backbone.View.extend(Views.AdminWrapper);
	
	var admin_wrapper = new Views.AdminWrapper({});
	//make available globally
	window.module.Globals.prototype.global_views = {};
	window.module.Globals.prototype.global_views.admin_wrapper = admin_wrapper;
	
	
});
