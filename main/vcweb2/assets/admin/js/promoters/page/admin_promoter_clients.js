if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_promoter_clients = function(){

		var users;
		
		
		var Models 		= {};
		var Collections = {};
		var Views 		= {};
		
		
		Models.User = {
			initialize: function(){
				
			}
		}; Models.User = Backbone.Model.extend(Models.User);
		
		Collections.Users = {
			model: Models.User,
			initialize: function(){
				
			}
		}; Collections.Users = Backbone.Collection.extend(Collections.Users);
		
		Views.UsersTable = {
			data_table: null,
			tagName: 'div',
			initialize: function(){
				
				this.render();
			},
			render: function(){
				
				if(this.data_table !== null){
					this.data_table.fnDestroy;
					this.data_table = null;
				}
				
				this.data_table = this.$el.find('table').dataTable({
					"bJQueryUI": true,
				});
				
				var _this = this;
				this.collection.each(function(m){
					
					console.log(m.toJSON());
					
					var data = [
						m.get('u_full_name'),
						m.get('facebook_data').sex,
						'',
					//	m.get('u_full_name'),
						m.get('u_phone_number').replace(/(\d{3})(\d{3})(\d{4})/, '($1)-$2-$3'),
						m.get('u_email')
					];
					
					console.log(data);
					
					_this.data_table.fnAddData(data);
					
				});
				
				
				this.$el.find('table').show();
				
			},
			events: {
				
			}
		}; Views.UsersTable = Backbone.View.extend(Views.UsersTable);
		
		
		
		var collection_users;
		var views_users;
		
		var clients_uids = [];
		for(var i in window.page_obj.clients){
			clients_uids.push(window.page_obj.clients[i].u_oauth_uid);
		}
		
		jQuery.fbUserLookup(clients_uids, 'uid, name, pic_square, pic_big, sex, third_party_id', function(rows){
	
			
			//combine fb and co data
			for(var i in rows){
				for(var k in window.page_obj.clients){
					if(rows[i].uid == window.page_obj.clients[k].u_oauth_uid){
						window.page_obj.clients[k].facebook_data = rows[i];
					}
				}
			}
			
			
			collection_users = new Collections.Users(window.page_obj.clients);
			views_users = new Views.UsersTable({
				el: '#all_clients',
				collection: collection_users
			});
			
		});


		
		
		





		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			

		}
		
		return;
		
		
		
		
		
		
		
		
		
		
		
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
		
		
		
		
		
		
		






		
		
	}
	
});