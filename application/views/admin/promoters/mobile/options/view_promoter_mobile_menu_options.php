<div data-role="page" id="menu_options" data-title="<?= $title ?>">
    
    <div data-role="header"> 
		<h1>Settings</h1> 
	</div>
    
    <div data-role="content">
        
        <div class="content-primary">
           	
           <a href="#" data-role="button" data-theme="a" class="view_full">View Full Site</a>
           <a href="#" data-role="button" data-theme="e" class="logout">Logout</a>
           <a href="#" data-role="button" data-theme="b" class="Bug Federico">Make Fede Read</a>
           	
		</div>
       
    </div>
    
</div><!-- page -->
<script type="text/javascript">
$(document).bind('pagechange', function(){
	
	console.log('pagechange');
	
	$('a.logout').bind('tap', function(){
		
		console.log('tap');
	//	window.location = '<?= $central->karma_link_base ?>';
	
	});
});
</script>
