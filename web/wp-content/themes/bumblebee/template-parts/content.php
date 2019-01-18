<?php
/** Content template

 * @package bumblebee
 */

$category = get_the_category();
$category = $category[0];
?>
<div class="pure-g opening-content">
	<div class="pure-u-md-3-24 pure-u-lg-3-24 pure-u-xl-3-24 hide-on-mobile">
		<div class="social-share social-menu-desktop">
			<?php get_template_part( 'template-parts/social-share', 'none' ); ?>
		</div>
	</div>
	<div class="pure-u-sm-1 pure-u-md-14-24 pure-u-lg-14-24 pure-u-xl-14-24">
		<div class="post-content">
			<a href="#"class="post-category-label"><?php echo esc_attr( $category->name ); ?></a>
			<h1 class="post-title"> <?php echo esc_html( get_the_title() ); ?> </h1>
			<div class="byline">
				<img src="http://cpt.test.rda.net/wp-content/uploads/sites/9/2018/10/Blue-Makeup-MAC-1200x675.jpg" class="author-image" alt="img"></img>
				<span class="author-name">Amrita</span>
			</div>
			<div class="pure-g social-menu-mobile">
						<?php get_template_part( 'template-parts/social-share', 'none' ); ?>
			</div>
			<div class="post-body">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
