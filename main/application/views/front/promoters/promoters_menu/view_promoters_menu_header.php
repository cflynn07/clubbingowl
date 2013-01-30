<script type="text/javascript">window.vc_promoter_oauth=<?= $promoter->up_users_oauth_uid ?>;</script>
<style type="text/css">
	.unauth_content{
		display: <?= (isset($vc_user) && $vc_user) ? 'none' : 'block'?>;
	}
	.auth_content{
		display: <?= (isset($vc_user) && $vc_user) ? 'block' : 'none'?>;
	}
</style>

<article class="promoter">

  <header>
    <h1><?= $promoter->u_full_name ?></h1>
    <div class="location"><?= $promoter->team->c_name . ', ' . $promoter->team->c_state ?><br/><p style="font-size:14px; margin:0; float:right;"><?= $promoter->team->t_name ?></p></div>
  </header>

  <div class="left">
    <div class="avatar">
    	<img style="border-radius:10px; border: 1px solid lightgray;" src="<?= $promoter->profile_image_complete_url ?>" alt="<?= $promoter->u_full_name ?>'s Picture">
    </div>
    <div style="text-align:center; padding-top:10px;">
    	
  		<?php if($promoter->up_last_login_time 		> (time() - 60 * 20)): //20 mins ?>
    		<img src="<?= $central->front_assets ?>images/on.png" style="vertical-align:bottom;" /> <span style="color:green;">Online Now</span>
  		<?php elseif($promoter->up_last_login_time 	> (time() - 60 * 60)): //60 mins): ?>
  			<img src="<?= $central->front_assets ?>images/off.png" style="vertical-align:bottom; filter:gray; -webkit-filter:grayscale(1); opacity:0.5;" /> <span>Last Online: This hour</span>
  		<?php elseif($promoter->up_last_login_time 	> (time() - 60 * 60 * 24)): //1 day): ?>
  			<img src="<?= $central->front_assets ?>images/off.png" style="vertical-align:bottom; filter:gray; -webkit-filter:grayscale(1); opacity:0.5;" /> <span>Last Online: Today</span>  			
  		<?php elseif($promoter->up_last_login_time 	> (time() - 60 * 60 * 24 * 7)): //1 week): ?>
  			<img src="<?= $central->front_assets ?>images/off.png" style="vertical-align:bottom; filter:gray; -webkit-filter:grayscale(1); opacity:0.5;" /> <span>Last Online: This week</span>  			
  		<?php else: ?>
  			<img src="<?= $central->front_assets ?>images/off.png" style="vertical-align:bottom; filter:gray; -webkit-filter:grayscale(1); opacity:0.5;" /> <span>Offline</span>  			
  		<?php endif; ?>  
  		
    </div>
   
    <div style="border-top:1px dashed #CCC;border-bottom:1px dashed #CCC;margin-top:15px;" id="add_as_friend">
    	<table style="width:100%;">
    		<tr>
    			<td><img src="<?= $central->front_assets ?>images/fb_add_friend_48x48.png" /></td>
    			<td style="text-align:center;"><p><a href="javascript:void(0);">Add <?= $promoter->u_full_name ?> as a Friend.</a></p></td>
    		</tr>
    	</table>
    </div>
    
	    <div class="rating" data-stars="1">
	      <div class="stars">
	        <div class="stars-on" style="width:0%"></div>
	        <div class="stars-off" style="width:100%;"></div>
	      </div>
	    </div>
	    
	    <a id="reviews_explain" href="#">What's This?</a>
	    <div style="display:none;" id="modal_reviews_explain">
	    	<h2 style="color:#474D6A;">Trusted Promoters</h2>
	    	<p>Did your promoter help you at the door? Answer your questions? And just plain help you have a great night?</p>
	    	<p><strong>Let your friends know!</strong> Join a guest-list, and after your night out, ClubbingOwl will let you review your promoter. Your review will be shared with all your ClubbingOwl friends.</p>
	   		<br/>
	   		<p style="width:100%;text-align:center;">Making nightlife easier with Facebook!</p>
	    </div>
	    
	    
	    <div id="reviews_holder">
		    <p><?= lang_key($this->lang->line('p-ranking_msg'), array('promoter_full_name' => $promoter->u_full_name)) ?></p>
		  
		  	<div class="unauth_content">
		  		<a class="fb-connect vc_fb_login" href="javascript: void(0);"><img src="<?= $central->front_assets ?>images/connect-large.png" alt="Facebook Connect" /></a>
		  	</div>
		  
		  	
		  	<div class="auth_content" id="pro_user_reviews">
		  	
		  		<img style="margin-left:auto; margin-right:auto;" src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
		  		<div id="up_public_identifier" style="display:none;"><?= $promoter->up_public_identifier ?></div>
		  		<div id="up_first_name" style="display:none;"><?= $promoter->u_first_name ?></div>
		  		
		  	</div>
  		</div>
  	<?= '<script type="text/javascript">window.u_up_pop=' . ((isset($u_up_pop)) ? json_encode($u_up_pop) : 'false') . ';</script>' ?>
  	
  	
  </div>

  <div class="right non_fb_plugin">
    
    <div class="tab-container">
    	      
      <div class="tab-content">