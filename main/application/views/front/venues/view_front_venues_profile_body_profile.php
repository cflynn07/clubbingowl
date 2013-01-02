<section id="profile" style="display: block;">
  <h1><?= $this->lang->line('v-profile') ?></h1>
  
  <h2><?= $this->lang->line('v-friends') ?></h2>
  <div class="bio">
  	
  	<div style="width:100%;text-align:center;">
  		
	  	<div class="unauth_content">
			<p><?= lang_key($this->lang->line('p-login_msg1'), array('team_venue_name' => '')) ?></p>
			<p><a class="fb-connect vc_fb_login" href="javascript: void(0);"><img src="<?= $central->front_assets ?>images/connect-large.png" alt="Facebook Connect" /></a></p>
		</div>
		
		<div id="venue_user_friends_feed" class="auth_content">
			<p class="auth_clear_content" id="friends_count_msg" style="text-align:left;"></p>
			<ul style="margin-top:20px;" class="auth_clear_content people" id="vc_friends"></ul>
			<img class="loading_indicator" style="margin-left:auto; margin-right:auto;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
		</div>
		
  	</div>
  	
  </div>
    
  <h2><?= $this->lang->line('v-promoters') ?></h2>
  <div class="bio">
  	
  	<ul class="promoters">
	  	<?php foreach($venue->venue_promoters as $promoter): ?>
	  		
	  		<?php $pro_link = $central->front_link_base . 'promoters/' . $promoter->up_public_identifier . '/guest_lists/'; ?>
	  		
	  	<li>
	  		<a class="ajaxify_t3" href="<?= $pro_link ?>">
				<img class="logo venue-image" src="<?=$central->s3_uploaded_images_base_url?>profile-pics/<?= $promoter->up_profile_image ?>_t.jpg" alt="Venue Banner" />
			</a>
	  		<div class="name">
	  			<a class="ajaxify_t3" href="<?= $pro_link ?>">
	  				<?= $promoter->u_full_name ?>
	  			</a>
	  		</div>
            <p class="auth_content" id="user_friends_<?= $promoter->up_id ?>">
            	<?php if(false): ?>
          	  <img style="margin-left:auto; margin-right:auto;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
       	    	<?php endif; ?>
       	    </p>
	  	</li>
	  	<?php endforeach; ?>
  	</ul>
  	
  	<div style="clear:both;"></div>
  	
  </div>
  
  <h2><?= $this->lang->line('v-friend_activity') ?></h2>
  <ul class="updates">
  	
  	<div style="width:100%;text-align:center;">
  		
	  	<div class="unauth_content">
			<p><?= lang_key($this->lang->line('p-login_msg1'), array('team_venue_name' => '')) ?></p>
			<p><a class="fb-connect vc_fb_login" href="javascript: void(0);"><img src="<?= $central->front_assets ?>images/connect-large.png" alt="Facebook Connect" /></a></p>
		</div>
		
		<div id="venue_user_news_feed" class="auth_content">			
			<ul class="auth_clear_content updates" id="news_feed"></ul>
			<img class="loading_indicator" style="margin-left:auto; margin-right:auto;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
		</div>
		
  	</div>
  	
  </ul>
  
  
  
</section>