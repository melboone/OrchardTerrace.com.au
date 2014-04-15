<?php get_header(); ?>
<!-- ascendent order -->
        <?php query_posts($query_string . '&order=ASC'); ?>
<!-- Finish ashcendent -->

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">

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
