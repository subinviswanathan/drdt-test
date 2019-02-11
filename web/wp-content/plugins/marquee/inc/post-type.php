<?php
/**
 * Marquee Post Type
 *
 * Registers the Marquee post type if it doesn't already exist.
 *
 * @package marquee
 */

if ( ! function_exists('register_marquee_post_type') ) {
	function register_marquee_post_type() {
		$labels = array(
			'name'                  => _x( 'Marquees', 'Post Type General Name', 'marquee' ),
			'singular_name'         => _x( 'Marquee', 'Post Type Singular Name', 'marquee' ),
			'menu_name'             => __( 'Marquees', 'marquee' ),
			'name_admin_bar'        => __( 'Marquee', 'marquee' ),
			'archives'              => __( 'Marquee Archives', 'marquee' ),
			'attributes'            => __( 'Marquee Attributes', 'marquee' ),
			'parent_item_colon'     => __( 'Parent Marquee:', 'marquee' ),
			'all_items'             => __( 'All Marquees', 'marquee' ),
			'add_new_item'          => __( 'Add New Marquee', 'marquee' ),
			'add_new'               => __( 'Add New', 'marquee' ),
			'new_item'              => __( 'New Marquee', 'marquee' ),
			'edit_item'             => __( 'Edit Marquee', 'marquee' ),
			'update_item'           => __( 'Update Marquee', 'marquee' ),
			'view_item'             => __( 'View Marquee', 'marquee' ),
			'view_items'            => __( 'View Marquees', 'marquee' ),
			'search_items'          => __( 'Search Marquee', 'marquee' ),
			'not_found'             => __( 'Not found', 'marquee' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'marquee' ),
			'featured_image'        => __( 'Featured Image', 'marquee' ),
			'set_featured_image'    => __( 'Set featured image', 'marquee' ),
			'remove_featured_image' => __( 'Remove featured image', 'marquee' ),
			'use_featured_image'    => __( 'Use as featured image', 'marquee' ),
			'insert_into_item'      => __( 'Insert into item', 'marquee' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'marquee' ),
			'items_list'            => __( 'Marquees list', 'marquee' ),
			'items_list_navigation' => __( 'Marquees list navigation', 'marquee' ),
			'filter_items_list'     => __( 'Filter items list', 'marquee' ),
		);
		$args = array(
			'label'                 => __( 'Marquee', 'marquee' ),
			'description'           => __( 'A custom post type for Marquees.', 'marquee' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'revisions' ),
			'taxonomies'            => array( 'category', 'post_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 20,
			'menu_icon'             => 'dashicons-megaphone',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'rewrite'               => false,
			'capability_type'       => 'post',
			'show_in_rest'          => true,
		);
		register_post_type( 'marquee', $args );
	}
	add_action( 'init', 'register_marquee_post_type', 0 );

	/**
	 * Use a different permalink when specified, or no link at all. Do this
	 * because marquees are never supposed to have a permalink of their own.
	 */
	function marquee_get_permalink( $permalink, $post, $leavename, $sample ) {
		if ( $post->post_type === 'marquee' ) {
			$permalink = get_post_meta( $post->ID, '_marquee_link', true );
		}
		return $permalink;
	}
	add_filter( 'post_type_link', 'marquee_get_permalink', 10, 4 );
}
