<?php
require( '../../../../wp-load.php' );
header('Content-Type: text/css');
?>
.stt-top { /* One of these will hide it without using display:none =P */
   display:block;
   text-indent:-9999em;
   font-size:0;
   line-height:0;
   height:0;
   visibility:hidden;
}
.stt-gototop {
   position:<?php echo $ScrollToTop->options['icon_container_selector'] === 'body' ? 'fixed' : 'absolute'; ?>;
   z-index:5000;
   <?php echo $ScrollToTop->options['location_y'] . ':' . $ScrollToTop->options['location_y_amt']; ?>px;
   <?php echo $ScrollToTop->options['location_x'] . ':' . $ScrollToTop->options['location_x_amt']; ?>px;
   background:url("<?php echo STT_IMAGES_URL . '/' . $ScrollToTop->options['image']; ?>") no-repeat top left;
   text-indent:-9999em;
   width:<?php echo $ScrollToTop->options['image_width']; ?>px;
   height:<?php echo $ScrollToTop->options['image_height']; ?>px;
   <?php if( $ScrollToTop->options['enable_scroll_event'] ) : ?>
   display:none;
   <?php endif; ?>
}
.stt-gototop:hover,
.stt-gototop:active,
.stt-gototop:focus {
   outline:0;
}