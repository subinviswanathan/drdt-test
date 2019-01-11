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

wp_enqueue_style( 'bumblebee-style-homepage', get_stylesheet_directory_uri() . '/homepage.css', [], '1.0.2' );
?>
<?php get_header(); ?>
	<main class="home-page">

		<section class="advertisement">
			<?php
			bumblebee_render_ad(
				uniqid( 'ad' ),
				[
					'slot-name'        => 'prearticle',
					'sizes'            => '970x250,970x90,728x90,3x3',
					'targeting'        => [
						'pos'      => 'prearticle',
						'location' => 'top',
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


		<?php if ( have_posts() ) : ?>
			<section class="archive-content pure-g">
				<?php
				// @todo: Pick from a custom list of posts.
				// Featured 1.
				if ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', 'hero' );
				}
				// Featured 2.
				if ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', 'featured' );
				}
				// Featured 3.
				if ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', 'featured' );
				}
				?>
			</section>

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
								if ( 1 === $section_num ) {
									$slot_name = 'top';
								} elseif ( 2 === $section_num ) {
									$slot_name = 'middle';
								}
								bumblebee_render_ad(
									uniqid( 'ad' ),
									[
										'slot-name'        => 'rail' . $slot_name,
										'sizes'            => '300x250,300x600',
										'responsive-sizes' => [
											'desktop'      => [ [ 300, 250 ], [ 300, 600 ] ],
											'large_screen' => [ [ 300, 250 ], [ 300, 600 ] ],
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
								'sizes'            => '970x550,970x250,970x90,728x90,300x250,3x3',
								'responsive-sizes' => [
									'mobile'       => [ [ 300, 250 ], [ 320, 50 ], [ 3, 3 ] ],
									'tablet'       => [ [ 300, 250 ], [ 320, 50 ], [ 3, 3 ] ],
									'desktop'      => [ [ 728, 90 ], [ 300, 250 ], [ 3, 3 ] ],
									'large_screen' => [ [ 970, 550 ], [ 970, 250 ], [ 970, 90 ], [ 728, 90 ], [ 300, 250 ], [ 3, 3 ] ],
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
					'sizes'            => '970x550,970x250,970x90,728x90,3x3,300x250',
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
