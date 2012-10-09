<style type="text/css">
.unauth_content{
	display: <?= ($this->session->userdata('vc_user')) ? 'none' : 'block'?>;
}
.auth_content{
	display: <?= ($this->session->userdata('vc_user')) ? 'block' : 'none'?>;
}
article.home{
	padding-top: 0px;
}
</style>


<article class="home" id="app_requests">
		
		<div class="auth_content">
			<?php 
				if($vc_user = $this->session->userdata('vc_user')){
					$vc_user = json_decode($vc_user);
				}
			?>
			<p style="font-size:30px;">
				Hey <span id="fname_greeting"><?= ($vc_user && isset($vc_user->first_name)) ? $vc_user->first_name : '' ?></span>, welcome back. 
			</p>
		</div>	
		
		<h2>Your friends have sent you these VibeCompass invitations</h2>
		
		<section style="border-top: 1px dashed #CCC; border-bottom: 1px dashed #CCC;" id="news">
							
			<img id="loading_indicator" src="<?= $central->global_assets . 'images/ajax.gif' ?>" alt="loading..." />
			
			<ul class="updates" id="requests_updates_ul" style="display:none;">
				
			</ul>
				
		</section>
		
		<div>
			
			<p>Join guest-lists and reserve tables at your favorite venues.</p>
									
			<p>
				With VibeCompass, there's no hassle to find a promoter and join a guest list. No filling out annoying forms, or crossing your fingers and hoping that your name actually made it to the list or if you actually have a table.
				Simply click to send a request, and VibeCompass will notify you (via SMS/email) the instant your promoter responds. Your name will automatically appear on the electronic guest list at the venue.<br>
			</p>
			
			<p style="margin-left:auto;margin-right:auto;text-align:center;">Discovering where your friends are partying has never been easier!</p>
					
			<div style="padding-top:0px;" app_id="<?= $central->facebook_app_id ?>" class="fb-facepile" data-size="large" data-max-rows="1" data-width="1000" data-colorscheme="light"></div>		
			
		</div>
			
</article>

<div id="ejs_requests_templates" style="display:none;">
	
	<?php //type 0 - 0 ?>
	<div id="add_promoter_gl">
		<li>
			<div class="avatar">
				<a class="link_[%=from.id%]" href="<?= $central->front_link_base ?>friends/">
					<div class="pic_square_[%=from.id%]">
						<img src="placehold.it/50" alt="[%from.name%]">
					</div>
				</a>
			</div>
	      	<div class="info">
	        	<a class="link_[%=from.id%]" href="<?= $central->front_link_base ?>friends/">
	        		<h2>[%=from.name%]</h2>
	        	</a>
	        	<p>[%=from.name%] has invited you to join their entourage on 
	        		[%= inline_link('promoters/' + data.retrieve_pgla.c_url_identifier + '/' + data.retrieve_pgla.up_public_identifier, data.retrieve_pgla.u_full_name, {}) %]'s guest list 
	        		"[%= inline_link('promoters/' + data.retrieve_pgla.c_url_identifier + '/' + data.retrieve_pgla.up_public_identifier + '/guest_lists/' + data.retrieve_pgla.pgla_name.replace(/ /g, '_') + '/', data.retrieve_pgla.pgla_name, {}) %]" 
	        		at [%= inline_link('venues/' + data.retrieve_pgla.c_url_identifier + '/' + data.retrieve_pgla.tv_name.replace(/ /g, '_'), data.retrieve_pgla.tv_name, {}) %]
	        	</p>
	      	</div>
		</li>
	</div>
	
	<?php //type 0 - 1?>
	<div id="add_team_gl">
		<li>
			<div class="avatar">
				<a class="link_[%=from.id%]" href="<?= $central->front_link_base ?>friends/">
					<div class="pic_square_[%=from.id%]">
						<img src="placehold.it/50" alt="[%from.name%]">
					</div>
				</a>
			</div>
	      	<div class="info">
	        	<a class="link_[%=from.id%]" href="<?= $central->front_link_base ?>friends/">
	        		<h2>[%=from.name%]</h2>
	        	</a>
	        	<p>[%=from.name%] has invited you to join their entourage on 
	        		[%= inline_link('venues/' + data.retrieve_tgla.c_url_identifier + '/' + data.retrieve_tgla.tv_name.replace(/ /g, '_') + '/', data.retrieve_tgla.tv_name, {}) %]'s guest list 
					"[%= inline_link('venues/' + data.retrieve_tgla.c_url_identifier + '/' + data.retrieve_tgla.tv_name.replace(/ /g, '_') + '/guest_lists/' + data.retrieve_tgla.tgla_name.replace(/ /g, '_') + '/', data.retrieve_tgla.tgla_name, {}) %]"</p>
	      	</div>
		</li>
	</div>
	
	<?php //type 1 ?>
	<div id="invite">
		<li>
			<div class="avatar">
				<a class="link_[%=from.id%]" href="<?= $central->front_link_base ?>friends/">
					<div class="pic_square_[%=from.id%]">
						<img src="placehold.it/50" alt="[%from.name%]">
					</div>
				</a>
			</div>
	      	<div class="info">
	        	<a class="link_[%=from.id%]" href="<?= $central->front_link_base ?>friends/">
	        		<h2>[%=from.name%]</h2>
	        	</a>
	        	<p>[%=from.name%] thinks VibeCompass is great and that you should check it out!</p>
	      	</div>
		</li>
	</div>
	
	<?php //type 2 ?>
	<div id="promoter_add_gl_manual">
		
	</div>
	
	<?php //type 3 ?>
	<div id="team_add_gl_manual">
		
	</div>
	
	<?php //type 4 ?>
	<div id="manager_promoter_invite">
		
	</div>
	
	<?php //type 5 ?>
	<div id="manager_host_invite">
		
	</div>
	
</div>



<?php if(false): ?>
<script type="text/javascript">
if(typeof window.vc_page_scripts !== 'undefined' && typeof window.vc_page_scripts.app_requests !== 'undefined'){
	window.vc_page_scripts.app_requests(true);
}else{
	jQuery(function(){
		window.vc_page_scripts.app_requests(true);
	});
}
</script>
<?php endif; ?>