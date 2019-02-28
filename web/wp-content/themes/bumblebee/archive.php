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
	// Protect against arbitrary paged values
	$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 28,
		'paged'          => $paged,
	);

	$the_query = new WP_Query( $args );

	query_posts( 'showposts=28' );

	if ( have_posts() ) {
		$intcounter = 0;

		while ( $the_query->have_posts() ) {

			$the_query->the_post();
			$intcounter++;

			switch ( $intcounter ) {
				case 1:
					$i = 0;
					while ( $the_query->have_posts() && $i++ < 1 ) :
						$the_query->the_post();
						get_template_part( 'template-parts/archive/content', 'hero' );
					endwhile;

					break;
				case 2:
					?>
					<ul class="featured-posts">
						<?php
						while ( $the_query->have_posts() && $i++ <= 6 ) :
							$the_query->the_post();
							get_template_part( 'template-parts/archive/content', 'featured' );
						endwhile;
						?>
					</ul>
					<?php
					break;
				case 3:
					?>
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
					<?php
					break;
				case 4:
					echo '<div class="pure-g recipes">';
					while ( $the_query->have_posts() && $i++ <= 15 ) :
						$the_query->the_post();
						get_template_part( 'template-parts/archive/content', 'grid' );
						endwhile;
					echo '</div>';
					break;
				case 5:
					?>
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
					<?php
					break;
				case 6:
					echo '<div class="pure-g recipes">';
					while ( $the_query->have_posts() && $i++ <= 26 ) :
						$the_query->the_post();
						get_template_part( 'template-parts/archive/content', 'grid' );
						endwhile;
					echo '</div>';
					break;
				case 7:
					echo '</section>';
					get_template_part( 'template-parts/archive/content', 'newsletter' );
					break;
				case 8:
					echo '<section class="archive-content"><div class="pure-g recipes">';
					while ( $the_query->have_posts() && $i++ <= 28 ) :
						$the_query->the_post();
						get_template_part( 'template-parts/archive/content', 'grid' );
						endwhile;
					echo '</div>';
					break;
			} // Switch

		} // While

	} // If
	?>
		<div class="pagination">
			<?php
			echo paginate_links(
				array(
					'format'    => 'page/%#%',
					'current'   => $paged,
					'total'     => $the_query->max_num_pages,
					'mid_size'  => 2,
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
