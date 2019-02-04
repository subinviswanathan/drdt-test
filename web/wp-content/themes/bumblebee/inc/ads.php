<?php
/**
 * DFP Ads
 *
 * @package bumblebee
 */

wp_register_script( 'ad-stack', get_template_directory_uri() . '/js/ad-stack.js', [], '1.0.0', true );

/**
 * Renders an ad placeholder
 *
 * @param int     $id        The ad ID.
 * @param mixed[] $options   Ad rendering options.
 *      string ['sizes']     Comma separated ad sizes, in the form of WxH. Example: 320x250,300x600.
 *      string ['slot-name'] Ad slot name.
 *      string ['targeting'] Custom key-value pairs for targeting.
 */
function bumblebee_render_ad( $id, $options ) {
	$ad_class = $options['class'] ?? '';
	unset( $options['class'] );
	$classes = [ 'ad', $ad_class ];
	$class   = implode( ' ', $classes );

	/**
	 * Filters ad options.
	 *
	 * @param array $options Ad rendering options
	 *    $options = [
	 *      'sizes'        => (string) Comma separated ad sizes, in the form of WxH. Example: 320x250,300x600.
	 *      'slot-name'    => (string) Ad slot name.
	 *      'targeting'    => (string) Custom key-value pairs for targeting.
	 *    ]
	 */
	$ad_options = apply_filters( 'ad_options', $options );

	$markup = '<div id="' . $id . '" class="' . $class . '" ' . join( ' ', bumblebee_ad_options_to_attributes( $ad_options ) ) . '></div>';

	// Make sure we load the Ad Stack if we're rendering ads.
	wp_enqueue_script( 'ad-stack' );
	echo wp_kses(
		$markup,
		[
			'div' => [
				'id'                       => [],
				'class'                    => [],
				'data-ad-slot-name'        => [],
				'data-ad-sizes'            => [],
				'data-ad-targeting'        => [],
				'data-ad-responsive-sizes' => [],
			],
		]
	);
}
/**
 * Transforms ad options into data attributes.
 *
 * @param mixed[] $ad_options   Ad rendering options.
 */
function bumblebee_ad_options_to_attributes( $ad_options ) {
	return array_map(
		function( $key ) use ( $ad_options ) {
			$att_name = 'data-ad-' . str_replace( [ '_', ' ' ], '-', $key );
			if ( is_bool( $ad_options[ $key ] ) ) {
				return $ad_options[ $key ] ? $att_name : '';
			} elseif ( is_array( $ad_options[ $key ] ) ) {
				return $att_name . '=\'' . wp_json_encode( $ad_options[ $key ] ) . '\'';
			}
			return $att_name . '="' . $ad_options[ $key ] . '"';
		},
		array_keys( $ad_options )
	);
}


/**
 * Prefix every ad slot name with the Ad Unit Path
 *
 * @param mixed[] $ad_options   Ad rendering options.
 */
function bumblebee_add_slot_name_prefix( $ad_options ) {
	// @todo: make this filterable and read proper tokens for ad unit 2, 3 and 4.
	$ad_unit_path_2 = apply_filters( 'ad_unit_path_2', 'homepage' );
	$ad_unit_path_3 = apply_filters( 'ad_unit_path_3', 'homepage' );
	$ad_options['slot-name'] = '/' . $ad_unit_path_2 . '/' . $ad_unit_path_3 . '/' . $ad_options['slot-name'];
	return $ad_options;
}
add_filter( 'ad_options', 'bumblebee_add_slot_name_prefix', 10, 1 );

/**
 * Remove Ad Stack (for ?variant=noads).
 */
function bumblebee_maybe_remove_ad_stack() {
	$variant = get_query_var( 'variant' );
	if ( 'noads' === $variant ) {
		wp_dequeue_script( 'ad-stack' );
	}
}
add_action( 'wp_footer', 'bumblebee_maybe_remove_ad_stack', 1 );

/**
 * Adds the `variant` query var.
 *
 * @param array $vars List of query variables.
 * @see query_vars
 */
function bumblebee_add_variant_query_var( $vars ) {
	$vars[] .= 'variant';
	return $vars;
}
add_filter( 'query_vars', 'bumblebee_add_variant_query_var' );
