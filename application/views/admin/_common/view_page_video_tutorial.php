<style type="text/css">
	
.page_video_tutorial{
	cursor: pointer;
}
.page_video_tutorial img{
	display: inline-block;
	vertical-align: top;
	margin-top: 3px;
}
.page_video_tutorial span{
	color: blue;
	text-decoration: underline;
}
div.ui-dialog-titlebar{
	cursor: pointer;
}

</style>

<div id="video_dialog" style="display:none; background:#000; overflow:hidden; text-align:center;"></div>
<?php if(false): ?>
<script type="text/javascript">
jQuery(function(){
	jQuery('.page_video_tutorial').bind('click', function(){
		
		jQuery('div#video_dialog').html('<iframe src="http://player.vimeo.com/video/40136171?title=0&amp;byline=0&amp;portrait=0&amp;color=ff9933&amp;autoplay=1" width="601" height="338" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>');
		
		jQuery('div#video_dialog').dialog({
			title: 'Video Tutorial',
			width: 640,
			height: 400,
			modal: true,
			position: 'center',
			resizable: false,
			close: function(){
				jQuery('div#video_dialog').empty();
			}
		});
	});
});
</script>
<?php endif; ?>