<?php get_header(); 
/**
 * Template Name: page with NO sidebar
 *
 */
?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
		<div class="post" id="post-<?php the_ID(); ?>">

			<div class="div-logo"><a href="/" title="Orchard Terrase"><img src="<?php bloginfo('template_directory') ?>/images/orchard-logo.png"></a></div>

			<div class="entry">
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

<a href="#" class="fixed-suite"></a>
				<?php the_content(); ?>
				<?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>

			</div>

			<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>

		</div>

		<?php endwhile; endif; ?>

<?php get_footer(); ?>
