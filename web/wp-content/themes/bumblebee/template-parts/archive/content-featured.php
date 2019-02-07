<?php
/** Archive Featured template

 * @package bumblebee
 */

$archive_hero_analytics = 'data-analytics-metrics=\'{"link_name":"' . get_the_title() . '","link_module":"content navigation","link_pos":"carousel"}\'';


?>
<li class=" featured-container">
	<a href="">
		<?php bumblebee_post_thumbnail( 'grid-thumbnail', 'carousel', 'content navigation' ); ?>
		<div class="recipe-content">
			<?php the_title( '<h5 class="entry-title"><a ' . $archive_hero_analytics . ' href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>
		</div>
	</a>
</li>
