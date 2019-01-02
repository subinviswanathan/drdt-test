<?php
/**
 * Bumblebee functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package bumblebee
 */

/**
 * Setting up the card content.
 *
 * @param String $content  content.
 */
function set_post_content_navigation( $content ) {
	global $post,$pages;
	setup_postdata( $post );
	if ( is_singular( 'listicle' ) && ! get_query_var( 'page' ) ) {
		$postcontent = '';
		$count       = count( $pages );
		if ( $count > 0 ) {
			for ( $i = 0; $i < $count; $i++ ) {
				$postcontent .= '<div class="tmbi-card">' . wpautop( $pages[ $i ] ) . '</div>' . PHP_EOL;
			}
			return( $postcontent );
		}
	}
	return ( $content );
}

add_filter( 'the_content', 'set_post_content_navigation' );


