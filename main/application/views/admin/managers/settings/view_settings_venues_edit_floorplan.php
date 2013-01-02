<?php
	$page_obj = new stdClass;
?>
<script type="text/javascript">window.page_obj=<?= json_encode($page_obj) ?>;</script>





<div id="admin_manager_settings_venues_edit_floorplan_wrapper">
	<div id="table_add_dialog" style="display:none;">
	
		<form>
			<fieldset>
				
				
				<?php if(false): ?>
					<p style="font-weight:bold;">Table Image:</p>
					<a href="<?= $central->manager_admin_link_base ?>manage_image/">
						<?php if(isset($manage_image->image_data)): ?>
							<img src="<?= $central->s3_uploaded_images_base_url . $manage_image->type . '/originals/temp/' . $manage_image->image_data->image . '_t.jpg'?>" alt="upload image" style="width:66px; height:88px;" />
						<?php else: ?>
							<img src="<?= $central->admin_assets ?>images/upload_image.png" alt="upload image" style="width:66px; height:88px;" />
						<?php endif; ?>	
					</a>
					
					<hr>
				<?php endif; ?>
				
				<p style="font-weight:400; margin-bottom:0;">Table Name:</p>
				<input class="sf" name="title" type="text" value="">
				
				<hr/>
				
				
				<p style="font-weight:400; margin-bottom:0;">Default Minimum Spend per Night:</p>
				
				
				<?php if(false): ?>
				<span class="note">Note: You can override at any time this for specific nights.</span>
				<?php endif; ?>
				
				
				
				
				
				<table>
					<tbody>
						<tr>
							<td><label>Monday: </label></td>
							<td><input class="day_price" name="monday" type="text" value=""></td>
						</tr>
						<tr>
							<td><label>Tuesday: </label></td>
							<td><input class="day_price" name="tuesday" type="text" value=""></td>
						</tr>
						<tr>
							<td><label>Wednesday: </label></td>
							<td><input class="day_price" name="wednesday" type="text" value=""></td>
						</tr>
						<tr>
							<td><label>Thursday: </label></td>
							<td><input class="day_price" name="thursday" type="text" value=""></td>
						</tr>
						<tr>
							<td><label>Friday: </label></td>
							<td><input class="day_price" name="friday" type="text" value=""></td>
						</tr>
						<tr>
							<td><label>Saturday: </label></td>
							<td><input class="day_price" name="saturday" type="text" value=""></td>
						</tr>
						<tr>
							<td><label>Sunday: </label></td>
							<td><input class="day_price" name="sunday" type="text" value=""></td>
						</tr>
					</tbody>
				</table>
							
							
							
				<?php if(false): ?>
							
				<div>
					<label>Monday: </label>
					<input class="day_price" name="monday" type="text" value="">
					<div style="clear:both;"></div>
				</div>
				<div>
					<label>Tuesday: </label>
					<input class="day_price" name="tuesday" type="text" value="">
					<div style="clear:both;"></div>
				</div>
				<div>
					<label>Wednesday: </label>
					<input class="day_price" name="wednesday" type="text" value="">
					<div style="clear:both;"></div>
				</div>
				<div>
					<label>Thursday: </label>
					<input class="day_price" name="thursday" type="text" value="">
					<div style="clear:both;"></div>
				</div>
				<div>
					<label>Friday: </label>
					<input class="day_price" name="friday" type="text" value="">
					<div style="clear:both;"></div>
				</div>
				<div>
					<label>Saturday: </label>
					<input class="day_price" name="saturday" type="text" value="">
					<div style="clear:both;"></div>
				</div>
				<div>
					<label>Sunday: </label>
					<input class="day_price" name="sunday" type="text" value="">
					<div style="clear:both;"></div>
				</div>
				
				<?php endif; ?>
				
				<hr>
				
				
				<p style="font-weight:400; margin-bottom:0;">Maximum Seating Capacity</p>
				<div>
					<label>Capacity: </label>
					<input class="max_capacity" type="text" value="" />
					<div style="clear:both;"></div>
				</div>
				
				
				
			</fieldset>
		</form>	
		
		<p class="error" style="min-height:50px;"></p>
		
	</div>
	
	<div id="no_delete_floor_dialog" style="display:none;">
		<p>Can't delete floor: Venue must have at least one floor.</p>
	</div>
	
	<div id="delete_floor_dialog" style="display:none;">
		<p>Are you sure you want to delete this floor?</p>
	</div>
	
	<div id="add_floor_dialog" style="display:none;">
		
		<form>
			<fieldset>
				<label>Floor Title:</label>
				<input class="sf" type="text" name="floor_title" value="">
			</fieldset>
		</form>
		
	</div>
	
	
	<h1>Edit <?= $venue_name ?> layout</h1>
	<p>
		Create a virtual layout of your venue to organize table assignments and share table availablity with your promoters
	</p>
	
	
	
	<p>
		<table>
			<tr>
				<td><input id="submit_floorplan" class="button" type="submit" value="Save Floorplan" /></td>
				<td><div style="width:20px;"></div></td>
				<td>
					<img id="ajax_loading" src="<?=$central->global_assets?>images/ajax.gif" alt="loading" style="display:none"/>
					<img id="ajax_complete" src="<?=$central->admin_assets?>images/icons/notifications/success.png" alt="complete" style="display:none"/>
				</td>
			</tr>
		</table>
	</p> 
	
	
			
	<div class="ui-widget ui-widget-content ui-corner-all" style="width:810px;">
	
	
	
		<div style="width: 810px;">
			
			<div class="items">
			
				<div class="item dancefloor pre_drop">
					
					<span class="title">(D)</span>
					<br>
					<span class="full_title">Dance Floor</span>
					
					<div class="tooltip">
					</div>
					
					<div class="del">X</div>
				</div>
				
				<div style="width:10px; display:inline-block;"></div>
				
				<div class="item stage pre_drop">
					
					<span class="title">(S)</span>
					<br>
					<span class="full_title">Stage</span>
					
					<div class="tooltip">
					</div>
					
					<div class="del">X</div>
				</div>
				
				<div style="width:10px; display:inline-block;"></div>
				
				<div class="item bar pre_drop">
					
					<span class="title">(B)</span>
					<br>
					<span class="full_title">Bar</span>
					
					<div class="tooltip">
					</div>
					
					<div class="del">X</div>
				</div>
				
				<div style="width:10px; display:inline-block;"></div>
				
				<div class="item table pre_drop">
					
					<span class="title">(T)</span>
					<br>
					<span class="full_title">Table</span>
					
					<div class="tooltip">
					</div>
					
					<div class="del">X</div>
				</div>
				
				<div style="width:10px; display:inline-block;"></div>
							
				<div class="item djbooth pre_drop">
					
					<span class="title">(DJ)</span>
					<br>
					<span class="full_title">DJ</span>
					
					<div class="tooltip">
					</div>
					
					<div class="del">X</div>
				</div>
				
				<div style="width:10px; display:inline-block;"></div>
				
				<div class="item bathroom pre_drop">
					
					<span class="title">(Br)</span>
					<br>
					<span class="full_title">Bathroom</span>
					
					<div class="tooltip">
					</div>
					
					<div class="del">X</div>
				</div>
				
				<div style="width:10px; display:inline-block;"></div>
				
				<div class="item entrance pre_drop">
					
					<span class="title">(E)</span>
					<br>
					<span class="full_title">Entrance</span>
					
					<div class="tooltip">
					</div>
					
					<div class="del">X</div>
				</div>
				
				<div style="width:10px; display:inline-block;"></div>
				
				<div class="item stairs pre_drop">
					
					<span class="title">(St)</span>
					<br>
					<span class="full_title">Stairs</span>
					
					<div class="tooltip">
					</div>
					
					<div class="del">X</div>
				</div>
				
			</div>
			
		</div>
	
	
	
	
	
	
	
		<div id="tabs" class="ui-widget ui-widget-content ui-corner-all" style="width:810px; border:0px; box-shadow:none; margin-bottom:0;">
			
			<div class="ui-widget-header">
				
				<span>Venue Floorplan</span>
				
				<div class="div_venue_floor_select" style="display: inline-block; float: right;">
					
					<span class="add_floor">Add Floor</span>
					 | 
					<span class="del_floor">Delete Floor</span>
					
					<div style="display:inline-block; width:20px;"></div>
					
					Select Floor: 
					<select class="venue_floor_select" style="height:20px; padding:0px; margin:0px; margin-right:-10px;">
						<?php if(count((array)$venue_floorplan) === 0): ?>
							
							<option value="0">Floor 0</option>
							
						<?php else: ?>
							
							<?php $count = 0; ?>
							<?php foreach($venue_floorplan as $key => $vf): ?>
								<option value="<?= $count ?>">
									<?= (strlen($vf->name) === 0) ? 'Floor ' . $count : $vf->name ?>
									<?php $count++; ?>
								</option>
							<?php endforeach; ?>
							
						<?php endif; ?>					
					</select>
								
				</div>
				
				<ul style="display:none;">
					<?php if(count((array)$venue_floorplan) === 0): ?>
						
						<li><a href="#tabs-0">Floor 0</a></li>
						
					<?php else: ?>
						
						<?php $count = 0; ?>
						<?php foreach($venue_floorplan as $key => $vf): ?>
							<li><a href="#tabs-<?= $count ?>"><?= (strlen($vf->name) === 0) ? 'Floor ' . $count : $vf->name ?></a></li>
							<?php $count++; ?>
						<?php endforeach; ?>
					
					<?php endif; ?>	
				</ul>
				
				<div style="clear: both"></div>
				
			</div>
			
			<?php if(count((array)$venue_floorplan) === 0): ?>
			<div id="tabs-0" class="layout_tabs">
				<div class="ui-widget-content">
							
					<div class="full_width last">
						
						<div class="venue_floor"></div>
						
					</div>
					
					<div style="clear: both"></div>
				</div>
			</div>
			<?php else: ?>
				
				<?php $count = 0; ?>
				<?php foreach($venue_floorplan as $key => $vf): ?>

				<div id="tabs-<?= $count ?>" class="layout_tabs">
					<div class="ui-widget-content">
								
						<div class="full_width last">
							
							<div class="venue_floor">
								
								
								<div style="display:none;" class="vlf_id"><?= $key ?></div>
								<div class="vlf_title"><?= $vf->name ?></div>
								
								
								<?php $table_count = 0; ?>
								<?php foreach($vf->items as $item): ?>
									<div class="item <?= $item->vlfi_item_type ?>" style="position:absolute; top:<?= $item->vlfi_pos_y ?>px; left:<?= $item->vlfi_pos_x ?>px; width:<?= $item->vlfi_width ?>px; height:<?= $item->vlfi_height ?>px;">
										<?php if($item->vlfi_item_type == 'table'): ?>
											
											
											<span style="color:lightblue; text-decoration:underline;" class="title"><?php if(isset($item->vlfit_title) && $item->vlfit_title): ?><?= $item->vlfit_title ?><?php else: ?>T-<?= $table_count ?><?php endif; ?></span>
												
												
											<div class="day_price monday">US$ <?= number_format($item->vlfit_monday_min, 0, '', ',') ?></div>
											<div class="day_price tuesday">US$ <?= number_format($item->vlfit_tuesday_min, 0, '', ',') ?></div>
											<div class="day_price wednesday">US$ <?= number_format($item->vlfit_wednesday_min, 0, '', ',') ?></div>
											<div class="day_price thursday">US$ <?= number_format($item->vlfit_thursday_min, 0, '', ',') ?></div>
											<div class="day_price friday">US$ <?= number_format($item->vlfit_friday_min, 0, '', ',') ?></div>
											<div class="day_price saturday">US$ <?= number_format($item->vlfit_saturday_min, 0, '', ',') ?></div>
											<div class="day_price sunday">US$ <?= number_format($item->vlfit_sunday_min, 0, '', ',') ?></div>
											<div class="max_capacity"><?= $item->vlfit_capacity ?></div>
											
											<?php $table_count++; ?>
											
										<?php elseif($item->vlfi_item_type == 'bar'): ?>
											<span class="title">(B)</span>
										<?php elseif($item->vlfi_item_type == 'stage'): ?>
											<span class="title">(S)</span>
										<?php elseif($item->vlfi_item_type == 'dancefloor'): ?>
											<span class="title">(D)</span>
										<?php elseif($item->vlfi_item_type == 'djbooth'): ?>
											<span class="title">(DJ)</span>
										<?php elseif($item->vlfi_item_type == 'bathroom'): ?>
											<span class="title">(Br)</span>
										<?php elseif($item->vlfi_item_type == 'entrance'): ?>
											<span class="title">(E)</span>
										<?php elseif($item->vlfi_item_type == 'stairs'): ?>
											<span class="title">(St)</span>
										<?php endif; ?>
										
										<div class="tooltip">
										</div>
										
										<div class="del">X</div>
										<div style="display:none;" class="vlfi_id"><?= $item->vlfi_id ?></div>
									</div>
								<?php endforeach; ?>
								<?php unset($table_count); ?>
								
							</div>
							
						</div>
						
						<div style="clear: both"></div>
					</div>
				</div>
					
					<?php $count++; ?>
				<?php endforeach; ?>
	
			<?php endif; ?>
				
		</div>
		
	
	</div>
	
</div>