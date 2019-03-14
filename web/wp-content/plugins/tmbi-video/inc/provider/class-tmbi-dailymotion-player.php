<?php

/**
 * Class TMBI_Dailymotion_Player
 */
class TMBI_Dailymotion_Player {
	const PLAYER_JS_SLUG = 'dailymotion_js_player';

	private static $rd_dm_videos = array();

	public function __construct() {
		add_action( 'acf/init', array( __CLASS__, 'register_acf_video_fields' ) );
		add_filter( 'tmbi_video_shortcode_default_attributes', array( __CLASS__, 'add_shortcode_attributes' ) );
		add_filter( 'tmbi_video_shortcode', array( __CLASS__, 'get_markup' ), 11, 2 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_dm_scripts' ) );
		add_action( 'wp_footer', array( __CLASS__, 'send_dm_data_to_js' ) );
		add_filter( 'amp_post_template_data', array( __CLASS__, 'register_amp_video_scripts' ), 11 );
	}

	public static function register_dm_scripts() {
		wp_register_script(
			'dailymotion_api',
			'https://api.dmcdn.net/all.js',
			array(),
			VIDEO_PLUGIN_VER,
			true
		);

		wp_register_script(
			self::PLAYER_JS_SLUG,
			plugins_url( '/js/dm-player.js', dirname( __DIR__ ) ),
			array( 'jquery', 'dailymotion_api' ),
			VIDEO_PLUGIN_VER,
			true
		);

		wp_enqueue_script( self::PLAYER_JS_SLUG );
		wp_enqueue_script( 'jquery-inview' );

	}

	public static function register_acf_video_fields() {
		acf_add_local_field_group(
			array(
				'key'      => 'group_rd_video_dailymotion',
				'title'    => 'DailyMotion',
				'fields'   => array(
					array(
						'key'   => 'field_rd_video_dailymotion_video_id',
						'label' => 'Video ID',
						'name'  => 'dailymotion_video_id',
						'type'  => 'text',
					),
					array(
						'key'     => 'field_rd_video_dailymotion_is_playlist',
						'label'   => 'Is this a playlist ID?',
						'name'    => 'dailymotion_is_playlist',
						'type'    => 'true_false',
						'message' => 'Check this if the ID is for an entire playlist. Leave unchecked if it is for a single video.',
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
		$dailymotion_atts = array(
			'dailymotion_video_id'    => null,
			'dailymotion_is_playlist' => null,
		);
		return array_merge( $atts, $dailymotion_atts );
	}

	public static function get_markup( $markup, $options ) {
		if ( ! empty( $options['dailymotion_video_id'] ) ) {
			$markup = self::render_dailymotion( $options );
		}
		return $markup;
	}


	/**
	 * DM Has a lot of players. We want to use Widgets, but as of now
	 * they don't work and are not recommended since will be refactored.
	 * In the meantime we use Jukebox and Single JS Players
	 */
	private static function render_dailymotion( $options ) {
		$is_playlist = ( ! empty( $options['dailymotion_is_playlist'] ) ? $options['dailymotion_is_playlist'] : false );
		if ( $is_playlist ) {
			return self::render_dailymotion_playlist( $options );
		} else {
			return self::render_dailymotion_single( $options );
		}
	}

	/**
	 * Renders an iFrame and DM does the rest
	 */
	private static function render_dailymotion_jukebox( $options ) {
		$dailymotion_url  = 'http://www.dailymotion.com/widget/jukebox?list[]=/playlist/';
		$dailymotion_url .= $options['dailymotion_video_id'];
		$dailymotion_url .= '&autoplay=1&mute=1';

		$html  = '<div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">';
		$html .= '<iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%" frameborder="0" width="480" height="270" src="' . $dailymotion_url . '" allowfullscreen></iframe>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Uses the JS player, because we need to detect when the user scrolls
	 */
	private static function render_dailymotion_single( $options ) {
		wp_enqueue_script( 'dailymotion_api' );

		$player_id = uniqid( 'dmpl' );
		$video_id  = $options['dailymotion_video_id'];
		$style     = 'style="background: url(https://www.dailymotion.com/thumbnail/video/' . $video_id . ') no-repeat center center; min-width: 320px; min-height: 240px;"';

		$html = '<div class="dmplayer" id="' . $player_id . '" ' . $style . '></div>';
		// Store al the DailyMotion videos, for later use with
		// wp_localize_script to pass the values to JS
		self::$rd_dm_videos[] = array(
			'player_id' => $player_id,
			'video_id'  => $video_id,
		);
		return $html;
	}

	private static function render_dailymotion_playlist( $options ) {
		wp_enqueue_script( 'dailymotion_api' );

		$player_id = uniqid( 'dmpl' );
		$video_id  = $options['dailymotion_video_id'];

		$html = '<div class="dmplayer" id="' . $player_id . '"></div>';

		self::$rd_dm_videos[] = array(
			'player_id'   => $player_id,
			'video_id'    => $video_id,
			'is_playlist' => true,
			'videos'      => self::get_videos_from_playlist( $video_id ),
		);
		wp_enqueue_script( self::PLAYER_JS_SLUG );
		wp_enqueue_script( 'jquery-inview' );
		return $html;
	}

	private static function get_videos_from_playlist( $playlist_id ) {
		$transient_id = "rd_video_playlist_{$playlist_id}_videos";
		$response     = get_transient( $transient_id );

		if ( $response === false ) {
			$dm_playlist_videos_url = "https://api.dailymotion.com/playlist/{$playlist_id}/videos";
			$dm_response            = wp_remote_get( $dm_playlist_videos_url );

			if ( is_array( $dm_response ) ) {
				$body   = json_decode( $dm_response['body'], true );
				$list   = $body['list'];
				$videos = array_column( $list, 'id' );

				set_transient( $transient_id, $videos, 60 * 60 * 6 );

				$response = $videos;
			}
		}
		return $response;
	}

	public static function send_dm_data_to_js() {
		wp_localize_script( 'dailymotion_js_player', 'rd_dm_videos', self::$rd_dm_videos );
	}

}
