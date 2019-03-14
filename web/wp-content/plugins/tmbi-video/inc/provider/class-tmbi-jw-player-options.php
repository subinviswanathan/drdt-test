<?php

/**
 * Retrieve this value with:
 * $jwplayer_options = get_option( 'jwplayer_settings' ); // Array of All Options
 * $player_id = $jwplayer_options['player_id']; // Player ID
 * $dfp_preroll_vast_url = $jwplayer_options['dfp_preroll_vast_url']; // DFP preroll VAST URL
 */

/**
 * Class TMBI_JW_Player_Options
 */
class TMBI_JW_Player_Options {

	private $jwplayer_options;
	private $jwplayer_generic_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'jwplayer_options_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'jwplayer_options_page_init' ) );
	}

	public function jwplayer_options_add_plugin_page() {
		add_options_page(
			'JWPlayer Options', // page_title
			'JWPlayer', // menu_title
			'manage_options', // capability
			'jwplayer-options', // menu_slug
			array( $this, 'jwplayer_options_create_admin_page' ) // function
		);
	}

	public function jwplayer_options_create_admin_page() {
		$this->jwplayer_options         = get_option( 'jwplayer_settings' );
		$this->jwplayer_generic_options = get_option( 'jwplayer_generic_settings' ); ?>

		<div class="wrap">
			<h2>JWPlayer Options</h2>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'jwplayer_settings' );
				do_settings_sections( 'jwplayer-options-admin' );
				submit_button();
				?>
			</form>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'jwplayer_generic_settings' );
				do_settings_sections( 'jwplayer-generic-options-admin' );
				submit_button();
				?>
			</form>

		</div>
		<?php
	}

	public function jwplayer_options_page_init() {
		register_setting(
			'jwplayer_settings', // option_group
			'jwplayer_settings', // option_name
			array( $this, 'sanitized_input_array' ) // sanitize_callback
		);

		add_settings_section(
			'jwplayer_options_setting_section', // id
			'Settings', // title
			array( $this, 'jwplayer_options_section_info' ), // callback
			'jwplayer-options-admin' // page
		);

		add_settings_field(
			'player_id', // id
			'Player ID', // title
			array( $this, 'player_id_callback' ), // callback
			'jwplayer-options-admin', // page
			'jwplayer_options_setting_section' // section
		);

		add_settings_field(
			'dfp_preroll_vast_url', // id
			'DFP preroll VAST URL', // title
			array( $this, 'dfp_preroll_vast_url_callback' ), // callback
			'jwplayer-options-admin', // page
			'jwplayer_options_setting_section' // section
		);

		add_settings_field(
			'jwp_vpaidmode ', // id
			'JWP Paid Mode', // title
			array( $this, 'jwp_vpaidmode_callback' ), // callback
			'jwplayer-options-admin', // page
			'jwplayer_options_setting_section' // section
		);

		add_settings_field(
			'jwp_requestTimeout ', // id
			'JWP Request Timeout', // title
			array( $this, 'jwp_requesttimeout_callback' ), // callback
			'jwplayer-options-admin', // page
			'jwplayer_options_setting_section' // section
		);

		add_settings_field(
			'jwp_vastLoadTimeout ', // id
			'JWP Vast Load Timeout', // title
			array( $this, 'jwp_vastloadtimeout_callback' ), // callback
			'jwplayer-options-admin', // page
			'jwplayer_options_setting_section' // section
		);

		add_settings_field(
			'jwp_loadVideoTimeout ', // id
			'JWP Load Video Timeout', // title
			array( $this, 'jwp_loadvideotimeout_callback' ), // callback
			'jwplayer-options-admin', // page
			'jwplayer_options_setting_section' // section
		);

		add_settings_field(
			'jwp_maxRedirects ', // id
			'JWP Max Redirects', // title
			array( $this, 'jwp_maxredirects_callback' ), // callback
			'jwplayer-options-admin', // page
			'jwplayer_options_setting_section' // section
		);

		add_settings_field(
			'jwp_preloadAds ', // id
			'JWP PreLoad Ads ', // title
			array( $this, 'jwp_preloadads_callback' ), // callback
			'jwplayer-options-admin', // page
			'jwplayer_options_setting_section' // section
		);

		add_settings_field(
			'jwp_headerBidding ', // id
			'JWP Header Bidding Timeout ', // title
			array( $this, 'jwp_headerbidding_callback' ), // callback
			'jwplayer-options-admin', // page
			'jwplayer_options_setting_section' // section
		);


		// JW Generic Settings.
		register_setting(
			'jwplayer_generic_settings', // option_group
			'jwplayer_generic_settings', // option_name
			array( $this, 'sanitized_input_array' ) // sanitize_callback
		);

		add_settings_section(
			'jwplayer_generic_options_setting_section', // id
			'JW Player - Generic Settings', // title
			array( $this, 'jwplayer_generic_options_section_info' ), // callback
			'jwplayer-generic-options-admin' // page
		);

		add_settings_field(
			'generic_jwp_autoplay', // id
			'Autoplay', // title
			array( $this, 'generic_jwp_autoplay_callback' ), // callback
			'jwplayer-generic-options-admin', // page
			'jwplayer_generic_options_setting_section' // section
		);

		add_settings_field(
			'generic_jwp_stickyplay', // id
			'StickyPlay', // title
			array( $this, 'generic_jwp_stickyplay_callback' ), // callback
			'jwplayer-generic-options-admin', // page
			'jwplayer_generic_options_setting_section' // section
		);

		add_settings_field(
			'generic_jwp_ads', // id
			'Ads', // title
			array( $this, 'generic_jwp_ads_callback' ), // callback
			'jwplayer-generic-options-admin', // page
			'jwplayer_generic_options_setting_section' // section
		);

		add_settings_field(
			'generic_jwp_mute', // id
			'Mute', // title
			array( $this, 'generic_jwp_mute_callback' ), // callback
			'jwplayer-generic-options-admin', // page
			'jwplayer_generic_options_setting_section' // section
		);

		add_settings_field(
			'generic_jwp_comscore', // id
			'Comscore', // title
			array( $this, 'generic_jwp_comscore_callback' ), // callback
			'jwplayer-generic-options-admin', // page
			'jwplayer_generic_options_setting_section' // section
		);

		add_settings_field(
			'generic_jwp_moat', // id
			'Moat', // title
			array( $this, 'generic_jwp_moat_callback' ), // callback
			'jwplayer-generic-options-admin', // page
			'jwplayer_generic_options_setting_section' // section
		);

	}

	/**
	 * Sanitizing user input from video setting text fields.
	 *
	 * @param array $input
	 *
	 * @return array
	 */
	public function sanitized_input_array( $input ) {
		$sanitized_value = array();

		foreach ( $input as $key => $value ) {
			$sanitized_value[ $key ] = sanitize_text_field( $value );
		}

		return $sanitized_value;
	}

	public function jwplayer_options_section_info() {
		// No description
	}

	public function jwplayer_generic_options_section_info() {
		print( 'Add Jw players default settings below' );
	}

	public function player_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="jwplayer_settings[player_id]" id="player_id" value="%s">',
			isset( $this->jwplayer_options['player_id'] ) ? esc_attr( $this->jwplayer_options['player_id'] ) : ''
		);
	}

	public function dfp_preroll_vast_url_callback() {
		printf(
			'<input class="regular-text" type="text" name="jwplayer_settings[dfp_preroll_vast_url]" id="dfp_preroll_vast_url" value="%s">',
			isset( $this->jwplayer_options['dfp_preroll_vast_url'] ) ? esc_attr( $this->jwplayer_options['dfp_preroll_vast_url'] ) : ''
		);
	}

	public function jwp_vpaidmode_callback() {
		$options = get_option( 'jwplayer_settings' );
		if ( empty( $options['jwp_vpaidmode'] ) || $options['jwp_vpaidmode'] === '' ) {
			$options['jwp_vpaidmode'] = 'enabled';
			update_option( 'jwplayer_settings', $options );
		}

		$values = array( 'disabled', 'enabled', 'insecure' );
		?>

		<select class="regular-text" name="jwplayer_settings[jwp_vpaidmode]" id="jwp_vpaidmode" >
			<option value="">Please select</option>
			<?php
			foreach ( $values as $val ) {
				printf(
					'<option value="%s" %s>%s</option>',
					$val,
					$options['jwp_vpaidmode'] === $val ? 'selected' : '',
					ucfirst( $val )
				);
			}
			?>
		</select>
		<?php
	}

	public function jwp_requesttimeout_callback() {
		$options = get_option( 'jwplayer_settings' );
		if ( empty( $options['jwp_requestTimeout'] ) ) {
			$options['jwp_requestTimeout'] = 20000;
			update_option( 'jwplayer_settings', $options );
		}

		printf(
			'<input class="regular-text" pattern="[0-9]{1,5}" title="Enter digits with length upto 5 " type="text" name="jwplayer_settings[jwp_requestTimeout]" id="jwp_requestTimeout" value="%s">',
			esc_attr( $options['jwp_requestTimeout'] )
		);
	}

	public function jwp_vastloadtimeout_callback() {
		$options = get_option( 'jwplayer_settings' );
		if ( empty( $options['jwp_vastLoadTimeout'] ) ) {
			$options['jwp_vastLoadTimeout'] = 12000;
			update_option( 'jwplayer_settings', $options );
		}

		printf(
			'<input class="regular-text" pattern="[0-9]{1,5}" title="Enter digits witlh length upto 5 " type="text" name="jwplayer_settings[jwp_vastLoadTimeout]" id="jwp_vastLoadTimeout" value="%s">',
			esc_attr( $options['jwp_vastLoadTimeout'] )
		);
	}

	public function jwp_loadvideotimeout_callback() {
		$options = get_option( 'jwplayer_settings' );
		if ( empty( $options['jwp_loadVideoTimeout'] ) ) {
			$options['jwp_loadVideoTimeout'] = 15000;
			update_option( 'jwplayer_settings', $options );
		}

		printf(
			'<input class="regular-text" pattern="[0-9]{1,5}" title="Enter digits with length upto 5 " type="text" name="jwplayer_settings[jwp_loadVideoTimeout]" id="jwp_loadVideoTimeout" value="%s">',
			esc_attr( $options['jwp_loadVideoTimeout'] )
		);
	}

	public function jwp_maxredirects_callback() {
		$options = get_option( 'jwplayer_settings' );
		if ( empty( $options['jwp_maxRedirects'] ) ) {
			$options['jwp_maxRedirects'] = 4;
			update_option( 'jwplayer_settings', $options );
		}

		printf(
			'<input class="regular-text" pattern="[0-9]{1,2}" title="Enter digits with length upto 2 " type="text" name="jwplayer_settings[jwp_maxRedirects]" id="jwp_maxRedirects" value="%s">',
			esc_attr( $options['jwp_maxRedirects'] )
		);
	}

	public function jwp_preloadads_callback() {
		$options = get_option( 'jwplayer_settings' );
		if ( empty( $options['jwp_preloadAds'] ) ) {
			$options['jwp_preloadAds'] = 'true';
			update_option( 'jwplayer_settings', $options );
		}

		$values = array( 'true', 'false' );
		?>
		<select class="regular-text" name="jwplayer_settings[jwp_preloadAds]" id="jwp_preloadAds" >
			<option value="">Please select</option>
			<?php
			foreach ( $values as $val ) {
				printf(
					'<option value="%s" %s>%s</option>',
					$val,
					$options['jwp_preloadAds'] === $val ? 'selected' : '',
					ucfirst( $val )
				);
			}
			?>
		</select>
		<?php
	}

	public function jwp_headerbidding_callback() {
		$options = get_option( 'jwplayer_settings' );
		if ( empty( $options['jwp_headerBidding'] ) ) {
			$options['jwp_headerBidding'] = 6000;
			update_option( 'jwplayer_settings', $options );
		}

		printf(
			'<input class="regular-text" pattern="[0-9]{1,5}" title="Enter digits with length upto 5 " type="text" name="jwplayer_settings[jwp_headerBidding]" id="jwp_headerBidding" value="%s">',
			esc_attr( $options['jwp_headerBidding'] )
		);
	}

	public function generic_jwp_stickyplay_callback() {

		$options = get_option( 'jwplayer_generic_settings' );
		if ( empty( $options['generic_jwp_stickyplay'] ) ) {
			$options['generic_jwp_stickyplay'] = 'true';
			update_option( 'generic_jwp_stickyplay', $options );
		}

		$values = array( 'true', 'false' );
		?>
		<select class="regular-text" name="jwplayer_generic_settings[generic_jwp_stickyplay]" id="generic_jwp_stickyplay" >
			<?php
			foreach ( $values as $val ) {
				printf(
					'<option value="%s" %s>%s</option>',
					$val,
					$options['generic_jwp_stickyplay'] === $val ? 'selected' : '',
					ucfirst( $val )
				);
			}
			?>
		</select>
		<?php
	}

	public function generic_jwp_autoplay_callback() {

		$options = get_option( 'jwplayer_generic_settings' );
		if ( empty( $options['generic_jwp_autoplay'] ) ) {
			$options['generic_jwp_autoplay'] = 'viewable';
			update_option( 'generic_jwp_autoplay', $options );
		}

		$values = array( 'viewable', 'true', 'false' );
		?>
		<select class="regular-text" name="jwplayer_generic_settings[generic_jwp_autoplay]" id="generic_jwp_autoplay" >
			<?php
			foreach ( $values as $val ) {
				printf(
					'<option value="%s" %s>%s</option>',
					$val,
					$options['generic_jwp_autoplay'] === $val ? 'selected' : '',
					ucfirst( $val )
				);
			}
			?>
		</select>
		<?php
	}

	public function generic_jwp_ads_callback() {

		$options = get_option( 'jwplayer_generic_settings' );
		if ( empty( $options['generic_jwp_ads'] ) ) {
			$options['generic_jwp_ads'] = 'true';
			update_option( 'generic_jwp_ads', $options );
		}

		$values = array( 'true', 'false' );
		?>
		<select class="regular-text" name="jwplayer_generic_settings[generic_jwp_ads]" id="generic_jwp_ads" >
			<?php
			foreach ( $values as $val ) {
				printf(
					'<option value="%s" %s>%s</option>',
					$val,
					$options['generic_jwp_ads'] === $val ? 'selected' : '',
					ucfirst( $val )
				);
			}
			?>
		</select>
		<?php
	}

	public function generic_jwp_mute_callback() {

		$options = get_option( 'jwplayer_generic_settings' );
		if ( empty( $options['generic_jwp_mute'] ) ) {
			$options['generic_jwp_mute'] = 'true';
			update_option( 'generic_jwp_mute', $options );
		}

		$values = array( 'true', 'false' );
		?>
		<select class="regular-text" name="jwplayer_generic_settings[generic_jwp_mute]" id="generic_jwp_mute" >
			<?php
			foreach ( $values as $val ) {
				printf(
					'<option value="%s" %s>%s</option>',
					$val,
					$options['generic_jwp_mute'] === $val ? 'selected' : '',
					ucfirst( $val )
				);
			}
			?>
		</select>
		<?php
	}

	public function generic_jwp_comscore_callback() {

		$options = get_option( 'jwplayer_generic_settings' );
		if ( empty( $options['generic_jwp_comscore'] ) ) {
			$options['generic_jwp_comscore'] = 'true';
			update_option( 'generic_jwp_comscore', $options );
		}

		$values = array( 'true', 'false' );
		?>
		<select class="regular-text" name="jwplayer_generic_settings[generic_jwp_comscore]" id="generic_jwp_comscore" >
			<?php
			foreach ( $values as $val ) {
				printf(
					'<option value="%s" %s>%s</option>',
					$val,
					$options['generic_jwp_comscore'] === $val ? 'selected' : '',
					ucfirst( $val )
				);
			}
			?>
		</select>
		<?php
	}

	public function generic_jwp_moat_callback() {

		$options = get_option( 'jwplayer_generic_settings' );
		if ( empty( $options['generic_jwp_moat'] ) ) {
			$options['generic_jwp_moat'] = 'true';
			update_option( 'generic_jwp_moat', $options );
		}

		$values = array( 'true', 'false' );
		?>
		<select class="regular-text" name="jwplayer_generic_settings[generic_jwp_moat]" id="generic_jwp_moat" >
			<?php
			foreach ( $values as $val ) {
				printf(
					'<option value="%s" %s>%s</option>',
					$val,
					$options['generic_jwp_moat'] === $val ? 'selected' : '',
					ucfirst( $val )
				);
			}
			?>
		</select>
		<?php
	}

}
