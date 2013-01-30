if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_promoter_manage_guest_lists_edit = function(){
						
		var unbind_callbacks = [];		
			
		jQuery('input.iphone').iphoneStyle();
		
		console.log('edit callback');
		
		
		
		jQuery('form#guest_list_new_form').dumbFormState();
		

		var prevent_func = function(event){
			// Allow: backspace, delete, tab, escape, and enter
		    if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
		         // Allow: Ctrl+A
		        (event.keyCode == 65 && event.ctrlKey === true) || 
		         // Allow: home, end, left, right
		        (event.keyCode >= 35 && event.keyCode <= 39)) {
		             // let it happen, don't do anything
		             return;
		    }
		    else {
		        // Ensure that it is a number and stop the keypress
		        if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 ) || 
		        // 1000 # limit
		        (jQuery(this).val().length > 3)) {
		            event.preventDefault(); 
		        }   
		    }
		}
		
		
		jQuery('form#guest_list_new_form input[name = guest_list_reg_cover]').keydown(prevent_func);
		jQuery('form#guest_list_new_form input[name = guest_list_gl_cover]').keydown(prevent_func);

		
		
		var image_data = false;
		var crop_object;
		
		var initialize_crop = function(response){
		
			if(crop_object && crop_object.remove)
				crop_object.remove();
		
		  	image_data = response;
	        	
        	var src = window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/originals/' + ((!response.live) ? 'temp/' : '') + response.image + '.jpg';
        	var img = jQuery('<img></img>').attr('src', src).bind('load', function(){ 
        		console.log('image_loaded'); 
        		
        		
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
					
						image_data.x0 = selection.x1;
						image_data.y0 = selection.y1;
						image_data.x1 = selection.x2;
						image_data.y1 = selection.y2;
						
					}
        		});
        		
        		
        	});
        	jQuery('div#image_holder').html(img);
			
		};
		
		// 
		initialize_crop({
			image: 	window.page_obj.guest_list.pgla_image,
			x0:		window.page_obj.guest_list.pgla_x0,
			x1:		window.page_obj.guest_list.pgla_x1,
			y0:		window.page_obj.guest_list.pgla_y0,
			y1:		window.page_obj.guest_list.pgla_y1,
			live: 	true,
			new_image: false
		});
		
		
		
		
		
		
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
	        		alert(response.message);
	        		return;
	        	}
	        	
	        	if(crop_object && crop_object.remove)
					crop_object.remove();
	         	  	
	         	  	
	        	
	        	response.image_data.live 		= false;
	        	response.image_data.new_image 	= true;
	      		initialize_crop(response.image_data);
	        		       
	        },
	        onSelect: function(){
	        	
	        }
	    });
		
		
		
		
		
		
		
		
		jQuery('input#submit_new_guest_list').bind('click', function(){
			
			jQuery('#ajax_loading').css('display', 'inline-block');
			jQuery('#ajax_complete').css('display', 'none');
			
			//cross-site request forgery token, accessed from session cookie
			//requires jQuery cookie plugin
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
			var data = {
				venue: jQuery('form#guest_list_new_form select[name = guest_list_venue]').val(),
				weekday: jQuery('form#guest_list_new_form select[name = guest_list_weekday]').val(),
				gl_name: jQuery('form#guest_list_new_form input[name = guest_list_name]').val(),
				gl_description: jQuery('form#guest_list_new_form textarea[name = guest_list_description]').val(),
				auto_approve: ((jQuery('form#guest_list_new_form input[name = guest_list_auto_approve]').attr('checked') == undefined) ? false : true),
				
				min_age: jQuery('form#guest_list_new_form select[name = guest_list_min_age]').val(),
				regular_cover: jQuery('form#guest_list_new_form input[name = guest_list_reg_cover]').val(),
				gl_cover: jQuery('form#guest_list_new_form input[name = guest_list_gl_cover]').val(),
				door_opens: jQuery('form#guest_list_new_form select[name = guest_list_open]').val(),
				door_closes: jQuery('form#guest_list_new_form select[name = guest_list_close]').val(),
				additional_info_1: jQuery('form#guest_list_new_form input[name = guest_list_additional_info_1]').val(),
				additional_info_2: jQuery('form#guest_list_new_form input[name = guest_list_additional_info_2]').val(),
				additional_info_3: jQuery('form#guest_list_new_form input[name = guest_list_additional_info_3]').val(),
				auto_promote: ((jQuery('form#guest_list_new_form input[name = guest_list_auto_promote]').attr('checked') == undefined) ? false : true),
				pgla_id: window.page_obj.pgla_id,
				
				ci_csrf_token: cct,
				vc_method: 'promoter_edit_guest_list',
				
				
				image_data: image_data
			}
			
			console.log(data);
			
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: data,
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
					jQuery('#ajax_loading').css('display', 'none');
					jQuery('#ajax_complete').css('display', 'inline-block');
					
					if(data.success){
					
						jQuery('form#guest_list_new_form').dumbFormState('remove');
						jQuery('a#back').trigger('click');
					
					}else{
						
						jQuery('p#display_message').html(data.message);
					
					}
					
				}
			});
		
			return false;
		});
		
		
		
		
		
		






		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			if(crop_object && crop_object.remove)
				crop_object.remove();
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});