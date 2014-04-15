(function() {
	tinymce.create('tinymce.plugins.SNPanelShortcode', {
		init : function(ed, url) {			
			ed.ajaxurl = ajaxurl;
			ed.addCommand('mceSNPanelShortcode', function() {
				ed.windowManager.open({
					file : url + '/dialog.htm',
					width : 700 + parseInt(ed.getLang('highlight.delta_width', 0)),
					height : 550 + parseInt(ed.getLang('highlight.delta_height', 0)),
					inline : 1,
					title : 'Slider Notification Panel Shortcode',
					resizable: true
				}, {
					plugin_url : url
				});
			});

			// Register Slider Notification Panel Shortcode button
			ed.addButton('snpanel_shortcode', {
				title : 'Slider Notification Panel Shortcode',
				cmd : 'mceSNPanelShortcode',
				image : url + '/img/icon-20.png'
			});
		},
		getInfo : function() {
			return {
				longname : 'Slider Notification Panel Shortcode',
				author : 'Tarmizi Ahmad',
				authorurl : 'http://hirewordpressplugindeveloper.com/',
				infourl : '',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('snpanel_shortcode', tinymce.plugins.SNPanelShortcode);
})();
