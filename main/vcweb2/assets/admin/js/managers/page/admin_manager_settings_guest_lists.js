if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_settings_guest_lists = function(){
						
		jQuery('input[type=checkbox]').iphoneStyle().bind('change', function(){
			
			var el 		= jQuery(this);
			var checked = (el.attr('checked') == 'checked') ? '1' : '0';
			var tgla_id = el.attr('data-tgla_id');
			jQuery.background_ajax({
				data: {
					vc_method: 		'set_auto_approve',
					auto_approve:	checked,
					tgla_id:		tgla_id
				},
				success: function(data){
					
					console.log(data);
					
				}
			});
			
			
		});
		
		
		
		jQuery('a[data-action="delete"]').bind('click', function(){
			
			var el 		= jQuery(this);
			var tgla_id = el.attr('data-tgla_id');
			
			
			
			
			jQuery('div#delete_list_dialog').dialog({
				title: 		'Delete Guest List',
				height: 	200,
				width: 		280,
				modal: 		true,
				position: 	['center', 'center'],
				buttons: {
					"Delete": function() {
						
						var _this = this;
						
						jQuery.background_ajax({
							data: {
								vc_method: 		'set_deleted',
								tgla_id:		tgla_id
							},
							success: function(data){
								
							//	jQuery(_this).dialog( "close" ).dialog( "destroy" );
							//	jQuery('a[href="' + window.module.Globals.prototype.front_link_base + 'admin/managers/settings_guest_lists/"].ajaxify:first').trigger('click');
								console.log(data);
								
								window.location.reload();
								
							}
						});
												
					},
					Cancel: function() {
						window.location.reload();
					}
				},
				close: function() {
					window.location.reload();
				}
			});
					
		});
					
					
					//jQuery('a[href="https://www.clubbingowl.dev/admin/managers/settings_guest_lists/"].ajaxify:first').trigger('click');
				
				
				
				
				
				
				
				
				
				
				
				
				



		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			

		}
		
	}
	
});