if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_promoter_clients = function(){
						
		var unbind_callbacks = [];
		
		
		
		
		
		
		
		
		
		
		
		fbEnsureInit(function(){
			
			//var users = eval('<?= json_encode($clients) ?>');
			var users = window.page_obj.clients;
			
			if(users.length == 0){
				
				jQuery('table#all_clients').dataTable();
				jQuery('img.loading_indicator').remove();
				
			}else{
				
				var fql = "SELECT uid, name, pic_square, pic_big, sex, third_party_id FROM user WHERE ";
				for(var i = 0; i < users.length; i++){
					if(i == (users.length - 1)){
						fql += "uid = " + users[i];
					}else{
						fql += "uid = " + users[i] + " OR ";
					}
				}
							
				var query = FB.Data.query(fql);
				query.wait(function(rows){				
					
					if(typeof vc_fql_users === 'undefined')
						window.vc_fql_users = rows;
					else{
						for(var i=0; i<rows.length; i++){
							window.vc_fql_users.push(rows[i]);
						}
					}
	
					for(var i = 0; i < rows.length; i++){
						
						jQuery('div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
						jQuery('div.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
						jQuery('div.gender_' + rows[i].uid).html('<span style="color:' + ((rows[i].sex == 'male') ? 'blue' : 'pink') + ';" >' + rows[i].sex + '</span>');
						
					}
					
					jQuery('table#all_clients').dataTable();				
					jQuery('img.loading_indicator').remove();
					
					setTimeout(function(){
											
						var fql = "SELECT uid2 FROM friend WHERE uid1 = " + window.page_obj.users_oauth_uid;
						var query = FB.Data.query(fql);
						query.wait(function(rows){
						    					    
						    for(var i=0; i < rows.length; i++){
						    	jQuery('td.friend_status_' + rows[i].uid2).removeClass('no_friend').html('<span class="fb_friends">Friends</span>');
						    }
						    
						    jQuery('td.no_friend').html('span class="fb_no_friends">Add Friend</span>');
						    
						});
						
					}, 500);
					
				});
			
				jQuery("div.dataTables_filter input[type = text]").bind("mousedown",function(e){ e.stopPropagation(); });
				
			}
	
		});
		
		jQuery('span.fb_no_friends').live('click', function(){
			//var uid = jQuery(this).parents('tr').find('td.uid').html();
			//alert(uid);
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