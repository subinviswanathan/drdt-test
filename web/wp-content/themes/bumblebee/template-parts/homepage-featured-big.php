<?php
/**
 * Template part for displaying big featured post on homepage
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bumblebee
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php bumblebee_post_thumbnail( 'homepage-featured-big' ); ?>
	<header class="entry-header">
		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
		<?php the_excerpt(); ?>
		<a href="<?php the_permalink(); ?>" class="button">Read more</a>
	</header><!-- .entry-header -->
</article><!-- #post-<?php the_ID(); ?> -->
