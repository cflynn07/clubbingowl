if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_promoters_guest_lists = function(){
						
		var unbind_callbacks = [];		
				
		jQuery('.tabs').tabs();
		jQuery('img.tooltip').tooltip();		
		jQuery("div.datepicker").datepicker();
		
		
		
		
		
		
		
		
		
		
		var fb_operation_complete = false;
		
		jQuery('ul.sitemap li').bind('click', function(){
			
			jQuery(this).parent().find('li').css('font-weight', 'normal');
			jQuery(this).css('font-weight', 'bold');
	
			var pgla_id = jQuery(this).find('span').html();
			jQuery(this).parents('div.guest_list_content').find('div.list').css('display', 'none');
			
			jQuery('div#pgla_' + pgla_id).css('display', 'block');
						
		});
		
		fbEnsureInit(function(){
			
			var display_first = function(){
				jQuery('div#main_loading_indicator').remove();
				
				if(window.page_obj.promoters.length > 0)
					jQuery('div#tabs').tabs().css('display', 'block');
					
				jQuery('ul.sitemap').each(function(){
					
					jQuery(this).children('li:first').css('font-weight', 'bold');
					var first_pgla_id = jQuery(this).children('li:first').find('span.pgla_id').html();
					
					jQuery('div#pgla_' + first_pgla_id).css('display', 'block');
					
				});		
			};
			
		//	var users = eval('<?= json_encode($users) ?>');
			var users = window.page_obj.users;
			
			if(users.length > 0){
				
				var fql = "SELECT uid, name, pic_square, pic_big, third_party_id FROM user WHERE ";
				for(var i = 0; i < users.length; i++){
					if(i == (users.length - 1)){
						fql += "uid = " + users[i];
					}else{
						fql += "uid = " + users[i] + " OR ";
					}
				}
				
				var query = FB.Data.query(fql);
				query.wait(function(rows){
					
					vc_fql_users = rows;
					
					//populate divs with FB data
					for(var i = 0; i < rows.length; i++){
						
						jQuery('div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
						jQuery('div.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
						
					}	
					
					display_first();
									
				});
				
			}else{
				
				display_first();
				
			}
	
			fb_operation_complete = true;
			
		});		
				
				
				
				
				
				
		
		jQuery('div#tabs > div.ui-widget-header select.promoter_select').bind('change', function(){
			jQuery('div#tabs').tabs('select', parseInt(jQuery(this).val()));
		});
		jQuery('div#tabs > div.ui-widget-header > ul').css('display', 'none');
				
				
				
				
				
				
				
				



		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			
			for(var i in unbind_callbacks){
				
				var callback = unbind_callbacks[i];
				callback();
				
			}
			
		}
		
	}
	
});