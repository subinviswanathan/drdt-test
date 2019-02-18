<?php
/**
 * Plugin Name: TMBI Taboola
 * Version: 1.0.0
 * Description: Integrate Taboola recirc modules
 * Author: Facundo Farias
 * Author URI: https://facundofarias.com.ar
 * Text Domain: taboola
 */

require_once 'taboola-widget.php';
require_once 'settings.php';

add_action( 'init', 'taboola_init' );

/**
 * Initialize Taboola plugin
 */
function taboola_init() {
	add_action( 'wp_enqueue_scripts', 'taboola_enqueue_scripts', 10, 1 );
}

/**
 * Register a widget to display Taboola modules
 */
function taboola_register_widget() {
	register_widget( 'Taboola_Widget' );
}
add_action( 'widgets_init', 'taboola_register_widget' );

/**
 * Enqueue localized Taboola scripts
 *
 * The loader takes two arguments: script (with the full path of the Taboola JS, unique per account id),
 * and a list of modules to render Taboola into.
 *
 * @todo: modules should be widgets.
 */
function taboola_enqueue_scripts() {
	wp_register_script( 'taboola_loader', plugin_dir_url( __FILE__ ) . 'js/taboola_loader.js', array(), '1.0.0', true );
	$tmbi_taboola_settings = get_option( 'tmbi_taboola_settings' );
	$taboola_publisher_id  = ! empty( $tmbi_taboola_settings['taboola_publisher_id'] ) ? $tmbi_taboola_settings['taboola_publisher_id'] : '';
	if ( empty( $taboola_publisher_id ) ) {
		return;
	}
	$taboola_widget_id = 'thumbnails-a'; // @todo: make this configurable per-widget.
	wp_localize_script(
		'taboola_loader',
		'tmbi_taboola',
		array(
			'script' => '//cdn.taboola.com/libtrc/' . $taboola_publisher_id . '/loader.js',
		)
	);
	wp_enqueue_script( 'taboola_loader' );
}

add_action( 'wp_footer', 'taboola_remove_if_blocked', 1 );
/**
 * Remove Taboola if it's blocked via querystring param.
 *
 * @todo: decouple from this plugin.
 * @todo: support for specific script blocking.
 * @todo: don't print widget content if Taboola is blocked.
 */
function taboola_remove_if_blocked() {
	$variant = get_query_var( 'variant' );
	if ( 'noads' === $variant ) {
		wp_dequeue_script( 'taboola_loader' );
	}
}
