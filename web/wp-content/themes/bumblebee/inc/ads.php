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
	if ( class_exists( 'Ad_Stack' ) ) {
		Ad_Stack::bumblebee_render_ad( $id, $options );
	}
}



