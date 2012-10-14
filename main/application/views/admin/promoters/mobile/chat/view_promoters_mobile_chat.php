<div data-role="page" id="chat" data-title="<?= $title ?>">

	<?php $this->load->view('admin/promoters/mobile/navigation/view_header', array('active' => 'Chat')); ?>	

    <div data-role="content">
        
        <div class="content-primary">
           <div class="ui-body ui-body-b" style="height:100%;">
           	
           			
				<div data-role="collapsible-set">
				
					<div id="chat_users" data-role="collapsible" data-collapsed="true">
						<h3>Team Chat Members (online: 4)</h3>
							<?php foreach($team_chat_members->managers as $manager): ?>
								<div class="user user_<?= $manager->oauth_uid ?>">
									<div style="display:inline-block" class="pic_square_<?= $manager->oauth_uid ?>">
										<div style="width:50px;height:50px;vertical-align:middle;text-align:center;">
											<img src="<?= $central->admin_assets ?>images/mobile_ajax_loader.gif" style="margin-left:auto;margin-right:auto;" />
										</div>
									</div>
									<span class="name_<?= $manager->oauth_uid ?>"></span>
								</div>
							<?php endforeach; ?>
							<?php foreach($team_chat_members->promoters as $promoter): ?>
								<div class="user user_<?= $promoter->oauth_uid ?>">
									<div style="display:inline-block" class="pic_square_<?= $promoter->oauth_uid ?>">
										<div style="width:50px;height:50px;vertical-align:middle;text-align:center;">
											<img src="<?= $central->admin_assets ?>images/mobile_ajax_loader.gif" style="margin-left:auto;margin-right:auto;" />
										</div>
									</div>
									<span class="name_<?= $promoter->oauth_uid ?>"></span>
								</div>
							<?php endforeach; ?>
						
						<style type="text/css">
							p#chat_users img.status{
								display: inline-block;
							}
						</style>
					</div>
					
									
				</div>
				
           	
	           <div style="height:260px;">
	           		<div id="team_chat_messages">
	           			<img class="message_loading_indicator" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
	           		</div>
	           		
	           		<style type="text/css">
	           			div#team_chat_messages{
	           				overflow-y: scroll;
	           				border: 1px solid #000;
	           				height: 96%;
	           				padding: 3px;
	           			}
	           			div#team_chat_messages div.chat_message{
	           				border-top: 1px solid #EEE;
	           				min-height: 70px;
	           				padding: 4px;
	           				position: relative;
	           			}
	           			div#team_chat_messages div.chat_message div.pic{
	           				display: inline-block;
	           				position: absolute;
	           			}
	           			div#team_chat_messages div.chat_message div.first_name{
	           				display: block;
	           				position: absolute;
	           				overflow: visible;
	           				color: red;
	           				top: 55px;
	           				width: 50px;
	           			}
	           			div#team_chat_messages div.chat_message div.message_wrapper{
	           				margin-left: 70px;
	           			}
	           			div#team_chat_messages div.chat_message div.message_wrapper div.message_content:before{
	           				content: ' - ';
	           			}
	           		</style>
	           		
	           </div>
	           
	           <div>
	           		<input style="margin-left:auto;margin-right:auto;width:94%;" id="team_chat_input" type="text"></input>
	           </div>
	           
	       </div>
		</div>
       
    </div>
    
    <?php $this->load->view('admin/promoters/mobile/navigation/view_footer', array('active' => 'Chat')); ?>	
    
    
<script type="text/javascript">
jQuery(document).unbind('pageinit');

jQuery(document).unbind('pagechange');
jQuery(document).bind('pagechange', function(){
	console.log('pageChange');
	jQuery('div#team_chat_messages').scrollTop(999999);
})


