if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.admin_manager_clients = function(){
		
		
		
		
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
					
					var data = [
						'<a class="ajaxify" href="' + window.module.Globals.prototype.front_link_base + 'admin/managers/clients/' + m.get('u_oauth_uid') + '/">' + m.get('u_full_name') + '</a>',
						m.get('facebook_data').sex,
						((m.get('friend_status')) ? 'Friend' : '<a data-action="add_friend" data-oauth_uid="' + m.get('u_oauth_uid') + '" href="#">Not Friend</a>'),
						m.get('u_phone_number').replace(/(\d{3})(\d{3})(\d{4})/, '($1)-$2-$3'),
						m.get('u_email'),
						'<a target="_new" href="http://www.facebook.com/' + m.get('u_oauth_uid') + '">Facebook</a>'
					];
					
					jQuery('textarea#clients_export').html(jQuery('textarea#clients_export').html() 
						+ m.get('u_full_name') 									+ ',' 
						+ data[1] 												+ ',' 
						+ ((m.get('friend_status')) ? 'Friend' : 'Not Friend') 	+ ',' 
						+ m.get('u_phone_number')								+ ',' 
						+ data[4] 												+ ',' 
						+ "\n");
					
					_this.data_table.fnAddData(data);
					
				});
				
				this.$el.find('#loading_indicator').hide();
				this.$el.find('table').show();
				this.$el.find('#clients_export_hidden').show();
				
			},
			events: {
				'click *[data-action]': 'click_data_action'
			},
			click_data_action: function(e){
				
				e.preventDefault();
				
				var el = jQuery(e.currentTarget);
				var action = el.attr('data-action');
				switch(action){
					case 'add_friend':
						
						var oauth_uid = el.attr('data-oauth_uid');
						fbEnsureInit(function(){
							
							FB.ui({
							    method: 'friends.add',
							    id: 	oauth_uid
							}, function(param) {
								
							}); 
							
						});
					
						break;
					case 'clients_export':
						this.$el.find('textarea#clients_export').show();
						this.$el.find('p#p_clients_export').show();
						break;
				}
				
				return false;
				
			}
		}; Views.UsersTable = Backbone.View.extend(Views.UsersTable);
		
		
		
		var collection_users;
		var views_users;
		
		var friends_uids = [];
		var clients_uids = [];
		for(var i in window.page_obj.clients){
			clients_uids.push(window.page_obj.clients[i].u_oauth_uid);
		}
		
		
		
		
		
		
		var op_1_complete = false;
		var op_2_complete = false;
		var op_complete_callback = function(){
			
			console.log(op_1_complete);
			console.log(op_2_complete);
			
			if(!(op_1_complete && op_2_complete))
				return;
				
			
			for(var i in window.page_obj.clients){
				
				var client = window.page_obj.clients[i];
				if(_.indexOf(friends_uids, client.u_oauth_uid) != -1){
					window.page_obj.clients[i].friend_status = true;
				}else{
					window.page_obj.clients[i].friend_status = false;
				}
								
			}
			
			collection_users = new Collections.Users(window.page_obj.clients);
			views_users = new Views.UsersTable({
				el: '#all_clients',
				collection: collection_users
			});
			
		}
				
		fbEnsureInit(function(){
			
			window.setTimeout(function(){
			
				FB.api('me/friends', function(result){
					
					console.log('me/friends');
					console.log(result);
					
					if(!result.data){
						op_1_complete = true;
						op_complete_callback();
						return;
					}
					
					//grab array of fb friends oauth uids
					for(var i in result.data){
						friends_uids.push(result.data[i].id);
					}
					
					op_1_complete = true;
					op_complete_callback();
					
				});
				
			}, 300);
						
		});
		
		jQuery.fbUserLookup(clients_uids, 'uid, name, pic_square, pic_big, sex, third_party_id', function(rows){
			
			//combine fb and co data
			for(var i in rows){
				for(var k in window.page_obj.clients){
					if(rows[i].uid == window.page_obj.clients[k].u_oauth_uid){
						window.page_obj.clients[k].facebook_data = rows[i];
					}
				}
			}
			
			op_2_complete = true;
			op_complete_callback();
						
		});


		
		
		//triggered when page is unloaded
		window.module.Globals.prototype.unbind_callback = function(){
			

		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		return;			
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