<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bumblebee
 */

add_filter(
	'ad_unit_path_2',
	function () {
		return 'archive';
	}
);

add_filter(
	'ad_unit_path_3',
	function () {
		return 'category';
	}
);

get_header();
?>

<style type="text/css">
	<?php require get_stylesheet_directory() . '/archive.css'; ?>
</style>
<main class="archive-page">
	<section class="advertisement">
		<?php
		bumblebee_render_ad(
			uniqid( 'ad' ),
			[
				'slot-name' => 'prearticle',
				'targeting' => [
					'pos'      => 'prearticle',
					'location' => 'top',
					'tf' => 'atf',
				],
				'responsive-sizes' => [
					'mobile'       => [ [ 320, 50 ] ],
					'tablet'       => [ [ 320, 50 ] ],
					'desktop'      => [ [ 728, 90 ], [ 640, 360 ] ],
					'large_screen' => [ [ 970, 250 ], [ 970, 90 ], [ 728, 90 ] ],
				],
			]
		);
		?>
	</section>
	<?php if ( have_posts() ) : ?>
		<section class="archive-content">
			<div class="archive-headings">
				<div class="breadcrumbs">
				<?php if ( function_exists( 'yoast_breadcrumb' ) ) : ?>
					<?php yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); ?>
				<?php endif; ?>
				</div>
				<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
				<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
			</div>

			<?php the_post(); ?>
			<?php get_template_part( 'template-parts/archive/content', 'hero' ); ?>
			<ul class="featured-posts">
				<?php
				$i = 0;
				while ( have_posts() && $i++ < 5 ) :
					the_post();
					get_template_part( 'template-parts/archive/content', 'featured' );
					endwhile;
				?>
			</ul>
		</section>
		<section class="full-width-ad">
			<?php
			$slot_name = 'scroll';
			$tf_slot = 'btf';
			if ( 1 === $section_num ) {
				$slot_name = 'top';
				$tf_slot = 'atf';
			} elseif ( 2 === $section_num ) {
				$slot_name = 'middle';
				$tf_slot = 'atf';
			}
			bumblebee_render_ad(
				uniqid( 'ad' ),
				[
					'slot-name' => $slot_name,
					'responsive-sizes' => [
						'mobile'       => [ [ 300, 250 ], [ 320, 50 ], [ 3, 3 ] ],
						'tablet'       => [ [ 300, 250 ], [ 320, 50 ], [ 3, 3 ] ],
						'desktop'      => [ [ 728, 90 ], [ 300, 250 ], [ 3, 3 ] ],
						'large_screen' => [ [ 970, 550 ], [ 970, 250 ], [ 970, 90 ], [ 728, 90 ], [ 300, 250 ], [ 3, 3 ] ],
					],
					'targeting' => [
						'tf' => $tf_slot,
						'pos' => $slot_name,
						'location' => $slot_name,
					],
				]
			);
			?>
		</section>
		<?php $section_num = 0; ?>
		<?php while ( have_posts() ) : ?>
			<?php $section_num++; ?>
			<section class="archive-content">
				<div class="pure-g recipes">
					<?php for ( $i = 0; $i < 8; $i++ ) : ?>
						<?php if ( ( $wp_query->current_post + 1 ) !== ( $wp_query->post_count ) ) : ?>
							<?php the_post(); ?>
							<?php get_template_part( 'template-parts/archive/content', 'grid' ); ?>
							<?php if ( 3 === $i ) : ?>
								</div><div class="pure-g recipes">
							<?php endif; ?>
						<?php endif; ?>
					<?php endfor; ?>
				</div>
			</section>
			<section class="full-width-ad">
				<?php
				$slot_name = 'scroll';
				$tf_slot = 'btf';
				if ( 1 === $section_num ) {
					$slot_name = 'top';
					$tf_slot = 'atf';
				} elseif ( 2 === $section_num ) {
					$slot_name = 'middle';
					$tf_slot = 'atf';
				}
				bumblebee_render_ad(
					uniqid( 'ad' ),
					[
						'slot-name' => $slot_name,
						'responsive-sizes' => [
							'mobile'       => [ [ 300, 250 ], [ 320, 50 ], [ 3, 3 ] ],
							'tablet'       => [ [ 300, 250 ], [ 320, 50 ], [ 3, 3 ] ],
							'desktop'      => [ [ 728, 90 ], [ 300, 250 ], [ 3, 3 ] ],
							'large_screen' => [ [ 970, 550 ], [ 970, 250 ], [ 970, 90 ], [ 728, 90 ], [ 300, 250 ], [ 3, 3 ] ],
						],
						'targeting' => [
							'tf' => $tf_slot,
							'pos' => $slot_name,
							'location' => $slot_name,
						],
					]
				);
				?>
			</section>
		<?php endwhile; ?>
	<?php endif; ?>
</main>
<?php
get_footer();
