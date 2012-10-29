if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	var templates = ejs_view_templates_admin_managers;
	
	var _views 			= {};
	var _models 		= {};
	var _collections 	= {};
	
	_views.wrapper = {
		card_num: 	null,
		card_cvc: 	null,
		card_exp_m: null,
		card_exp_y: null,
		initialize: function(){
			
			this.card_num 	= this.$el.find('input.card-number');
			this.card_cvc 	= this.$el.find('input.card-cvc');
			this.card_exp_m = this.$el.find('input.card-expiry-month');
			this.card_exp_y = this.$el.find('input.card-expiry-year');
		
			this.card_num.mask('9999-9999-9999-9999');
			this.card_cvc.mask('999?9');
			this.card_exp_m.mask('99');
			this.card_exp_y.mask('9999');
		
			this.render();
		},
		render: function(){
			
			//show on-file payment
			var html = new EJS({
				text: templates.on_file_payment
			}).render({});
			this.$el.find('#on_file_payment').html(html);
			
			
			return this;
		},
		events: {
			'submit form': 'events_submit_form'
		},
		events_submit_form: function(e){
			
			e.preventDefault();
			
			
			
			return false;
			
		}
	}; _views.wrapper = Backbone.View.extend(_views.wrapper);
	var wrapper = null;
	
	
	window.vc_page_scripts.admin_manager_settings_payment = function(){
						
		var unbind_callbacks = [];		
				
				
		jQuery.getScript('https://js.stripe.com/v1/', function(){
			wrapper = new _views.wrapper({
				el: '#admin_manager_settings_payments_wrapper'
			});
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