if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_promoter_manage_guest_lists = function(){
						
		var unbind_callbacks = [];

		
			
		jQuery('input.iphone').iphoneStyle();
		
		
		
		
		
		var delete_list_function = function(button){
			
			jQuery('div#delete_list_dialog').dialog({
				title: 'Delete Guest List',
				height: 200,
				width: 280,
				modal: true,
				position: ['center', 'center'],
				buttons: {
					"Delete": function() {
						
						var pgla_id = button.parents('tr').find('td.pgla_id').html();
						
						var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
						
						jQuery.ajax({
							url: window.location,
							type: 'post',
							data: {
								vc_method: 'delete_guest_list',
								ci_csrf_token: cct,
								pgla_id: pgla_id
							},
							cache: false,
							dataType: 'json',
							success: function(data, textStatus, jqXHR){
								
								if(data.success)
									window.location.reload(true);
																
							}
						});
						
					},
					Cancel: function() {
						jQuery(this).dialog( "close" );
					}
				},
				close: function() {
					return;
				}
			});
			
		}
		jQuery('a.delete_guest_list_button').bind('click', function(){
			
			delete_list_function(jQuery(this));
			return false;
			
		});
		
		var update_auto_approve_function = function(button){
			
			var pgla_id = button.parents('tr').find('td.pgla_id').html();
			var auto_approve = (button.attr('checked') == undefined) ? false : true;
			
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
			jQuery.ajax({
				url: window.location,
				type: 'post',
				data: {
					vc_method: 'update_auto_approve',
					pgla_id: pgla_id,
					ci_csrf_token: cct,
					auto_approve: auto_approve
				},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
				}
			});
			
		}
		
		jQuery('input[type = checkbox]').bind('change', function(){
			
			update_auto_approve_function(jQuery(this));
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