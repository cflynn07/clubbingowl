if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_promoter_manage_guest_lists_new = function(){
						
		var unbind_callbacks = [];
			
		jQuery('input.iphone').iphoneStyle();
		
		
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
		
		
		
		
	//	jQuery('form#guest_list_new_form').dumbFormState();
		
		
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
		
		
		
		
		//alert user if guest list name invalid
		jQuery('form#guest_list_new_form input[name = guest_list_name]').keyup(function(){			
			if( /[^a-zA-Z0-9 ]/.test(jQuery(this).val()) ){
				jQuery('p#guest_list_name_error').css('display', 'inline-block');
			}else{
				jQuery('p#guest_list_name_error').css('display', 'none');
			}
		});
		
		





		var image_data = false;
		var crop_object;
		
		var initialize_crop = function(response){
		
		  	image_data = response;
	        	
        	var src = window.module.Globals.prototype.s3_uploaded_images_base_url + 'guest_lists/originals/temp/' + response.image + '.jpg';
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
			
		}
		
		
		
		
		
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
	         	  	
	        	
	      		initialize_crop(response.image_data);
	        		       
	        },
	        onSelect: function(){
	        	
	        }
	    });
		
		
		
		
		
		
		
		
		
		
		jQuery('input#submit_new_guest_list').bind('click', function(e){
			
			e.preventDefault();
			
			jQuery('#ajax_loading').css('display', 'inline-block');
			jQuery('#ajax_complete_success').css('display', 'none');
			jQuery('#ajax_complete_error').css('display', 'none');
			
			//cross-site request forgery token, accessed from session cookie
			//requires jQuery cookie plugin∂
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
			var data = {
				venue: 		jQuery('form#guest_list_new_form select[name = guest_list_venue]').val(),
				
				
				
				type: 		jQuery('form#guest_list_new_form input[type=radio][name=guest_list_type]').val(),
				weekday: 	jQuery('form#guest_list_new_form select[name = guest_list_weekday]').val(),
				date: 		jQuery.datepicker.formatDate('y-mm-dd', jQuery('form#guest_list_new_form input[name = event_date]').datepicker('getDate')),
				
				
				gl_name: 	jQuery('form#guest_list_new_form input[name = guest_list_name]').val(),
				gl_description: jQuery('form#guest_list_new_form textarea[name = guest_list_description]').val(),
				auto_approve: ((jQuery('form#guest_list_new_form input[name = guest_list_auto_approve]').attr('checked') == undefined) ? false : true),
				ci_csrf_token: cct,
				vc_method: 'promoter_new_guest_list',
				
				min_age: jQuery('form#guest_list_new_form select[name = guest_list_min_age]').val(),
				regular_cover: jQuery('form#guest_list_new_form input[name = guest_list_reg_cover]').val(),
				gl_cover: jQuery('form#guest_list_new_form input[name = guest_list_gl_cover]').val(),
				door_opens: jQuery('form#guest_list_new_form select[name = guest_list_open]').val(),
				door_closes: jQuery('form#guest_list_new_form select[name = guest_list_close]').val(),
				additional_info_1: jQuery('form#guest_list_new_form input[name = guest_list_additional_info_1]').val(),
				additional_info_2: jQuery('form#guest_list_new_form input[name = guest_list_additional_info_2]').val(),
				additional_info_3: jQuery('form#guest_list_new_form input[name = guest_list_additional_info_3]').val(),
				auto_promote: ((jQuery('form#guest_list_new_form input[name = guest_list_auto_promote]').attr('checked') == undefined) ? false : true),
				
				image_data: image_data
			}
						
			jQuery.ajax({
				url: 		window.location,
				type: 		'post',
				data: 		data,
				cache: 		false,
				dataType: 	'json',
				success: 	function(data, textStatus, jqXHR){
					
					jQuery('#ajax_loading').css('display', 'none');
					
					if(data.success){
					
						jQuery('#ajax_complete_success').css('display', 'inline-block');
						jQuery('form#guest_list_new_form').dumbFormState('remove');
					//	window.location = window.module.Globals.prototype.front_link_base + 'admin/promoters/manage_guest_lists/';
						jQuery('a#back').trigger('click');
					
					}else{
						
						jQuery('#ajax_complete_error').css('display', 'inline-block');
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