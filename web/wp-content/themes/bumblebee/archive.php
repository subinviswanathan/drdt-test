<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bumblebee
 */

get_header();
?>
<main class="archive-page">
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
	<?php
	if ( have_posts() ) :
	?>
		<section class="archive-content">
			<div class="archive-headings">
				<div class="breadcrumbs">
					<a href="#">JOBSITE TIPS</a> > <a href="#">BUSINESS</a>
				</div>
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="archive-description">', '</div>' );
				?>
			</div>

			<?php
			the_post();
			get_template_part( 'template-parts/archive/content', 'hero' );
			?>
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
		<?php $section_num = 0; ?>
		<?php while ( have_posts() ) : ?>
			<?php $section_num++; ?>
			<section class="archive-content">
				<div class="pure-g recipes">
					<?php for ( $i = 0; $i < 8; $i++ ) : ?>
					<?php if ( ( $wp_query->current_post + 1 ) !== ( $wp_query->post_count ) ) : ?>
					<?php the_post(); ?>
					<?php get_template_part( 'template-parts/archive/content', 'grid' ); ?>
					<?php if ( 3 === $i ) { ?>
					</div><div class="pure-g recipes">
					<?php } ?>
					<?php endif; ?>
					<?php endfor; ?>
				</div>
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
		<?php endwhile; ?>
	<?php endif; ?>
</main>
<?php
get_footer();
