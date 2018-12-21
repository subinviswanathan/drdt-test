<?php
/** Featured template

 * @package bumblebee
 */

?>
<div class="pure-u-1 pure-u-sm-1-5">
	<div class="single-recipe single-item">
		<?php bumblebee_post_thumbnail( 'grid-thumbnail' ); ?>
		<div class="recipe-content">
			<?php the_title( '<h5 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>
		</div>

	</div>
</div>
