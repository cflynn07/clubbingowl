<li style="margin-bottom:30px;">
    <div class="avatar">
    	<%= inline_link('friends/' + third_party_id, image_insert('https://graph.facebook.com/' + uid + '/picture?type=large&width=100&height=100', {alt: name + '\'s Avatar', class: 'venue-image', style: "padding-top:0; margin:0;"})) %>
    </div>
    <%= inline_link('friends/' + third_party_id, name, {class: 'name', style: 'margin-top:5px;'}) %>
</li>