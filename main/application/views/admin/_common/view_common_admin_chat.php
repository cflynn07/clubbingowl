<?php if(false): ?>
<script type="text/javascript">

admin_title_base = document.title;

pageslide_open = function(){
	
	if(jQuery('div#team_chatbox_header'))
		jQuery('div#team_chatbox_header').css('display', 'none');
	
 	jQuery.pageslide({
		href: '#team_chat',
		modal: true
	});
	
	jQuery('div#team_chat div.team_chat_messages').scrollTop(999999);
		
	var vc_user = jQuery.cookies.get('vc_user');
	var vc_admin_user = vc_user.vc_admin_user;
	if(vc_admin_user){
		vc_admin_user.chat_open = true;
		vc_admin_user.unread = 0;
		vc_user.vc_admin_user = vc_admin_user;
		jQuery.cookies.set('vc_user', vc_user);
		jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').html('');
	}
	
	document.title = admin_title_base;
	
};


window_focus = true;
jQuery(function(){

	//-------- windowfocus ------------

	jQuery(window).focus(function() {
	//	console.log('focus');
	    window_focus = true;
	    
	    if(jQuery('div#pageslide').css('display') == 'block'){
	    		    	
	    	var vc_user = jQuery.cookies.get('vc_user');
	    	var vc_admin_user = vc_user.vc_admin_user;
			if(vc_admin_user){
				vc_admin_user.chat_open = true;
				vc_admin_user.unread = 0;
				vc_user.vc_admin_user = vc_admin_user;
				jQuery.cookies.set('vc_user', vc_user);
				jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').html('');
			}
			
			document.title = admin_title_base;
			
	    }
	    
	}).blur(function() {
    //    console.log('blur');
        window_focus = false;
        console.log(window_focus);
        
    });
	//-------- end windowfocus ------------
	
	
	
	
	//-------- soundmanager ------------
//	soundManager.url = '<?= $central->global_assets . 'swf/soundmanager/' ?>soundmanager2.swf'; // directory where SM2 .SWFs live
	
	soundManager.url = window.location.protocol + '//' + window.location.host + '/vcweb2/assets/global/swf/soundmanager/soundmanager2.swf';
	
	soundManager.onready(function(){
	
	  	// SM2 has loaded - now you can create and play sounds!
	
	  	adminAlertSound = soundManager.createSound({
	    	id: 'adminAlert',
	  		url: '<?= $central->global_assets . 'audio/' ?>alert.mp3'
	  	});
		
	});
	
	//-------- end soundmanager ------------
	
	
	
	
	//---- idle timeout ---- //
	
	idleTime = 0;
	
	var timerIncrement = function() {
	    idleTime = idleTime + 1;
	    if (idleTime > 2) { // 3 minutes
			//alert inactive
			
			var ajax_request = function(){
			
				//cross-site request forgery token, accessed from session cookie
				//requires jQuery cookie plugin
				var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		
				jQuery.ajax({
					url: '/ajax/admin_messages/',
					type: 'post',
					data: {ci_csrf_token: cct,
							admin_panel: '<?= $this->uri->segment(2) ?>',
							vc_method: 'alert_inactive'},
					cache: false,
					dataType: 'json',
					success: function(data, textStatus, jqXHR){
						
														
					}
				});	  
			};
					
			var vc_user = jQuery.cookies.get('vc_user');
			var vc_admin_user = vc_user.vc_admin_user;
						
	    	if(vc_admin_user){
	    		
	    		if(vc_admin_user.active == undefined || vc_admin_user.active){
	    			ajax_request();
	    			vc_admin_user.active = false;
	    		}
	    	
	    	}else{
	    		
	    		ajax_request();
	    		
	    		vc_admin_user = {};
	    		vc_admin_user.active = false;
	    		
	    	}
	    	
	    	vc_user.vc_admin_user = vc_admin_user;
	    	jQuery.cookies.set('vc_user', vc_user);
	    	
	    }
	}
	
	var modeActive = function(){
	    idleTime = 0;
	    	    
	    var vc_user = jQuery.cookies.get('vc_user');
	    var vc_admin_user = vc_user.vc_admin_user;
	    
	    if(vc_admin_user && (vc_admin_user.active != undefined) && !vc_admin_user.active){
	    	//alert active
	    	
	    	vc_admin_user.active = true;
	    	vc_user.vc_admin_user = vc_admin_user;
	    	jQuery.cookies.set('vc_user', vc_user);
	    	    	
	    	//cross-site request forgery token, accessed from session cookie
			//requires jQuery cookie plugin
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
	
			jQuery.ajax({
				url: '/ajax/admin_messages/',
				type: 'post',
				data: {ci_csrf_token: cct,
						admin_panel: '<?= $this->uri->segment(2) ?>',
						vc_method: 'alert_active'},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					
														
				}
			});	    	
	    	
	    }
	    
	}
	
	//Increment the idle time counter every minute.
	var idleInterval = setInterval(timerIncrement, 60000); // 1 minute
	
	//Zero the idle timer on mouse movement.
	jQuery(document).mousemove(function (e) {
		modeActive();
	});
	jQuery(document).keypress(function (e) {
		modeActive();
	});
	
	//---- end idle timeout ---- //
	
	
	
	
	
	
	
	var count_cycle = 0; 
	var cycle_notify = function(){
		
		var vc_user = jQuery.cookies.get('vc_user');
		var vc_admin_user = vc_user.vc_admin_user;
		
		if(vc_admin_user && vc_admin_user.unread && vc_admin_user.unread > 0){

			if(count_cycle % 2){

				jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').css('color', 'white');
				document.title = admin_title_base + ' (' + vc_admin_user.unread + ')';
			
			}else{
		
				jQuery('div#team_chatbox_header div#team_chatbox_header_tab span.new').css('color', 'red');
				document.title = 'New Message!';
		
			}
			
		}
		
		count_cycle++;
		if(count_cycle > 1000) 
			count_cycle = 0;
			
		setTimeout(cycle_notify, 1000);
	};
	cycle_notify();
	
	jQuery('div#notification_container').notify();
		
		
		
		
		
		
		
		
		
	var pusher;
	window.vc_team_chat_users;
	window.team_chat_channel;
	Pusher.channel_auth_endpoint = '/ajax/admin_messages/';
	//--------------------------------------- OVERRIDE PUSHER AUTH AJAX REQUEST FOR CI_CSRF_TOKEN -----------------------------------------------
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
	    var admin_panel = '<?= $this->uri->segment(2) ?>';

	    xhr.send('socket_id=' + encodeURIComponent(pusher.connection.socket_id) + '&channel_name=' + encodeURIComponent(self.name) + '&ci_csrf_token=' + csrf_token + '&admin_panel=' + admin_panel);
	};
	//--------------------------------------- OVERRIDE PUSHER AUTH AJAX REQUEST FOR CI_CSRF_TOKEN -----------------------------------------------
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/* ------ pusher ------ */
	//Called from end of fbEnsureInit
	var pusher_init = function(){








		
		
		
		
		
		
		
		
		
		<?php if($is_manager): ?>
		team_chat_channel.bind('team_guest_list_reservation', function(data){
			
			console.log('team_guest_list_reservation');
			console.log(data);
			
			if(data.manual_add == 1 || data.manual_add == '1')
				return;
			
			if(Object.prototype.toString.call(data.entourage) === '[object Array]')
				var users = data.entourage;
			else
				users = [];
			
			users.push(data.head_oauth_uid);
			
			var fql = "SELECT uid, name, pic_square, pic_big, third_party_id FROM user WHERE ";
			for(var i = 0; i < users.length; i++){
				if(i == (users.length - 1)){
					fql += "uid = " + users[i];
				}else{
					fql += "uid = " + users[i] + " OR ";
				}
			}
			
			
			FB.api({
				method: 'fql.query', 
				query: fql
			}, function(rows){
				
				
				
			});
			var query = FB.Data.query(fql);
			query.wait(function(rows){
				
				if(!window.vc_fql_users)
					window.vc_fql_users = [];
				
				//add users to window.vc_fql_users
				for(var i=0; i < rows.length; i++){
					window.vc_fql_users.push(rows[i]);
				}
								
				//find head user
				var fb_head_user;
				for(var i=0; i<rows.length; i++){
					if(rows[i].uid == data.head_oauth_uid){
						fb_head_user = rows[i];
						break;
					}
				}
				
				// new add notification
				var text = '<span style="font-weight:bold;">' + fb_head_user.name + '</span> has requested to join ';
				text += 'your guest list "<span style="text-decoration:underline;">' + data.guest_list_name + '</span>" at '; 
				text += data.venue_name + ' on ' + data.guest_list_date;
				
				if((data.entourage.length - 1) > 0)
					text += ' with ' + (data.entourage.length - 1) + ' friend' + (((data.entourage.length - 1) > 1) ? 's.' : '.');	
				
				if((data.request_msg.length - 1) > 0)
					text += '<br><span style="text-decoration:underline; font-weight:bold;">Request Message</span>: ' + data.request_msg;
				
				jQuery("div#notification_container").notify("create", {
					icon: fb_head_user.pic_square,
			   		title: '<span style="color:red;">Promoter Guest List Request!</span>',
			   		color: '#FFF',
				    text: text
				},{
					expires: (window.idleTime < 2 && window.focus), //expire if window active
				    speed: 1000
				});
				
			//	if(!window_focus || idleTime > 2)
					adminAlertSound.play();	
					
		<?php if($this->uri->rsegment(3) == 'guest_lists'): ?>
	
				var table_body = jQuery('tbody#tgla_id_' + data.tgla_id);
				var tgla_id = table_body.find('td.tgla_id').html();
				var tv_id = table_body.find('td.tv_id').html();
				var venue_name = table_body.find('td.venue_name').html();
				var date = table_body.find('td.date').html();
						
				table_body.find('td.tglr_head_user').each(function(){
					//remove if already present
				
					if(jQuery(this).html() == data.head_oauth_uid){
						jQuery(this).parent().remove();
					}
				
				});
				
				if(Object.prototype.toString.call(data.entourage) === '[object Array]')
					var users = data.entourage;
				else
					users = [];
				
				users.push(data.head_oauth_uid);
	
				//populate divs with FB data
				var table_html = '<tr class="new_add">';
				table_html += '<td class="request_type" style="display:none;">manager</td>';
				table_html += '<td class="table" style="display:none;">' + data.table_request + '</td>';
				table_html += '<td class="tglr_id hidden" style="display:none">' + data.tglr_id + '</td>';
				table_html += '<td class="tglr_head_user hidden" style="display:none">' + data.head_oauth_uid + '</td>';
				table_html += '<td class="tv_id" style="display:none;">' + tv_id + '</td>';
				table_html += '<td class="venue_name" style="display:none;">' + venue_name + '</td>';
				table_html += '<td class="date" style="display:none;">' + date + '</td>';
				
				//fields not shown on GL page but shown on dash page
				table_html += '<td class="venue" style="display:none;">' + venue_name + '</td>'; //TODO <-- make sure matches venue html on dashboard page
				table_html += '<td class="promoter" style="display:none;"> - </td>';
				table_html += '<td class="min_spend" style="display:none;">$500</td>';
											
				table_html += '<td class="user_name visual"><span class="name_' + data.head_oauth_uid + '"></span></td>';
				table_html += '<td class="user_pic visual"><div class="pic_square_' + data.head_oauth_uid + '"></div></td>';
				table_html += '<td class="visual">';
				table_html += '	<table class="user_messages" style="width:152px; text-wrap: unrestricted;">';
				table_html += '		<tr><td class="message_header">Request Message:</td></tr>';
				table_html += '		<tr><td>' + ((data.request_msg.length > 0) ? data.request_msg : ' - ') + '</td></tr>';
				table_html += '		<tr><td class="message_header">Response Message:</td></tr>';
				table_html += '		<tr><td class="response_message"> - </td></tr>';
				table_html += '		<tr><td class="message_header">Host Notes:</td></tr>';
				table_html += '		<tr style="max-width:122px;">';
				table_html += '			<td class="host_notes" style="max-width:122px;">';
				table_html += '				<div class="edit" style="display:none;">';
				table_html += '					<textarea></textarea>';
				table_html += '					<br>';
				table_html += '					<span class="message_remaining"></span>';
				table_html += '				</div>';
				table_html += '				<span class="original">';
				table_html += '					<span style="font-weight: bold;">Edit Message</span>';
				table_html += '				</span>';
				table_html += '				<img class="message_loading_indicator" style="display:none;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />';
				table_html += '			</td>';
				table_html += '		</tr>';
				table_html += '	</table>';
				table_html += '</td>';
				
				table_html += '<td class="visual"><span style="color:' + ((data.table_request == 1) ? 'green' : 'red') + ';">' + ((data.table_request == 1) ? 'Yes' : 'No') + '</span></td>';
				table_html += '<td class="visual">' + ((data.approved == 1) ? '<span style="color:green;">Approved</span>' : '<span class="app_dec_action" style="font-weight: bold; text-decoration: underline; cursor: pointer; color: blue;">Requested</span>') + '</td>';
				
				table_html += '<td class="entourage visual" style="white-space:nowrap; width:244px;">';
				if(data.entourage.length > 0){
					table_html += '		<table>';
					table_html += '			<thead>';
					table_html += '				<tr>';
					table_html += '					<th>Name</th>';
					table_html += '					<th>Picture</th>';
					table_html += '				</tr>';
					table_html += '			</thead>';
					table_html += '			<tbody>';
					for(var i=0; i<data.entourage.length; i++){
						
						if(data.entourage[i] == data.head_oauth_uid)
							continue;
							
						table_html += '<tr' + ((i % 2) ? ' class="odd"' : '') + '>';
						table_html += '		<td><span class="name_' + data.entourage[i] + '"></span></td>';	
						table_html += '		<td><div class="pic_square_' + data.entourage[i] + '"></div></td>';	
						table_html += '</tr>';											
					}
					table_html += '			</tbody>';
					table_html += '		</table>';
				}else{
					table_html += '<p>No Entourage</p>';
				}
				table_html += '</td>';
				table_html += '</tr>';
				
								
				table_body.find('tr.no_reservations').remove();			
				table_body.find('tr:last').before(table_html);
				
				//-------------				
				var index = parseInt(jQuery('div.div_venue_select').find('div.' + data.tv_id).find('div.index').html());
				jQuery('select.venue_select').val(index);
				
				//set new guest list visible
				jQuery('div#tabs-' + index + ' ul.sitemap li').css('font-weight', 'normal');
				jQuery('div#tabs-' + index + ' ul.sitemap').find('li.' + data.tgla_id).css('font-weight', 'bold');
				
				var count = parseInt(jQuery('div#tabs-' + index + ' ul.sitemap').find('li.' + data.tgla_id).find('span.count_tgla_id').html());
				count++;
				jQuery('div#tabs-' + index + ' ul.sitemap').find('li.' + data.tgla_id).find('span.count_tgla_id').html(count);
				
				console.log(count);
				console.log(index);
				console.log(data.tgla_id);
				console.log(jQuery('div#tabs-' + index + ' ul.sitemap').find('li.' + data.tgla_id).find('span.count_tgla_id'));
				
				jQuery('div#tabs-' + index + ' div#lists_container div.list').css('display', 'none');
				jQuery('div#tabs-' + index + ' div#tgla_' + data.tgla_id).css('display', 'block');
				
				var name = jQuery('div#tabs div.ui-widget-header div.' + data.tv_id).find('div.name').html();
				var tv_count = parseInt(jQuery('div#tabs div.ui-widget-header div.' + data.tv_id).find('div.count').html());
				tv_count += 1;
				jQuery('div#tabs div.ui-widget-header select.venue_select option.' + data.tv_id).html(name + ' (' + tv_count + ')');
				jQuery('div#tabs div.ui-widget-header div.' + data.tv_id).find('div.count').html(tv_count);
				
				jQuery('div#tabs').tabs('select', index);
				//-------------
				
				//find out offset of the added GL entry
				var offset = jQuery('tr.new_add').offset();
				jQuery(document).scrollTop(offset.top - 50);
								
				for(var i=0; i<rows.length; i++){
					jQuery('div.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
					jQuery('span.name_' + rows[i].uid).html('<a href="#" class="vc_name"><span class="uid" style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</a>');
				}
				
				jQuery('tr.new_add td.visual').show('highlight', { color:'red' }, 1500, function(){
					jQuery('tr.new_add').removeClass('new_add');
					jQuery('td.visual').removeClass('visual');
					
					window.zebraRows('table.guestlists > tbody > tr:odd', 'odd');
					
					if(!jQuery.isIpad())
						jQuery('table.guestlists > tbody > tr').hover(function(){
							jQuery(this).addClass('hovered');
						}, function(){
							jQuery(this).removeClass('hovered');
						});
				});
				
		<?php endif; ?>
					
			});
						
		});
				
	<?php endif; ?>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		
		team_chat_channel.bind('new', function(message){
			
			console.log('new');
			console.log(message);
				
			var user;
			for(var i=0; i < vc_team_chat_users.length; i++){
				if(vc_team_chat_users[i].uid == message.oauth_uid){
					user = vc_team_chat_users[i];
					break;
				}
			}
			
			var indicator = jQuery('div#team_chat div.team_chat_users span.chat_name_' + message.oauth_uid);
			indicator.each(function(){
				jQuery(this).parent().find('img.chat_activity').css('display', 'none');
			});
			
			if(admin_last_message_uid == message.oauth_uid){
						
				jQuery('div#team_chat').each(function(){
					jQuery(this).find('div.team_chat_messages div.chat_message:last').find('div.message_wrapper').append('<div class="message_content">' + message.message + '</div>');
				});
			
			
			}else{
						
				admin_last_message_uid = message.oauth_uid;
				
				jQuery('div#team_chat div.team_chat_messages').each(function(){
					jQuery(this).append('<div class="chat_message"><div class="pic pic_square_' + message.oauth_uid + '"><img src="' + user.pic_square + '" alt="picture" /></div><div class="first_name chat_first_name_' + message.oauth_uid + '"><div class="vc_name"><span style="display: none;">' + message.oauth_uid + '</span>' + user.first_name + '</div></div><div class="message_wrapper"><div class="message_content">' + message.message + '</div></div></div>');
				});
			
			}
			
			jQuery('div#team_chat div.team_chat_messages').scrollTop(999999);
			
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
		
		team_chat_channel.bind('pusher:subscription_succeeded', function(members){
			
			console.log('subscription_succeeded');
			
			members.each(function(member){

				var span = jQuery('div#team_chat div.team_chat_users span.chat_name_' + member.id);
				span.find('img.status').remove();
				span.append('<img src="<?= $central->admin_assets . 'images/green_dot.png' ?>" alt="" class="status online" />');
				
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
						admin_panel: '<?= $this->uri->segment(2) ?>',
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
								
					jQuery('div#team_chat div.team_chat_messages').html(message_html);
					setTimeout(function(){
						jQuery('div#team_chat div.team_chat_messages').scrollTop(999999);
					}, 40);
					
					//populate divs with FB data
					for(var i = 0; i < vc_team_chat_users.length; i++){
						
						jQuery('div#team_chat div.team_chat_messages div.chat_pic_square_' + vc_team_chat_users[i].uid).html('<img src="' + vc_team_chat_users[i].pic_square + '" alt="picture" />');
						jQuery('div#team_chat div.team_chat_messages div.chat_first_name_' + vc_team_chat_users[i].uid).html('<div class="vc_name"><span style="display: none;">' + vc_team_chat_users[i].uid + '</span>' + vc_team_chat_users[i].first_name + '</div>');
						jQuery('div#team_chat div.team_chat_messages span.chat_name_' + vc_team_chat_users[i].uid).html('<div class="vc_name"><span style="display: none;">' + vc_team_chat_users[i].uid + '</span>' + vc_team_chat_users[i].name + '</div>');
						
					}
					
					admin_chat_inactive_users = data.chat_inactives;
					for(var i=0; i<data.chat_inactives.length; i++){
						jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + data.chat_inactives[i].ci_users_oauth_uid + ' img.status').remove();
						jQuery('div#team_chat div#team_chat_wrapper div.team_chat_users').find('span.chat_name_' + data.chat_inactives[i].ci_users_oauth_uid).append('<img src="<?= $central->admin_assets . 'images/orange_dot.png' ?>" alt="" class="status away" />');
					}
					
				}
			});
			
			
			window.adminChatUserTypingNotification = false;
			jQuery('div#team_chat div.team_chat_input textarea').bind('keydown', function(e){
								
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
								admin_panel: '<?= $this->uri->segment(2) ?>',
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
									admin_panel: '<?= $this->uri->segment(2) ?>',
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
				icon: '<img src="' + user.pic_square + '" alt="' + user.name + '" />',
		   		title: user.name,
		   		color: 'green',
			    text: user.name + ' has signed into team chat.'
			},{
			    speed: 1000
			});
			
			if(!window_focus)
				adminAlertSound.play();
		});
		
		
		
		
		
		<?php
		
			$users = array();
			
			foreach($team_chat_members->managers as $man){
				$users[] = $man->oauth_uid;
			}
			
			foreach($team_chat_members->promoters as $pro){
				$users[] = $pro->oauth_uid;
			}
			
			foreach($team_chat_members->hosts as $host){
				$users[] = $host->oauth_uid;
			}
		
		?>
		
		<?php foreach($users as $u): ?>
		var user_timeout_<?= $u ?> = false;<?= PHP_EOL ?>
		<?php endforeach; ?>
				
				
				
				
				
				
				
		team_chat_channel.bind('pusher:member_removed', function(member){
			
			console.log('member_removed');
			console.log(member);
			
			window['user_timeout_' + member.id] = setTimeout(function(){
				var span = jQuery('div#team_chat div.team_chat_users span.chat_name_' + member.id);
				span.find('img.status').remove();
				window['user_timeout_' + member.id] = false;
				
				var user;
				for(var i=0; i<vc_team_chat_users.length; i++){
					if(vc_team_chat_users[i].uid == member.id){
						user = vc_team_chat_users[i];
						break;
					}
				}
				
				jQuery("div#notification_container").notify("create", {
					icon: '<img src="' + user.pic_square + '" alt="' + user.name + '" />',
			   		title: user.name,
			   		color: 'red',
				    text: user.name + ' has signed out of team chat.'
				},{
				    speed: 1000
				});
			
				
			}, (1000 * 10));
						
		});
		
		team_chat_channel.bind('user_chat_activity', function(member){
			
			console.log('user_chat_activity');
			console.log(member);
			
			if(member.oauth_uid == '<?= $users_oauth_uid ?>')
				return;
			
			var indicator = jQuery('div#team_chat div.team_chat_users span.chat_name_' + member.oauth_uid).parent().find('img.chat_activity');
			
			console.log(indicator);
			
			if(member.chat_activity == 'true'){
				indicator.each(function(){
					jQuery(this).css('display', 'inline-block');
				});
			}else{
				indicator.each(function(){
					jQuery(this).css('display', 'none');
				});
			}
			
		});
		
		//Pusher init event trigger
//		window.EventHandlerObject.pusher_init();
		
	};	//<-------- end pusher_init();
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//populate facebook faces of team chat users
	fbEnsureInit(function(){
			
		var users = eval('<?= json_encode($users) ?>');
		
		<?php unset($users); ?>
		
		if(users.length > 0){
			
			var fql = "SELECT uid, name, first_name, pic_square, pic_big FROM user WHERE ";
			for(var i = 0; i < users.length; i++){
				if(i == (users.length - 1)){
					fql += "uid = " + users[i];
				}else{
					fql += "uid = " + users[i] + " OR ";
				}
			}
			
			console.log(fql);
			
			FB.api({
				method: 'fql.query',
				fql: fql
			}, function(rows){
				
			});
			
			var query = FB.Data.query(fql);
			query.wait(function(rows){
				console.log(rows);
				vc_team_chat_users = rows;
				
				//populate divs with FB data
				for(var i = 0; i < rows.length; i++){
					
					jQuery('div#team_chat div.chat_pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="picture" />');
					jQuery('div#team_chat span.chat_name_' + rows[i].uid).html('<div class="vc_name"><span style="display: none;">' + rows[i].uid + '</span>' + rows[i].name + '</div>');
					
				}
				
				pusher = new Pusher('<?= $this->config->item('pusher_api_key') ?>');
				team_chat_channel = pusher.subscribe('presence-<?= $team_fan_page_id ?>');
				pusher_init();
			});	
		}
	});
		
	/* ------ pageslide ------ */
	var close_pageslide = jQuery.pageslide.close;
	jQuery.pageslide.close = function(){
				
		jQuery('div#team_chatbox_header').css('display', 'block');
		close_pageslide();
		
		var vc_user = jQuery.cookies.get('vc_user');
		var vc_admin_user = vc_user.vc_admin_user;
		
		if(vc_admin_user){
						
			vc_admin_user.chat_open = false;
			vc_user.vc_admin_user = vc_admin_user;
			jQuery.cookies.set('vc_user', vc_user);
		}
			
	}
	
	jQuery('div#team_chatbox_header').live('click', pageslide_open);
	
	jQuery('div#team_chat div#team_chatbox_header_tab_close').bind('click', function(){
				
		jQuery.pageslide.close();
		
		return false;
	});
		
		
		
		
		
		
		
	var supportsOrientationChange = "onorientationchange" in window,
	    orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";

	jQuery(window).bind(orientationEvent, function(){
		
		jQuery('meta[name="viewport"]').attr('content', 'user-scalable=0, width=1110');
		setTimeout(function(){
			jQuery('meta[name="viewport"]').attr('content', 'user-scalable=0, width=1110');
		}, 100);
		
	});







	window.globalScrollOffset = 0;
	jQuery(document).bind('scroll', function(){
		window.globalScrollOffset = jQuery(this).scrollTop();	
	});
	
	jQuery('div#team_chat textarea').val('Start Typing...').css({
		color: '#333',
		'background-color': 'rgba(0,0,0,.8)'
	});
	
	jQuery('div#team_chat textarea').live('focus', function(e){
				
		if(jQuery(this).css('color') == 'rgb(51, 51, 51)')
			jQuery(this).val('').css('color', '#FFF');
		
		var scroll_offset = window.globalScrollOffset;
				
		document.ontouchmove = function(e2){
			e2.preventDefault();
		}
		
		if(jQuery.isIpad())
			setTimeout(function(){
	
				var orientation = Math.abs(window.orientation) == 90 ? 'landscape' : 'portrait';
	
				jQuery(document).scrollTop(scroll_offset);
				
				if(orientation == 'landscape')
					jQuery('div#pageslide').css('bottom', '59%');
				else
					jQuery('div#pageslide').css('bottom', '33%');
				
			},1);
				
	});
	
	jQuery('div#team_chat textarea').live('blur', function(){
		
		document.ontouchmove = function(e){
			return;
	    }
	    
	    if(jQuery(this).val() == '')
	    	jQuery(this).val('Start Typing...').css('color', '#333');
		
		jQuery('div#pageslide').css('bottom', '0');
		
		if(window.adminChatUserTypingNotification){
			var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
			jQuery.ajax({
				url: '/ajax/admin_messages/',
				type: 'post',
				data: {
						ci_csrf_token: cct,
						vc_method: 'chat_activity',
						admin_panel: '<?= $this->uri->segment(2) ?>',
						chat_activity: false
				},
				cache: false,
				dataType: 'json',
				success: function(data, textStatus, jqXHR){
					console.log(data);		
				}
			});
			
			window.adminChatUserTypingNotification = false;
		}
		
	});
	
});

