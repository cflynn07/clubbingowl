<div id="fb-root"></div>
<?php if(MODE == 'local'): ?>
	<script type="text/javascript" src="<?= $central->static_assets_domain . 'assets/js/facebook_sdk_admin/?cache=' . $this->config->item('cache_global_js') ?>"></script>
<?php else: ?>
	<script type="text/javascript" src="<?= $central->static_assets_domain . 'vcweb2/assets/all_facebook_sdk_admin_' . $this->config->item('cache_global_js') . '.js' ?>"></script>
<?php endif; ?>

<div id="loading_modal" style="display:none;">
	<div id="loading_modal_inner">
		<p><?= $this->lang->line('ad-loading') ?>...</p>
		<img src="<?= $central->global_assets . 'images/ajax.gif' ?>" alt="loading..." />
	</div>
</div>