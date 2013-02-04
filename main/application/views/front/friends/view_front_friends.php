<article id="friends_list">
		
	<div id="unauth_content_holder" <?= ($this->session->userdata('vc_user')) ? 'style="display:none"' : 'style="display:block"' ?>>
		
		<?php if(false): ?>
		<h1>VibeCompass</h1>
	
		<p class="message"><?= $this->lang->line('fr-login_message') ?></p>		
	
		<p><a class="fb-connect vc_fb_login" href="javascript: void(0);"><img src="<?= $central->front_assets ?>images/connect-large.png" alt="Facebook Connect" /></a></p>
		
		<div style="padding-top:0px;" app_id="<?= $central->facebook_app_id ?>" class="fb-facepile" data-size="large" data-max-rows="1" data-width="1000" data-colorscheme="light"></div>		
		<?php endif; ?>
		
			<p class="message">Making your night-life even more social! <strong>It’s free, try it!</strong></p>
			<p><a class="fb-connect vc_fb_login" href="javascript: void(0);"><img src="<?= $central->front_assets ?>images/connect-large.png" alt="Facebook Connect" /></a></p>
						
	</div>
	
	<div id="friends_holder" <?= ($this->session->userdata('vc_user')) ? 'style="display:block"' : 'style="display:none"' ?>>
		
		<h1>Your Friends on ClubbingOwl</h1>
		<hr style="height: 0; border-top: 1px dashed #CCC;">
		
		<ul id="vc_friends" class="people"></ul> 
		
	</div>
	
	<div style="clear:both;"></div>
</article>