</script>


<?php endif; ?>
	
	
<div id="notification_container" style="display:none;">
	
	<div id="basic-template">
        <a class="ui-notify-close ui-notify-cross" href="#">x</a>
		<div style="float:left; margin:0 10px 0 0;">
			#{icon}
		</div>
		<h1>#{title}</h1>
		<p style="color:#{color}">#{text}</p>
    </div>
    
	<div id="new-promoter-request">
        <a class="ui-notify-close ui-notify-cross" href="#">x</a>
        <div>
			<div style="float:left; margin:0 10px 0 0;">
				#{icon}
			</div>
			<div style="display:inline-block; float:right; width:250px;">
				<h1>#{title}</h1>
				<p style="color:#{color}">#{text}</p>
				<div class="notification_faces">
					#{ent0}
					#{ent1}
					#{ent2}
					#{ent3}
					#{ent4}
					#{ent5}
					#{ent6}
					#{ent7}
					#{ent8}
				</div>
			</div>
		</div>
		<div style="clear:both;"></div>
    </div>
    
</div>

<div id="team_chat" style="display:none">
	
	<div id="team_chatbox_header_tab_close"><a class="close_button" style="color:red;" href="#">Close</a></div>
	
	<br>
	
	<div <?php if(isset($mt_live_status) && !$mt_live_status): ?> style="display:none;" <?php endif; ?> id="team_chat_wrapper">
		<div class="team_chat_users">
			
			<p style="color:red; margin:0px;">Managers</p>
			<?php foreach($team_chat_members->managers as $man): ?>
				<div style="display:block;"><span class="chat_name_<?= $man->oauth_uid ?>"></span><img class="chat_activity" style="display:none;" src="<?= $central->admin_assets . 'images/chat_user_activity.gif' ?>" alt="" /></div>
			<?php endforeach; ?>
			
			<p style="color:red; margin:0px;">Promoters</p>
			<?php foreach($team_chat_members->promoters as $pro): ?>
				<?php if(!($pro->pt_banned == '1' || $pro->pt_banned == 1 || $pro->pt_quit == '1' || $pro->pt_quit == 1)): ?>
				<div style="display:block;"><span class="chat_name_<?= $pro->oauth_uid ?>"></span><img class="chat_activity" style="display:none;" src="<?= $central->admin_assets . 'images/chat_user_activity.gif' ?>" alt="" /></div>
				<?php endif; ?>
			<?php endforeach; ?>
			<p style="color:red; margin:0px;">Hosts</p>
			<?php foreach($team_chat_members->hosts as $host): ?>
				<?php if(!($host->th_banned == '1' || $host->th_banned == 1 || $host->th_quit == '1' || $host->th_quit == 1)): ?>
				<div style="display:block;"><span class="chat_name_<?= $host->oauth_uid ?>"></span><img class="chat_activity" style="display:none;" src="<?= $central->admin_assets . 'images/chat_user_activity.gif' ?>" alt="" /></div>
				<?php endif; ?>
			<?php endforeach; ?>
				
		</div>
		
		<div class="team_chat_messages_wrapper">
			<div class="team_chat_messages">
			
				<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." /><br>
				<span>Connecting...</span>
			
			</div>
			
			<br>
		
			<div class="team_chat_input">
				<textarea rows="1"></textarea>
			</div>
		</div>

		
	</div>
	 
</div>

<div id="team_chatbox_header">
	<div id="team_chatbox_header_tab">
		<span class="team_chat">Team Chat</span> <span class="new"></span>
	</div>
</div>