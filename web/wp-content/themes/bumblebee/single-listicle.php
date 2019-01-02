<?php
/**
 * The template for displaying all single listicles
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-listicle
 *
 * @package bumblebee
 */

/**
 * Include the functions file
 */
require_once 'functions.listicle.php';

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
						for ( $i = 0; $i < 3; $i++ ) :
							if ( ( $current_card ) <= ( $total_cards - 1 ) ) :
								get_the_card_markup( $current_card + 1, $total_cards, $cards[ $current_card + 1 ] );
								$current_card++;
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

get_header();
$category = get_the_category();
$category = $category[0];
?>
<main class="listicle-page">
	<section class="advertisement">
		<?php
		bumblebee_render_ad(
			uniqid( 'ad' ),
			[
				'slot-name' => 'prearticle',
				'sizes'     => '970x250,970x90,728x90,3x3',
				'targeting' => [
					'pos'      => 'prearticle',
					'location' => 'top',
				],
			]
		);

		?>
	</section>
	<section class="content pure-g">
	<section class="social-share-bar-desktop pure-u-lg-2-24">
		<ul class="pure-menu-list social-menu">
			<li class="pure-menu-item"><a href="#" class="pure-menu-link"><img class="social-icons" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/envelope-regular.svg" /></a></li>
			<li class="pure-menu-item"><a href="#" class="pure-menu-link"><img class="social-icons" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/facebook-f-brands.svg" /></a></li>
			<li class="pure-menu-item"><a href="#" class="pure-menu-link"><img class="social-icons" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/pinterest-p-brands.svg" /></a></li>
		</ul>
	</section>
	<section class=" pure-u-1 pure-u-lg-14-24">
		<div class="contentbarheader">
			<a href="#" class="post-category-label"><?php echo esc_attr( $category->name ); ?></a>
			<h1 class="entry-title"><?php echo get_the_title(); ?></h1>
			<div class="byline">
				<img src="http://cpt.test.rda.net/wp-content/uploads/sites/9/2018/10/Blue-Makeup-MAC-1200x675.jpg" class="author-image" alt="img"></img>
				<span class="author-name">Amrita</span>
			</div>
			<section class="social-share-bar-mobile">
				<ul class="pure-menu-list social-menu">
					<li class="pure-menu-item"><a href="#" class="pure-menu-link"><img class="social-icons" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/envelope-regular.svg" /></a></li>
					<li class="pure-menu-item"><a href="#" class="pure-menu-link"><img class="social-icons" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/facebook-f-brands.svg" /></a></li>
					<li class="pure-menu-item"><a href="#" class="pure-menu-link"><img class="social-icons" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/pinterest-p-brands.svg" /></a></li>
				</ul>
			</section>
			<div class="dek"><?php the_excerpt(); ?></div>
		</div>
	</section>
	<section class="sidebar pure-u-1 pure-u-lg-8-24"></section>
</section>

	<?php
	the_content();
	?>
</main>
	<?php
	get_footer();
	?>
