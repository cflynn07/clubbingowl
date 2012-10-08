<li>
   <div class="avatar">
    	<%= inline_link('friends/' + third_party_id, image_insert(pic_square, {alt: first_name + ' ' + last_name + '\'s Avatar'}), {}) %>
    </div>
    <%= inline_link('friends/' + third_party_id, first_name + ' ' + last_name, {class: 'name'}) %> 
</li>