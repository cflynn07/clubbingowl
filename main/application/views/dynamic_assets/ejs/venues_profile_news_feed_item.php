<% if(notification.notification_type == 'join_promoter_guest_list'){ %>

<li>
  <div class="avatar">
  	
  	<%= inline_link('friends/' + user_friend.third_party_id,
  		image_insert(user_friend.pic_square, {alt: user_friend.first_name + ' ' + user_friend.last_name},
  		{})
  	) %>
  	
  </div>
  <div class="info">
    <h2>
    	<%= inline_link('friends/' + user_friend.third_party_id, user_friend.first_name + ' ' + user_friend.last_name) %>
    </h2>
    <p><?= $this->lang->line('v-jpgl') ?></p>
  </div>
</li>

<% }else if(notification.notification_type == 'join_team_guest_list'){ %>

<li>
  <div class="avatar">
  	
  	<%= inline_link('friends/' + user_friend.third_party_id,
  		image_insert(user_friend.pic_square, {alt: user_friend.first_name + ' ' + user_friend.last_name},
  		{})
  	) %>
  	
  </div>
  <div class="info">
    <h2>
    	<%= inline_link('friends/' + user_friend.third_party_id, user_friend.first_name + ' ' + user_friend.last_name) %>
    </h2>
    <p><?= $this->lang->line('v-jtgl') ?></p>
  </div>
</li>

<% } %>