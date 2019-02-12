<?php

class Marquee_Metaboxes {
	const LINK_META            = '_marquee_link';
	const FEATURED_META        = '_marquee_featured';
	const FEATURED_META_SLOT_2 = '_marquee_featured_2';
	const FEATURED_META_SLOT_3 = '_marquee_featured_3';
	const CONTENT_ID_META      = '_marquee_content_id';
	const HOMEPAGE_SLOT_SLUG   = 'homepage-marquee-slot-js';
	const HOMEPAGE_SLOT_FILE   = '../js/homepage_slot_selector.js';

	public static $meta_forms = array(
		'marquee_options' => array(
			'#label'                   => 'Marquee Options',
			self::LINK_META            => array(
				'#type'  => 'text',
				'#class' => 'widefat',
				'#label' => 'Marquee Link',
			),
			self::FEATURED_META        => array(
				'#type'        => 'checkbox',
				'#checked'     => 'Yes',
				'#label'       => 'Featured on Homepage?',
				'#class'       => 'homepage_slots',
				'#description' => 'Check here to set the first slot content of home page',
			),
			self::FEATURED_META_SLOT_2 => array(
				'#type'        => 'checkbox',
				'#checked'     => 'Yes',
				'#label'       => 'HOME PAGE SLOT 2',
				'#class'       => 'homepage_slots',
				'#description' => 'Check here to set the second slot content of home page',
			),
			self::FEATURED_META_SLOT_3 => array(
				'#type'        => 'checkbox',
				'#checked'     => 'Yes',
				'#label'       => 'HOME PAGE SLOT 3',
				'#class'       => 'homepage_slots',
				'#description' => 'Check here to set the third slot content of home page',
			),
			self::CONTENT_ID_META      => array(
				'#type'        => 'text',
				'#class'       => 'widefat',
				'#label'       => 'Content ID',
				'#description' => 'Content ID of the post, collection or recipe is required in order for this same content to not appear in another slot on the archive page.',
			),

		),
	);

	public static function admin_enqueue_scripts( $hook ) {
		if ( 'post-new.php' === $hook || 'post.php' === $hook ) {
			wp_register_script( self::HOMEPAGE_SLOT_SLUG, plugin_dir_url( __FILE__ ) . self::HOMEPAGE_SLOT_FILE, array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( self::HOMEPAGE_SLOT_SLUG );
		}
	}

	/**
	 * Register meta boxes for this CPT
	 */
	public static function register_metaboxes( $post ) {
		add_action( 'post_submitbox_misc_actions', array( 'Marquee_Metaboxes', 'add_metabox_nonce' ) );
		add_action( 'attachment_submitbox_misc_actions', array( 'Marquee_Metaboxes', 'add_metabox_nonce' ) );
		foreach ( WP_Forms_API::get_elements( self::$meta_forms ) as $key => $form ) {
			$form += array(
				'#context'  => 'normal',
				'#priority' => 'default',
			);
			add_meta_box(
				"marquee-$key",
				$form['#label'],
				array( 'Marquee_Metaboxes', 'render_form' ),
				'marquee',
				$form['#context'],
				$form['#priority'],
				$form
			);
		}
	}

	/**
	 * Add a nonce for saving all metadata
	 */
	public function add_metabox_nonce() {
		global $post;
		if ( $post->post_type === 'marquee' ) {
			wp_nonce_field( 'marquee_meta_nonce', 'marquee_meta_nonce', false, true );
		}
	}

	public static function save_metaboxes_values( $post_id ) {
		if ( ! self::check_nonce() ) {
			return;
		}

		// Only save custom meta for the Marquee post type.
		$post = get_post( $post_id );
		if ( $post->post_type !== 'marquee' ) {
			return;
		}

		// @todo: Remove dependency on WP_Forms_API.
		WP_Forms_API::process_form( self::$meta_forms, $meta_values );
		if ( empty( $meta_values ) ) {
			return;
		}

		foreach ( $meta_values as $meta_key => $meta_value ) {
			// Delete truly empty values
			if ( is_null( $meta_value ) || $meta_value === '' || $meta_value === false ) {
				delete_post_meta( $post->ID, $meta_key );
			} else {
				update_post_meta( $post->ID, $meta_key, $meta_value );
			}
		}
	}

	private static function check_nonce() {
		$nonce_key = 'marquee_meta_nonce';
		if ( isset( $_POST[ $nonce_key ] ) && wp_verify_nonce( $_POST[ $nonce_key ], $nonce_key ) ) {
			unset( $_POST[ $nonce_key ] );
			return true;
		}
	}

	public static function render_form( $post, $args ) {
		$form   = $args['args'];
		$post   = get_post( $post );
		$meta   = get_post_custom( $post->ID );
		$values = array();
		foreach ( $meta as $meta_key => $meta_values ) {
			$values[ $meta_key ] = maybe_unserialize( $meta_values[0] );
		}
		echo WP_Forms_API::render_form( $form, $values );
	}
}

add_action( 'admin_enqueue_scripts', array( 'Marquee_Metaboxes', 'admin_enqueue_scripts' ) );
add_action( 'add_meta_boxes_marquee', array( 'Marquee_Metaboxes', 'register_metaboxes' ) );
add_action( 'save_post', array( 'Marquee_Metaboxes', 'save_metaboxes_values' ) );
