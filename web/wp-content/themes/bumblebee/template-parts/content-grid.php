<?php
?>
<div class="pure-u-1 pure-u-sm-1-3">
<div class="single-recipe">
		<?php bumblebee_post_thumbnail( 'grid-thumbnail' ); ?>
		<div class="recipe-content">
			<?php the_title( '<h5 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h5>' ); ?>
		</div>

</div>
</div>