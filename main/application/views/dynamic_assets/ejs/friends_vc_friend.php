<li>
    <div class="avatar">
    	<%= inline_link('friends/' + third_party_id, image_insert(pic_square, {alt: name + '\'s Avatar'}), {class: 'venue-image'}) %>
    </div>
    <%= inline_link('friends/' + third_party_id, name, {class: 'name'}) %>
</li>