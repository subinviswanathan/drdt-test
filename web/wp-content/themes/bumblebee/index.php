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

add_filter(
	'ad_unit_path_2',
	function () {
		return 'homepage';
	}
);

add_filter(
	'ad_unit_path_3',
	function () {
		return 'homepage';
	}
);
?>
<?php get_header(); ?>
	<style type="text/css">
		<?php require get_stylesheet_directory() . '/homepage.css'; ?>
	</style>
	<main class="home-page">

		<section class="advertisement">
			<?php
			bumblebee_render_ad(
				uniqid( 'ad' ),
				[
					'slot-name'        => 'prearticle',
					'targeting'        => [
						'pos'      => 'prearticle',
						'location' => 'top',
						'tf'       => 'atf',
					],
					'responsive-sizes' => [
						'mobile'       => [ [ 320, 50 ] ],
						'tablet'       => [ [ 728, 90 ] ],
						'desktop'      => [ [ 970, 250 ], [ 970, 90 ], [ 728, 90 ] ],
						'large_screen' => [ [ 970, 250 ], [ 970, 90 ], [ 728, 90 ], [ 3, 3 ] ],
					],
				]
			);
			?>
		</section>

		<?php // @todo: This could be a widget. ?>
		<?php if ( function_exists( 'get_marquee_post' ) ) : ?>
			<section class="archive-content pure-g">
				<?php
				// @todo: The templates should accept a $post argument to avoid overriding globals.
				$post = get_marquee_post();
				if ( $post ) {
					setup_postdata( $post );
					get_template_part( 'template-parts/content', 'hero' );
				}

				$post = get_marquee_post( 2 );
				if ( have_posts() ) {
					setup_postdata( $post );
					get_template_part( 'template-parts/content', 'featured' );
				}

				$post = get_marquee_post( 3 );
				if ( $post ) {
					setup_postdata( $post );
					get_template_part( 'template-parts/content', 'featured' );
				}
				?>
			</section>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<?php $section_num = 0; ?>
			<?php while ( have_posts() ) : ?>
				<?php $section_num++; ?>
				<section class="archive-content">
					<div class="pure-g">
						<section class="pure-u-1 pure-u-sm-3-4 homepage-article">
							<div class="pure-g recipes">
								<?php for ( $i = 0; $i < 6; $i++ ) : ?>
									<?php the_post(); ?>
									<?php get_template_part( 'template-parts/content', 'grid' ); ?>
									<?php if ( 2 === $i ) : ?>
										</div><div class="pure-g recipes">
									<?php endif; ?>
								<?php endfor; ?>
							</div>
						</section>
						<section class="pure-u-sm-1-4">
							<aside class="sidebar">
								<?php
								$slot_name = 'scroll';
								$tf_slot   = 'btf';
								if ( 1 === $section_num ) {
									$slot_name = 'top';
									$tf_slot   = 'atf';
								} elseif ( 2 === $section_num ) {
									$slot_name = 'middle';
									$tf_slot   = 'atf';
								}
								bumblebee_render_ad(
									uniqid( 'ad' ),
									[
										'slot-name'        => 'rail' . $slot_name,
										'responsive-sizes' => [
											'large_screen' => [ [ 300, 250 ], [ 300, 600 ] ],
										],
										'targeting'        => [
											'tf'       => $tf_slot,
											'pos'      => 'rail' . $slot_name,
											'location' => $slot_name,
										],
									]
								);
								?>
							</aside>
						</section>
					</div>
				</section>
				<?php if ( ( $wp_query->current_post + 1 ) !== ( $wp_query->post_count ) ) : ?>
					<div class="full-width-ad">
						<?php
						bumblebee_render_ad(
							uniqid( 'ad' ),
							[
								'slot-name'        => $slot_name,
								'responsive-sizes' => [
									'mobile'       => [ [ 300, 250 ], [ 320, 50 ], [ 3, 3 ] ],
									'tablet'       => [ [ 300, 250 ], [ 320, 50 ], [ 3, 3 ] ],
									'desktop'      => [ [ 728, 90 ], [ 300, 250 ], [ 3, 3 ] ],
									'large_screen' => [ [ 970, 550 ], [ 970, 250 ], [ 970, 90 ], [ 728, 90 ], [ 300, 250 ], [ 3, 3 ] ],
								],
								'targeting'        => [
									'tf'       => $tf_slot,
									'pos'      => $slot_name,
									'location' => $slot_name,
								],
							]
						);
						?>
					</div>
				<?php endif; ?>

			<?php endwhile; ?>
		<?php endif; ?>

		<div class="postarticle_ad">
			<?php
			bumblebee_render_ad(
				uniqid( 'ad' ),
				[
					'slot-name'        => 'postarticle',
					'responsive-sizes' => [
						'mobile'       => [ [ 320, 50 ], [ 300, 250 ], [ 3, 3 ] ],
						'tablet'       => [ [ 320, 50 ], [ 300, 250 ], [ 3, 3 ] ],
						'desktop'      => [ [ 728, 90 ], [ 640, 360 ], [ 3, 3 ], [ 300, 250 ] ],
						'large_screen' => [ [ 970, 550 ], [ 970, 250 ], [ 970, 90 ], [ 728, 90 ], [ 3, 3 ], [ 300, 250 ] ],
					],
				]
			);
			?>
		</div>
	</main>
<?php get_footer(); ?>
