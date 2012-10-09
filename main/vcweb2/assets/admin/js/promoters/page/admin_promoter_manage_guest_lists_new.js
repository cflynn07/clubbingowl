if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_promoter_manage_guest_lists_new = function(){
						
		var unbind_callbacks = [];
			
		jQuery('input.iphone').iphoneStyle();
		
		
		
		
		
		
		console.log('hello 1');
		
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
		
		
		
		
		//alert user if guest list name invalid
		jQuery('form#guest_list_new_form input[name = guest_list_name]').keyup(function(){			
			if( /[^a-zA-Z0-9 ]/.test(jQuery(this).val()) ){
				jQuery('p#guest_list_name_error').css('display', 'inline-block');
			}else{
				jQuery('p#guest_list_name_error').css('display', 'none');
			}
		});
		
		jQuery('input#submit_new_guest_list').bind('click', function(){
			
			jQuery('#ajax_loading').css('display', 'inline-block');
			jQuery('#ajax_complete_success').css('display', 'none');
			jQuery('#ajax_complete_error').css('display', 'none');
			
			//cross-site request forgery token, accessed from session cookie
			//requires jQuery cookie plugin∂
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
			var data = {
				venue: jQuery('form#guest_list_new_form select[name = guest_list_venue]').val(),
				weekday: jQuery('form#guest_list_new_form select[name = guest_list_weekday]').val(),
				gl_name: jQuery('form#guest_list_new_form input[name = guest_list_name]').val(),
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
				auto_promote: ((jQuery('form#guest_list_new_form input[name = guest_list_auto_promote]').attr('checked') == undefined) ? false : true)
				
			}
			
			console.log(data);
			
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
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});