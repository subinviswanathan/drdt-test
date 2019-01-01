<?php
/**
 * Bumblebee functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package bumblebee
 */

add_filter(
	'the_content',
	function( $content ) {
		$delimiter    = '<div class="tmbi-card">';
		$cards        = array_filter( explode( $delimiter, $content ) );
		$total_cards  = count( $cards );
		$current_card = 0;
		$section_num  = 0;
		for ( $j = 0; $j <= $total_cards - 1; $j = $j + 3 ) :
			$section_num++;
			?>
			<section class="content pure-g">
				<section class="social-share-bar-desktop pure-u-lg-2-24"></section>
				<section class=" pure-u-1 pure-u-lg-14-24">
					<div class="contentbar">
						<?php
						for ( $i = 0; $i < 3; $i ++ ) :
							if ( ( $current_card ) <= ( $total_cards - 1 ) ) :
								get_the_card_markup( $current_card + 1, $total_cards, $cards[ $current_card + 1 ] );
								$current_card ++;
							endif;
						endfor;
						?>
					</div>
				</section>
				<section class="sidebar pure-u-1 pure-u-lg-8-24">
					<?php
					bumblebee_render_ad(
						uniqid( 'ad' ),
						[
							'slot-name' => 'rail' . ( 1 === $section_num ? 'top' : 2 === $section_num ? 'middle' : 'scroll' ),
							'sizes'     => '300x250,300x600',
						]
					);
					?>
				</section>
			</section>
			<?php if ( $current_card < $total_cards ) { ?>
			<section class="full-width-ad">
				<?php
				bumblebee_render_ad(
					uniqid( 'ad' ),
					[
						'slot-name' => ( 1 === $section_num ? 'top' : 2 === $section_num ? 'middle' : 'scroll' ),
						'sizes'     => '970x550,970x250,970x90,728x90,300x250,3x3',
					]
				);
				?>
			</section>
			<?php } ?>
		<?php
		endfor;
		return $cards;
	},
	11
);

/**
 * Getting card content.
 *
 * @param String $current_card  current card.
 * @param String $total_cards  total no of cards.
 * @param String $card  card data.
 */
function get_the_card_markup( $current_card, $total_cards, $card ) {
	preg_match( '|<h[^>]+>(.*)</h[^>]+>|iU', $card, $headings );
	preg_match_all( '%(<p[^>]*>.*?</p>)%i', $card, $paragraph );
	$image = $paragraph[0][0];
	$dek   = $paragraph[0][1];
	ob_start();
	?>
	<div class="listicle-card">
		%s
		<div class="card-number">
			<span class="current-page-count">%s</span><span class="total-page-count">/%s</span>
		</div>
		<div class="card-content">
			%s
			<p class="content">%s</p>
		</div>
	</div>

	<?php
	$base_template = ob_get_clean();
	printf(
		wp_kses_post( $base_template ),
		wp_kses_post( $paragraph[0][0] ),
		intval( $current_card ),
		intval( $total_cards ),
		wp_kses_post( $headings[0] ),
		wp_kses_post( $dek )
	);
}


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

?>
