<li><?= $this->lang->line('mu-welcome') ?></li>
<li><%= inline_link('profile', '<?= $this->lang->line('mu-profile') ?>', {}) %></li>
<% if(vc_promoter){ %>
<li><%= inline_link('admin/promoters', '<?= $this->lang->line('mu-promoter_admin_area') ?>', {class: 'no-ajaxy'}) %></li>
<% } %>
<% if(vc_manager){ %>
<li><%=inline_link('admin/managers', '<?= $this->lang->line('mu-manager_admin_area') ?>', {class: 'no-ajaxy'})  %></li>
<% } %>
<% if(vc_super_admin){ %>
<li><%=inline_link('admin/super_admins', '<?= $this->lang->line('mu-super_admin_area') ?>', {class: 'no-ajaxy'})  %></li>
<% } %>
<% if(host){ %>
<li><%=inline_link('admin/hosts', '<?= $this->lang->line('mu-host_admin_area') ?>', {class: 'no-ajaxy'})  %></li>
<% } %>
<% if(typeof invitations !== 'undefined'){ %>
<li><a id="user_invitations_href" href="javascript: void(0);"><?= $this->lang->line('mu-invitations') ?></a></li>
<% } %>
<li><a class="no-ajaxy" id="vc_fb_logout" href="javascript: void(0);"><?= $this->lang->line('mu-logout') ?></a></li>