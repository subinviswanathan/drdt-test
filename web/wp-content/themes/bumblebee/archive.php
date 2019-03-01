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
				'slot-name'        => 'prearticle',
				'targeting'        => [
					'pos'      => 'prearticle',
					'location' => 'top',
					'tf'       => 'atf',
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
	</section>
	<section class="archive-content">

		<?php
			// Hero post
		if ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/archive/content', 'hero' );
		}
		?>
		<ul class="featured-posts">
			<?php
				// Row of 5 posts
				$i = 0;
			while ( have_posts() && $i++ < 5 ) :
				the_post();
				get_template_part( 'template-parts/archive/content', 'featured' );
				endwhile;
			?>
		</ul>
		<section class="ad">
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
		</section>
		<div class="pure-g recipes">
			<?php
			$i = 0;
			// Row of 8 posts
			while ( have_posts() && $i++ < 8 ) :
				the_post();
				get_template_part( 'template-parts/archive/content', 'grid' );
			endwhile;
			?>
		</div>
		<section class="">
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
		</section>
		<div class="pure-g recipes">
			<?php
			$i = 0;
			// Row of 8 posts
			while ( have_posts() && $i++ < 8 ) :
				the_post();
				get_template_part( 'template-parts/archive/content', 'grid' );
			endwhile;
			?>
		</div>

		<?php get_template_part( 'template-parts/archive/content', 'newsletter' ); ?>

		<div class="pure-g recipes">
			<?php
			$i = 0;
			// Row of 8 posts
			while ( have_posts() && $i++ < 4 ) :
				the_post();
				get_template_part( 'template-parts/archive/content', 'grid' );
			endwhile;
			?>
		</div>

		<div class="pagination">
			<?php
			global $wp_query;

			$big = 999999999; // need an unlikely integer

			echo paginate_links(
				array(
					'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format'    => 'page/%#%',
					'current'   => max( 1, get_query_var( 'paged' ) ),
					'total'     => $wp_query->max_num_pages,
					'prev_text' => __( '&laquo; Prev Page' ),
					'next_text' => __( 'Next Page &raquo;' ),
				)
			);
			?>
		</div>
	</section>
</main>
<?php
get_footer();
