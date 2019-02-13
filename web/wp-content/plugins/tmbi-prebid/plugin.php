<?php
/**
 * Plugin Name: TMBI Prebid
 * Description: Integrates Prebid.js with TMBI Ad Stack
 * Plugin URI: https://rd.com
 * Author: Facundo Farias
 * Author URI: https://facundofarias.com.ar
 * Version: 1.0.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: prebidjs
 * Network: false
 */


require_once 'inc/settings.php';
require_once 'inc/rubicon/settings.php';
require_once 'inc/openx/settings.php';
require_once 'inc/appnexus/settings.php';

class Prebid_JS {
	const VERSION = '1.0.0';
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 10, 1 );
	}

	public static function enqueue_scripts() {
		// @todo: Prebid.js needs to be built with the adapters we want (to avoid loading 200+ bidders that won't be used)
		wp_enqueue_script( 'wp-hooks-js', plugins_url( 'js/wp-js-hooks.js', __FILE__ ), array(), self::VERSION, true );
		wp_enqueue_script( 'prebidjs', plugins_url( 'js/prebid.js', __FILE__ ), array(), self::VERSION, true );
		$prebid_js_options = get_option( 'prebid_js', false );
		wp_register_script( 'tmbi-prebidjs', plugins_url( 'js/tmbi-prebid.js', __FILE__ ), array( 'prebidjs', 'wp-hooks-js' ), self::VERSION, true );
		wp_localize_script( 'tmbi-prebidjs', 'prebid_conf', $prebid_js_options );
		wp_enqueue_script( 'tmbi-prebidjs' );
	}
}
add_action( 'init', array( 'Prebid_JS', 'init' ) );
