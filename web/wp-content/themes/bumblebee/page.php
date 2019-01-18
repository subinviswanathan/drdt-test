<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bumblebee
 */

wp_enqueue_style( 'bumblebee-style-listicle', get_stylesheet_directory_uri() . '/page.css', [], '1.0.2' );

get_header();
?>

	<main class="page-template">
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
						'tablet'       => [ [ 320, 50 ] ],
						'desktop'      => [ [ 728, 90 ], [ 640, 360 ], [ 3, 3 ] ],
						'large_screen' => [ [ 970, 550 ], [ 970, 250 ], [ 970, 90 ], [ 728, 90 ], [ 3, 3 ] ],
					],
				]
			);

			?>
		</section>
		<section class="pure-g page-content">
			<section class="pure-u-1 pure-u-lg-2-3">
				<div class="content">
			<?php
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/content', 'page' );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>
				</div>
			</section>
			<section class="pure-u-1 pure-u-lg-1-3">
				<aside class="sidebar">
				<div class="article-sidebar-top-ad">
					<?php
					bumblebee_render_ad(
						uniqid( 'ad' ),
						[
							'slot-name'        => 'railtop',
							'sizes'            => '970x250,970x90,728x90,3x3',
							'targeting'        => [
								'pos'      => 'railtop',
								'location' => 'rail',
							],
							'responsive-sizes' => [
								'mobile'       => [],
								'tablet'       => [],
								'desktop'      => [ [ 300, 250 ] ],
								'large_screen' => [ [ 300, 250 ] ],
							],
						]
					);
					?>
				</div>
				<div class="article-sidebar-middle-ad">
					<?php
					bumblebee_render_ad(
						uniqid( 'ad' ),
						[
							'slot-name'        => 'railmiddle',
							'sizes'            => '970x250,970x90,728x90,3x3',
							'targeting'        => [
								'pos'      => 'railmiddle',
								'location' => 'rail',
							],
							'responsive-sizes' => [
								'mobile'       => [],
								'tablet'       => [],
								'desktop'      => [],
								'large_screen' => [ [ 160, 600 ], [ 300, 250 ], [ 300, 600 ] ],
							],
						]
					);
					?>
				</div>
				<div class="article-sidebar-scroll-ad">
					<?php
					bumblebee_render_ad(
						uniqid( 'ad' ),
						[
							'slot-name'        => 'railscroll',
							'sizes'            => '970x250,970x90,728x90,3x3',
							'targeting'        => [
								'pos'      => 'railscroll',
								'location' => 'rail',
							],
							'responsive-sizes' => [
								'mobile'       => [],
								'tablet'       => [],
								'desktop'      => [],
								'large_screen' => [ [ 160, 600 ], [ 300, 250 ], [ 300, 600 ], [ 300, 1050 ] ],
							],
						]
					);
					?>
				</div>

			</aside>
			</section>
		</section><!-- #main -->
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
	</main><!-- #primary -->

<?php
get_footer();
