<?php //Kint::dump($this->session->userdata('vc_user')); ?>
<?php //Kint::dump($this->session->userdata('vc_user') == true); ?>

<div id="unauth_content_holder" <?= ($this->session->userdata('vc_user')) ? 'style="display:none"' : 'style="display:block"' ?>>
	<article class="home">
					
					
			<?php if(false): ?>				
				<h1>ClubbingOwl</h1>
				<h2><?= $this->lang->line('f-m1') ?></h2>
				<p><?= $this->lang->line('f-m2') ?></p>			
				<p style="margin-left:auto;margin-right:auto;text-align:center;"><?= $this->lang->line('f-m3') ?></p>
			<?php endif; ?>
		
			<p>Making nightlife easier with Facebook! <strong>It’s free, try it!</strong></p>
			<p><a class="fb-connect vc_fb_login" href="javascript: void(0);"><img src="<?= $central->front_assets ?>images/connect-large.png" alt="Facebook Connect" /></a></p>
			
			<div style="padding-top:0px;" app_id="<?= $central->facebook_app_id ?>" class="fb-facepile" data-size="large" data-max-rows="1" data-width="1000" data-colorscheme="light"></div>		
	
	</article>
</div>
	
<?php //-------------------------------------------------------------------------------------------------------------------- ?>
	
<div id="notifications_holder" <?= ($this->session->userdata('vc_user')) ? 'style="display:block"' : 'style="display:none"' ?>>

	<?php $this->load->view('front/_common/view_front_invite'); ?>
	
	
	<section id="news" style="position:relative;">
		
		<h2><?= $this->lang->line('ha-activity_updates') ?></h2>
		
		<div id="news_feed_side_data">

			<div id="side_data_content_tracker" style="height:0;margin:0;border:0;padding:0"></div>
			<div id="side_data_content" style="display:none;">
				
				<p class="header">Friends' Favorite Guest Lists</p>
				<div id="trending_gl">
					<ul></ul>
				</div>
				
			</div>
			<div style="clear:both;"></div>
		</div>
		
		<ul class="updates"></ul>
		<div style="clear:both;"></div>
	</section>	
	
</div>