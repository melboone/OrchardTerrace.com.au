<?php
/*
Plugin Name: Slide Notification Panel
Plugin URL: http://wpslideboss.com
Description: Enables customizable popup panel on posts and pages when reader scrolls down to a user-specified portion of the webpage.
Version: 1.0.0
Author: Dan Adair
Author URI: http://wpslideboss.com/
License: GPL2 or later
*/

/*  Copyright 2012  Dan Adair  (email : dan@tnbizcoop.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// plugin definitions
define( 'SNPANEL_PLUGIN_NAME', 'Slide Notification Panel' );
define( 'SNPANEL_CURRENT_VERSION', '1.0.0' );
define( 'SNPANEL_I18N_DOMAIN', 'snpanel' );

define( 'SNPANEL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SNPANEL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'SNPANEL_SETTINGS_PAGE', 'options-general.php?page=snpanel-settings' );

// include files
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

// begin!
new SNPanel();

class SNPanel {

	/**
	 * Plugin's main entry point.
	 **/
	function __construct() {
		register_uninstall_hook( __FILE__, array( 'SNPanel', 'uninstall' ) );
				
		if ( is_admin() ) {
			add_action( 'plugins_loaded', array( &$this, 'load_language_file' ) );
			add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );

			add_action( 'admin_menu', array( &$this, 'create_menus' ) );
			add_action( 'admin_init', array( &$this, 'register_mce_button' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_load_scripts' ) );			

			add_action( 'wp_ajax_snpanel_crud_request', array( &$this, 'ajax_crud_request' ) );			
			add_action( 'wp_ajax_snpanel_get_request', array( &$this, 'ajax_get_request' ) );			
		} else {
			add_action( 'wp_enqueue_scripts', array( &$this, 'wp_load_scripts' ) );
			
			add_shortcode( 'snpanel', array( &$this, 'snpanel_shortcode' ) );
			add_filter( 'widget_text', 'do_shortcode' );
			add_filter ( 'the_content', array( &$this, 'append_master_panel' ) );
		}
	}
	
	/**
	 * Clean up upon plugin uninstall.
	 **/
	static function uninstall() {
		delete_option( 'snpanel_settings' );
		delete_option( 'snpanel_master_panel_name' );
	}
		
	function register_mce_button() {
		// register snpanel shortcode button and MCE plugin
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		
		if ( get_user_option('rich_editing') == 'true' ) {
			add_filter( 'mce_external_plugins', array( &$this, 'add_shortcode_plugin' ) );
			add_filter( 'mce_buttons', array( &$this, 'register_shortcode_button' ) );
		}
	}
	
	/**
	 * Add settings link for this plugin to Wordpress 'Installed plugins' page.
	 **/
	function plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( dirname(__FILE__) . '/slide-notification-panel.php' ) ) {
			$links[] = '<a href="' . SNPANEL_SETTINGS_PAGE . '">' . __('Settings') . '</a>';
		}

		return $links;
	}

	/**
	 * Load language file upon plugin init (for future extension, if any)
	 **/
	function load_language_file() {
		load_plugin_textdomain( SNPANEL_I18N_DOMAIN, false, SNPANEL_PLUGIN_PATH . 'languages/' ); 
	}
	
	/**
	 * Add admin settings menu to backend.
	 **/
	function create_menus() {
		$page = add_options_page( 
			__( 'Slide Notification Panel', SNPANEL_I18N_DOMAIN ),
			__( 'Slide Notification Panel', SNPANEL_I18N_DOMAIN ),
			'manage_options', 
			'snpanel-settings', 
			array( &$this, 'settings_page' )
		); 
	}
	
	/**
	 * Returns the HTML required to display form with latest values from options DB.
	 * The <form> tag itself is not included.
	 **/
	function get_settings_form_child( $is_shortcode = false ) {
		$panels = get_option( 'snpanel_settings' );
		if ( false === $panels || ! is_array( $panels ) ) {
			$panels = array();
			update_option( 'snpanel_settings', $panels );
		}
		$master_panel = get_option( 'snpanel_master_panel_name' );
		if ( false === $master_panel ) {
			$master_panel = '';
			update_option( 'snpanel_master_panel_name', '' );
		}
		$marked_as_master_text = __( '(* marked as master)', SNPANEL_I18N_DOMAIN );
		ob_start();
		?>
		<table class="form-table">
			<input type="hidden" id="panels_count" value="<?php echo count( $panels ); ?>" />
			<tr valign="top">
				<th scope="row"><label id="snpanel_list_label" for="snpanel_list">Select a panel:</label></th>
				<td>
					<select name="snpanel_list" id="snpanel_list">
					<?php
						if ( empty( $panels ) ) {
							?><option value="-1"><?php _e( 'No panels found! Please add one below!', SNPANEL_I18N_DOMAIN ); ?></option><?php
						} else {
							$index = 0;
							foreach ( $panels as $panel_name => $panel ) {
								$panel_name = stripslashes( $panel_name );
								?><option value="<?php echo $index; ?>" data-panel-name="<?php echo esc_attr( $panel_name ); ?>"><?php echo $panel_name . ( ( $panel_name === $master_panel ) ? ( ' ' . $marked_as_master_text ) : '' ); ?></option><?php
								$index++;
							}
						}
					?>
					</select>
				</td>
			</tr>					
		</table>
		<p class="submit submit-main" id ="snpanel_submit_main" style="display:none;">
			<input id="snpanel_submit_main_reload" type="submit" class="button-primary" value="<?php _e( 'Reload all', SNPANEL_I18N_DOMAIN ); ?>" />
			<input id="snpanel_submit_main_add" type="submit" class="button-primary" value="<?php _e( 'Add', SNPANEL_I18N_DOMAIN ); ?>" />
			<input id="snpanel_submit_main_edit" type="submit" class="button-primary" value="<?php _e( 'Edit', SNPANEL_I18N_DOMAIN ); ?>" />
			<input id="snpanel_submit_main_delete" type="submit" class="button-primary" value="<?php _e( 'Delete', SNPANEL_I18N_DOMAIN ); ?>" />
			<?php if ( $is_shortcode ) { ?>
			<input id="snpanel_submit_main_insert" type="submit" class="button-primary" value="<?php _e( 'Insert shortcode', SNPANEL_I18N_DOMAIN ); ?>" />
			<?php } ?>
		</p>
		<h3 id="snpanel_delete_h3" style="display:none;" class="snpanel_h3"><?php _e( 'Delete panel', SNPANEL_I18N_DOMAIN ); ?></h3>
		<div id="snpanel_settings_delete" class="settings_table" style="display:none;">
			<?php _e( 'Please confirm deletion of the selected panel:', SNPANEL_I18N_DOMAIN ); ?> <strong class="snpanel_delete_name"></strong>
		</div>
		<p class="submit submit-delete" id="snpanel_submit_delete" style="display:none;">
			<input type="submit" id="snpanel_confirm_delete" class="button-primary" value="<?php _e( 'Confirm Delete', SNPANEL_I18N_DOMAIN ); ?>" />
			<input type="submit" id="snpanel_cancel_delete" class="button-primary cancel-button" value="<?php _e( 'Cancel', SNPANEL_I18N_DOMAIN ); ?>" />
		</p>
		<?php
		$index = -1;
		// find a new name that does not yet exist
		$new_base_name = 'New panel name';
		$new_name = $new_base_name;
		$count = 1;
		while ( isset( $panels[ $new_name ] ) ) {
			$new_name = $new_base_name . ' #' . $count;
			$count++;
		}
		$panels = array( $new_name => array( 
			'class_name' => '',
			'width' => '150',
			'height' => '150',
			'position_top' => '',
			'position_left' => '',
			'position_right' => '10px',
			'position_bottom' => '10px',
			'background_color' => 'FFFFFF',
			'border_style' => 'solid',
			'border_width' => '1',
			'border_color' => 'FF0000',
			'padding_left' => '5',
			'padding_top' => '5',
			'padding_right' => '5',
			'padding_bottom' => '5',
			'close_button' => '<span class="snpanel-close" style="cursor:pointer;z-index:99999999;position:absolute;right:5px;top:5px;"><img src="' . admin_url('/images/no.png') . '" title="Close" /></span>',
			'contents' => "<div style='color:red;font-family:serif;font-size:16px;'><strong>HELLO!</strong></div>\n\n<div style='font-size:12px;color:black;'>\n    <p>You can replace the contents of this div with your own HTML content, <em>including <a href='http://www.google.com/' target='_blank'>links</a> and <img src='" . admin_url('/images/yes.png') . "' />images.</em></p>\n</div>",
			'styles' => "",
			'target_type' => '2',
			'target_element' => '',
			'target_offset' => '600'
			) ) + $panels;
		foreach ( $panels as $panel_name => $panel ) {
			$panel_name = stripslashes( $panel_name );
			$save_button_id = 'save_edit_' . $index;
			if ( $index === -1 ) {
				$save_button_id = 'save_add';
				?><h3 id="snpanel_add_h3" style="display:none;" class="snpanel_h3"><?php _e( 'Add new panel', SNPANEL_I18N_DOMAIN ); ?></h3><?php
			} else {
				?><h3 id="snpanel_edit_h3_<?php echo $index; ?>" style="display:none;" class="snpanel_h3"><?php echo sprintf( __( 'Edit panel (%s)', SNPANEL_I18N_DOMAIN ), esc_html( $panel_name ) ); ?></h3><?php
			}
			?>
			<table class="form-table settings_table" id="snpanel_settings_table_<?php echo $index; ?>" style="display:none;">
				<tr valign="top">
					<th scope="row"><label for="snpanel_name_<?php echo $index; ?>"><?php _e( 'Panel name', SNPANEL_I18N_DOMAIN ); ?></label></th>
					<td><input name="snpanel_name_<?php echo $index; ?>" type="text" id="snpanel_name_<?php echo $index; ?>" value="<?php echo esc_attr( $panel_name ); ?>" class="regular-text" /></td>
				</tr>					
				<tr valign="top">
					<th scope="row"><label for="snpanel_class_name_<?php echo $index; ?>"><?php _e( 'Panel HTML classes', SNPANEL_I18N_DOMAIN ); ?></label></th>
					<td>
						<input name="snpanel_class_name_<?php echo $index; ?>" type="text" id="snpanel_class_name_<?php echo $index; ?>" value="<?php echo esc_attr( $panel['class_name'] ); ?>" class="regular-text" />
						<p class="description">You can specify optional additional class names for this panel's HTML DIV element here, and override or add CSS styling to it below.<br />
						Note that all panels will automatically have class name "snpanel".
						</p>
					</td>
				</tr>					
				<tr valign="top">
				</tr>					
				<tr valign="top">
					<th scope="row"><?php _e( 'Size', SNPANEL_I18N_DOMAIN ); ?></th>
					<td>
						<label for="snpanel_width_<?php echo $index; ?>"><?php _e( 'Width', SNPANEL_I18N_DOMAIN ); ?></label><input name="snpanel_width_<?php echo $index; ?>" type="number" step="1" min="0" id="snpanel_width_<?php echo $index; ?>" value="<?php echo $panel['width']; ?>" />
						<label for="snpanel_height_<?php echo $index; ?>"><?php _e( 'Height', SNPANEL_I18N_DOMAIN ); ?></label><input name="snpanel_height_<?php echo $index; ?>" type="number" step="1" min="0" id="snpanel_height_<?php echo $index; ?>" value="<?php echo $panel['height']; ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="snpanel_background_color_<?php echo $index; ?>"><?php _e( 'Background color', SNPANEL_I18N_DOMAIN ); ?></label></th>
					<td><input class="color" name="snpanel_background_color_<?php echo $index; ?>" type="text" id="snpanel_background_color_<?php echo $index; ?>" value="<?php echo $panel['background_color']; ?>" class="regular-text" /></td>
				</tr>					
				<tr valign="top">
					<th scope="row"><?php _e( 'Border', SNPANEL_I18N_DOMAIN ); ?></th>
					<td>
						<label for="snpanel_border_width_<?php echo $index; ?>"><?php _e( 'Width', SNPANEL_I18N_DOMAIN ); ?></label><input name="snpanel_border_width_<?php echo $index; ?>" type="number" step="1" min="0" id="snpanel_border_width_<?php echo $index; ?>" value="<?php echo $panel['border_width']; ?>" />
						<label for="snpanel_border_style_<?php echo $index; ?>"><?php _e( 'Style', SNPANEL_I18N_DOMAIN ); ?></label>
						<select name="snpanel_border_style_<?php echo $index; ?>" id="snpanel_border_style_<?php echo $index; ?>">
							<option <?php echo $panel['border_style'] == 'none' ? 'selected="selected" ' : ''; ?>value="none">none</option>
							<option <?php echo $panel['border_style'] == 'hidden' ? 'selected="selected" ' : ''; ?>value="hidden">hidden</option>
							<option <?php echo $panel['border_style'] == 'dotted' ? 'selected="selected" ' : ''; ?>value="dotted">dotted</option>
							<option <?php echo $panel['border_style'] == 'dashed' ? 'selected="selected" ' : ''; ?>value="dashed">dashed</option>
							<option <?php echo $panel['border_style'] == 'solid' ? 'selected="selected" ' : ''; ?>value="solid">solid</option>
							<option <?php echo $panel['border_style'] == 'double' ? 'selected="selected" ' : ''; ?>value="double">double</option>
							<option <?php echo $panel['border_style'] == 'groove' ? 'selected="selected" ' : ''; ?>value="groove">groove</option>
							<option <?php echo $panel['border_style'] == 'ridge' ? 'selected="selected" ' : ''; ?>value="ridge">ridge</option>
							<option <?php echo $panel['border_style'] == 'inset' ? 'selected="selected" ' : ''; ?>value="inset">inset</option>
							<option <?php echo $panel['border_style'] == 'inherit' ? 'selected="selected" ' : ''; ?>value="inherit">inherit</option>
						</select>
						<label for="snpanel_border_color_<?php echo $index; ?>"><?php _e( 'Color', SNPANEL_I18N_DOMAIN ); ?></label><input class="color" name="snpanel_border_color_<?php echo $index; ?>" type="text" id="snpanel_border_color_<?php echo $index; ?>" value="<?php echo $panel['border_color']; ?>">
					</td>
				</tr>					
				<tr valign="top">
					<th scope="row"><?php _e( 'Position offset', SNPANEL_I18N_DOMAIN ); ?></th>
					<td>
						<label for="snpanel_position_top_<?php echo $index; ?>"><?php _e( 'Top', SNPANEL_I18N_DOMAIN ); ?></label><input name="snpanel_position_top_<?php echo $index; ?>" type="text" id="snpanel_position_top_<?php echo $index; ?>" value="<?php echo $panel['position_top']; ?>" />
						<label for="snpanel_position_bottom_<?php echo $index; ?>"><?php _e( 'Bottom', SNPANEL_I18N_DOMAIN ); ?></label><input name="snpanel_position_bottom_<?php echo $index; ?>" type="text" id="snpanel_position_bottom_<?php echo $index; ?>" value="<?php echo $panel['position_bottom']; ?>" /><br />
						<label for="snpanel_position_left_<?php echo $index; ?>"><?php _e( 'Left', SNPANEL_I18N_DOMAIN ); ?></label><input name="snpanel_position_left_<?php echo $index; ?>" type="text" id="snpanel_position_left_<?php echo $index; ?>" value="<?php echo $panel['position_left']; ?>" />
						<label for="snpanel_position_right_<?php echo $index; ?>"><?php _e( 'Right', SNPANEL_I18N_DOMAIN ); ?></label><input name="snpanel_position_right_<?php echo $index; ?>" type="text" id="snpanel_position_right_<?php echo $index; ?>" value="<?php echo $panel['position_right']; ?>" />
						<p class="description">Position offset values can be in pixels or percentage values. <br />
						Example valid values: 10px, 25%, 10%, 15px<br />
						Example to make panel appear in center of viewport: top = 50%, left = 50%, right =, bottom =
						</p>
					</td>
				</tr>					
				<tr valign="top">
					<th scope="row"><?php _e( 'Trigger target', SNPANEL_I18N_DOMAIN ); ?></th>
					<td>
						<label for="snpanel_target_type_<?php echo $index; ?>"><?php _e( 'Type', SNPANEL_I18N_DOMAIN ); ?></label>
						<select name="snpanel_target_type_<?php echo $index; ?>" id="snpanel_target_type_<?php echo $index; ?>">
							<option value="0"<?php echo intval( $panel['target_type'] ) === 0 ? ' selected="selected"' : ''; ?>>HTML element (by CSS selector)</option>
							<option value="1"<?php echo intval( $panel['target_type'] ) === 1 ? ' selected="selected"' : ''; ?>>y-offset from top of page</option>
							<option value="2"<?php echo intval( $panel['target_type'] ) === 2 ? ' selected="selected"' : ''; ?>>y-offset from bottom of page</option>
							<option value="3"<?php echo intval( $panel['target_type'] ) === 3 ? ' selected="selected"' : ''; ?>>wherever shortcode appears in posts/pages/widgets</option>
						</select>
						<label <?php echo intval( $panel['target_type'] ) !== 0 ? 'style="display:none; "' : '' ; ?>id="snpanel_target_element_label_<?php echo $index; ?>" for="snpanel_target_element_<?php echo $index; ?>"><?php _e( 'CSS Selector', SNPANEL_I18N_DOMAIN ); ?></label><input <?php echo intval( $panel['target_type'] ) !== 0 ? 'style="display:none; "' : '' ; ?>name="snpanel_target_element_<?php echo $index; ?>" type="text" id="snpanel_target_element_<?php echo $index; ?>" value="<?php echo $panel['target_element']; ?>" />
						<label <?php echo ( intval( $panel['target_type'] ) === 0 || intval( $panel['target_type'] ) === 3 ) ? 'style="display:none; "' : ''; ?>id="snpanel_target_offset_label_<?php echo $index; ?>" for="snpanel_target_offset_<?php echo $index; ?>"><?php _e( 'y-offset', SNPANEL_I18N_DOMAIN ); ?></label><input <?php echo ( intval( $panel['target_type'] ) === 0 || intval( $panel['target_type'] ) === 3 ) ? 'style="display:none; "' : ''; ?>name="snpanel_target_offset_<?php echo $index; ?>" type="number" step="1" min="0" id="snpanel_target_offset_<?php echo $index; ?>" value="<?php echo $panel['target_offset']; ?>" />
						<p <?php echo intval( $panel['target_type'] ) !== 0 ? 'style="display:none; "' : ''; ?>class="description" id="snpanel_target_element_desc_<?php echo $index; ?>">If CSS selector matches multiple elements, only first element is used. Example CSS selectors: #element-id, .class-name<br />
						</p>
					</td>
				</tr>					
				<tr valign="top">
					<th scope="row"><?php _e( 'Padding between border and content', SNPANEL_I18N_DOMAIN ); ?></th>
					<td>
						<label for="snpanel_padding_top_<?php echo $index; ?>"><?php _e( 'Top', SNPANEL_I18N_DOMAIN ); ?></label><input name="snpanel_padding_top_<?php echo $index; ?>" type="number" step="1" min="0" id="snpanel_padding_top_<?php echo $index; ?>" value="<?php echo $panel['padding_top']; ?>" />
						<label for="snpanel_padding_bottom_<?php echo $index; ?>"><?php _e( 'Bottom', SNPANEL_I18N_DOMAIN ); ?></label><input name="snpanel_padding_bottom_<?php echo $index; ?>" type="number" step="1" min="0" id="snpanel_padding_bottom_<?php echo $index; ?>" value="<?php echo $panel['padding_bottom']; ?>" /><br />
						<label for="snpanel_padding_left_<?php echo $index; ?>"><?php _e( 'Left', SNPANEL_I18N_DOMAIN ); ?></label><input name="snpanel_padding_left_<?php echo $index; ?>" type="number" step="1" min="0" id="snpanel_padding_left_<?php echo $index; ?>" value="<?php echo $panel['padding_left']; ?>" />
						<label for="snpanel_padding_right_<?php echo $index; ?>"><?php _e( 'Right', SNPANEL_I18N_DOMAIN ); ?></label><input name="snpanel_padding_right_<?php echo $index; ?>" type="number" step="1" min="0" id="snpanel_padding_right_<?php echo $index; ?>" value="<?php echo $panel['padding_right']; ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Close button HTML', SNPANEL_I18N_DOMAIN ); ?></th>
					<td>
						<textarea style="width: 550px; height: 140px;" name="snpanel_close_button_<?php echo $index; ?>" id="snpanel_close_button_<?php echo $index; ?>"><?php echo esc_html( $panel['close_button'] ); ?></textarea><br />
						<p class="description"><?php echo esc_html( __( 'Close button HTML should be wrapped inside an element with class name "snpanel-close". Leave blank if close button is not required.', SNPANEL_I18N_DOMAIN ) ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'CSS Styles', SNPANEL_I18N_DOMAIN ); ?></th>
					<td>
						<textarea style="width: 550px; height: 140px;" name="snpanel_styles_<?php echo $index; ?>" id="snpanel_styles_<?php echo $index; ?>"><?php echo esc_html( $panel['styles'] ); ?></textarea><br />
						<p class="description"><?php echo esc_html( __( 'Insert any external <link> tag or inline CSS <style> here. They will be inserted before the HTML closing </head> tag.', SNPANEL_I18N_DOMAIN ) ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'HTML content', SNPANEL_I18N_DOMAIN ); ?></th>
					<td><textarea style="width: 550px; height: 140px;" name="snpanel_contents_<?php echo $index; ?>" id="snpanel_contents_<?php echo $index; ?>"><?php echo esc_html( $panel['contents'] ); ?></textarea></td>
				</tr>
				<tr valign="top">
					
				</tr>
			</table>
			<p class="submit submit-save" id="snpanel_submit_<?php echo $save_button_id; ?>" style="display:none;">
				<input id="<?php echo $save_button_id; ?>" type="submit" class="button-primary save_edit" value="<?php _e( 'Save Changes', SNPANEL_I18N_DOMAIN ); ?>" />
				<input id="cancel_<?php echo $save_button_id; ?>" type="submit" class="button-primary cancel-button" value="<?php _e( 'Back', SNPANEL_I18N_DOMAIN ); ?>" />
			</p>
			<?php
			$index++;
		}
		
		return ob_get_clean();
	}
	
	/**
	 * Display settings page.
	 **/
	function settings_page() {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br /></div>
			<h2><?php _e( 'Slide Notification Panel', SNPANEL_I18N_DOMAIN ); ?></h2>
			<div id="snpanel-processing-dialog" style="display:none;"><img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>"></img> Processing...please wait...</div>
			<form action="" id="snpanel_form">
				<?php echo $this->get_settings_form_child(); ?>
			</form>		
		</div>		
		<?php
	}

	/**
	 * Loads Javascripts required for admin post and settings pages.
	 **/
	function admin_load_scripts() {						
		global $pagenow;
				
		if ( is_admin() &&
			'post-new.php' === $pagenow || 'post.php' === $pagenow || 
			( 'options-general.php' === $pagenow && isset( $_REQUEST['page'] ) && 'snpanel-settings' === $_REQUEST['page'] ) ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_script( 'jscolor.js', plugins_url( 'js/jscolor/jscolor.js', __FILE__ ) );
			wp_enqueue_script( 'snpanel_settings.js', plugins_url( 'tinymce-custom/mce/snpanel_shortcode/js/dialog.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog' ) );

			wp_enqueue_style( 'jquery-ui.css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
		}
	}
	
	/**
	 * Loads Javascripts required for front-end.
	 **/
	function wp_load_scripts() {		
		global $snpanel_count;

		// need everywhere, in case widgets use our shortcodes, or for master panels
		wp_enqueue_script( 'jquery' );
		
		if ( is_single() || is_page() ) {
			// for single posts/pages, master panel will be appended to the end via shortcode
			return;
		}
		
		// find the master panel
		$master_panel_name = get_option( 'snpanel_master_panel_name' );
		if ( ! isset( $master_panel_name ) || empty( $master_panel_name ) ) {
			return;	// no master panel set
		}
		$panels = get_option( 'snpanel_settings' );
		if ( ! isset( $panels[ $master_panel_name ] ) ) {
			return;		// panel doesn't exist
		}
		$panel = $panels[ $master_panel_name ];

		wp_enqueue_script( 'snpanel.js', plugins_url( 'js/snpanel.js', __FILE__ ), array( 'jquery' ) );
		
		// pass the master panel parameters to script
		wp_localize_script( 'snpanel.js', 'snPanel', $this->get_localize_array( $panel ) );		

		// ensure no other panels appear on page
		$snpanel_count = 1;
	}
	
	/**
	 * Returns array of values to be sent via wp_localize_script or json_encode to Javascript.
	 **/
	function get_localize_array( $panel ) {
		return array(
			'class_name' => $panel['class_name'],
			'width' => $panel['width'],
			'height' => $panel['height'],
			'position_top' => $panel['position_top'],
			'position_left' => $panel['position_left'],
			'position_right' => $panel['position_right'],
			'position_bottom' => $panel['position_bottom'],
			'background_color' => $panel['background_color'],
			'border_style' => $panel['border_style'],
			'border_width' => $panel['border_width'],
			'border_color' => $panel['border_color'],
			'padding_left' => $panel['padding_left'],
			'padding_right' => $panel['padding_right'],
			'padding_top' => $panel['padding_top'],
			'padding_bottom' => $panel['padding_bottom'],
			'close_button' => str_replace( '-->', '// -->', html_entity_decode( $panel['close_button'] ) ),
			'contents' => str_replace( '-->', '// -->', html_entity_decode( $panel['contents'] ) ),
			'styles' => str_replace( '-->', '// -->', html_entity_decode( $panel['styles'] ) ),
			'target_type' => $panel['target_type'],
			'target_element' => $panel['target_element'],
			'target_offset' => $panel['target_offset']
		);
	}

	/** 
	 * Register button for snpanel shortcode.
	 **/
	function register_shortcode_button( $buttons ) {
		array_push( $buttons, "|", "snpanel_shortcode" );
		return $buttons;
	}

	/**
	 * Add new MCE plugin for snpanel shortcode.
	 **/
	function add_shortcode_plugin( $plugin_array ) {
		$plugin_array['snpanel_shortcode'] = SNPANEL_PLUGIN_URL . 'tinymce-custom/mce/snpanel_shortcode/snpanel_shortcode_plugin.js';
		return $plugin_array;
	}
	
	/** 
	 * Append master panel to end of each post/page (for master panels with "shortcode" trigger target position).
	 **/
	function append_master_panel( $content ) {
		global $snpanel_count;

		if ( ! is_null( $snpanel_count ) && $snpanel_count >= 1 ) {
			return $content;	// only 1 panel allowed per page!
		}
		
		if ( is_single() || is_page() ) {
			$master_panel_name = get_option( 'snpanel_master_panel_name' );
			if ( ! isset( $master_panel_name ) || empty( $master_panel_name ) ) {
				return $content;	// no master panel set
			}
			
			// find the master panel
			$panels = get_option( 'snpanel_settings' );
			if ( ! isset( $panels[ $master_panel_name ] ) ) {
				return $content;		// panel doesn't exist
			}
			$panel = $panels[ $master_panel_name ];
			
			$content .= $this->get_snpanel_shortcode_script( $panel );
			$snpanel_master_set = true;

			$snpanel_count = 1;
		}
		return $content;
	}

	/**
	 * Replace opening and closing snpanel shortcodes with empty .snpanel_scroll_start and snpanel_scroll_start span elements.
	 **/
	function snpanel_shortcode( $atts ) {
		global $snpanel_count;

		if ( ! is_null( $snpanel_count ) && $snpanel_count >= 1 ) {
			return;	// only 1 panel allowed per page!
		}
		
		if ( ! is_page() && ! is_single() ) {
			return;	// only shortcodes in pages and single posts (and widgets for these posts/pages) allowed!
		}
		
		extract( shortcode_atts( array(
			'name' => '' 
			), $atts ) );		
		
		if ( empty( $name ) ) {
			return;		// nothing to do if panel name is not set
		}
		$name = stripslashes( $name );
		
		// find the panel referred to in the shortcode
		$panels = get_option( 'snpanel_settings' );
		if ( ! isset( $panels[ $name ] ) ) {
			return;		// panel doesn't exist
		}
		$panel = $panels[ $name ];
		
		$snpanel_count = 1;		
		return $this->get_snpanel_shortcode_script( $panel );
	}
	
	/**
	 * Returns the HTML/Javascript for the specified panel shortcode.
	 **/
	function get_snpanel_shortcode_script( $panel ) {
		ob_start();
		?>
		<span class="snpanel_scroll_shortcode_target" />
		<script type="text/javascript">
		//<![CDATA[
		snPanel = <?php echo json_encode( $this->get_localize_array( $panel ) ); ?>;
		//]]>
		<?php
		require( 'js/snpanel.js' );
		?>
		</script>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * Handling of AJAX request fatal error.
	 **/
	function ajax_fatal_error( $sErrorMessage = '' ) {
		header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
		die( $sErrorMessage );
	}

	/**
	 * Handling of AJAX non-fatal error.
	 **/
	function ajax_non_fatal_error( $response, $sErrorMessage = '' ) {
		$response = array_merge( $response, array( 'error' => $sErrorMessage ) );
		echo json_encode( $response );
		die();
	}

	/**
	 * Handle AJAX request to populate panels form. Generates wp_nonce value for future crud operations as well.
	 **/
	function ajax_get_request() {
		$response = array();
		
		$is_shortcode = false;
		if ( isset( $_POST['shortcode'] ) ) {
			$is_shortcode = $_POST['shortcode'];
		}
		
		$response['nonce'] = wp_create_nonce( 'snpanel-nonce' );
		$response['panels_html'] = $this->get_settings_form_child( $is_shortcode );
		echo json_encode( $response );
		die();
	}
	
	/**
	 * Handle AJAX requests to add/update/delete panel.
	 **/
	function ajax_crud_request() {
		check_ajax_referer( 'snpanel-nonce' );

		$is_shortcode = false;
		if ( isset( $_POST['shortcode'] ) ) {
			$is_shortcode = $_POST['shortcode'];
		}
		
		$response = array();
		$response['nonce'] = wp_create_nonce( 'snpanel-nonce' );
		
		if ( ! isset( $_POST['action2' ] ) ) {
			$this->ajax_fatal_error( __( 'No action specified', SNPANEL_I18N_DOMAIN ) );
			return;
		}	
		$action = $_POST['action2'];
		
		$panels = get_option( 'snpanel_settings' );
		$panel_old_name = '';

		// if request is for update/delete, ensure panel old name is set, and panel actually exists
		if ( 'update' === $action || 'delete' === $action ) {			
			if ( ! isset( $_POST['panel_old_name'] ) && 'add' !== $_POST['action'] ) {
				$this->ajax_fatal_error( __( 'Panel name not specified', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_old_name = stripslashes( $_POST['panel_old_name'] );
			
			// ensure panel exists
			if ( false === $panels || ! is_array( $panels ) || ! isset( $panels[ $panel_old_name ]  ) ) {
				$this->ajax_fatal_error( sprintf( __( 'Unknown panel %s', SNPANEL_I18N_DOMAIN ), $panel_old_name ) );
			}
		}
		
		// if request is for add/update, ensure new panel doesn't already exist, and ensure all panel fields are set
		$new_panel = null;
		if ( 'add' === $action || 'update' === $action ) {
			// ensure panel name is set
			if ( ! isset( $_POST['panel_new_name'] ) ) {
				$this->ajax_fatal_error( __( 'New panel name not specified', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_new_name = stripslashes( $_POST['panel_new_name'] );
			
			// ensure panel name doesn't contain invalid characters
			if ( strpbrk( $panel_new_name, '[\'"]<>\\') !== false ) {
				$this->ajax_non_fatal_error( $response, __( 'Sorry, panel name cannot contain the characters [ ] \' " < > \\', SNPANEL_I18N_DOMAIN ) );
			}
			// ensure panel name doesn't already exist (if we're adding)
			if ( 'add' === $action && is_array( $panels ) && isset( $panels[ $panel_new_name ] ) ) {
				$this->ajax_non_fatal_error( $response, __( 'Panel name already exists!', SNPANEL_I18N_DOMAIN ) );
			}
			
			// ensure panel settings are ok
			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_class_name'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel class_name must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_class_name = stripslashes( $_POST['panel_class_name'] );

			if ( ! isset( $_POST['panel_width'] ) ) {
				$this->ajax_fatal_error( $response, __( 'Panel width must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_width = sprintf( "%d", intval( stripslashes( $_POST['panel_width'] ) ) );
			
			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_height'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel height must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_height = sprintf( "%d", intval( stripslashes( $_POST['panel_height'] ) ) );
			
			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_background_color'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel background_color must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_background_color = trim( stripslashes( $_POST['panel_background_color'] ) );
			if ( substr( $panel_background_color, 0, 1 ) === '#' ) {
				$panel_background_color = substr( $panel_background_color, 1 );
			}

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_border_style'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel border_style must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_border_style = trim( stripslashes( $_POST['panel_border_style'] ) );

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_border_width'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel border_width must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_border_width = sprintf( "%d", intval( stripslashes( $_POST['panel_border_width'] ) ) );
			
			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_border_color'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel border_color must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_border_color = trim( stripslashes( $_POST['panel_border_color'] ) );
			if ( substr( $panel_border_color, 0, 1 ) === '#' ) {
				$panel_border_color = substr( $panel_border_color, 1 );
			}

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_contents'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel contents must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_contents = stripslashes( $_POST['panel_contents'] );

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_styles'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel styles must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_styles = stripslashes( $_POST['panel_styles'] );

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_close_button'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel close_button must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_close_button = stripslashes( $_POST['panel_close_button'] );

			// position offset values must have a unit e.g. 'px' or '%'. Assume 'px' if unspecified
			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_position_left'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel position_left must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_position_left = trim( stripslashes( $_POST['panel_position_left'] ) );
			if ( $panel_position_left === sprintf( '%d', $panel_position_left ) ) {
				$panel_position_left .= 'px';
			}

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_position_right'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel position_right must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_position_right = trim( stripslashes( $_POST['panel_position_right'] ) );
			if ( $panel_position_right === sprintf( '%d', $panel_position_right ) ) {
				$panel_position_right .= 'px';
			}

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_position_top'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel position_top must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_position_top = trim( stripslashes( $_POST['panel_position_top'] ) );
			if ( $panel_position_top === sprintf( '%d', $panel_position_top ) ) {
				$panel_position_top .= 'px';
			}

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_position_bottom'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel position_bottom must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_position_bottom = trim( stripslashes( $_POST['panel_position_bottom'] ) );
			if ( $panel_position_bottom === sprintf( '%d', $panel_position_bottom ) ) {
				$panel_position_bottom .= 'px';
			}
			
			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_padding_left'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel padding_left must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_padding_left =  sprintf( "%d", intval( stripslashes( $_POST['panel_padding_left'] ) ) );;

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_padding_right'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel padding_right must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_padding_right =  sprintf( "%d", intval( stripslashes( $_POST['panel_padding_right'] ) ) );;

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_padding_top'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel padding_top must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_padding_top =  sprintf( "%d", intval( stripslashes( $_POST['panel_padding_top'] ) ) );;

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_padding_bottom'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel padding_bottom must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_padding_bottom =  sprintf( "%d", intval( stripslashes( $_POST['panel_padding_bottom'] ) ) );;

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_target_type'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel target_type must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_target_type = trim( stripslashes( $_POST['panel_target_type'] ) );

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_target_element'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel target_element must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_target_element = stripslashes( $_POST['panel_target_element'] );

			if ( ! isset( $response['error'] ) && ! isset( $_POST['panel_target_offset'] ) ) {
				$this->ajax_fatal_error( $response,  __( 'Panel target_offset must be set', SNPANEL_I18N_DOMAIN ) );
			}
			$panel_target_offset = sprintf( "%d", intval( stripslashes( $_POST['panel_target_offset'] ) ) );

			// create the new panel
			$new_panel = array(
				$panel_new_name => array(
					'class_name' => $panel_class_name,
					'width' => $panel_width,
					'height' => $panel_height,
					'background_color' => $panel_background_color,
					'border_style' => $panel_border_style,
					'border_width' => $panel_border_width,
					'border_color' => $panel_border_color,
					'contents' => $panel_contents,
					'styles' => $panel_styles,
					'close_button' => $panel_close_button,
					'position_left' => $panel_position_left,
					'position_right' => $panel_position_right,
					'position_top' => $panel_position_top,
					'position_bottom' => $panel_position_bottom,
					'padding_left' => $panel_padding_left,
					'padding_right' => $panel_padding_right,
					'padding_top' => $panel_padding_top,
					'padding_bottom' => $panel_padding_bottom,
					'target_type' => $panel_target_type,
					'target_element' => $panel_target_element,
					'target_offset' => $panel_target_offset
				)
			);

			// need to update master panel?
			if ( isset( $_POST['panel_is_master'] ) ) {	
				// set to current panel
				update_option( 'snpanel_master_panel_name', $panel_new_name );
			} else {
				$current_master_panel_name = get_option( 'snpanel_master_panel_name' );
				if ( $current_master_panel_name === $panel_new_name ) {	
					// unset master panel
					update_option( 'snpanel_master_panel_name', '' );
				}
			}

		}
		
		switch ( $action ) {
			// add new panel
			case "add":
				// insert at end of panel array
				if ( false === $panels ) {
					$panels = array();
				}
				$panels = array_merge( $panels, $new_panel );
			break;
			
			// update existing panel
			case "update":
				// update is done by removing old panel and inserting new one
				unset( $panels[ $panel_old_name ] );
				$panels = array_merge( $panels, $new_panel );
			break;
			
			// delete existing panel
			case "delete":
				unset( $panels[ $panel_old_name ] );
				
				// unset master panel if it was the current one
				$current_master_panel_name = get_option( 'snpanel_master_panel_name' );
				if ( $current_master_panel_name === $panel_old_name ) {	
					update_option( 'snpanel_master_panel_name', '' );
				}
			break;
			
			// unknown action
			default:
				$this->ajax_fatal_error( __( 'Unknown action specified', SNPANEL_I18N_DOMAIN ) );
				return;
		}
		
		// save the updated panel data
		update_option( 'snpanel_settings', $panels );
		
		// return the latest form values
		$response['panels_html'] = $this->get_settings_form_child( $is_shortcode );
		
		echo json_encode( $response );
		die();
	}
}