<?php
/** Archive Featured template

 * @package bumblebee
 */

?>
<li class=" featured-container">
	<a href="">
		<?php bumblebee_post_thumbnail( 'grid-thumbnail' ); ?>
		<div class="recipe-content">
			<?php the_title( '<h5 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>
		</div>
	</a>
</li>
