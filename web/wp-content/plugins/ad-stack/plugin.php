<?php
/*
Plugin Name: TMBI Ad Stack
Version 1.0.0
Description: Show ads on TMBI sites
Author: PLT Team
Text Domain: tmbi-ad-stack
*/

include( 'inc/ads-global-targeting.php' );

class AdStack {

	const VERSION = '1.0.0';

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_filter( 'ad_options', array( $this, 'bumblebee_add_slot_name_prefix' ), 10, 1 );
		add_filter( 'query_vars', array( $this, 'bumblebee_add_variant_query_var' ) );
		add_action( 'wp_footer', array( $this, 'bumblebee_maybe_remove_ad_stack' ), 1 );
	}


	public static function register_scripts() {
		wp_register_script( 'ad-stack', plugins_url() . '/ad-stack/js/ad-stack.js', [], self::VERSION, true );
	}

	/**
	 * Prefix every ad slot name with the Ad Unit Path
	 *
	 * @param mixed[] $ad_options Ad rendering options.
	 */
	public static function bumblebee_add_slot_name_prefix( $ad_options ) {
		// @todo: make this filterable and read proper tokens for ad unit 2, 3 and 4.
		$ad_unit_path_2          = apply_filters( 'ad_unit_path_2', 'homepage' );
		$ad_unit_path_3          = apply_filters( 'ad_unit_path_3', 'homepage' );
		$ad_options['slot-name'] = '/' . $ad_unit_path_2 . '/' . $ad_unit_path_3 . '/' . $ad_options['slot-name'];
		return $ad_options;
	}

	/**
	 * Remove Ad Stack (for ?variant=noads).
	 */
	public static function bumblebee_maybe_remove_ad_stack() {
		$variant = get_query_var( 'variant' );
		if ( 'noads' === $variant ) {
			wp_dequeue_script( 'ad-stack' );
		}
	}

	/**
	 * Adds the `variant` query var.
	 *
	 * @param array $vars List of query variables.
	 * @see query_vars
	 */
	public static function bumblebee_add_variant_query_var( $vars ) {
		$vars[] .= 'variant';
		return $vars;
	}
}

$adstack = new AdStack();