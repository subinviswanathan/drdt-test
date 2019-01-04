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
	<section class="sidebar pure-u-1 pure-u-lg-8-24"></section>
</section>

	<?php
	the_content();
	?>
</main>
	<?php
	get_footer();
	?>
