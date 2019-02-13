<?php

class PrebidJS_Settings {
	private static $options;
	const PREBIDJS_SLUG = 'prebid_js';

	/**
	 * Init.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'admin_settings' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_page_init' ), 20 );
	}

	/**
	 * Admin Settings.
	 */
	public static function admin_settings() {
		add_options_page(
			'PrebidJS settings',
			'PrebidJS settings',
			'manage_options',
			self::PREBIDJS_SLUG,
			array( __CLASS__, 'show_prebidjs_admin_page' )
		);
	}

	/**
	 * Admin Page.
	 */
	public static function show_prebidjs_admin_page() {
		?>
		<div class="wrap">
			<h1>PREBIDJS Settings</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'prebidjs_options' );
				do_settings_sections( 'prebidjs_settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Admin Page.
	 */
	public static function admin_page_init() {
		register_setting(
			'prebidjs_options',
			self::PREBIDJS_SLUG
		);
		self::$options = get_option( 'prebid_js' );

		add_settings_section(
			'prebidjs_settings_main', // id.
			'Prebid.js - General Settings', // title.
			array( __CLASS__, 'info' ), // callback.
			'prebidjs_settings' // page.
		);

		add_settings_field(
			'prebidjs_granularity', // id.
			'Granularity', // title.
			array( __CLASS__, 'prebidjs_granularity_callback' ), // callback.
			'prebidjs_settings', // page.
			'prebidjs_settings_main' // section.
		);

	}

	/**
	 * Heading for page.
	 */
	public function info() {
		echo '<p>Prebid.js - General settings</p>';
	}

	/**
	 * Granularity.
	 */
	public function prebidjs_granularity_callback() {
		?>
			<select name="prebid_js[prebidjs_granularity]">
				<option value="" <?php selected( self::$options['prebidjs_granularity'] ?? '', '' ); ?>>Default</option>
				<option value="low" <?php selected( self::$options['prebidjs_granularity'] ?? '', 'low' ); ?>>Low</option>
				<option value="med" <?php selected( self::$options['prebidjs_granularity'] ?? '', 'med' ); ?>>Med</option>
				<option value="high" <?php selected( self::$options['prebidjs_granularity'] ?? '', 'high' ); ?>>High</option>
				<option value="auto" <?php selected( self::$options['prebidjs_granularity'] ?? '', 'auto' ); ?>>Auto</option>
				<option value="dense" <?php selected( self::$options['prebidjs_granularity'] ?? '', 'dense' ); ?>>Dense</option>
			</select>
		<?php
		echo '<p><small>Make sure it matches your DFP settings!</small></p>';
	}

}
add_action( 'init', array( 'PrebidJS_Settings', 'init' ) );
