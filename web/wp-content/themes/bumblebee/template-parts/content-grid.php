<?php
/** Content grid template

 * @package bumblebee
 */

?>
<div class="pure-u-1 pure-u-sm-1-3">
<div class="single-recipe">
		<?php bumblebee_post_thumbnail( 'grid-thumbnail', 'individual content well', 'content navigation' ); ?>
		<div class="recipe-content">
			<?php the_title( '<h5 class="entry-title"><a data-analytics-metrics=\'{"link_name":"' . get_the_title() . '","link_module":"content navigation","link_pos":"individual content well"}\' href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>
		</div>

</div>
</div>
