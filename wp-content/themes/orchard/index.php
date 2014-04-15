<?php get_header(); ?>
<!-- ascendent order -->
        <?php query_posts($query_string . '&order=ASC'); ?>
<!-- Finish ashcendent -->

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">



						<!--start contactable -->
<div id="my-contact-div"><!-- contactable html placeholder --></div>

<link rel="stylesheet" href="<?php bloginfo('template_directory') ?>/contactable.css" type="text/css" />

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory') ?>/js/jquery.contactable.js"></script>

<script>
	jQuery(function(){
		jQuery('#my-contact-div').contactable(
        {
            subject: 'feedback URL:'+location.href,
            url: 'mail.php',
            name: '<h3 id="register-title">Register your interest</h3>',
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

<!-- made by sppice, tutorial at megscoding.tumblr.com -->

<div id="oneout"><span class="onetitle">
<img src="<?php bloginfo('template_directory') ?>/images/display-suite.png" alt="">
</span><div id="oneout_inner">
<p class="inner-title">Display now open</p>
<p id="inner-text">222 williams road . Doncaster</p>
<hr>
<p class="inner-bottom">Please call</p>
<p class="inner-phone">1300 155 999
</div></div>

			<div class="entry">
				<?php the_content(); ?>
			</div>



		</div>

	<?php endwhile; ?>

	<?php include (TEMPLATEPATH . '/inc/nav.php' ); ?>

	<?php else : ?>

		<h2>Not Found</h2>

	<?php endif; ?>


<?php get_footer(); ?>
