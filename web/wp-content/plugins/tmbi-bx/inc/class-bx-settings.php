<?php
/**
 * Bounce Xchange Settings
 *
 * @package     Bounce Xchange Settings
 *  This is for the settings page of header bidder.
 */

/**
 *  Class Bounce Xchange.
 */
class BX_Settings {
	/**
	 * This will have the value of the script id
	 *
	 * @var String
	 */
	public static $options;

	/**
	 *  Init.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'bx_page_init' ), 20 );
	}

	/**
	 *  Admin Page Init.
	 */
	public static function bx_page_init() {
		self::$options = get_option( 'ad_stack' );
		add_settings_section(
			'ad_stack_bx_xchange_settings', // id.
			'Bounce Exchange - General Settings', // title.
			array( __CLASS__, 'info' ), // callback.
			'ad-stack-admin' // page.
		);

		add_settings_field(
			'bx_xchange_script_id', // id.
			'Script ID', // title.
			array( __CLASS__, 'bx_xchange_callback' ), // callback.
			'ad-stack-admin', // page.
			'ad_stack_bx_xchange_settings' // section.
		);
	}

	/**
	 * Print section.
	 */
	public function info() {
		echo '<p>Bounce Exchange Script ID</p>';
	}

	/**
	 * Callback function for getting the js from cms.
	 */
	public function bx_xchange_callback() {
		printf(
			'<input class="regular-text" type="text" name="ad_stack[bx_xchange_script_id]" id="bx_xchange_script_id" value="%s">',
			isset( self::$options['bx_xchange_script_id'] ) ? esc_attr( self::$options['bx_xchange_script_id'] ) : ''
		);
	}
}

add_action( 'init', array( 'BX_Settings', 'init' ) );
