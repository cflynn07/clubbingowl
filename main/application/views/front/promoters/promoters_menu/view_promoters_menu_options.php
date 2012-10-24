<?php $rseg = $this->uri->rsegment(5); ?>
<ul class="tabs">
  	<li <?= ($rseg === false) ? 'class="active"' : '' ?>>
  		<a class="ajaxify_t2" href="<?= $central->front_link_base ?>promoters/<?= $promoter->team->c_url_identifier . '/' . $promoter->up_public_identifier ?>/"><?= $this->lang->line('p-profile') ?></a>
  	</li>
  	
  	<li <?= ($rseg == 'guest_lists') ? 'class="active"' : '' ?>>
  		<a class="ajaxify_t2" href="<?= $central->front_link_base ?>promoters/<?= $promoter->team->c_url_identifier . '/' . $promoter->up_public_identifier ?>/guest_lists/"><?= $this->lang->line('p-gl_t') ?></a>
  	</li>
  	
  	<?php if(false): ?>
    <li <?= ($rseg == 'events') ? 'class="active"' : '' ?>>
    	<a class="ajaxify_t2" href="<?= $central->front_link_base ?>promoters/<?= $promoter->team->c_url_identifier . '/' . $promoter->up_public_identifier ?>/events/"><?= $this->lang->line('p-events') ?></a>
    </li>
    <?php endif; ?>
    
</ul>