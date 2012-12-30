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
		submit_busy: false,
		initialize: function(){
			
			this.card_num 	= this.$el.find('input.card-number');
			this.card_cvc 	= this.$el.find('input.card-cvc');
			this.card_exp_m = this.$el.find('input.card-expiry-month');
			this.card_exp_y = this.$el.find('input.card-expiry-year');
		
			this.card_num.mask('9999-9999-9999-9999');
			this.card_cvc.mask('999?9');
			this.card_exp_m.mask('99');
			this.card_exp_y.mask('2099');
				
			if(window.card_data && window.card_data.last4){
				this.card_num.attr({
					'placeholder': 'xxxx-xxxx-xxxx-' + window.card_data.last4
				});
			}
				
			this.render();
		},
		render: function(){
			
			//show on-file payment
		//	var html = new EJS({
		//		text: templates.on_file_payment
		//	}).render({});
		//	this.$el.find('#on_file_payment').html(html);
			
			return this;
		},
		events: {
			'submit form': 				'events_submit_form',
			'click  #show_pay_info': 	'click_show_pay_info',
			'click  #submit': 			'events_submit_form'
		},
		click_show_pay_info: function(e){
			
			e.preventDefault();
			
			//this.$el.find('#update').slideDown();
			
			return false;
			
		},
		events_submit_form: function(e){
			
			e.preventDefault();
			
			if(this.submit_busy)
				return;
		//	this.submit_busy = true;
			
			
						
			var stripe_pub_key = this.$el.find('#stripe_pub_key').html();			
			var _this = this;
			this.$el.find("#payment_errors").hide()
			this.$el.find("#loading").show()
			
			Stripe.setPublishableKey(stripe_pub_key);
			Stripe.createToken({
		        number: 		this.card_num.val().replace(/\D/g,''),	
		        cvc: 			this.card_cvc.val().replace(/\D/g,''),
		        exp_month: 		this.card_exp_m.val().replace(/\D/g,''),
		        exp_year: 		this.card_exp_y.val().replace(/\D/g,'')
		    }, function(status, response){
		    	
		    	console.log('stripe response');
		    	console.log(arguments);
		    	
		    	
		    	 if (response.error) {

			        // show the errors on the form
			        _this.$el.find("#payment_errors").show().html(response.error.message);
			        _this.$el.find("#loading").hide()
					_this.submit_busy = false;
					
			    } else {
			    	
			    	
			        var token = response['id'];
			        jQuery.background_ajax({
			        	data: {
			        		vc_method: 	'update_stripe_token',
			        		token:		token
			        	},
			        	success: function(data){
			        		
			        		_this.$el.find("#payment_errors").hide();
					        _this.$el.find("#loading").hide()
							jQuery('a[href="' + window.location.href + '"]').trigger('click');
			        		
			        		if(!jQuery('a[href="' + window.location.href + '"]').length)
			        			window.location.reload();
			        		
			        	}
			        });
			        
			    
			    }
		    	
		    });

			
			
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