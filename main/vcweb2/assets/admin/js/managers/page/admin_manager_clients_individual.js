if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_clients_individual = function(){


		var Models 		= {};
		var Collections = {};
		var Views 		= {};


		Models.Client = {
			initialize: function(){
				
			},
			defaults: {
				
			}
		}; Models.Client = Backbone.Model.extend(Models.Client);
		
		Views.Client = {
			el: '#clients_individual_wrapper',
			initialize: function(){
				
				if(window.page_obj.client)
					this.render();
				else
					null;
			},
			render: function(){
				
				this.$el.find('*[data-client-name]').html(this.model.get('u_full_name'));
				this.$el.find('*[data-phone-number]').html(this.model.get('u_phone_number').replace(/(\d{3})(\d{3})(\d{4})/, '($1)-$2-$3'));
				this.$el.find('*[data-email]').html(this.model.get('u_email'));
				this.$el.find('img#client_pic').attr({
					src: 'https://graph.facebook.com/' + this.model.get('u_oauth_uid') + '/picture?type=large'
				});
				
				this.$el.find('textarea.notes').cleditor({
					width: '100%',
					height: 200
				});
				
				var _this = this;
				jQuery.fbUserLookup(window.page_obj.users, '', function(rows){
					
					console.log('rows');
					console.log(rows);
					
					for(var i=0; i<rows.length; i++){
						_this.$el.find('*[data-name="' + rows[i].uid + '"]').html(rows[i].name);
					}
					
				});
									
			},
			events: {
				'click *[data-action]': 'click_data_action'
			},
			click_data_action: function(e){
				
				e.preventDefault();
				var el = jQuery(e.currentTarget);
				var action = el.attr('data-action');
				switch(action){
					case 'save':
					
						this.$el.find('*[data-action="save"]').hide();
						this.$el.find('img.loading_indicator').show();
						var _this = this;
						
						jQuery.background_ajax({
							data: {
								vc_method: 		'update_client_notes',
								private_notes: 	_this.$el.find('#private_notes').val(),
								public_notes: 	_this.$el.find('#public_notes').val()
							},
							success: function(data){
								
								console.log(data);
								_this.$el.find('*[data-action="save"]').show();
								_this.$el.find('img.loading_indicator').hide();
								
							}
						})
					
						break;
				}
				
				
				return false;
				
			}
		}; Views.Client = Backbone.View.extend(Views.Client);
		

		
		
		if(!window.page_obj.client){
			
			
			jQuery.fbUserLookup([window.page_obj.oauth_uid], '', function(rows){
				
				var vc_user = jQuery.cookies.get('vc_user');
				
				if(rows.length === 0 || rows[0].uid == vc_user.vc_oauth_uid){
					jQuery('a[href="https://' + window.location.host + '/admin/managers/"].ajaxify:first').trigger('click');
					return;
				}
				
				var row = rows[0];
				
				window.page_obj.client = {
					gl_history: 		[],
					u_email: 			"---",
					u_first_name: 		row.first_name,
					u_full_name: 		row.name,
					u_last_name: 		row.last_name,
					u_oauth_uid: 		row.uid,
					u_opt_out_email: 	"0",
					u_phone_number: 	"---",
					is_client: 			false
				}

				var model_client = new Models.Client(window.page_obj.client);
				var view_client = new Views.Client({
					model: model_client
				});
				
			});


			
		}else{
			
			window.page_obj.client.is_client = true;
			
			var model_client = new Models.Client(window.page_obj.client);
			var view_client = new Views.Client({
				model: model_client
			});
			
		}
		

		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			

		}
		
	}
	
});