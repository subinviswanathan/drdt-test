<?php

class PrebidJS_AppNexus_Settings {
	private static $options;

	/**
	 *  Init.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'admin_init_page' ), 20 );
	}

	/**
	 *  Admin page.
	 */
	public function admin_init_page() {
		self::$options = get_option( 'prebid_js' );

		add_settings_section(
			'ad_stack_prebidjs_appnexus_settings', // id.
			'Prebid.js - AppNexus Settings', // title.
			array( __CLASS__, 'info' ), // callback.
			'prebidjs_settings' // page.
		);

		add_settings_field(
			'appnexus_enabled', // id.
			'Enable AppNexus', // title.
			array( __CLASS__, 'appnexus_enabled_callback' ), // callback.
			'prebidjs_settings', // page.
			'ad_stack_prebidjs_appnexus_settings' // section.
		);
	}

	/**
	 *  Heading for page.
	 */
	public function info() {
		echo '<p>Prebid.js - Configure AppNexus integration</p>';
	}

	/**
	 *  Input value for appnexus.
	 */
	public function appnexus_enabled_callback() {
		?>
			<input type="checkbox" name="prebid_js[appnexus_enabled]" value="1" <?php checked( self::$options['appnexus_enabled'] ?? 0, 1 ); ?>/>
		<?php
	}

}
add_action( 'init', array( 'PrebidJS_AppNexus_Settings', 'init' ) );
