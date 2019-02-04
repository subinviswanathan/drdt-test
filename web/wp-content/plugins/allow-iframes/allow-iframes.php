<?php
/**
 * Plugin Name: Allow Iframes
 * Description: Allow editors to use iframes, without shortcodes
 * Plugin URI: https://facundofarias.com.ar/
 * Author: Facundo Farias
 * Author URI: https://facundofarias.com.ar/
 * Version: 2.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package drdt
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'wp_kses_allowed_html', 'allow_iframes' );

/**
 * Allow iframes as a "safe" tag.
 *
 * Users that can edit posts can use iframes as a valid (safe) tag.
 *
 * @uses wp_kses_allowed_html.
 *
 * @param array $allowedposttags Context to judge allowed tags by.
 * @return array.
 */
function allow_iframes( $allowedposttags ) {
	if ( current_user_can( 'edit_posts' ) ) {
		$allowedposttags['iframe'] = array(
			'align'        => true,
			'width'        => true,
			'height'       => true,
			'frameborder'  => true,
			'name'         => true,
			'src'          => true,
			'id'           => true,
			'class'        => true,
			'style'        => true,
			'scrolling'    => true,
			'marginwidth'  => true,
			'marginheight' => true,
		);
	}

	return $allowedposttags;
}
