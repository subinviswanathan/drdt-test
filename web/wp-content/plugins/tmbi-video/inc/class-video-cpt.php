<?php

/**
 * Class Video_CPT
 */
class Video_CPT {
	// Text domain for Video CPT.
	const VIDEO_CPT_DOMAIN = 'video-post-type';

	// Name that used for Post Type singular name.
	const SINGULAR_NAME = 'Video';

	// Name that used for Post Type singular name.
	const PULURAL_NAME = 'Videos';

	// lowercase singular name for Post Type.
	const REST_BASE = 'video';

	// lowercase singular name for Post Type.
	const LW_POST_TYPE = 'video';

	/**
	 * Video_CPT constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_video_post_type' ), 0 );
	}

	/**
	 * Registering Video Post Type with necessary settings
	 */
	public function register_video_post_type() {

		/**
		 * Labels for video post type
		 */
		$labels = array(
			'name'               => self::PULURAL_NAME,
			'singular_name'      => self::SINGULAR_NAME,
			'menu_name'          => self::PULURAL_NAME,
			'name_admin_bar'     => self::SINGULAR_NAME,
			'archives'           => self::SINGULAR_NAME . ' Archives',
			'attributes'         => self::SINGULAR_NAME . ' Attributes',
			'parent_item_colon'  => 'Parent Item:',
			'all_items'          => 'All ' . self::PULURAL_NAME,
			'add_new_item'       => 'Add New ' . self::SINGULAR_NAME,
			'add_new'            => 'Add New',
			'new_item'           => 'New ' . self::SINGULAR_NAME,
			'edit_item'          => 'Edit ' . self::SINGULAR_NAME,
			'update_item'        => 'Update ' . self::SINGULAR_NAME,
			'view_item'          => 'View ' . self::SINGULAR_NAME,
			'view_items'         => 'View ' . self::PULURAL_NAME,
			'search_items'       => 'Search ' . self::SINGULAR_NAME,
			'not_found'          => 'Not found',
			'not_found_in_trash' => 'Not found in Trash',
		);

		/**
		 * Supported CMS fields
		 */
		$supports = array(
			'title',
			'author',
			'thumbnail',
			'editor',
			'excerpt',
		);

		/**
		 * Supported taxonomies
		 */
		$taxonomies = array(
			'category',
			'post_tag',
		);

		/**
		 * rewrite base
		 */
		$rewrite = array(
			'slug'       => self::REST_BASE,
			'with_front' => false,
			'feeds'      => true,
		);

		/**
		 * Video Post Type arguments
		 */
		$args = array(
			'label'               => self::SINGULAR_NAME,
			'description'         => self::SINGULAR_NAME . ' post type',
			'labels'              => $labels,
			'supports'            => $supports,
			'taxonomies'          => $taxonomies,
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-format-video',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
			'rest_base'           => self::REST_BASE,
		);

		// Calling WordPress native register post function
		register_post_type( self::REST_BASE, $args );
	}
}
