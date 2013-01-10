<?php
	$page_obj = $data;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>


<?php Kint::dump($data); ?>

<div class="ui-widget" id="admin_manager_settings_checkin_categories_wrapper">
	
	<div class="ui-widget-header">
		<span>Checkin Categories</span>
	</div>
	
	<div style="padding:5px;" class="ui-widget-content">
		<p>Create 'catagories' with associated prices and details for your team hosts to check in clients with. Ex: 'Comped', 'Reduced', etc</p>
		
		<a data-action="add" class="button_link" href="">Add Category</a>
		<br/><br/>	
		
		<table id="categories" class="normal" style="width:100%;">
			<thead>
				<tr>
					<th>Category</th>
					<th>$ Value</th>
					<th>Description</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="4">No categories yet</td>
				</tr>
			</tbody>
		</table>
	
		
	</div>

</div>
<div style="height: 40px;" class="clearboth"></div> 