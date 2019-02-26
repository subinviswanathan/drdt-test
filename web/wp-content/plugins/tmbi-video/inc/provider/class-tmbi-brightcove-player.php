<?php

/**
 * Class TMBI_Brightcove_Player
 */
class TMBI_Brightcove_Player {

	/**
	 * TMBI_Brightcove_Player constructor.
	 */
	public function __construct() {
		add_action( 'acf/init', array( __CLASS__, 'register_acf_video_fields' ) );
		add_filter( 'tmbi_video_shortcode_default_attributes', array( __CLASS__, 'add_shortcode_attributes' ) );
		add_filter( 'tmbi_video_shortcode', array( __CLASS__, 'get_markup' ), 10, 2 );
	}

	/**
	 * Registering ACF fields
	 */
	public static function register_acf_video_fields() {
		acf_add_local_field_group(
			array(
				'key'      => 'group_rd_video_brightcove',
				'title'    => 'Brightcove',
				'fields'   => array(
					array(
						'key'   => 'field_rd_video_brightcove_url',
						'label' => 'URL',
						'name'  => 'brightcove_url',
						'type'  => 'text',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'video',
						),
					),
				),
				'options'  => array(
					'position' => 'acf_after_title',
				),
			)
		);
	}

	/**
	 * Moving away from BC. Renders the player. Options controlled in their platform
	 */
	private static function render_brightcove( $options ) {
		$brightcove_url = $options['brightcove_url'];
		if ( get_page_template_slug() === 'video-article-native-ads.php' ) {
			return '<div class="video-player"><div><iframe src="' . $brightcove_url . '" allowfullscreen webkitallowfullscreen mozallowfullscreen></iframe></div></div>';
		}
		return '<div class="video-player" style="display: block; position: relative; max-width: 740px;"><div style="padding-top: 54.0541%;"><iframe src="' . $brightcove_url . '" allowfullscreen webkitallowfullscreen mozallowfullscreen style="width: 100%; height: 100%; position: absolute; top: 0px; bottom: 0px; right: 0px; left: 0px;"></iframe></div></div>';
	}

	/**
	 * @param $atts
	 *
	 * @return array
	 */
	public static function add_shortcode_attributes( $atts ) {
		$brightcove_atts = array(
			'brightcove_url' => null,
		);
		return array_merge( $atts, $brightcove_atts );
	}

	/**
	 * @param $markup
	 * @param $options
	 *
	 * @return string
	 */
	public static function get_markup( $markup, $options ) {
		if ( ! empty( $options['brightcove_url'] ) ) {
			$markup = self::render_brightcove( $options );
		}
		return $markup;
	}
}
