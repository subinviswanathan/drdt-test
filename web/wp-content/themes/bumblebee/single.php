<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package bumblebee
 */

get_header();
?>

<div class="site-container">
	<main class="site-content">
		<div class="pure-g pre-article-ad text-center">
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
		</div>
		<div class="pure-g opening-content">
	<?php
	if ( have_posts() ) :
		/* Start the Loop */
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'post' );
		endwhile;
	endif;
?>
	<div class="pure-u-md-7-24 pure-u-lg-7-24 pure-u-xl-7-24 hide-on-mobile">
		<div class="sidebar-ad-wrapper text-center">
			<aside class="sidebar">
				<?php
				bumblebee_render_ad(
					uniqid( 'ad' ),
					[
						'slot-name' => 'rail' . ( 1 === $section_num ? 'top' : 2 === $section_num ? 'middle' : 'scroll' ),
						'sizes'     => '300x250,300x600',
					]
				);
				?>
			</aside>
		</div>
	</div>
		</div>
	</main>
</div>
	<?php

	get_footer();
	?>
