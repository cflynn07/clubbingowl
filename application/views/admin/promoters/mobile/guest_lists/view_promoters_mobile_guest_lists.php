<div data-role="page" id="guest_lists" data-title="<?= $title ?>">

	<?php $this->load->view('admin/promoters/mobile/navigation/view_header', array('active' => 'Guest Lists')); ?>	

    <div data-role="content">
        
        <div class="content-primary">
           	
           	<ul data-role="listview" data-theme="g" data-count-theme="b">
           		
           		<?php if(!$weekly_guest_lists): ?>
           			<li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-count ui-btn-up-d">No Guest Lists</li>
           		<?php endif; ?>
           		
           		<?php $weekdays = array('mondays', 'tuesdays', 'wednesdays', 'thursdays', 'fridays', 'saturdays', 'sundays'); ?>
           		<?php foreach($weekdays as $weekday): ?>
           			
           			<?php $weekday_displayed = false; ?>
           			
	           		<?php foreach($weekly_guest_lists as $wgl): ?>
	           			
	           			<?php if($wgl->pgla_day == $weekday): ?>
	           				
	           				<?php if(!$weekday_displayed): 
	           						$weekday_displayed = true;
	           				?>
			           		<li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-b"><?= ucfirst($weekday) ?></li>
			           		<?php endif; ?>
			           		
			           		<li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-count ui-btn-up-d">
								<a data-transition="slide" href="<?= $central->promoter_admin_link_base ?>mobile/guest_lists/<?= $wgl->pgla_id ?>">
									
									<?php if($wgl->pgla_image): ?>
										<div style="width:66px;height:88px;background:#000;display:inline-block;vertical-align:middle;">
											<img style="display:inline-block;vertical-align:middle" src="<?= $central->s3_uploaded_images_base_url . 'guest_lists/' . $wgl->pgla_image . '_t.jpg' ?>" alt="" />
										</div>
									<?php else: ?>
										<div style="width:66px;height:88px;background:#000;display:inline-block;vertical-align:middle;"></div>	
									<?php endif; ?>
										
									<span class="ui-btn-text">
										<div style="display:inline-block;position:relative;top:8px;">
											<span style="text-decoration:underline;"><?= $wgl->pgla_name ?></span><br>
											@ <?= $wgl->tv_name ?><br>
											<span style="color:gray;"><?= date('F j, Y', strtotime(rtrim($wgl->pgla_day, 's'))) ?></span>
										</div>
									</span>
									
									<span class="ui-li-count ui-btn-up-c ui-btn-corner-all"><?= count($wgl->groups) ?></span>
									
								</a>
							</li>	
						<?php endif; ?>
						
	           		<?php endforeach; ?>
	           		
           		<?php endforeach; ?>
			</ul>
           	
		</div>
       
    </div>
    
   	<?php $this->load->view('admin/promoters/mobile/navigation/view_footer', array('active' => 'Guest Lists')); ?>	
    
<script type="text/javascript">
jQuery(document).unbind('pageinit');
jQuery(document).unbind('pagechange');
jQuery(document).bind('pageinit', function(){
	
	console.log('pagechange -- guest lists');
	
});
</script>
    
</div>