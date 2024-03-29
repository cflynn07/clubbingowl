if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_settings_guest_lists_new = function(){
		
		var crop_object;	
				
		
		var Views = {};
		Views.NewGLForm = {
			el: '#guest_list_new_form',
			ocupload_instance: null,
			image_data: false,
			initialize: function(){
				
				this.$el.find('input.iphone').iphoneStyle();
				this.initialize_ocupload();
			
			
			
			
			
				jQuery('input[type=radio][name=guest_list_type]').bind('change', function(e){
					
					jQuery('#weekday_select').hide();
					jQuery('#date_select').hide();
					
					var el = jQuery(e.currentTarget),
					type = el.val();
					if(type == 'weekly_list'){
						jQuery('#weekday_select').show();
					}else if(type == 'event'){
						jQuery('#date_select').show();
					}
					
				}).trigger('change');
				
				
				jQuery('input[name=event_date]').datepicker({
					minDate: 'today'
				});
				
			
			
			
			
			
			
			},
			initialize_ocupload: function(){
				
				var _this_view = this;
				var _this = this;
				
				var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
				//initialize one-click upload for profile picture
		        window.ocupload_instance = jQuery('#upload_new_image').upload({
			        name: 'file',
			        action: window.location.href,
			        enctype: 'multipart/form-data',
			        params: {
			        	ocupload: 		true,
			        	ci_csrf_token: 	cct
			        },
			        autoSubmit: true,
			        onSubmit: function(){
			        	
			        	console.log('onSubmit');
			        	jQuery('#upload_new_image').hide();
			        	jQuery('#ajax_loading_image').show();
			        	
			        },
			        onComplete: function(response){
			        	
			        	console.log('onComplete');
			        	
			        	
			        	
			        	jQuery('#ajax_loading_image').hide();
			         	jQuery('#upload_new_image').show();
			         	jQuery("input[name=file]").val('');
			         
			         	  	
			         	  	
			        	response = jQuery.parseJSON(response);
			        	
			        	if(!response.success){
			        		
			        		_this_view.initialize_ocupload();
			        		
			        		alert(response.message);
			        		return;
			        	}
			        	
			        	if(crop_object && crop_object.remove)
							crop_object.remove();
	         	  	
			        	
			        	
			      		_this_view.initialize_crop(response.image_data);
			        	
			       
			        },
			        onSelect: function(){
			        	
			        }
			    });
				
			},
			initialize_crop: function(response){
				
					var _this = this;
					var _this_view = this;
				  	_this.image_data = response;
			        	
		        	var src = window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/originals/temp/' + response.image + '.jpg';
		        	var img = jQuery('<img></img>').attr('src', src).bind('load', function(){ 
		        		console.log('image_loaded'); 
		        		
		        		var _this = this;
		        		
		        		crop_object = jQuery(this).imgAreaSelect({
		        			instance: 		true,
		        			handles:		true,
		        			aspectRatio:	'3:4',
		        			show:			true,
		        			persistent:		true,
		        			minWidth: 		188,
							minHeight: 		266,
							aspectRatio: 	'188:266',
							x1: 			response.x0,
							y1:				response.y0,
							x2:				response.x1,
							y2: 			response.y1,
							imageHeight: 	jQuery(this).height(),
							imageWidth: 	jQuery(this).width(),
							onSelectChange: function(img, selection){
								
								
								
								_this_view.image_data.x0 = selection.x1;
								_this_view.image_data.y0 = selection.y1;
								_this_view.image_data.x1 = selection.x2;
								_this_view.image_data.y1 = selection.y2;
								
							}
		        		});
		        		
		        		
		        	});
		        	jQuery('div#image_holder').html(img);
				
			},
			render: function(){
				
			},
			display_error: function(message){
				
				this.$el.find('#display_message').html(message);
				
			},
			events: {
				'submit form$guest_list_new_form': 		'form_submit',
				'click input#submit_new_guest_list': 	'form_submit',
				'keyup input[name=guest_list_name]': 	'change_input_guest_list_name'
			},
			change_input_guest_list_name: function(e){
				
				var el = jQuery(e.currentTarget);
				
			//	if(el.val().length !== el.val().replace(/\W/g, '').length)
			//		this.$el.find('p#guest_list_name_error').show();
			//	else
			//		this.$el.find('p#guest_list_name_error').hide();
								
								
			},
			form_submit: function(e){
				
				var _this = this;
				
				console.log(jQuery(e.currentTarget).find('input[type=file]').length);
				if(jQuery(e.currentTarget).find('input[type=file]').length)
					return true;
				
				e.preventDefault();
				
				var form_object = this.$el.serializeObjectPHP();
				form_object.date = jQuery.datepicker.formatDate('yy-mm-dd', jQuery('form#guest_list_new_form input[name = event_date]').datepicker('getDate'));
				
				
				
				if(!form_object.guest_list_name){
					this.display_error('Please supply a guest list name.');
					return false;
				}
				
		//		if(form_object.guest_list_name.length !== form_object.guest_list_name.replace(/\W/g, '').length){
		//			this.display_error('Name must only contain alpha-numeric characters.');
		//			return false;
		//		}
				
				if(form_object.guest_list_name.length < 8){
					this.display_error('Name must be at least 8 characters');
					return false;
				}
				
				if(!form_object.guest_list_description){
					this.display_error('Please supply a guest list description.');
					return false;
				}
				
				if(!form_object.guest_list_reg_cover){
					this.display_error('Please supply a regular cover charge.');
					return false;
				}
				
				if(!form_object.guest_list_gl_cover){
					this.display_error('Please supply a guest list cover charge.');
					return false;
				}
				
				if(!this.image_data){
					this.display_error('Please choose an image to represent your guest list.');
					return false;
				}
			
				
			//	this.$el.find('input#submit_new_guest_list').hide();
				this.$el.find('#ajax_loading').show();
				
								
				form_object.image_data = this.image_data;
				
				jQuery.background_ajax({
					data: {
						vc_method: 	'new_team_guest_list',
						gl_form: form_object
					},
					success: function(data){
						
						if(data.success){
							
							jQuery('#back').trigger('click');
							
						}else{
							
							_this.display_error(data.message);
							
							_this.$el.find('input#submit_new_guest_list').show();
							_this.$el.find('#ajax_loading').hide();
							
						}
						
						console.log('data');
						console.log(data);
						
						
					}
				});
							
				
				return false;
			}
		}; Views.NewGLForm = Backbone.View.extend(Views.NewGLForm);						
				
		var views_newglform = new Views.NewGLForm({});
				
				
				
				
				
				
				



		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			if(crop_object && crop_object.remove)
				crop_object.remove();

		}
		
	}
	
});