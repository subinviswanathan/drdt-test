<?php
/** Archive hero template

 * @package bumblebee
 */

$archive_hero_analytics = 'data-analytics-metrics=\'{"name":"' . get_the_title() . '","module":"content navigation","position":"marquee"}\'';

?>
<div class="pure-g hero-container">
	<a <?php echo $archive_hero_analytics; ?> href="<?php esc_url( get_permalink() ); ?>" class="pure-u-sm-2-5 hero-image">
		<?php echo get_the_post_thumbnail( $post_id, 'homepage-featured-big', array( 'class' => 'pure-img' ) ); ?>
	</a>
	<div class="pure-u-sm-3-5">
		<div class="hero-text">
			<?php the_title( '<h3 class="entry-title"><a ' . $archive_hero_analytics . ' href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
			<div class="hero-excerpt"><?php the_excerpt(); ?></div>
		</div>
	</div>
</div>
