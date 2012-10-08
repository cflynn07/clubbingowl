<div id="friends_venues">
		<p><?= $this->lang->line('v-friends_visited_venue') ?></p>
		<% for(var k in tv_friends_pop){ %><% var friend = user_friends[tv_friends_pop[k]]; %><%= inline_link('friends/' + friend.third_party_id, 
					img_tag(friend.pic_square, friend.name),
					{style: 'display:inline-block;', class: 'thumbnail'}) %><% } %></div>