<?php
require( '../../../../wp-load.php' );
header('Content-Type: text/javascript');
?>
(function($) {
   var isTransitioned = true,
       transparent = 0,
       translucent = 0.3,
       opaque = 1;

<?php if( $ScrollToTop->options['enable_scroll_event'] ) : ?>
   var fade = function() {
      if(isTransitioned) {
         isTransitioned = false;
         if(<?php echo $ScrollToTop->options['scroll_event_location']; ?> < $(document).scrollTop()) {
            $("#stt-gototop-0").show().fadeTo("slow", translucent, function() {
               isTransitioned = true;
            });
         } else {
            $("#stt-gototop-0").fadeTo("slow", transparent, function() {
               isTransitioned = true;
               $(this).hide();
            });
         }
      }
   }
<?php endif; ?>

   $(function() {
      $("<?php echo $ScrollToTop->options['icon_container_selector']; ?>").each(function(i) {
         $(this).prepend('<a id="stt-top-' + i + '" class="stt-top">Top</a>\n<a href="#stt-top-' + i + '" id="stt-gototop-' + i + '" class="stt-gototop">Top of page</a>');
      });

      $(".stt-gototop").click(function() {
         $.scrollTo($($(this).attr('href')), <?php echo $ScrollToTop->options['scroll_speed']; ?>);

<?php if( $ScrollToTop->options['icon_container_selector'] === 'body' && $ScrollToTop->options['enable_scroll_event'] ) : ?>
         $(this).fadeOut();
<?php endif; ?>

         return false;
      });

<?php if( $ScrollToTop->options['enable_scroll_event'] ) : ?>
      fade();
      $(document).scroll(fade);
<?php endif; ?>

<?php if( !$ScrollToTop->options['enabled_scroll_event'] ) : ?>
      $(".stt-gototop").fadeTo(0, translucent);
<?php endif; ?>

      $(".stt-gototop").mouseover(function() {
         if(isTransitioned) {
            $(this).fadeTo("slow", opaque);
         }
      }).mouseout(function() {
         if(isTransitioned) {
            $(this).fadeTo("slow", translucent);
         }
      });
   });
})(jQuery);