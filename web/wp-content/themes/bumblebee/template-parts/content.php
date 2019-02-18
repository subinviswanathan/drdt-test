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
			<?php
			$category = get_the_category();
			if ( ! empty( $category[0] ) ) {
				$category      = $category[0];
				$category_name = $category->name;
				$category_link = get_category_link( $category->term_id );
			}
			?>
			<a href="<?php echo esc_url( $category_link ); ?>" class="post-category-label">
				<?php echo esc_attr( $category->name ); ?>
			</a>
			<h1 class="post-title"> <?php echo esc_html( get_the_title() ); ?> </h1>
			<?php bumblebee_posted_by(); ?>
			<div class="pure-g social-menu-mobile">
				<?php get_template_part( 'template-parts/social-share', 'none' ); ?>
			</div>
			<div class="post-body">
				<?php the_content(); ?>
			</div>
		</div>
		<?php if ( is_active_sidebar( 'after-content' ) ) : ?>
			<?php dynamic_sidebar( 'after-content' ); ?>
		<?php endif; ?>
	</div>
</div>
