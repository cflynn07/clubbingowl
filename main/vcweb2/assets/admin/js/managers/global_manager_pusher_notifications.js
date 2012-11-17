if(typeof window.vc_page_scripts === 'undefined')
	window.vc_page_scripts = {};

jQuery(function(){
	
	window.vc_page_scripts.global_manager_pusher_notifications = function(){	
		
		var team_chat_channel = team_chat_object.team_chat_channel;
		
		team_chat_channel.bind('promoter_guest_list_reservation', function(data){
			
			// ------------- Assemble all UIDS of all users in request ------------
			if(Object.prototype.toString.call(data.entourage) === '[object Array]')
				var users = data.entourage;
			else
				users = [];
			
			users.push(data.head_oauth_uid);
			// ------------- Assemble all UIDS of all users in request ------------
			
			jQuery.fbUserLookup(users, '', function(rows){
				
				//find head user
				var fb_head_user;
				var entourage;
				for(var i=0; i<rows.length; i++){
					if(rows[i].uid == data.head_oauth_uid){
						fb_head_user = rows[i];
						entourage = rows.slice();
						entourage.splice(i, 1);
						break;
					}
				}
				
				//find the promoter
				var promoter;
				for(var i=0; i < vc_team_chat_users.length; i++){
					if(vc_team_chat_users[i].uid == data.promoter_oauth_uid){
						promoter = vc_team_chat_users[i];
						break;
					}
				}
				
				//TODO ----- look into improving this mess
				if(!window.vc_fql_users)
					window.vc_fql_users = [];
				
				//add users to window.vc_fql_users
				for(var i=0; i < rows.length; i++){
					window.vc_fql_users.push(rows[i]);
				}
				//TODO ----- look into improving this mess
				
				var image_gen = function(src){
					return '<img src="' + src + '" alt="" />';
				}
				
				//Growl notification of new guest list request
				if(data.manual_add == 0){
					var text = '<span style="font-weight:bold;">' + fb_head_user.name + '</span> has requested to join ';
					text += promoter.name + '\'s guest list "<span style="text-decoration:underline;">' + data.guest_list_name + '</span>" at '; 
					text += data.venue_name + ' on ' + data.guest_list_date;
					
					if((data.entourage.length - 1) > 0)
						text += ' with ' + (data.entourage.length - 1) + ' friend' + (((data.entourage.length - 1) > 1) ? 's.' : '.');	
					
					if((data.request_msg.length - 1) > 0)
						text += '<br><span style="text-decoration:underline; font-weight:bold;">Request Message</span>: ' + data.request_msg;
					
					jQuery("div#notification_container").notify('create', 'new-promoter-request', {
						icon: fb_head_user.pic_square,
				   		title: '<span style="color:red;">Promoter Guest List Request!</span>',
				   		color: '#FFF',
					    text: text,
					    ent0: (0 < entourage.length) ? image_gen(entourage[0].pic_square) : '',
					    ent1: (1 < entourage.length) ? image_gen(entourage[1].pic_square) : '',
					    ent2: (2 < entourage.length) ? image_gen(entourage[2].pic_square) : '',
					    ent3: (3 < entourage.length) ? image_gen(entourage[3].pic_square) : '',
					    ent4: (4 < entourage.length) ? image_gen(entourage[4].pic_square) : '',
					    ent5: (5 < entourage.length) ? image_gen(entourage[5].pic_square) : '',
					    ent6: (6 < entourage.length) ? image_gen(entourage[6].pic_square) : '',
					    ent8: (7 < entourage.length) ? '...' : ''
					},{
						expires: false, //expire if window active
					    speed: 1000
					});
				}else{
					var text = '<span style="font-weight:bold;">' + promoter.name + '</span> has added ';
					text += fb_head_user.name + ' to his guest list "<span style="text-decoration:underline;">' + data.guest_list_name + '</span>" at '; 
					text += data.venue_name + ' on ' + data.guest_list_date;
					if(data.request_msg.length > 0)
						text += '<br><span style="text-decoration:underline; font-weight:bold;">Request Message:</span> ' + data.request_msg;
								
					jQuery("div#notification_container").notify("create", {
						icon: fb_head_user.pic_square,
				   		title: '<span style="color:red;">Promoter Guest List Manual Addition!</span>',
				   		color: '#FFF',
					    text: text,
					    ent0: (0 < entourage.length) ? image_gen(entourage[0].pic_square) : '',
					    ent1: (1 < entourage.length) ? image_gen(entourage[1].pic_square) : '',
					    ent2: (2 < entourage.length) ? image_gen(entourage[2].pic_square) : '',
					    ent3: (3 < entourage.length) ? image_gen(entourage[3].pic_square) : '',
					    ent4: (4 < entourage.length) ? image_gen(entourage[4].pic_square) : '',
					    ent5: (5 < entourage.length) ? image_gen(entourage[5].pic_square) : '',
					    ent6: (6 < entourage.length) ? image_gen(entourage[6].pic_square) : '',
					    ent8: (7 < entourage.length) ? '...' : ''
					},{
						expires: false,
					    speed: 1000
					});
				}
				
				adminAlertSound.play();
				
			});
			
		});
		
		
		team_chat_channel.bind('team_guest_list_reservation', function(data){
			
		});
		
		
	};
	
});