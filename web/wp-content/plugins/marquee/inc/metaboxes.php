<?php

class Marquee_Metaboxes extends WP_CPT_Base {
	const post_type            = 'marquee';
	const VERSION              = '0.2.3';
	const LINK_META            = '_marquee_link';
	const FEATURED_META        = '_marquee_featured';
	const FEATURED_META_SLOT_2 = '_marquee_featured_2';
	const FEATURED_META_SLOT_3 = '_marquee_featured_3';
	const CONTENT_ID_META      = '_marquee_content_id';
	const HOMEPAGE_SLOT_SLUG   = 'homepage-marquee-slot-js';
	const HOMEPAGE_SLOT_FILE   = '../js/homepage_slot_selector.js';

	// Form name for Nonce and fields
	const form_name = 'marquee-fields';

	public $meta_forms = array(
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

	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts( $hook ) {
		if ( 'post-new.php' === $hook || 'post.php' === $hook ) {
			wp_register_script( self::HOMEPAGE_SLOT_SLUG, plugin_dir_url( __FILE__ ) . self::HOMEPAGE_SLOT_FILE, array( 'jquery' ), self::VERSION, true );
			wp_enqueue_script( self::HOMEPAGE_SLOT_SLUG );
		}
	}
}
Marquee_Metaboxes::instance();
