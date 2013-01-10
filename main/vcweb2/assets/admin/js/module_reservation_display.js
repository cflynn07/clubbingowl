(function(globals){
	
	var EVT = window.ejs_view_templates_admin_promoters || window.ejs_view_templates_admin_managers || window.ejs_view_templates_admin_hosts;
	
	var Models 		= {};
	var Collections = {};	
	var Views 		= {};
	
	
	Views.ReservationDetailPaine = Backbone.View.extend({
				
		initialize: function(){
	//		model_display_settings.on('change', this.render, this);
		},
		render: function(){
			
			this.$el.addClass('ui-widget');
			this.$el.draggable();
			this.$el.attr({
				id: 'reservation_detail_paine'
			});
			
			
			var vlf 	= el.parents('body');
			var top 	= el.offset().top - 1 - vlf.offset().top;
			var left 	= el.offset().left + el.width() + 3 - vlf.offset().left;
			
			this.$el.css({
				left:			left + 'px',
				top: 			top + 'px',
				zIndex: 		'1000 !important',
				position: 		'absolute'
			//	'min-height': 	(el.height() + 2) + 'px'
			});
			
			console.log(this.model.toJSON());
			
			var html = new EJS({
				text: EVT['tables/t_vlfit_reservation_detail']
			}).render(this.model.toJSON());
			
			
			this.$el.html(html);

			
			var _this = this;
			jQuery.populateFacebook(this.$el, function(){
				_this.$el.find('#reservation_info').show();
				_this.$el.find('#reservation_loading_indicator').hide();
				
				_this.$el.resizable({
					minWidth: 	_this.$el.width(),
					minHeight: 	_this.$el.height()
				});
				
			});
		
			
			
			return this;
		},
		events: {
			'click *[data-actions]': 	'click_actions',
			'click a.ajaxify': 			'click_client'
		},
		click_client: function(e){
					
			jQuery('div#dialog_actions').dialog('close');
			
		},
		click_actions: function(e){
			
			var el = jQuery(e.currentTarget);
			var action = el.attr('data-actions');
			
			switch(action){
				case 'remove':
				
					module_reservation_display.remove();
				
					break;
			}
			
		}
	});
	
	
	
	var el, 
		model_display_settings,
		reservation, 
		view_reservation_detail_paine;
	
	var module_reservation_display = {
		
		display: function(opts){
			
			
			model_display_settings = opts.model_display_settings;
			
			
			
			if(view_reservation_detail_paine){
				view_reservation_detail_paine.remove();
			}
			
			

			el 								= opts.el;	
			reservation 					= opts.reservation;
			view_reservation_detail_paine 	= new Views.ReservationDetailPaine({
				model: reservation
			});
			
			
			jQuery('body').append(view_reservation_detail_paine.el)
			
			view_reservation_detail_paine.render();
			
			
		},
		remove: function(){
			
			if(view_reservation_detail_paine){
				view_reservation_detail_paine.remove();
			}
			
		}
		
	};
	
	globals.module_reservation_display = module_reservation_display;
	
}(window.module.Globals.prototype));
