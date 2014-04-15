jQuery(document).ready(function($) {
	// inject CSS into HTML body
	if (snPanel.styles.length > 0) {
		jQuery('head').append(snPanel.styles);
	}
	
	// create and inject panel into HTML body
	var snPanelStyle = 'display:none;z-index:9999999;width:' + snPanel.width + 'px;height:' + snPanel.height + 'px;background-color:#' + snPanel.background_color + ';';
	snPanelStyle += 'border:' + snPanel.border_width + 'px ' + snPanel.border_style + ' #' + snPanel.border_color + ';';
	snPanelStyle += 'position:fixed;' +
		(snPanel.position_top != '' ? 'top:' + snPanel.position_top + ';' : '') +
		(snPanel.position_bottom != '' ? 'bottom:' + snPanel.position_bottom + ';' : '') +
		(snPanel.position_left != '' ? 'left:' + snPanel.position_left + ';' : '') +
		(snPanel.position_right != '' ? 'right:' + snPanel.position_right + ';' : '');
	snPanelStyle += 
		(snPanel.padding_top != '' ? 'padding-top:' + snPanel.padding_top + 'px;' : '') +
		(snPanel.padding_bottom != '' ? 'padding-bottom:' + snPanel.padding_bottom + 'px;' : '') +
		(snPanel.padding_left != '' ? 'padding-left:' + snPanel.padding_left + 'px;' : '') +
		(snPanel.padding_right != '' ? 'padding-right:' + snPanel.padding_right + 'px;' : '');
	$snPanel = $( '<div class="snpanel ' + snPanel.class_name + '" style="' + snPanelStyle + '">' + snPanel.close_button + snPanel.contents + '</div>' ).appendTo( 'body' );
	
	// periodically check current viewport and show/hide each panel if necessary
	isSnPanelHidden = true;
	snPanelInterval = setInterval(
		function() {
			// (re)determine scroll target each time, in case page elements change
			targetTop = 0;
			if (snPanel.target_type === '0') { 
				// html element
				targetTop = $(snPanel.target_element).first().offset().top;
			} else if (snPanel.target_type === '1') {
				// absolute y-offset from page top
				targetTop = snPanel.target_offset;
			} else if (snPanel.target_type === '2') {
				// absolute y-offset from page bottom
				targetTop = $(document).height() - snPanel.target_offset;
			} else {
				// shortcode position
				$targets = $('.snpanel_scroll_shortcode_target');
				if ($targets.length === 0) { 
					return;
				}
				targetTop = $targets.first().offset().top;
			} 
	
			snPanelScroll = $(window).scrollTop() + $(window).height();
			
			if ( snPanelScroll >= targetTop ) {
				if (isSnPanelHidden) {
					isSnPanelHidden = false;
					$snPanel.slideToggle("slow");
				}
			} else {
				if (!isSnPanelHidden) {
					isSnPanelHidden = true;
					$snPanel.slideToggle("slow");
				}
			} 
		}, 
		350
	);
	
	// handle close button click
	$('.snpanel-close').click(function() {
		clearInterval(snPanelInterval);
		if (!isSnPanelHidden) {
			isSnPanelHidden = true;
			$snPanel.slideToggle("slow");					
		}
	});
});