<?php

class PrebidJS_Rubicon_Settings {
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
			'ad_stack_prebidjs_rubicon_settings', // id.
			'Prebid.js - Rubicon Settings', // title.
			array( __CLASS__, 'info' ), // callback.
			'prebidjs_settings' // page.
		);

		add_settings_field(
			'rubicon_account_id', // id.
			'Account ID', // title.
			array( __CLASS__, 'rubicon_account_id_callback' ), // callback.
			'prebidjs_settings', // page.
			'ad_stack_prebidjs_rubicon_settings' // section.
		);

		add_settings_field(
			'rubicon_site_id', // id.
			'Site ID', // title.
			array( __CLASS__, 'rubicon_site_id_callback' ), // callback.
			'prebidjs_settings', // page.
			'ad_stack_prebidjs_rubicon_settings' // section.
		);

		add_settings_field(
			'rubicon_atf_zone_id', // id.
			'ATF Zone ID', // title.
			array( __CLASS__, 'rubicon_atf_zone_id_callback' ), // callback.
			'prebidjs_settings', // page.
			'ad_stack_prebidjs_rubicon_settings' // section.
		);

		add_settings_field(
			'rubicon_btf_zone_id', // id.
			'BTF Zone ID', // title.
			array( __CLASS__, 'rubicon_btf_zone_id_callback' ), // callback.
			'prebidjs_settings', // page.
			'ad_stack_prebidjs_rubicon_settings' // section.
		);
	}

	/**
	 *  Heading for page.
	 */
	public function info() {

		echo '<p>Prebid.js - Configure Rubicon integration</p>';
	}

	/**
	 *  Input value for account_id.
	 */
	public function rubicon_account_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="prebid_js[rubicon_account_id]" id="rubicon_account_id" value="%s">',
			isset( self::$options['rubicon_account_id'] ) ? esc_attr( self::$options['rubicon_account_id'] ) : ''
		);
	}

	/**
	 *  Input value for siteid.
	 */
	public function rubicon_site_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="prebid_js[rubicon_site_id]" id="rubicon_site_id" value="%s">',
			isset( self::$options['rubicon_site_id'] ) ? esc_attr( self::$options['rubicon_site_id'] ) : ''
		);
	}

	/**
	 *  Input value for atf zone.
	 */
	public function rubicon_atf_zone_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="prebid_js[rubicon_atf_zone_id]" id="rubicon_atf_zone_id" value="%s">',
			isset( self::$options['rubicon_atf_zone_id'] ) ? esc_attr( self::$options['rubicon_atf_zone_id'] ) : ''
		);
	}

	/**
	 *  Input value for btf zone.
	 */
	public function rubicon_btf_zone_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="prebid_js[rubicon_btf_zone_id]" id="rubicon_btf_zone_id" value="%s">',
			isset( self::$options['rubicon_btf_zone_id'] ) ? esc_attr( self::$options['rubicon_btf_zone_id'] ) : ''
		);
	}

}
add_action( 'init', array( 'PrebidJS_Rubicon_Settings', 'init' ) );
