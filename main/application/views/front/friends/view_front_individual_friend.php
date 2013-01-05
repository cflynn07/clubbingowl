<style type="text/css">
	.unauth_content{
		display: <?= ($this->session->userdata('vc_user')) ? 'none' : 'block' ?>;
	}
	.auth_content{
		display: <?= ($this->session->userdata('vc_user')) ? 'block' : 'none' ?>;
	}
</style>



<article class="profile">




  <header class="auth_content">
  	<div>
	  	<div class="avatar">
	  		<img style="border-radius:10px;" src="https://graph.facebook.com/<?= $friend->users_oauth_uid ?>/picture?type=large"  />
	  	</div>
	  	<div>
	  		<h1><?= $friend->users_full_name ?></h1>
	  		<br><br><br>
	  		
	  		<?php if(false): ?>
	  		<p id="since_date">ClubbingOwl since: <?= strftime('%B, %G', $friend->users_join_time) ?></p>
	  		<?php endif; ?>
	  		
	  		<p id="since_date"><?= lang_key($this->lang->line('fr-since_date'), array(
				'date' => strftime('%B, %G', $friend->users_join_time)
			)) ?></p>
	  	
	  	</div>
  		<div style="clear:both;"></div>
  	</div>
  </header>
  
  
  
  
  
  
	<div id="loading_indicator" class="auth_content" style="text-align:center; margin-top:5px;">
		<img src="<?= $central->global_assets ?>images/ajax.gif" alt="Loading..." style="margin-left:auto; margin-right:auto; position:relative;" />
	</div>
	
	
	
	
	
	<p id="friend_error"></p>
	
	
	<div style="width:100%; text-align:center;" class="unauth_content">
		
		<?php if(false): ?>
		<h2><?= $friend->users_first_name ?> is on ClubbingOwl!</h2>
		<?php endif; ?>
		
	
		<table style="margin:120px auto 40px auto;">
			
			<tr>
				<td style="vertical-align:top;">
					<div class="avatar">
				  		<img id="friend_pic_unauth" class="fb-connect venue-image vc_fb_login" style="cursor:pointer;" src="https://graph.facebook.com/<?= $friend->users_oauth_uid ?>/picture?type=large"  />
				  	</div>
				</td>
				<td style="padding-top:0; vertical-align:top;">
					<h2 style="margin-top:0;"><?= lang_key($this->lang->line('fr-indiv_friend_title'), array(
						'full_name' => $friend->users_full_name
					)) ?></h2>
					
					<?php if(true): ?>
						<p class="message">Log in to ClubbingOwl with Facebook to see <?= $friend->users_first_name ?>'s favorite clubs, promoters and guest-lists.</p>		
					<?php endif; ?>
					
					<p><a class="fb-connect vc_fb_login" href="javascript: void(0);"><img src="<?= $central->front_assets ?>images/connect-large.png" alt="Facebook Connect" /></a></p>
					
				</td>
			</tr>
			
			<script type="text/javascript">
				jQuery('#friend_pic_unauth').bind('click', function(){ jQuery('a.vc_fb_login:first').trigger('click'); });
			</script>

		</table>
		
				
		<?php if(false): ?>
		<p class="message"><?= lang_key($this->lang->line('fr-login_message_indiv'), array(
			'first_name' => $friend->users_first_name
		)) ?></p>		
		<?php endif; ?>
			
		<div style="padding-top:0px; top:-20px; width:90%;" app_id="<?= $central->facebook_app_id ?>" class="fb-facepile" data-size="large" data-max-rows="1" data-width="1000" data-colorscheme="light"></div>		
		
		
		<img id="toro" style="width:50%; margin-bottom:50px; margin-top:10px;" src="<?= $central->front_assets ?>images/ClubbingOwlBackgroundWeb.png">
		
		
	</div>
	
	
	<div id="friend_content" style="display:none;">
		
		  <section style="margin-top:0;" id="friends">
		    <h2><?= $this->lang->line('fr-friends_title') ?></h2>
		    <ul id="vibecompass_friends" class="people"></ul>
		  </section>
		  
		  
		  <section style="margin-top:0;" id="activity">
		    <h2><?= $this->lang->line('fr-recent_activity_title') ?></h2>
		    <ul id="recent_activity"></ul>
		  </section>
		
		
		  <section style="margin-top:0;" id="promoters">
		    <h2><?= $this->lang->line('fr-promoters_title') ?></h2>
		    <ul id="user_promoters" class="people"></ul>
		  </section> 
		  
	</div>

</article>


<script type="text/javascript">
<?php
	$obj = new stdClass;
	$obj->users_oauth_uid = $friend->users_oauth_uid;
	$obj->users_full_name = $friend->users_full_name;
	$obj->users_first_name = $friend->users_first_name;
?>
window.individual_friend_obj = <?= json_encode($obj) ?>;
</script>