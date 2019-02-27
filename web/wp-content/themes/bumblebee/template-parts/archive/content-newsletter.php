<?php
/** Archive Newsletter template

 * @package bumblebee
 */


$bg_image = get_theme_mod( 'bumblebee_archive_nl_bg_image' );
?>
<div class="full-width-nl" style="background: url('<?php echo $bg_image; ?>') no-repeat; background-size:cover;background-position:center;">
	<div class="nl-container">
		<?php newsletter_module(); ?>
	</div>
</div>
