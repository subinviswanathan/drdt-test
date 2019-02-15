<?php
/** Archive Featured template

 * @package bumblebee
 */

$archive_hero_analytics = 'data-analytics-metrics=\'{"name":"' . get_the_title() . '","module":"content navigation","position":"carousel"}\'';


?>
<li class=" featured-container">
	<a href="">
		<?php bumblebee_post_thumbnail( 'grid-thumbnail', 'carousel', 'content navigation' ); ?>
		<div class="recipe-content">
			<?php the_title( '<h5 class="entry-title"><a ' . $archive_hero_analytics . ' href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>
		</div>
	</a>
</li>
