<div data-role="page" id="requests" data-title="<?= $title ?>">
    
	<?php $this->load->view('admin/promoters/mobile/navigation/view_header', array('active' => 'Requests')); ?>	

    <div data-role="content">
        
        
        
        
        <div class="content-primary">
        	
        	<ul data-role="listview" data-theme="g" data-count-theme="b">
           		
           		<li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-b" style="padding-bottom:15px;">Pending Reservation Requests</li>
       			    			
       			<?php $no_groups = true; ?>
           		<?php foreach($weekly_guest_lists as $wgl): ?>
           			<?php foreach($wgl->groups as $group): ?>
           				
           				<?php
           					if($group->pglr_approved != '0')
								continue;
           				?>
           				<?php $no_groups = false; ?>
		           		<li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-count ui-btn-up-d" style="position:relative; top:-10px;">
							<a style="margin-top:-26px; margin-bottom:5px;" data-transition="slide" href="<?= $central->promoter_admin_link_base ?>mobile/pglr/<?= $group->id ?>">
								
								<?php if($wgl->pgla_image): ?>
									<div style="width:66px;height:88px;background:#000;display:inline-block;vertical-align:middle;">
										<img style="display:inline-block;vertical-align:middle" src="<?= $central->s3_uploaded_images_base_url . 'guest_lists/' . $wgl->pgla_image . '_t.jpg' ?>" alt="" />
									</div>
								<?php else: ?>
									<div style="width:66px;height:88px;background:#000;display:inline-block;vertical-align:middle;"></div>	
								<?php endif; ?>
									
								<span class="ui-btn-text">
									<div style="display:inline-block;position:relative;top:32px;">
										
										<div style="display:inline-block;vertical-align:middle;" class="pic_square_small_<?= $group->head_user ?>">
											<div style="width:20px;height:20px;display:inline-block;">
												<img src="<?= $central->admin_assets ?>images/mobile_ajax_loader.gif" style="margin-left:auto;margin-right:auto;width:20px;height:20px;display:inline-block;" />
											</div>
										</div>
										
										<span style="color:blue;" class="name_<?= $group->head_user ?>"></span> (+ <?= count($group->entourage_users) ?>) <br>
										
										<span style="text-decoration:underline;"><?= $wgl->pgla_name ?></span><br>
										@ <?= $wgl->tv_name ?><br>
										<span style="color:gray;"><?= date('F j, Y', strtotime(rtrim($wgl->pgla_day, 's'))) ?></span>
									</div>
								</span>
								
							</a>
						</li>	
					
					<?php endforeach; ?>
           		<?php endforeach; ?>
           		
           		<?php if($no_groups): ?>
           			<li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-count ui-btn-up-d">No Pending Requests</li>
           		<?php endif; ?>
           		
			</ul>
        	
			
		</div>
		
		
		
       
    </div>
    
    <?php $this->load->view('admin/promoters/mobile/navigation/view_footer', array('active' => 'Requests')); ?>

<script type="text/javascript">
jQuery(document).unbind('pagechange');

jQuery(document).bind('pagechange', function(){
	
	console.log('pagechange -- requests');
	
	fbEnsureInit(function(){
		
		var users = eval('<?= $users ?>');
		
		if(users.length > 0){
			var fql = "SELECT uid, name, pic_square FROM user WHERE ";
			for(var i = 0; i < users.length; i++){
				if(i == (users.length - 1)){
					fql += "uid = " + users[i];
				}else{
					fql += "uid = " + users[i] + " OR ";
				}
			}
			
			var query = FB.Data.query(fql);
			query.wait(function(rows){
				
				vc_fql_users = rows;
				
				for(var i = 0; i < rows.length; i++){
					
					jQuery('.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="" />');
					jQuery('.pic_square_small_' + rows[i].uid).html('<img style="width:20px;height:20px;" src="' + rows[i].pic_square + '" alt="" />');
					jQuery('.name_' + rows[i].uid).html(rows[i].name);
					
				}
									
			});
		}
		
	});
	
});
</script>

</div><!-- page -->