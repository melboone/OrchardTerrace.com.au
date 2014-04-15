jQuery(document).ready(function($) {	
	snpanel_is_shortcode = 0;	// false
	if ( typeof tinyMCEPopup !== 'undefined' && typeof tinyMCEPopup.editor !== 'undefined' ) {
		ajaxurl = tinyMCEPopup.editor.ajaxurl;
		snpanel_is_shortcode = 1;	// true
	}
	var nonce = '';
	
	// set up the Ajax loading "Please wait" blocking dialog message
	$( "#snpanel-processing-dialog" ).dialog({
		title: 'Message',
		modal: true,
		autoOpen: false,
		resizable: false,
		closeOnEscape: false,
		dialogClass: 'no-close',
		height: 80,
		width: 250
	});

	// send generic ajax request to specified server, blocking UI in the process  
	function ajax(actionText, data, successCallback, dontShowSuccessMsg) {
		// disable all buttons
		$('.submit input[type=submit]').attr('disabled', 'disabled');
		$('#snpanel-processing-dialog').dialog("open");
		
		data._ajax_nonce = nonce;
		
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: data,
			timeout: 60000,
			dataType: 'json',
			success: 
				function(response, textStatus, jqXHR) {     
					$('.submit input[type=submit]').removeAttr('disabled');
					// disable edit/delete if no items
					if ($("#panels_count").val() === '0') {
						$('#snpanel_submit_main_delete, #snpanel_submit_main_edit, #snpanel_submit_main_insert').attr('disabled', 'disabled');
					}
					$('#snpanel-processing-dialog').dialog("close");
					if (response.error) {
						alert(actionText + ' failed. Error: ' + response.error);
						return;
					}
					nonce = response.nonce;
					if ( typeof dontShowSuccessMsg === 'undefined' ) {
						alert(actionText + ' successful!');
					}
					reload_html(response.panels_html);
					if ( successCallback !== null ) {
						successCallback( response );
					}
				},
			error: 
				function(jqXHR, textStatus, errorThrown) {
					$('.submit input[type=submit]').removeAttr('disabled');
					// disable edit/delete if no items
					if ($("#panels_count").val() === '0') {
						$('#snpanel_submit_main_delete, #snpanel_submit_main_edit, #snpanel_submit_main_insert').attr('disabled', 'disabled');
					}
					$('#snpanel-processing-dialog').dialog("close");
					if (textStatus === 'timeout') {   // server timeout
						alert(actionText + " failed. Please try again. (Error: Server timeout)");
					} else {                  // unexpected error
						alert(actionText + " failed. Please try again. (Error: " + jqXHR.responseText + ")");
					}
					
					console.error("Error processing " + actionText + " request. " + textStatus + "(" + jqXHR.responseText + "). Response: " + jqXHR.responseText);
				}
		});
	}

	// reset html
	function reload_html(form_html) {
		$('#snpanel_form').children().remove();
		return $(form_html).appendTo('#snpanel_form');
	}
	
	// request ajax for reloading
	function reload_all() {
		ajax('Loading panels', {
				action      : 'snpanel_get_request',
				shortcode	: snpanel_is_shortcode
			},
			after_success, 'dontShowSuccessMsg'
		);
	}
	
	reload_all();
	
	// returns appropriate set of data for currently selected table index
	function formatDataForTableIndex(index) {
		if (typeof index === 'undefined') {
			index = $('#snpanel_list').val();
		}
		data = {
			panel_old_name: $('#snpanel_list option:selected').attr('data-panel-name'),
			panel_new_name: $('#snpanel_name_' + index).val(),
			panel_width: $('#snpanel_width_' + index).val(),
			panel_height: $('#snpanel_height_' + index).val(),
			panel_background_color: $('#snpanel_background_color_' + index).val(),
			panel_border_color: $('#snpanel_border_color_' + index).val(),
			panel_border_width: $('#snpanel_border_width_' + index).val(),
			panel_border_style: $('#snpanel_border_style_' + index).val(),
			panel_position_top: $('#snpanel_position_top_' + index).val(),
			panel_position_left: $('#snpanel_position_left_' + index).val(),
			panel_position_right: $('#snpanel_position_right_' + index).val(),
			panel_position_bottom: $('#snpanel_position_bottom_' + index).val(),
			panel_padding_top: $('#snpanel_padding_top_' + index).val(),
			panel_padding_left: $('#snpanel_padding_left_' + index).val(),
			panel_padding_right: $('#snpanel_padding_right_' + index).val(),
			panel_padding_bottom: $('#snpanel_padding_bottom_' + index).val(),
			panel_target_type: $('#snpanel_target_type_' + index).val(),
			panel_target_element: $('#snpanel_target_element_' + index).val(),
			panel_target_offset: $('#snpanel_target_offset_' + index).val(),
			panel_contents: $('#snpanel_contents_' + index).val(),
			panel_styles: $('#snpanel_styles_' + index).val(),
			panel_class_name: $('#snpanel_class_name_' + index).val(),
			panel_close_button: $('#snpanel_close_button_' + index).val()
		};
		if ( $('#snpanel_master_' + index).is(':checked') ) {
			data.panel_is_master = '1';
		}
		return data;
	}
	
	// bind trigger target select change element to hide/show element or offset inputs
	function dynamic_trigger_target_handler(index) {
		$('#snpanel_target_type_' + index).change(function() {
			$targetElementElem = $('#snpanel_target_element_' + index + ', #snpanel_target_element_label_' + index + ', #snpanel_target_element_desc_' + index).hide();
			$targetOffsetElem = $('#snpanel_target_offset_' + index + ', #snpanel_target_offset_label_' + index).hide();
			if ($(this).val() == '0') {
				$targetElementElem.show();
			} else if ($(this).val() !== '3') {
				$targetOffsetElem.show();
			}
		});
	}
	
	// (re)bind events after successful AJAX load
	function after_success(response) {
		$( '.ui-dialog-titlebar-close').hide();
		
		$( '.snpanel_h3, .settings_table, .submit' ).hide();
		$( '#snpanel_submit_main' ).show();
        
		$( '.color' ).each(function() {
			new jscolor.color( this, {} );
		});

		// disable edit/delete/insert if no items
		if ($("#panels_count").val() === '0') {
			$('#snpanel_submit_main_delete, #snpanel_submit_main_edit, #snpanel_submit_main_insert').attr('disabled', 'disabled');
		}
		
		// reload all button
		$('#snpanel_submit_main_reload').click(function(e) {
			e.preventDefault();
			reload_all();
			return false;	
		});
        
		// add new panel button
		$('#snpanel_submit_main_add').click(function(e) {
			e.preventDefault();
			$('.snpanel_h3, .settings_table, .submit').hide();
			$('#snpanel_list, #snpanel_list_label').hide();
			$('#snpanel_add_h3, #snpanel_submit_save_add, #snpanel_settings_table_-1').show();
			dynamic_trigger_target_handler('-1');
			return false;	
		});
		
		// save add new/edit
		$('#save_add, .save_edit').click(function(e) {
			e.preventDefault();
			
			var index = -1;
			var action2 = 'add';
			var actionStr = 'Adding new panel';
			if ($(this).attr('id') !== 'save_add') {
				index = $('#snpanel_list option:selected').val();
				action2 = 'update';
				actionStr = 'Editing panel';
			}
			data = formatDataForTableIndex(index);			
			data.action = 'snpanel_crud_request';
			data.action2 = action2;
			data.shortcode = snpanel_is_shortcode;
			
			var successCallback = function() {
				after_success();
				
				// select newly updated panel
				$("#snpanel_list option").each(function() {
					if ($(this).attr('data-panel-name') === data.panel_new_name) {
						$(this).attr('selected', 'selected');
					}
				});
			};
			ajax(actionStr, data, successCallback);
			
			return false;
		});
        
		// edit selected panel button
		$('#snpanel_submit_main_edit').click(function(e) {
			e.preventDefault();
			$('.snpanel_h3, .settings_table, .submit').hide();
			$('#snpanel_list, #snpanel_list_label').hide();
			
			index = $('#snpanel_list').val();
			dynamic_trigger_target_handler(index);
			$('#snpanel_edit_h3_' + index + ', #snpanel_submit_save_edit_' + index + ', #snpanel_settings_table_' + index).show();
			return false;	
		});
        
		// delete selected panel button
		$('#snpanel_submit_main_delete').click(function(e) {
			e.preventDefault();
			$('.snpanel_delete_name').text($('#snpanel_list option:selected').attr('data-panel-name'));
			$('.snpanel_h3, .settings_table, .submit').hide();
			$('#snpanel_list, #snpanel_list_label').hide();
			$('#snpanel_delete_h3, .submit-delete, #snpanel_settings_delete').show();
			return false;	
		});
		
		// confirm delete
		$('#snpanel_confirm_delete').click(function(e) {
			e.preventDefault();

			data = {};
			data.action = 'snpanel_crud_request';
			data.action2 = 'delete';
			data.shortcode = snpanel_is_shortcode;
			data.panel_old_name = $('#snpanel_list option:selected').attr('data-panel-name');
			ajax('Deleting panel', data, after_success);
			
			return false;
		});
		
		// cancel buttons
		$('.cancel-button').click(function(e) {
			e.preventDefault();
			$('.snpanel_h3, .settings_table, .submit').hide();
			$('#snpanel_list, #snpanel_list_label').show();
			$('.submit-main').show();
			return false;
		});
		
		// insert snpanel_is_shortcode button
		$('#snpanel_submit_main_insert').click(function(e) {
			function escapeHtml(unsafe) {
				return unsafe
					.replace(/&/g, "&amp;")
					.replace(/</g, "&lt;")
					.replace(/>/g, "&gt;")
					.replace(/"/g, "&quot;")
					.replace(/'/g, "&#039;");
			}
			
			e.preventDefault();
			shortcodeVal = '[snpanel name="' + $('#snpanel_list option:selected').attr('data-panel-name') + '"]';
			tinyMCEPopup.editor.execCommand('mceInsertContent', false, shortcodeVal);
			tinyMCEPopup.close();
			return false;
		});
	}

});