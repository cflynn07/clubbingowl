if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_clients = function(){
						
		var unbind_callbacks = [];		
				
		jQuery('.tabs').tabs();
		



		fbEnsureInit(function(){
			
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
						jQuery('div.pic_big_' + rows[i].uid).html('<img src="' + rows[i].pic_big + '" alt="picture" />');
						jQuery('div.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
						
					}	
		
					jQuery('table.clients_list').dataTable();
					jQuery("div.dataTables_filter input[type = text]").bind("mousedown",function(e){ e.stopPropagation(); });
		
					jQuery('div#main_loading_indicator').remove();
					jQuery('div#tabs').tabs().css('display', 'block');
									
				});
				
			}else{
				
				jQuery('table.clients_list').dataTable();
				jQuery("div.dataTables_filter input[type = text]").bind("mousedown",function(e){ e.stopPropagation(); });
				
				jQuery('div#main_loading_indicator').remove();
				jQuery('div#tabs').tabs().css('display', 'block');
				
			}
	
			fb_operation_complete = true;
			
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