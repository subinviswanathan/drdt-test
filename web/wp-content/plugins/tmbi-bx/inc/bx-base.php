<?php

/**
 * Created by PhpStorm.
 * User: MJ
 * Date: 3/28/2017
 * Time: 1:10 PM
 */
class BX_Base {
	const FILE_SPEC         = __DIR__;
	const LOCALIZED_LABEL   = 'rd_bx';
	const LOADER_SCRIPT     = 'js/smart-tag.js';
	const LOADER_LABEL      = 'fhm-bx-loader';
	const SCRIPT_ID         = 'default';
	const DEPENDS           = '';
	static public $version;

	public static function set_version( $version ) {
		static::$version = $version;
	}

	public static function render_bx_load_failure() {
		print( '<!-- bounce exchange script has failed to load -->' . PHP_EOL );
	}

	public static function render_bx_is_blocked() {
		print( '<!-- Bounce Exchange has been blocked -->' . PHP_EOL );
	}

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	public static function enqueue_scripts() {
		wp_register_script(
			static::LOADER_LABEL,
			self::get_asset_url( static::LOADER_SCRIPT ),
			static::DEPENDS,
			static::$version,
			static::IN_FOOTER
		);
		$localized_data = array(
			'script_id' => static::SCRIPT_ID,
		);
		wp_localize_script( static::LOADER_LABEL, static::LOCALIZED_LABEL, $localized_data );
		wp_enqueue_script( static::LOADER_LABEL );
	}

}
