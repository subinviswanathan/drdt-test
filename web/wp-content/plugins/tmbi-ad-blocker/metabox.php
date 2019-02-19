<?php

function tmbi_ad_blocker_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function tmbi_ad_blocker_add_meta_box() {
	$supported_post_types = get_post_types_by_support( 'ad_blocker' );
	foreach ( $supported_post_types as $post_type ) {
		add_meta_box(
			'ad_blocker-ad-blocker',
			__( 'Ad Blocker', 'ad_blocker' ),
			'tmbi_ad_blocker_html',
			$post_type,
			'side',
			'low'
		);
	}
}
add_action( 'add_meta_boxes', 'tmbi_ad_blocker_add_meta_box' );

// @todo: get the checkboxes via `ad_services` filter.
function tmbi_ad_blocker_html( $post ) {
	wp_nonce_field( '_ad_blocker_nonce', 'ad_blocker_nonce' );
	$ad_services = apply_filters( 'ad_services', [] );
	?>

	<?php if ( empty( $ad_services ) ) : ?>
		<p>There are no registered ad services.</p>
	<?php else : ?>
		<p>Select which ads and third party services to block on this page.</p>
		<?php foreach ( $ad_services as $key => $name ) : ?>
			<p>
				<input type="checkbox" name="block_<?php echo $key; ?>" id="block_tb" value="1" <?php echo ( tmbi_ad_blocker_get_meta( 'block_' . $key ) === '1' ) ? 'checked' : ''; ?>>
				<label for="block_tb"><?php echo $name; ?></label>
			</p>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php
}

function tmbi_ad_blocker_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['ad_blocker_nonce'] ) || ! wp_verify_nonce( $_POST['ad_blocker_nonce'], '_ad_blocker_nonce' ) ) {
		return;
	}
	// @todo: Provide more specific permissions
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$ad_services = apply_filters( 'ad_services', [] );
	foreach ( $ad_services as $key => $name ) {
		if ( isset( $_POST['block_' . $key] ) ) {
			update_post_meta( $post_id, 'block_' . $key, esc_attr( $_POST['block_' . $key] ) );
		} else {
			update_post_meta( $post_id, 'block_' . $key, null );
		}
	}
}
add_action( 'save_post', 'tmbi_ad_blocker_save' );

/*
	Usage: tmbi_ad_blocker_get_meta( 'block_tb' )
	Usage: tmbi_ad_blocker_get_meta( 'block_nt' )
*/
