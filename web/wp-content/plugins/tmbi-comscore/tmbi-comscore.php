<?php
/**
 * Plugin Name: TMBI Comscore
 * Description: Adds Comscore tags to the website
 * Author: Facundo Farias
 * Author URI: https://facundofarias.com.ar
 * Version: 1.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tmbi-comscore
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'tmbi_comscore_register_scripts' );
add_action( 'wp_footer', 'tmbi_comscore_print_noscript' );
add_filter( 'amp_post_template_analytics', 'tmbi_comscore_amp', 11 );

function tmbi_comscore_register_scripts() {
	/*
	@todo: only purpose of this script is to call yet another script
	(https://sb.scorecardresearch.com/beacon.js) and push to an array.
	Consider inlining it to reduce unnecessary network requests.
	*/
	wp_register_script(
		'comscore-mmx',
		plugin_dir_url( __FILE__ ) . 'js/comscore-mmx.js',
		[],
		'1.0.0',
		true
	);
	wp_localize_script(
		'comscore-mmx',
		'comscore_vars',
		array(
			'c1' => 2,
			'c2' => tmbi_comscore_get_id(),
		)
	);
	wp_enqueue_script( 'comscore-mmx' );
	wp_register_script(
		'streaming-jwplayer',
		'https://sb.scorecardresearch.com/c2/plugins/streamingtag_plugin_jwplayer.js',
		[ 'comscore-mmx' ],
		'1.0.0',
		true
	);
}

function tmbi_comscore_print_noscript() {
	printf( '<noscript><img src="%s" height="1" width="1" alt="*"></noscript>', 'https://sb.scorecardresearch.com/p?c1=2&c2=' . tmbi_comscore_get_id() . '&cv=2.0&cj=1' );
}

function tmbi_comscore_amp( $analytics ) {
	if ( ! is_array( $analytics ) ) {
		$analytics = [];
	}
	if ( ! empty( $analytics['amp-comscore-analytics'] ) ) {
		return $analytics;
	}
	$analytics['amp-comscore-analytics'] = array(
		'type'        => 'comscore',
		'attributes'  => array(),
		'config_data' => array(
			'vars' => array(
				'c2' => tmbi_comscore_get_id(),
			),
		),
	);
	return $analytics;
}

function tmbi_comscore_get_id() {
	// @todo: Make this an option
	return '6034767';
}
