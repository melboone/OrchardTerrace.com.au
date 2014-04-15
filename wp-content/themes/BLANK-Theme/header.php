<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
	
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	
	<?php if (is_search()) { ?>
	   <meta name="robots" content="noindex, nofollow" /> 
	<?php } ?>

	<title>
		   <?php
		      if (function_exists('is_tag') && is_tag()) {
		         single_tag_title("Tag Archive for &quot;"); echo '&quot; - '; }
		      elseif (is_archive()) {
		         wp_title(''); echo ' Archive - '; }
		      elseif (is_search()) {
		         echo 'Search for &quot;'.wp_specialchars($s).'&quot; - '; }
		      elseif (!(is_404()) && (is_single()) || (is_page())) {
		         wp_title(''); echo ' - '; }
		      elseif (is_404()) {
		         echo 'Not Found - '; }
		      if (is_home()) {
		         bloginfo('name'); echo ' - '; bloginfo('description'); }
		      else {
		          bloginfo('name'); }
		      if ($paged>1) {
		         echo ' - page '. $paged; }
		   ?>
	</title>
	
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" />
	
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

	<?php wp_head(); ?>

 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js" type="text/javascript"></script>
    <script src="<?php bloginfo('template_directory') ?>/js/jquery.tabSlideOut.v1.3.js"></script>
         
         <script>
         $(function(){
             $('.slide-out-suite').tabSlideOut({
                 tabHandle: '.handle',                              //class of the element that will be your tab
                 pathToTabImage: '<?php bloginfo('template_directory') ?>/images/close.png',          //path to the image for the tab (optionaly can be set using css)
                 imageHeight: '180px',                               //height of tab image
                 imageWidth: '50px',                               //width of tab image    
                 tabLocation: 'left',                               //side of screen where tab lives, top, right, bottom, or left
                 speed: 300,                                        //speed of animation
                 action: 'click',                                   //options: 'click' or 'hover', action to trigger animation
                 topPos: '14%',                                   //position from the top
                 fixedPosition: true                               //options: true makes it stick(fixed position) on scroll
             });
         });

         </script>
							<!--start contactable -->
<div id="my-contact-div"><!-- contactable html placeholder --></div>

<link rel="stylesheet" href="<?php bloginfo('template_directory') ?>/contactable.css" type="text/css" />
<script type="text/javascript" src="<?php bloginfo('template_directory') ?>/js/jquery.contactable.js"></script>
<script>
	jQuery(function(){
		jQuery('#my-contact-div').contactable(
        {
            subject: 'feedback URL:'+location.href,
            url: 'mail.php',
            name: '<p id="register-title">Register your interest now</p>',
            email: '',
            message : '',
            submit : 'SUBMIT',
            recievedMsg : '<span id="thank-top">Thank you</span><br> for registering your interest.<br><hr id="thank-message"><br>We will be contacting you soon.',
            notRecievedMsg : 'Sorry but your message could not be sent, try again later',
            disclaimer: '',
            hideOnSubmit: true
        });
	});
</script>
<!--end contactable -->
<img id="display-suite-image" src="<?php bloginfo('template_directory') ?>/images/display-suite.png">
    <div class="slide-out-suite">
        <a id="close-button-suite" class="handle">Display Suite</a>
    <div class="display-inner"><p class="inner-title">Display now open</p>
    <p id="inner-text">222 Williamsons road, Doncaster</p>
        <hr>
    <p class="inner-bottom">Please call</p>
    <p class="inner-phone">1300 919 888</p></div>
    </div>

<!-- register now -->
    </div>
	<div id="page-wrap">

		<div id="header">
			<a href="<?php echo get_option('home'); ?>/"><img src="<?php bloginfo('template_directory') ?>/images/orchard-logo.png" alt="Orchard logo"></a>
		</div>