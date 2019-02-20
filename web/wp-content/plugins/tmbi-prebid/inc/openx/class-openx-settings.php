<?php
/**
 * OpenX Settings
 *
 * @package     OpenX Settings
 *  This is for the settings page of openx.
 */

/**
 *  Class OpenX Settings.
 */
class OpenX_Settings {

	/**
	 * This will have the value of the options
	 *
	 * @var String
	 */
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
			'ad_stack_prebidjs_openx_settings', // id.
			'Prebid.js - OpenX Settings', // title.
			array( __CLASS__, 'info' ), // callback.
			'prebidjs_settings' // page.
		);

		add_settings_field(
			'openx_del_domain', // id.
			'delDomain', // title.
			array( __CLASS__, 'openx_del_domain_callback' ), // callback.
			'prebidjs_settings', // page.
			'ad_stack_prebidjs_openx_settings' // section.
		);

	}

	/**
	 *  Heading for page.
	 */
	public function info() {
		echo '<p>Prebid.js - Configure OpenX integration</p>';
	}

	/**
	 *  Input value for openx.
	 */
	public function openx_del_domain_callback() {
		printf(
			'<input class="regular-text" type="text" name="prebid_js[openx_del_domain]" id="openx_del_domain" value="%s">',
			isset( self::$options['openx_del_domain'] ) ? esc_attr( self::$options['openx_del_domain'] ) : ''
		);
		echo '<p><small>Remove this value to disable OpenX integration</small></p>';
	}

}
add_action( 'init', array( 'OpenX_Settings', 'init' ) );
