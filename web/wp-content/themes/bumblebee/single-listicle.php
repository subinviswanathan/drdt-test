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
wp_enqueue_style( 'bumblebee-style-listicle', get_stylesheet_directory_uri() . '/listicle.css', [], '1.0.2' );

require_once 'functions.listicle.php';

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
	<section class="social-menu-desktop pure-u-lg-2-24">
		<?php get_template_part( 'template-parts/social-share', 'none' ); ?>
	</section>
	<section class=" pure-u-1 pure-u-lg-14-24">
		<div class="contentbarheader">
			<a href="#" class="post-category-label"><?php echo esc_attr( $category->name ); ?></a>
			<h1 class="entry-title"><?php echo get_the_title(); ?></h1>
			<div class="byline">
				<img src="http://cpt.test.rda.net/wp-content/uploads/sites/9/2018/10/Blue-Makeup-MAC-1200x675.jpg" class="author-image" alt="img"></img>
				<span class="author-name">Amrita</span>
			</div>
			<section class="social-menu-mobile">
				<?php get_template_part( 'template-parts/social-share', 'none' ); ?>
			</section>
			<div class="dek"><?php the_excerpt(); ?></div>
		</div>
	</section>
	<?php
	$data         = listicle_data();
	$card_data    = $data[0];
	$total_cards  = $data[1];
	$current_card = 1;
	$section_num  = 1;
	for ( $j = 1; $j <= $total_cards; $j += 3 ) {
	?>
	<section class="content pure-g">
		<section class="social-share-bar-desktop pure-u-lg-2-24"></section>
		<section class=" pure-u-1 pure-u-lg-14-24">
			<div class="contentbar">
				<?php
				for ( $i = 0; $i < 3; $i++ ) :
					if ( ( $current_card ) <= ( $total_cards ) ) :
						$card_content = get_the_card_data( $current_card, $total_cards, $card_data[ $current_card ] );
						$card_image   = $card_content[0];
						$current_card = $card_content[1];
						$all_cards    = $card_content[2];
						$card_heading = $card_content[3];
						$card_brief   = $card_content[4];
						?>
						<div class="listicle-card">
							<?php echo wp_kses_post( $card_content[0] ); ?>
							<div class="card-number">
								<span class="current-page-count"><?php echo wp_kses_post( $current_card ) . ' '; ?></span><span class="total-page-count"><?php echo '/ ' . wp_kses_post( $all_cards ); ?></span>
							</div>
							<div class="card-content">
								<?php echo wp_kses_post( $card_heading ); ?>
								<p class="content"><?php echo wp_kses_post( $card_brief ); ?></p>
							</div>
						</div>
						<?php
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
	<?php $section_num++; } ?>
</main>
	<?php
	get_footer();
	?>
