<?php
/**
 * DFP Ads
 *
 * @package bumblebee
 */

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


