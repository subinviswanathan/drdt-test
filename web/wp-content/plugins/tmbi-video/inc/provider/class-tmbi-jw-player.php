<?php

/**
 * Class TMBI_JW_Player
 */
class TMBI_JW_Player {
	private static $jw_videos = array();

	public function __construct() {
		add_action( 'acf/init', array( __CLASS__, 'register_acf_video_fields' ) );
		add_filter( 'tmbi_video_shortcode_default_attributes', array( __CLASS__, 'add_shortcode_attributes' ) );
		add_filter( 'tmbi_video_shortcode', array( __CLASS__, 'get_markup' ), 11, 2 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_jw_scripts' ) );
		add_action( 'wp_footer', array( __CLASS__, 'send_jw_data_to_js' ) );
	}

	public static function register_jw_scripts() {
		$jwplayer_options = get_option( 'jwplayer_settings' );
		$player_id        = $jwplayer_options['player_id'];

		if ( ! $player_id ) {
			return;
		}

		wp_register_script(
			'jwplayer_api',
			'https://content.jwplatform.com/libraries/' . $player_id . '.js',
			array(),
			VIDEO_PLUGIN_VER,
			true
		);

		wp_register_script(
			'jwplayer_moat',
			'https://z.moatads.com/jwplayerplugin0938452/moatplugin.js',
			array(),
			VIDEO_PLUGIN_VER,
			true
		);

		wp_register_script(
			'jwplayer_js_player',
			plugins_url( '/js/jw-player.js', dirname( __DIR__ ) ),
			array( 'jquery', 'jwplayer_api', 'jwplayer_moat' ),
			VIDEO_PLUGIN_VER,
			true
		);

		wp_enqueue_script( 'jwplayer_js_player' );
		wp_enqueue_script( 'jquery-inview' );
	}

	public static function register_acf_video_fields() {
		acf_add_local_field_group(
			array(
				'key'      => 'group_rd_video_jwplayer',
				'title'    => 'JW Player',
				'fields'   => array(
					array(
						'key'   => 'field_rd_video_jwplayer_video_id',
						'label' => 'Video ID',
						'name'  => 'jwplayer_video_id',
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

	public static function add_shortcode_attributes( $atts ) {
		$default_options = get_option( 'jwplayer_generic_settings' );
		$jwplayer_atts   = array(
			'jwplayer_video_id' => null,
			'autoplay'          => $default_options['generic_jwp_autoplay'],
			'mute'              => $default_options['generic_jwp_mute'],
			'ads'               => $default_options['generic_jwp_ads'],
			'stickyplay'        => $default_options['generic_jwp_stickyplay'],
			'comscore'          => $default_options['generic_jwp_comscore'],
			'moat'              => $default_options['generic_jwp_moat'],
		);
		return( array_merge( $atts, $jwplayer_atts ) );
	}


	public static function get_markup( $markup, $options ) {
		if ( ! empty( $options['jwplayer_video_id'] ) ) {
			$markup = self::render_jwplayer( $options );
		}
		return $markup;
	}


	/**
	 * DM Has a lot of players. We want to use Widgets, but as of now
	 * they don't work and are not recommended since will be refactored.
	 * In the meantime we use Jukebox and Single JS Players
	 * @param $options
	 *
	 * @return string
	 */
	private static function render_jwplayer( $options ) {
		wp_enqueue_script( 'jwplayer_api' );
		wp_enqueue_script( 'jwplayer_js_player' );

		$player_id = md5( serialize( $options ) );
		$video_id  = $options['jwplayer_video_id'];

		$video_autoplay = 'viewable';
		$stickyplay     = 'true';

		if ( isset( $options['stickyplay'] ) ) {
			$stickyplay = $options['stickyplay'];
		}

		if ( isset( $options['autoplay'] ) ) {
			$video_autoplay = $options['autoplay'];
		}

		$comscore = 'true';
		if ( isset( $options['comscore'] ) ) {
			$comscore = $options['comscore'];
		}
		if ( $comscore === 'true' ) {
			wp_enqueue_script( 'streaming-jwplayer' );
		}

		$mute = true;
		if ( isset( $options['mute'] ) ) {
			$mute = $options['mute'];
		}

		$ads = 'true';
		if ( isset( $options['ads'] ) ) {
			$ads = $options['ads'];
		}

		$style = 'style="background: url(https://content.jwplatform.com/thumbs/' . $video_id . '.jpg) no-repeat center center; min-width: 320px; min-height: 240px;"';
		// Store al the JW Player videos, for later use with
		// wp_localize_script to pass the values to JS
		// Tried this hack to fix the duplicate video ids which is generated from WPDT-69793//
		$html   = '<div class="jwplayer" id="' . $player_id . '" ' . $style . '></div>';
		$native = 'false';
		if ( get_page_template_slug() === 'video-article-native-ads.php' ) {
			$native = 'true';
		}

		self::$jw_videos[] = array(
			'player_id' => $player_id,
			'video_id'  => $video_id,
			'sticky'    => $stickyplay,
			'autoplay'  => $video_autoplay,
			'mute'      => $mute,
			'comscore'  => $comscore,
			'ads'       => $ads,
			'native'    => $native,
		);

		return $html;
	}

	public static function send_jw_data_to_js() {
		$jwplayer_settings    = get_option( 'jwplayer_settings', false );
		$advertising_settings = array(
			'requestTimeout'   => $jwplayer_settings['jwp_requestTimeout'] ? : 20000,
			'vastLoadTimeout'  => $jwplayer_settings['jwp_vastLoadTimeout'] ? : 12000,
			'loadVideoTimeout' => $jwplayer_settings['jwp_loadVideoTimeout'] ? : 15000,
			'maxRedirects'     => $jwplayer_settings['jwp_maxRedirects'] ? : 4,
			'vpaidmode'        => $jwplayer_settings['jwp_vpaidmode'] ? : 'enabled',
			'preloadAds'       => $jwplayer_settings['jwp_preloadAds'] ? : true,

			'bids'             => array(
				'settings' => array(
					'mediationLayerAdServer' => get_option( 'mediationLayerAdServer', 'dfp' ),
					'bidTimeout'             => $jwplayer_settings['jwp_headerBidding'] ? $jwplayer_settings['jwp_headerBidding'] : 6000,
				),
				'bidders'  => array(
					array(
						'name' => get_option( 'mediationLayerAdServerName', 'SpotX' ),
						'id'   => get_option( 'mediationLayerAdServerId', 235491 ),
					),
				),
			),
		);

		wp_localize_script(
			'jwplayer_js_player',
			'jw_settings',
			array(
				'jw_videos'            => self::$jw_videos,
				'advertising_settings' => $advertising_settings,
			)
		);
	}

}
