if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_manage_image = function(){
						
		var unbind_callbacks = [];		
				
					
					
					
					
		var initial = true;
		
		jQuery('img#image').bind('load', function(){
			
			jQuery('input#crop_button').css('display', 'inline-block');
			
			jQuery(this).css('display', 'block');
			
			if(!initial){
				jQuery('#ajax_loading').css('display', 'inline-block');
				jQuery('#ajax_complete').css('display', 'none');
				initial = false;
			}
			
	    	initialize_crop();
			
		});
		
		jQuery('input#crop_button').bind('click', function(){
			
			jQuery('#ajax_loading').css('display', 'inline-block');
			jQuery('#ajax_complete').css('display', 'none');
			
			//initialize 1-click-image upload with iframe
		    var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		    
		    jQuery.ajax({
				url: window.location,
				type: 'post',
				data: jQuery('form#pic_crop_form').serialize() + '&ci_csrf_token=' + cct + '&vc_method=crop_action',
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					console.log(data);
					
					jQuery('#ajax_loading').css('display', 'none');
					jQuery('#ajax_complete').css('display', 'inline-block');
				
					if(data.success){
						jQuery('a#back').trigger('click');
					//	window.location = '/admin/promoters/' + window.page_obj.manage_image.return + '/'; //<?= $manage_image->return ?>/';
					}
				
				},
				failure: function(){
					//ToDo: improve message
					alert('AJAX Failure, server failed to respond');
				}
			});
			
		});
		
		var initialize_ocupload = function(){
			
			//initialize 1-click-image upload with iframe
		    var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		    
	        //initialize one-click upload for profile picture
	        myUpload = jQuery('#ocupload_button').upload({
		        name: 'file',
		        action: window.location,
		        enctype: 'multipart/form-data',
		        params: {ocupload: true,
		        		ci_csrf_token: cct},
		        autoSubmit: true,
		        onSubmit: function(){
		        	
		        	jQuery('#ajax_loading').css('display', 'inline-block');
					jQuery('#ajax_complete').css('display', 'none');
		        	
		        },
		        onComplete: function(response){
		        		        	
		        	response = jQuery.parseJSON(response);
		        	
		        	console.log(response);
		        	
		        	if(!response.success){
		        		alert(response.message);
		        		return;
		        	}
		        	
	        		//update crop form
	        		var pic_crop_form = jQuery('form#pic_crop_form');
		        	pic_crop_form.find('input[name = x0]').val(response.image_data.x0);
					pic_crop_form.find('input[name = y0]').val(response.image_data.y0);
					pic_crop_form.find('input[name = x1]').val(response.image_data.x1);
					pic_crop_form.find('input[name = y1]').val(response.image_data.y1);
					pic_crop_form.find('input[name = width]').val(parseInt(response.image_data.x1) - parseInt(response.image_data.x0));
					pic_crop_form.find('input[name = height]').val(parseInt(response.image_data.y1) - parseInt(response.image_data.y0));
		        	
		        	//load new profile image
		        	jQuery('img#image').attr('src', window.module.Globals.prototype.s3_uploaded_images_base_url + window.page_obj.manage_image.type + '/originals/temp/' + response.image_data.image + '.jpg');
		      
		        },
		        onSelect: function(){
		        	
		        }
		    });
		}
		
		var initialize_crop = function(){
			
			var pic_crop_form = jQuery('form#pic_crop_form');
	
			var crop_settings_obj = {
				instance: true,
				handles: true,
				show: true,
				persistent: true,
				x1: parseInt(pic_crop_form.find('input[name = x0]').val()),
				y1: parseInt(pic_crop_form.find('input[name = y0]').val()),
				x2: parseInt(pic_crop_form.find('input[name = x1]').val()),
				y2: parseInt(pic_crop_form.find('input[name = y1]').val()),
				imageHeight: jQuery('img#image').height(),		//parseInt(pic_crop_form.find('input[name = height]').val()),
				imageWidth: jQuery('img#image').width(),   		//parseInt(pic_crop_form.find('input[name = width]').val()),
				
				onSelectChange: function(img, selection){
					pic_crop_form.find('input[name = x0]').val(selection.x1);
					pic_crop_form.find('input[name = y0]').val(selection.y1);
					pic_crop_form.find('input[name = x1]').val(selection.x2);
					pic_crop_form.find('input[name = y1]').val(selection.y2);
					pic_crop_form.find('input[name = width]').val(selection.width);
					pic_crop_form.find('input[name = height]').val(selection.height);
				}
			};

			if(window.page_obj.manage_image.type == 'guest_lists' || window.page_obj.manage_image.type == 'events'){
				
				crop_settings_obj.minWidth = 188;
				crop_settings_obj.minHeight = 266;
				crop_settings_obj.aspectRatio = '188:266';
				
			}else if(window.page_obj.manage_image.type == 'venues/banners'){
				
				crop_settings_obj.minWidth = 1000;
				crop_settings_obj.minHeight = 300;
				crop_settings_obj.aspectRatio = '1000:300';
				
			}
			
			var crop_object = jQuery('img#image').imgAreaSelect(crop_settings_obj);
			unbind_callbacks.push(function(){
				crop_object.remove();
			});
			
		
		}
		
		initialize_ocupload();			
					
				
				
				
				
				
				
				
				
				
				
				
				
				



		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});