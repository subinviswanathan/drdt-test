<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bumblebee
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="content-image">
		<?php bumblebee_post_thumbnail( 'large' ); ?>
	</div>
	<div class="content-container">
		<header class="entry-header">
		<?php
		if ( 'post' === get_post_type() ) {
			$categories_list = get_the_category_list( esc_html__( ', ', 'bumblebee' ) );
			if ( $categories_list ) {
				echo '<span class="cat-links">';
				/* translators: 1: list of categories. */
				printf( esc_html( '%1$s' ), $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput
				echo '</span>';
			}
			the_title( sprintf( '<h3 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
		}
		?>

		</header><!-- .entry-header -->

		<div class="entry-summary">
		<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
	</div>
</article><!-- #post-<?php the_ID(); ?> -->


