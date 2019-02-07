<?php
/** Featured template

 * @package bumblebee
 */

?>
<div class="pure-u-1 pure-u-sm-1-5">
	<div class="single-recipe single-item">
		<?php bumblebee_post_thumbnail( 'homepage-featured-small', 'marquee', 'content navigation' ); ?>
		<div class="recipe-content">
			<?php the_title( '<h5 class="entry-title"><a data-analytics-metrics=\'{"link_name":"' . get_the_title() . '","link_module":"content navigation","link_pos":"marquee"}\' href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>
		</div>

	</div>
</div>
