<?php 

$lang["v-friends_visited_venue"]		= "<%=tv_friends_pop.length%> amigo(s) han estado aquí. ";
$lang["v-coming_soon"]		= "Próximamente!";
$lang["v-map"]		= "Mapa";
$lang["v-description"]		= "Descripción ";
$lang["v-friends"]		= "Amigos";
$lang["v-promoters"]		= "Promotores";
$lang["v-friend_activity"]		= "Actividad de tus Amigos";
$lang["v-profile"]		= "Perfil";
$lang["v-gl_t"]		= "Listas &amp; Mesas";
$lang["v-events"]		= "Eventos";
$lang["v-no_venues_city"]		= "No hay clubes en <%=location%> aun! Vuelve a revisar pronto!";
$lang["v-jpgl"]		= "<%= user_friend.first_name + ' ' + user_friend.last_name %> ha reservado su espacio en la lista de <%= inline_link('promoters/' + ((notification_data.c_url_identifier) ? notification_data.c_url_identifier : 'boston') + '/' + notification_data.up_public_identifier, notification_data.u_full_name, {}) %> llamada \"<%= inline_link('promoters/' + ((notification_data.c_url_identifier) ? notification_data.c_url_identifier : 'boston') + '/' + notification_data.up_public_identifier + '/guest_lists/' + pi_link_convert(notification_data.pgla_name), notification_data.pgla_name) %>\"";
$lang["v-jtgl"]		= "<%= user_friend.first_name + ' ' + user_friend.last_name %> ha reservado su espacio en la lista \"<%= notification_data.tgla_name %>\"";
$lang["v-friend_count_msg"]		= "<%=count%> amigos han estado aquí. ";
