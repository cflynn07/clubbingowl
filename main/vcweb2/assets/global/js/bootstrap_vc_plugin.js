(function(window){ 
	var iframe;
	
	iframe = document.createElement('iframe');
	
	if(typeof window.vc_plugin_tv_id !== 'undefined')
		iframe.src = 'https://www.clubbingowl.com/plugin/0?tv_id=' + window.vc_plugin_tv_id;
	else
		iframe.src = 'https://www.clubbingowl.com/plugin/' + window.vc_plugin_tfpid;

	iframe.style.width = '100%';
	iframe.style.height = '100%';
	iframe.style.border = '0px';
	
	
	var vc_plugin = document.getElementById('vc_plugin');
	var vc_plug = document.getElementById('vc_plug');
	vc_plug.style.float = 'right';
	vc_plug.style.fontSize = '10px';
	
	
	vc_plugin.style.width = '650px';
	vc_plugin.style.height = '1600px';
	vc_plugin.style.border = '1px solid #CCC';
	vc_plugin.style.marginLeft = 'auto';
	vc_plugin.style.marginRight = 'auto';
	vc_plugin.insertBefore(iframe, vc_plug);
})(window);