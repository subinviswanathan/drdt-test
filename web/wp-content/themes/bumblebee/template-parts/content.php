<?php
/** Content template

 * @package bumblebee
 */

?>
<div class="pure-g opening-content">
	<div class="pure-u-md-3-24 pure-u-lg-3-24 pure-u-xl-3-24 hide-on-mobile">
		<div class="social-share social-menu-desktop">
			<?php get_template_part( 'template-parts/social-share', 'none' ); ?>
		</div>
	</div>
	<div class="pure-u-sm-1 pure-u-md-14-24 pure-u-lg-14-24 pure-u-xl-14-24">
		<div class="post-content">
			<h1 class="post-title"> <?php echo get_the_title(); ?> </h1>
			<div class="pure-g social-menu-mobile">
						<?php get_template_part( 'template-parts/social-share', 'none' ); ?>
			</div>
			<div class="post-body">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