jQuery(document).bind('pageinit', function(){
	
	console.log('pageinit -- chat');
	jQuery('div#team_chat_messages').scrollTop(999999);
	
	var team_chat_init = function(){
		
		var pusher, team_chat_channel, vc_team_chat_users;
		Pusher.channel_auth_endpoint = '/ajax/admin_messages/';
		
		Pusher.authorizers.ajax = function(pusher, callback){
			var self = this, xhr;
	
		    if (Pusher.XHR) {
		      xhr = new Pusher.XHR();
		    } else {
		      xhr = (window.XMLHttpRequest ? new window.XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP"));
		    }
		
		    xhr.open("POST", Pusher.channel_auth_endpoint, true);
		    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
		    xhr.onreadystatechange = function() {
		      if (xhr.readyState == 4) {
		        if (xhr.status == 200) {
		          var data, parsed = false;
		
		          try {
		            data = JSON.parse(xhr.responseText);
		            parsed = true;
		          } catch (e) {
		            callback(true, 'JSON returned from webapp was invalid, yet status code was 200. Data was: ' + xhr.responseText);
		          }
		
		          if (parsed) { // prevents double execution.
		            callback(false, data);
		          }
		        } else {
		          Pusher.warn("Couldn't get auth info from your webapp", status);
		          callback(true, xhr.status);
		        }
		      }
		    };
		    
		    var csrf_token = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		    
		    xhr.send('socket_id=' + encodeURIComponent(pusher.connection.socket_id) + '&channel_name=' + encodeURIComponent(self.name) + '&ci_csrf_token=' + csrf_token);
		};
		
		pusher = new Pusher('<?= $this->config->item('pusher_api_key') ?>');
		team_chat_channel = pusher.subscribe('presence-<?= $team_fan_page_id ?>');
				
		team_chat_channel.bind('pusher:subscription_succeeded', function(members){
			
			console.log('subscription_succeeded');
			
			members.each(function(member){
				
				console.log(member);
				
				var user = jQuery('div#chat_users div.user_' + member.id);
				console.log(user);
				user.find('img.status').remove();
				user.append('<img src="<?= $central->admin_assets . 'images/green_dot.png' ?>" alt="" class="status online" />');
				
			});
			
			//indicate which users are online
			
			//retrieve chat messages and add to feed
			
			//cross-site request forgery token, accessed from session cookie
			//requires jQuery cookie plugin
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
	
			jQuery.ajax({
				url: '/ajax/admin_messages/',
				type: 'post',
				data: {ci_csrf_token: cct,
						vc_method: 'read'},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
					var message_html = '';
					admin_last_message_uid = 0;
					
					if(data.messages.length > 0){
						
						for(var i = 0; i < data.messages.length; i++){
							
							if(data.messages[i].m_users_oauth_uid != admin_last_message_uid){
								
								admin_last_message_uid = data.messages[i].m_users_oauth_uid;
								
								if(i > 0){
									message_html += '<div style="clear:both"></div>';
									message_html += '</div>';
									message_html += '</div>';
								}
								
								message_html += '<div class="chat_message">';
								message_html += '<div class="pic chat_pic_square_' + data.messages[i].m_users_oauth_uid + '"></div>';
								message_html += '<div class="first_name chat_first_name_' + data.messages[i].m_users_oauth_uid + '"></div>';
								message_html += '<div class="message_wrapper">';
								message_html += '<div class="message_content">' + data.messages[i].m_message_content + '</div>';

							}else{
																
								message_html += '<div class="message_content">' + data.messages[i].m_message_content + '</div>';								
																							
							}							
									
						}
					}				
								
					jQuery('div#team_chat_messages').html(message_html);
					setTimeout(function(){
						jQuery('div#team_chat_messages').scrollTop(999999);
					}, 40);
					
					//populate divs with FB data
					for(var i = 0; i < window.team_chat_users.length; i++){
						
						jQuery('div#team_chat_messages div.chat_pic_square_' + team_chat_users[i].uid).html('<img src="' + team_chat_users[i].pic_square + '" alt="picture" />');
						jQuery('div#team_chat_messages div.chat_first_name_' + team_chat_users[i].uid).html('<div class="vc_name"><span style="display: none;">' + team_chat_users[i].uid + '</span>' + team_chat_users[i].first_name + '</div>');
						jQuery('div#team_chat_messages span.chat_name_' + team_chat_users[i].uid).html('<div class="vc_name"><span style="display: none;">' + team_chat_users[i].uid + '</span>' + team_chat_users[i].name + '</div>');
						
					}
					
					return;
					
					admin_chat_inactive_users = data.chat_inactives;
					for(var i=0; i<data.chat_inactives.length; i++){
						jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + data.chat_inactives[i].ci_users_oauth_uid + ' img.status').remove();
						jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + data.chat_inactives[i].ci_users_oauth_uid).append('<img src="<?= $central->admin_assets . 'images/orange_dot.png' ?>" alt="" class="status away" />');
					}
					
				}
			});
					
			
			window.adminChatUserTypingNotification = false;
			jQuery('input#team_chat_input').bind('keydown', function(e){
								
				if(e.keyCode == 13){
					e.preventDefault();
					
					window.adminChatUserTypingNotification = false;
					
					var message = jQuery(this).val();
					jQuery(this).val('');
					message = jQuery.trim(message);
					
					if(message.length == 0)
						return;
					
					var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			
					jQuery.ajax({
						url: '/ajax/admin_messages/',
						type: 'post',
						data: {
								ci_csrf_token: cct,
								vc_method: 'new',
								message: message
						},
						cache: false,
						dataType: 'json',
						success: function(data, textStatus, jqXHR){
							console.log(data);		
						}
					});
					
				}else{
					
					if(!window.adminChatUserTypingNotification){
						var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
						jQuery.ajax({
							url: '/ajax/admin_messages/',
							type: 'post',
							data: {
									ci_csrf_token: cct,
									vc_method: 'chat_activity',
									chat_activity: true
							},
							cache: false,
							dataType: 'json',
							success: function(data, textStatus, jqXHR){
								console.log(data);		
							}
						});
						
						window.adminChatUserTypingNotification = true;
					}
											
				}
				
			});
			
		});
		
		
		team_chat_channel.bind('new', function(message){
							
			var user;
			for(var i=0; i < team_chat_users.length; i++){
				if(team_chat_users[i].uid == message.oauth_uid){
					user = team_chat_users[i];
					break;
				}
			}
			
			/*
			var indicator = jQuery('div#team_chat div.team_chat_users span.chat_name_' + message.oauth_uid);
			indicator.each(function(){
				jQuery(this).parent().find('img.chat_activity').css('display', 'none');
			});
			*/
			
			if(admin_last_message_uid == message.oauth_uid){
						
				jQuery('div#team_chat_messages div.chat_message:last').find('div.message_wrapper').append('<div class="message_content">' + message.message + '</div>');			
			
			}else{
						
				admin_last_message_uid = message.oauth_uid;
				
				jQuery('div#team_chat_messages').append('<div class="chat_message"><div class="pic pic_square_' + message.oauth_uid + '"><img src="' + user.pic_square + '" alt="picture" /></div><div class="first_name chat_first_name_' + message.oauth_uid + '"><div class="vc_name"><span style="display: none;">' + message.oauth_uid + '</span>' + user.first_name + '</div></div><div class="message_wrapper"><div class="message_content">' + message.message + '</div></div></div>');
			
			}
			
			jQuery('div#team_chat_messages').scrollTop(999999);
			
			
			return;
			
			
			//if the message belongs to THIS user, ignore
			if(message.oauth_uid == '<?= $users_oauth_uid ?>')
				return;
			
			//increment unread if pageslide closed
			if(jQuery('div#pageslide').css('display') == 'none' || !window_focus){
				
				var vc_user = jQuery.cookies.get('vc_user');
				var vc_admin_user = vc_user.vc_admin_user;
				
				if(vc_admin_user){
					
					if(!vc_admin_user.unread)
						vc_admin_user.unread = 0;
					
					vc_admin_user.unread++;
					jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').html('(' + vc_admin_user.unread + ')');
								
					vc_user.vc_admin_user = vc_admin_user;
					jQuery.cookies.set('vc_user', vc_user);
				
				}else{
					
					vc_admin_user = {};
					vc_admin_user.unread = 1;
					jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').html('(' + vc_admin_user.unread + ')');
					
					vc_user.vc_admin_user = vc_admin_user;
					jQuery.cookies.set('vc_user', vc_user);

				}
				
				if(!window_focus)
					adminAlertSound.play();
				
			}
				
		});
		
		
		return;
		
		team_chat_channel.bind('member_inactive', function(member){
			
			jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + member.id + ' img.status').remove();
			jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + member.id).append('<img src="<?= $central->admin_assets . 'images/orange_dot.png' ?>" alt="" class="status away" />');
			
		});

		team_chat_channel.bind('member_active', function(member){
			
			jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + member.id + ' img.status').remove();
			jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + member.id).append('<img src="<?= $central->admin_assets . 'images/green_dot.png' ?>" alt="" class="status online" />');
			
		});
		
		team_chat_channel.bind('pusher:member_added', function(member){
			
			console.log('member_added');
			console.log(member);
			
			if(window['user_timeout_' + member.id]){
				clearTimeout(window['user_timeout_' + member.id]);
				window['user_timeout_' + member.id] = false;
				return;
			}
			
			var indicator = jQuery('div#team_chat div.team_chat_users span.chat_name_' + member.oauth_uid);
			indicator.each(function(){
				jQuery(this).parent().find('img.chat_activity').css('display', 'none');
			});
			
			var span = jQuery('div#team_chat div.team_chat_users span.chat_name_' + member.id);
			
			span.find('img.status').remove();
			span.append('<img src="<?= $central->admin_assets . 'images/green_dot.png' ?>" alt="" class="status online" />');
			
			var user;
			for(var i=0; i<vc_team_chat_users.length; i++){
				if(vc_team_chat_users[i].uid == member.id){
					user = vc_team_chat_users[i];
					break;
				}
			}
			
			jQuery("div#notification_container").notify("create", {
				icon: user.pic_square,
		   		title: user.name,
		   		color: 'green',
			    text: user.name + ' has signed into team chat.'
			},{
			    speed: 1000
			});
			
			if(!window_focus)
				adminAlertSound.play();
		});
		
		
		
		
	};
	
	
	
	
	
	
	
	fbEnsureInit(function(){
		
		<?php 
			$users = array();
			foreach($team_chat_members->managers as $manager){
				$users[] = $manager->oauth_uid;
			}
			foreach($team_chat_members->promoters as $promoter){
				$users[] = $promoter->oauth_uid;
			}
			$users = json_encode($users);
		?>
		
		var users = eval('<?= $users ?>');
		
		if(users.length > 0){
			var fql = "SELECT uid, first_name, name, pic_square FROM user WHERE ";
			for(var i = 0; i < users.length; i++){
				if(i == (users.length - 1)){
					fql += "uid = " + users[i];
				}else{
					fql += "uid = " + users[i] + " OR ";
				}
			}
			
			var query = FB.Data.query(fql);
			query.wait(function(rows){
				
				window.team_chat_users = rows;
				
				for(var i = 0; i < rows.length; i++){
					
					jQuery('.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="" />');
					jQuery('.name_' + rows[i].uid).html(rows[i].name);
					
				}
				
				team_chat_init();
									
			});
		}
		
	});
	
	
	
	
	
	
	
	
});
</script>
    
</div>