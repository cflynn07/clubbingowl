<div class="tab-content">
    
    <section id="events">
      <h1>Events</h1>
      <p>Coming soon!</p>
    </section>
  
  </div>
  
<?php if(false): ?>
<script type="text/javascript">
if(typeof window.vc_page_scripts !== 'undefined' && typeof window.vc_page_scripts.promoter_pusher_presence_channels !== 'undefined'){
	window.vc_page_scripts.promoter_pusher_presence_channels('<?= $promoter->up_users_oauth_uid ?>', '<?= $promoter->team->t_fan_page_id ?>', '<?= $promoter->up_id ?>', '<?= $this->config->item('pusher_api_key') ?>');
}else{
	jQuery(function(){
		window.vc_page_scripts.promoter_pusher_presence_channels('<?= $promoter->up_users_oauth_uid ?>', '<?= $promoter->team->t_fan_page_id ?>', '<?= $promoter->up_id ?>', '<?= $this->config->item('pusher_api_key') ?>');
	});
}
</script>
<?php endif; ?>