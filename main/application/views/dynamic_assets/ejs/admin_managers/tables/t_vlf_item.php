<% if(vlfi_item_type == 'table'){ %>
	
	<% if(typeof vlfit_title !== 'undefined' && vlfit_title){ %>										
		<span style="margin:0; padding:0;" class="title"><%= vlfit_title %></span>
	<% }else{ %>
		<span style="margin:0; padding:0;" class="title">T</span>
	<% } %>
	<br/>
	
	<%
		var day_price = 0;
	 	day = parseInt(day);
	 	if(day === 0){
	 		day_price = vlfit_sunday_min;
	 	}else if(day == 1){
	 		day_price = vlfit_monday_min;
	 	}else if(day == 2){
	 		day_price = vlfit_tuesday_min;
	 	}else if(day == 3){
	 		day_price = vlfit_wednesday_min;
	 	}else if(day == 4){
	 		day_price = vlfit_thursday_min;
	 	}else if(day == 5){
	 		day_price = vlfit_friday_min;
	 	}else if(day == 6){
	 		day_price = vlfit_saturday_min;
	 	}
	%>
	
	<span style="margin:0; padding:0; top:-14px; color:green; position:relative;" data-day="" data-day_price="" >$<%= day_price %></span>

			
	<?php if(false): ?>
	<div class="day_price monday">US$ <?= number_format($item->vlfit_monday_min, 		0, '', ',') ?></div>
	<div class="day_price tuesday">US$ <?= number_format($item->vlfit_tuesday_min, 		0, '', ',') ?></div>
	<div class="day_price wednesday">US$ <?= number_format($item->vlfit_wednesday_min, 	0, '', ',') ?></div>
	<div class="day_price thursday">US$ <?= number_format($item->vlfit_thursday_min, 	0, '', ',') ?></div>
	<div class="day_price friday">US$ <?= number_format($item->vlfit_friday_min, 		0, '', ',') ?></div>
	<div class="day_price saturday">US$ <?= number_format($item->vlfit_saturday_min, 	0, '', ',') ?></div>
	<div class="day_price sunday">US$ <?= number_format($item->vlfit_sunday_min, 		0, '', ',') ?></div>
	<div class="max_capacity"><?= $item->vlfit_capacity ?></div>
	<?php endif; ?>
	
		
<% }else if(vlfi_item_type == 'bar'){ %>
	
	<span class="title">(B)</span>
	
<% }else if(vlfi_item_type == 'stage'){ %>
	
	<span class="title">(S)</span>
	
<% }else if(vlfi_item_type == 'dancefloor'){ %>
	
	<span class="title">(D)</span>
	
<% }else if(vlfi_item_type == 'djbooth'){ %>
	
	<span class="title">(DJ)</span>
	
<% }else if(vlfi_item_type == 'bathroom'){ %>
	
	<span class="title">(Br)</span>
	
<% }else if(vlfi_item_type == 'entrance'){ %>
	
	<span class="title">(E)</span>
	
<% }else if(vlfi_item_type == 'stairs'){ %>
	
	<span class="title">(St)</span>
	
<% } %>

<?php if(false): ?>
<div class="vlfi_id" style="display:none;"><?= $item->vlfi_id ?></div>
<div class="vlfi_id_<?= $item->vlfi_id ?>" style="display:none;"><?= $item->vlfi_id ?></div>
<div class="pos_x" style="display:none;"><?= $item->vlfi_pos_x ?></div>
<div class="pos_y" style="display:none;"><?= $item->vlfi_pos_y ?></div>
<div class="width" style="display:none;"><?= $item->vlfi_width ?></div>
<div class="height" style="display:none;"><?= $item->vlfi_height ?></div>
<div class="itmCls" style="display:none;"><?= $item->vlfi_item_type ?></div>
<?php endif; ?>