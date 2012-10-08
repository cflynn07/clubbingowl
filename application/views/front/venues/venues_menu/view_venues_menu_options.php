<ul class="tabs">
	<li <?= ($this->uri->rsegment(5) === false) ? 'class="active"' : '' ?>>
		<a class="ajaxify_t2" href="<?= $central->front_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/"><?= $this->lang->line('v-profile') ?></a>
	</li>
	<li <?= ($this->uri->rsegment(5) == 'guest_lists') ? 'class="active"' : '' ?>>
		<a class="ajaxify_t2" href="<?= $central->front_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/guest_lists/"><?= $this->lang->line('v-gl_t') ?></a>
	</li>
	<li <?= ($this->uri->rsegment(5) == 'events') ? 'class="active"' : '' ?>>
		<a class="ajaxify_t2" href="<?= $central->front_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/events/"><?= $this->lang->line('v-events') ?></a>
	</li>
</ul>

	<div class="tab-content">