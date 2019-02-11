<?php
/**
 * Render a related posts meta box for a particular post type relationship.
 *
 * Relationships can either be 'post' (for many-to-many), 'parent' (for one-one,)
 * 'child' (for one-to-many)
 *
 * For 'post' or 'child' relations, only one post type will be honored.
 *
 * For 'parent' relations, the pool of potential parent posts will be chosen among
 * the given post types.
 *
 * For 'post' relations, they are related by the _related_{$post_type} meta key as well
 * as a taxonomy term _related-$post_id-$post_type. This way the relation is one-to-many
 * but can be resolved in the other direction easily.
 *
 * The widget UI driver code is found in js/admin-related.js
 *
 * @param $post_id - The post to relate to.
 * @param string $type - The relationship type. Either 'post', 'child', or 'parent'
 * @param mixed $post_types - The related post type (or post types, for 'parent')
 * @param mixed $reference - The related post_type, child IDs, or parent ID
 * @param bool $see_all – If true (Default), show 'see all' link as well
 */
function wcb_related_meta_box( $post_id, $type = 'parent', $post_types = array( 'post' ), $label = null, $see_all = true ) {
	$post_types = (array) $post_types;
	$post_type = $post_types[0];
	$ids = array();

	if ( ! isset( $label ) ) {
		$label = $post_type;
	}

	if ( 'child' === $type || 'post' === $type ) {
		$post_type_object = get_post_type_object( $post_type );

		if ( $post_type_object ) {
			$label = $post_type_object->labels->add_new_item;
		}

		if ( 'child' === $type ) {
			$query = new WP_Query( wp_parse_args( array(
				'post_parent' => $post_id,
				'post_type' => $post_type,
				'fields' => 'ids',
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'post_status' => 'any',
			) ) );

			$ids = $query->posts;
		} else if ( 'post' === $type ) {
			// Only be reflexive if the related post type specifies so.
			$ids = get_post_meta( $post_id, '_related_' . $post_type, true );
		}
	} else if ( is_numeric( $post_id ) ) {
		$post = get_post( $post_id );

		$label = __( 'Select...', 'wcb' );
		$ids = $post ? array( $post->post_parent ) : array();
	}

	// Feed in control parameters for the post list widget with data- attributes
?>
<div class="<?php echo esc_attr( 'post-list-container post-list-form post-list-count-0 post-list-' . $type ); ?>">
	<?php
	$form = array();
	$form[ 'post-list-select-' . implode( '-', $post_types ) . '-' . $type ] = array(
		'post-list-select' => array(
			'#type' => 'post_select',
			'#post_type' => $post_types,
			'#placeholder' => $label,
		),
	);
	echo WP_Forms_API::render_form( $form, $values );
	?>

	<div class="post-list"
		data-type="<?php echo esc_attr( $type ); ?>"
		data-list-name="<?php echo esc_attr( $post_type ); ?>">
		<input type="hidden" class="post-list-ids" name="<?php echo esc_attr( $type == 'parent' ? 'post_parent' : 'post_list_ids[' . $post_type . ']' ); ?>" value="<?php echo esc_attr( $ids ? join( ',', $ids ) : '' ); ?>" />
	</div>

<?php if ( $type != 'parent' ) {
	if ( $type == 'child' ) {
		$args = array(
			'post_parent' => $post_id,
			'post_type' => $post_type,
		);
	} else {
		$args = array(
			'related_to' => $post_id,
			'post_types' => $post_types,
		);
	}

	if ( $see_all ) {
?>
	<a href="<?php echo esc_url( admin_url( 'edit.php?' . http_build_query( $args ) ) ); ?>"><?php esc_html_e( 'See all...', 'wcb' ); ?></a>
	<?php }
} ?>
</div>
<?php
}
