<?php
/**
 * ScrollTo Top plugin options page. Included by ScrollToTop::options_page()
 *
 * @author Daniel Imhoff
 * @package WordPress
 * @subpackage ScrollToTop
 * @since 1.0

   Copyright 2011  Daniel Imhoff  (email : dwieeb@gmail.com)

   This file is part of ScrollTo Top

   ScrollTo Top is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   ScrollTo Top is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with ScrollTo Top. If not, see <http://www.gnu.org/licenses/>.
 */

if( !function_exists( 'add_action' ) ) {
   die( __( 'You are not allowed to access this file outside of WordPress.', 'scrollto-top' ) );
}
?>
<style type="text/css">
.no-padding td {
   padding:0 10px 0 0;
}
#stt-image-list {
   list-style:none;
   margin:0;
}
#stt-image-list li {
   display:inline-block;
   width:100px;
   text-align:center;
}
</style>
<script type="text/javascript">
(function($) {
   $(function() {
      var hide_image_until_needed = $('#enable_scroll_event_yes').is(':checked');

      $('input[name="icon_container_selector"]').bind('stt_icon_container_change', function(event) {
         if($(this).val() === 'body') {
            $('#hide_image_until_needed').fadeIn();
            if(hide_image_until_needed) {
               $('input[name="enable_scroll_event"]').removeAttr('checked');
               $('#enable_scroll_event_yes').attr('checked', 'checked');
            }
            else {
               $('input[name="enable_scroll_event"]').removeAttr('checked');
               $('#enable_scroll_event_no').attr('checked', 'checked');
            }
         }
         else {
            $('#hide_image_until_needed').fadeOut();
            $('#enable_scroll_event_yes').removeAttr('checked');
            $('#enable_scroll_event_no').attr('checked', 'checked');
         }
         $('input[name="enable_scroll_event"]').trigger('stt_toggle_scroll_event');
      });

      $('input[name="enable_scroll_event"]').bind('stt_toggle_scroll_event', function(event) {
         if($(this).is(':checked')) {
            $(this).val() == 1 ? $('#scroll_event_location_container').fadeIn() : $('#scroll_event_location_container').fadeOut();
         }
      });

      $('input[name="icon_container_selector"]').trigger('stt_icon_container_change');

      $('input[name="icon_container_selector"]').bind('input propertychange', function() {
         $(this).trigger('stt_icon_container_change');
      });

      $('input[name="enable_scroll_event"]').click(function() {
         $(this).trigger('stt_toggle_scroll_event');
      });
   });
})(jQuery);
</script>
<div id="scrollto-top" class="wrap">
<?php screen_icon('options-general'); ?>
<h2><?php _e( 'ScrollTo Top', 'scrollto-top' ); ?></h2>
<br class="clear" />
<?php if( $error = $this->errors->get_error_message( 'scrollto-top_nodir' ) ) : ?>
<div id="message" class="error fade"><p><?php echo $error; ?></p></div>
<?php endif; ?>
<?php if( $error = $this->errors->get_error_message( 'scrollto-top_renamedir' ) ) : ?>
<div id="message" class="error fade"><p><?php echo $error; ?></p></div>
<?php endif; ?>
<?php if( $file_error_size ) : ?>
<div id="message" class="error fade"><p><?php _e( 'There was an error while uploading your image. The maximum width and height allowed for icons is 100x100px. Please try again.', 'scrollto-top' ); ?></p></div>
<?php endif; ?>
<?php if( $file_error && !$file_error_size ) : ?>
<div id="message" class="error fade"><p><?php _e( 'There was an error while uploading your image. Remember, only JPG, GIF, and PNG files are allowed. Please try again.', 'scrollto-top' ); ?></p></div>
<?php endif; ?>
<?php if( !$is_dir || !$is_writable ) : ?>
<div id="message" class="error fade"><p><?php printf( __( 'The file directory is either missing or has incorrect permissions and I can\'t fix it! Please chmod %s to 755 for this plugin to work correctly.', 'scrollto-top' ), '<code>' . STT_IMAGES_DIR . '</code>' ); ?></p></div>
<?php endif; ?>
<?php $nonce = wp_create_nonce( 'scrollto-top' ); ?>
<form method="post" id="scrollto-top_options" name="scrollto-top_options" action="<?php echo STT_OPTIONS_URL . '&amp;_wpnonce=' . $nonce; ?>" enctype="multipart/form-data">
<h3><?php _e( 'Settings', 'scrollto-top' ); ?></h3>
<p><?php _e( 'After changing these values, you will need to clear your browser\'s cache before you can see your changes. (Press CTRL + F5 when viewing your website).' ); ?></p>
<table class="form-table">
<tr>
<th scope="row"><?php _e( 'Icon container jQuery selector', 'scrollto-top' ); ?></th>
<td>
<input type="text" name="icon_container_selector" value="<?php echo stripcslashes( htmlentities( $this->options['icon_container_selector'] ) ); ?>" size="25" />
<p><?php _e( 'Leave this value alone if you don\'t know what this means.' ); ?></p>
</td>
</tr><tr>
<th scope="row"><?php _e( 'Icon location in container', 'scrollto-top' ); ?></th>
<td>
<table class="no-padding">
<tr>
<td><label><input type="radio" <?php if($this->options['location_y'] == 'top' && $this->options['location_x'] == 'left') echo 'checked="checked" '; ?>value="0" name="location" /> <?php _e( 'Top left', 'scrollto-top' ); ?></label></td>
<td><label><input type="radio" <?php if($this->options['location_y'] == 'top' && $this->options['location_x'] == 'right') echo 'checked="checked" '; ?>value="1" name="location" /> <?php _e( 'Top right', 'scrollto-top' ); ?></label></td>
</tr><tr>
<td><label><input type="radio" <?php if($this->options['location_y'] == 'bottom' && $this->options['location_x'] == 'left') echo 'checked="checked" '; ?>value="2" name="location" /> <?php _e( 'Bottom left', 'scrollto-top' ); ?></label></td>
<td><label><input type="radio" <?php if($this->options['location_y'] == 'bottom' && $this->options['location_x'] == 'right') echo 'checked="checked" '; ?>value="3" name="location" /> <?php _e( 'Bottom right', 'scrollto-top' ); ?></label></td>
</tr>
</table>
</td>
</tr><tr>
<th scope="row"><?php _e( 'Distance from left/right side', 'scrollto-top' ); ?></th>
<td>
<input type="text" name="location_x_amt" value="<?php echo $this->options['location_x_amt']; ?>" size="5" />px
</td>
</tr><tr>
<th scope="row"><?php _e( 'Distance from top/bottom', 'scrollto-top' ); ?></th>
<td>
<input type="text" name="location_y_amt" value="<?php echo $this->options['location_y_amt']; ?>" size="5" />px
</td>
</tr><tr>
<th scope="row"><?php _e( 'Speed of transition', 'scrollto-top' ); ?></th>
<td>
<input type="text" name="scroll_speed" value="<?php echo $this->options['scroll_speed']; ?>" size="5" /> <?php _e( 'milliseconds' ); ?>
</td>
</tr><tr id="hide_image_until_needed">
<th scope="row"><?php _e( 'Hide image until needed?', 'scrollto-top' ); ?></th>
<td>
<label><input type="radio" <?php if(!isset($this->options['enable_scroll_event']) || $this->options['enable_scroll_event']) echo 'checked="checked" '; ?>value="1" name="enable_scroll_event" id="enable_scroll_event_yes" /> <?php _e( 'Yes', 'scrollto-top' ); ?></label>
<label><input type="radio" <?php if(isset($this->options['enable_scroll_event']) && !$this->options['enable_scroll_event']) echo 'checked="checked" '; ?>value="0" name="enable_scroll_event" id="enable_scroll_event_no" /><?php _e( 'No', 'scrollto-top' ); ?></label>
<p><?php _e( 'If checked, then whenever the user scrolls down the page to a certain point, the go-to-top icon will appear. Yes: More style. No: Better preformance.', 'scrollto-top' ); ?></p>
</td>
</tr><tr id="scroll_event_location_container">
<th scope="row"><?php _e( 'Fade image in/out when the top of the browser window is...', 'scrollto-top' ); ?></th>
<td>
<input type="text" name="scroll_event_location" value="<?php echo $this->options['scroll_event_location']; ?>" size="5" /> <?php _e( 'pixels from the top of the website' ); ?>
</td>
</tr>
</table>
<h3><?php _e( 'Select an icon', 'scrollto-top' ); ?></h3>
<p><?php printf( __( 'To remove pictures, navigate to %s and remove them manually.', 'scrollto-top' ), '<code>' . STT_IMAGES_DIR . '</code>' ); ?></p>
<?php if( empty( $image_array ) ) : ?>
<p><?php _e( 'There are no images in the image directory. Please upload some.', 'scrollto-top' ); ?></p>
<?php else : ?>
<ul id="stt-image-list">
<?php foreach($image_array as $image) : ?>
<li>
   <label><img src="<?php echo STT_IMAGES_URL . '/' . $image; ?>" title="<?php echo $image; ?>" alt="<?php echo $image; ?>" /><br />
   <input type="radio" <?php if($this->options['image'] == $image) echo 'checked="checked" '; ?>value="<?php echo $image; ?>" name="image" /></label>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<h3><?php _e( 'Upload your own icon', 'scrollto-top' ); ?></h3>
<table cellspacing="2" cellpadding="0" border="0">
<tr>
<td>
<input type="file" id="icon_upload" name="icon_upload" />
</td>
<td>
<input type="submit" class="button" value="<?php _e('Upload file', 'specific-files' ); ?>" name="submit" />
</td>
</tr>
</table>
<br class="clear" /><br />
<input type="submit" class="button-primary" value="<?php _e('Update Options', 'scrollto-top' ); ?>" name="submit" />
</form>
<div class="clear">
<p><?php _e( 'ScrollTo Top Wordpress Plugin', 'scrollto-top' ); echo ' ' . STT_VERSION; ?> &copy; 2012 - <a href="http://www.danielimhoff.com/">Daniel Imhoff</a></p>
<p><?php _e( 'ScrollTo jQuery Plugin', 'scrollto-top' ); ?> &copy; 2007-2009 <a href="http://flesler.blogspot.com">Ariel Flesler</a></p>
</div>