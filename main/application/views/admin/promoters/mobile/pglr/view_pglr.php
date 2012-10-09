<div data-role="page" id="pglr" data-title="<?= $title ?>" data-add-back-btn="true">
    
   	<?php $this->load->view('admin/promoters/mobile/navigation/view_header', array('active' => 'Guest Lists')); ?>	

    <div data-role="content">
        
        <div class="content-primary">
           	
           	<?php if(false): ?>
           	<p>This is a guest list request</p>
           	<pre><?= var_dump($pglr) ?></pre>
           	<pre><?= var_dump($users) ?></pre>
           	<?php endif; ?>
           	
           	<div class="ui-body ui-body-b">
           		
       			<div style="display:inline-block;" class="pic_square_<?= $pglr->pglr_user_oauth_uid ?>">
       				<div style="width:50px;height:50px;vertical-align:middle;text-align:center;">
						<img src="<?= $central->admin_assets ?>images/mobile_ajax_loader.gif" style="margin-left:auto;margin-right:auto;" />
					</div>
       			</div>
       			
       			<h5 style="margin-top:0px;">
       				<span class="name_<?= $pglr->pglr_user_oauth_uid ?>"></span>'s Reservation Request<br>
       				<span style="color:red;"><?= $pglr->pgla_name ?></span> @ <span style="font-weight:bold"><?= $pglr->tv_name ?></span><br>
       				<span style="color:gray;"><?= date('l F j, Y', strtotime(rtrim($pglr->pgla_day, 's'))) ?></span>
       			</h5>
           		
           		<hr>
           		
           		<ul data-role="listview" data-theme="g" data-count-theme="b" data-inset="true">
           			<li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-b"><span style="text-decoration:underline;">Request Message</li>
           				<li><?= $pglr->pglr_request_msg ?></li>
           			<li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-b"><span style="text-decoration:underline;">Response Message</li>
           				<li><?= $pglr->pglr_response_msg ?></li>
           			<li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-b"><span style="text-decoration:underline;">Host Notes</li>
           				<li><?= $pglr->pglr_host_message ?></li>
           		</ul>
           		<style type="text/css">
           			li.ui-li{
           				font-size: 10px;
           			}
           		</style>
           		
           		<hr>
           		
           		<h5>Entourage</h5>
           		
           		<?php if(!$pglr->entourage): ?>
           		<p style="color:gray;">No Entourage</p>
           		<?php endif; ?>
           		
           		<?php foreach($pglr->entourage as $ent_user): ?>
           			
           			<div style="display:inline-block" class="pic_square_<?= $ent_user ?>">
           				<div style="width:50px;height:50px;vertical-align:middle;text-align:center;">
							<img src="<?= $central->admin_assets ?>images/mobile_ajax_loader.gif" style="margin-left:auto;margin-right:auto;" />
						</div>
           			</div>
           			<span class="name_<?= $ent_user ?>"></span>
           			<br>
           			
           		<?php endforeach; ?>
           		
           		<hr>
           		
           		<h5>Actions</h5>
           		
           		<?php if($pglr->pglr_approved == '0'): ?>
           		
           			<a href="#" data-role="button" data-theme="a" style="background:green;">Approve</a>
           			<a href="#" data-role="button" data-theme="a" style="background:red;">Decline</a>
           		
           		<?php elseif($pglr->pglr_approved == '1'): ?>
           		
           			<p style="color:green">Approved</p>
           		
           		<?php else: ?>
           		
           			<p style="color:red">Declined</p>
           		
           		<?php endif; ?>
           		
           	</div>
           	
		</div>
       
    </div>
    
    <?php $this->load->view('admin/promoters/mobile/navigation/view_footer', array('active' => 'Guest Lists')); ?>
    
<script type="text/javascript">
jQuery(document).unbind('pageinit');
jQuery(document).unbind('pagechange');

jQuery(document).bind('pageinit', function(){
	
	console.log('pageinit -- pglr');
	
	fbEnsureInit(function(){
		
		console.log('this happened');
		
		var users = eval('<?= $users ?>');
		
		console.log(users);
		
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
				
				console.log(rows);
				
				for(var i = 0; i < rows.length; i++){
					
					jQuery('.pic_square_' + rows[i].uid).html('<img src="' + rows[i].pic_square + '" alt="" />');
					jQuery('.pic_square_ent_' + rows[i].uid).html('<img style="width:20px;height:20px;" src="' + rows[i].pic_square + '" alt="" />');
					jQuery('.name_' + rows[i].uid).html(rows[i].name);
					
				}
									
			});
		}
		
	});
	
});
</script>
    
</div><!-- page -->