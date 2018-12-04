<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bumblebee
 */

?>
<?php get_header(); ?>
<a name="content" id="content"></a>
<?php if ( have_posts() ) : ?>
	<?php if ( is_home() && ! is_front_page() ) : ?>
		<header>
			<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
		</header>
	<?php endif; ?>
	<section class="featured">
		<?php
		// @todo: Pick from a custom list of posts.
		// Featured 1.
		if ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		}

		// Featured 2.
		if ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		}

		// Featured 3.
		if ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		}
		?>
	</section>

	<?php while ( have_posts() ) : ?>
		<section>
			<div>
				<?php for ( $i = 0; $i < 6; $i++ ) : ?>
					<?php the_post(); ?>
					<?php get_template_part( 'template-parts/content', get_post_type() ); ?>
				<?php endfor; ?>
			</div>
			<aside class="sidebar"></aside>
		</section>
		<?php if ( ( $wp_query->current_post + 1 ) !== ( $wp_query->post_count ) ) : ?>
			<div class="full-width-ad"></div>
		<?php endif; ?>
	<?php endwhile; ?>

	<div class="footer-ad"></div>
	<?php the_posts_navigation(); ?>
<?php else : ?>
	<?php get_template_part( 'template-parts/content', 'none' ); ?>
<?php endif; ?>
<?php if ( is_single() ) : ?>
	<?php get_sidebar(); ?>
<?php endif; ?>
<?php get_footer(); ?>
