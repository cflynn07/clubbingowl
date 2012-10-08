<div data-role="page" id="guest_lists" data-title="<?= $title ?>" data-add-back-btn="true">

	<?php $this->load->view('admin/promoters/mobile/navigation/view_header', array('active' => 'Guest Lists')); ?>	

    <div data-role="content">
        
        <div class="content-primary">
           	           
           <ul data-role="listview" data-theme="g" data-count-theme="b">
           	
           		<li data-role="list-divider" role="heading" class="ui-li ui-li-divider ui-bar-b"><span style="text-decoration:underline;"><?= $guest_list->pgla_name ?></span> - <?= date('F j, Y', strtotime(rtrim($guest_list->pgla_day, 's'))) ?></li>
           		
           		<?php if(!$guest_list->groups): ?>
           			<li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-count ui-btn-up-d">No Reservation Requests</li>
           		<?php endif; ?>
           		
           		<?php foreach($guest_list->groups as $group): ?>
           			
           			<li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-li-has-count ui-btn-up-d">
						<a data-transition="slide" href="<?= $central->promoter_admin_link_base ?>mobile/pglr/<?= $group->id ?>">
																			
							<div style="display:inline-block;position:relative;top:0px;">
								
								<div style="display:inline-block;vertical-align:top;" class="pic_square_<?= $group->head_user ?>">
									<div style="width:50px;height:50px;vertical-align:middle;text-align:center;">
										<img src="<?= $central->admin_assets ?>images/mobile_ajax_loader.gif" style="margin-left:auto;margin-right:auto;" />
									</div>
								</div>
								<div style="display:inline-block;position:relative;">
									
									<span class="name_<?= $group->head_user ?> ui-btn-text"></span>
									
									<br>
									
									Status: 
									<?php if($group->pglr_approved == '0'): ?>
									<span class="ui-btn-text" style="color:black;">Requested</span>
									<?php elseif($group->pglr_approved == '1'): ?>
									<span class="ui-btn-text" style="color:green;">Approved</span>
									<?php else: ?>
									<span class="ui-btn-text" style="color:red;">Declined</span>
									<?php endif; ?>
									
									
									<?php if($group->pglr_table_request == '1'): ?>
									<span class="ui-btn-text" style="color:gray;">Table</span>
									<?php endif; ?>
									
									<br>
									
									<span class="ui-btn-text">Entourage (<?= count($group->entourage_users) ?>):</span><br>
									
									<div>
										<?php foreach($group->entourage_users as $ent_user): ?>
										<div style="display:inline-block;vertical-align:top;width:20px;height:20px;" class="pic_square_ent_<?= $ent_user ?>"></div>
										<?php endforeach; ?>
									</div>
								
								</div>
								
							</div>
														
						</a>
					</li>
           			
           		<?php endforeach; ?>
           		
			</ul>
           
           <style type="text/css">
           ul li .ui-btn-text{
           		font-size: 12px;
           }
           </style>
           
		</div>
       
    </div>
    
   	<?php $this->load->view('admin/promoters/mobile/navigation/view_footer', array('active' => 'Guest Lists')); ?>	

<script type="text/javascript">
jQuery(document).unbind('pageinit');
jQuery(document).unbind('pagechange');

jQuery(document).bind('pageinit', function(){
	
	console.log('pagechange -- requests_individual');
	
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
					jQuery('.pic_square_ent_' + rows[i].uid).html('<img style="width:20px;height:20px;" src="' + rows[i].pic_square + '" alt="" />');
					jQuery('.name_' + rows[i].uid).html(rows[i].name);
					
				}
									
			});
		}
		
	});
	
});
</script>
    
</div>
