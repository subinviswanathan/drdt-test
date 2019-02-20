<?php
/** Hero template

 * @package bumblebee
 */

$home_hero_analytics = 'data-analytics-metrics=\'{"name":"' . get_the_title() . '","module":"content navigation","position":"marquee"}\'';

?>
<div class="pure-u-1 pure-u-sm-3-5">
	<div class="hero-container ">
		<div class="pure-g ">
			<a <?php echo $home_hero_analytics; ?> class="pure-u-sm-2-5 hero-image">
				<?php echo get_the_post_thumbnail( $post_id, 'homepage-featured-big', array( 'class' => 'pure-img' ) ); ?>
			</a>
			<div class="pure-u-sm-3-5">
				<div class="hero-text">
					<?php the_title( '<h3 class="entry-title"><a data-analytics-metrics=\'{"name":"' . get_the_title() . '","module":"content navigation","position":"marquee"}\' href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
					<div class="hero-excerpt"><?php the_excerpt(); ?> </div>
					<a data-analytics-metrics='{"name":"<?php echo get_the_title(); ?>","module":"content navigation","position":"marquee"}' href="<?php echo esc_url( get_permalink() ); ?> " class="read-more">read more</a>
				</div>
			</div>
		</div>
	</div>
</div>
