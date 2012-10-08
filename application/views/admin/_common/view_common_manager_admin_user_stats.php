<script type="text/javascript">
<?php if(false): ?>
jQuery(function(){
	
	jQuery('a.vc_name').live('click', function(){
			
		var dialog_user_stats = jQuery('div#dialog_user_stats');
		
		dialog_user_stats.find('div.user_stats_content').css('display', 'none');
		dialog_user_stats.find('div.loading_indicator').css('display', 'block');
		
		var uid = jQuery(this).find('span.uid').html();
		
		jQuery('div#dialog_user_stats').dialog({
			height: 700,
			width: 900,
			modal: true,
			resizable: false,
			draggable: false,
			position: ['center', 'center'],
			title: 'Loading...'
		});
		
		fbEnsureInit(function(){
			
			var fql = 'SELECT uid, name, first_name, last_name, pic_square, pic_big, sex FROM user WHERE uid = ' + uid;
			
			console.log(fql);
			
			var query = FB.Data.query(fql);
			query.wait(function(rows){
				
				if(rows.length == 0){
					alert('Error opening user statistics, Facebook returned no data');
					jQuery('div#dialog_user_stats').dialog('close');
					return;
				}
				
				var user = rows[0];
				
				jQuery('div#dialog_user_stats').dialog({
					title: 'Client "' + user.name + '" statistics'
				});
				
				dialog_user_stats.find('img.pic').attr('src', user.pic_big);
				dialog_user_stats.find('p.name').html(user.name);
				
				dialog_user_stats.find('div.user_stats_content').css('display', 'block');
				dialog_user_stats.find('div.loading_indicator').css('display', 'none');
				
			});
			
		});
		
		
		
		
		
		
		
		//cross-site request forgery token, accessed from session cookie
		//requires jQuery cookie plugin
		var cct = jQuery.cookies.get('ci_csrf_token') || 'no_csrf';
		
		jQuery.ajax({
			url: window.location,
			type: 'post',
			data: {
				ci_csrf_token: cct,
				vc_method: 'user_stats_retrieve',
				vc_user: uid
			},
			cache: false,
			dataType: 'json',
			success: function(data, textStatus, jqXHR){
				
				console.log(data);
				
			},
			failure: function(){
				console.log('failure');
			}
		});
		
		
		
		
		
				
		return false;
					
	});

});
<?php endif; ?>

</script>

<style type="text/css">

div.ui-widget-overlay{
	position: fixed;
	z-index: 1000;
	background: rgba(0, 0, 0, 0.5);
}
p.name{
	font-weight: bold;
}
</style>

<div id="dialog_user_stats" style="display: none;">
	
	<div class="loading_indicator">
		<img src="<?=$central->global_assets . 'images/ajax.gif'?>" alt="loading..." />
	</div>
	
	<div class="user_stats_content" style="display:none;">
		
		<div class="ui-widget">
			<div class="one_fourth">
				<p class="name"></p>
				<img class="pic" src="" alt="" />
			</div>
			<div class="three_fourth last">
				
				<table class="normal">
					<thead>
						<tr>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						
						<tr>
							<td>Client Since:</td>
							<td></td>
						</tr>
						<tr class="odd">
							<td>Requests:</td>
							<td></td>
						</tr>
						<tr>
							<td>Approved Requests:</td>
							<td>17</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
				
			</div>
			<div style="clear:both;"></div>
		</div>
		
	</div>

</div>