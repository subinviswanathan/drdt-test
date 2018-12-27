<?php
/**
 * Template part for displaying small featured post on homepage
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bumblebee
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php bumblebee_post_thumbnail( 'homepage-featured-small' ); ?>
	<header class="entry-header">
		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
	</header><!-- .entry-header -->
</article><!-- #post-<?php the_ID(); ?> -->
