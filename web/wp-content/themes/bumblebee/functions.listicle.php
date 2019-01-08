<?php
/**
 * Bumblebee functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package bumblebee
 */

/**
 * Reading the listicle content.
 *
 * @return array of content.
 */
function listicle_data() {
	$content     = get_the_content();
	$content     = apply_filters( 'the_content', $content );
	$delimiter   = '<div class="tmbi-card">';
	$cards       = array_filter( explode( $delimiter, $content ) );
	$total_cards = count( $cards );
	return array( $cards, $total_cards );
}

/**
 * Getting card content.
 *
 * @param String $current_card  current card.
 * @param String $total_cards  total no of cards.
 * @param String $card  card data.
 * @return array of card data.
 */
function get_the_card_data( $current_card, $total_cards, $card ) {
	preg_match( '|<h[^>]+>(.*)</h[^>]+>|iU', $card, $headings );
	preg_match_all( '%(<p[^>]*>.*?</p>)%i', $card, $paragraph );
	$image        = $paragraph[0][0];
	$dek          = $paragraph[0][1];
	$current_card = intval( $current_card );
	$total_cards  = intval( $total_cards );
	$card_heading = wp_kses_post( $headings[0] );
	$card_excerpt = wp_kses_post( $dek );

	return array( $image, $current_card, $total_cards, $card_heading, $card_excerpt );
}

/**
 * Setting up the card content.
 *
 * @param String $content  content.
 * @return string of content.
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


