<div class="ui3">
	
	<h3>Choose from your Facebook friends & Clubbing Owl clients</h3>
	
	
	<table class="normal" style="display:none;" id="reservations_holder">
		<thead>
			<tr>
				<td colspan="2">
					<p style="margin:0;">Guest List Head</p>
				</td>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	
	<table class="normal" style="display:none;" id="reservations_holder_entourage">
		<thead>
			<tr>
				<td colspan="3">
					<p style="margin:0;"><span data-role="head_user_name"></span>'s Entourage</p>
				</td>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	
	
	
	
	<div style="background:#CCC; border-radius:5px; padding:5px;" id="add_flow">
		
		<p id="head_user_message" style="margin-bottom:0;">Add a client to your guest list.</p>
		<p id="ent_user_message" style="margin-bottom:0;display:none;">Add a client to <span data-role="head_user_name"></span>'s <strong style="text-decoration:underline;">entourage</strong>. (optional)</p>
		
		<table style="margin-top:10px; margin-left:auto; margin-right:auto;">
			<tr>
				<td>
					<img id="selected_pic" style="display:none;" src="" alt="" />
				</td>
				<td style="vertical-align:top; padding-left:10px;">
					<input placeholder="Start typing a name..." type="text" class="sf" />
				</td>
				<td style="vertical-align:top; padding-top:8px;">
					<a href="#" data-action="gl-flow-add-head" class="button_link btn-action">Add</a>
				</td>
			</tr>
		</table>
		
		<p style="color:red; background:#000; text-align:center;" id="message"></p>
		
	</div>
	
	<br/>
	
	<a href="#" style="display:none; float:right;" data-action="gl-flow-add-final" class="button_link btn-action">Create Reservation</a>	
	
	
	<style>
    .ui-autocomplete {
        max-height: 400px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }
    /* IE 6 doesn't support max-height
     * we use height instead, but this forces the menu to always be this tall
     */
    * html .ui-autocomplete {
        height: 400px;
    }
    
    .ui-autocomplete li table td {
    	vertical-align: middle;
    	padding-right: 5px;
    }
    </style>
		
</div>

