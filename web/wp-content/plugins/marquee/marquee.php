<?php
/*
Plugin Name: Marquee
Version: 0.2.3
Description: Create marquees to highlight featured content.
Author: Facundo Farias
Author URI: https://facundofarias.com.ar
Text Domain: marquee
*/

// Explicitly declare dependencies
// @todo: Remove dependency on WP_Forms_API.
if ( ! class_exists( 'WP_Forms_API' ) ) {
	function marquee_metaboxes_missing_wp_forms_api_notice() {
		$message = sprintf(
			wp_kses(
				/* translators: Link to https://github.com/oomphinc/WP-Forms-API */
				__( 'The Marquee plugin requires the <a href="%s">WP Forms API</a> to work. Please install and activate it.', 'marquee' ),
				array( 'a' => array( 'href' => array() ) )
			),
			esc_url( 'https://github.com/oomphinc/WP-Forms-API' )
		);
		printf( '<div class="notice notice-error"><p>%2$s</p></div>', $message );
	}
	add_action( 'admin_notices', 'marquee_metaboxes_missing_wp_forms_api_notice' );
	return;
}

require_once 'inc/post-type.php';
require_once 'inc/metaboxes.php';

/**
 * get marquee content
 * @return \WP_Post|bool
 */
function marquee_get_post( $marquee_post, $slot = false ) {
	$args = array(
		'post_type'      => 'marquee',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'orderby'        => 'modified',
	);

	if ( is_home() ) {
		$key = '_marquee_featured';
		if ( $slot > 1 ) {
			$key .= '_' . $slot;
		}
		$args['meta_query'] = array(
			array(
				'key'     => $key,
				'compare' => 'EXISTS',
			),
		);
	} elseif ( is_category() ) {
		$args['cat'] = get_queried_object_id();
	} elseif ( is_tag() ) {
		$args['tag_id'] = get_queried_object_id();
	} else {
		return false;
	}

	$marquee_post = get_posts( $args );
	if ( ! empty( $marquee_post[0] ) ) {
		return $marquee_post[0];
	}

	return false;
}
add_filter( 'get_marquee_content', 'marquee_get_post', 10, 2 );

/**
 * Get a single marquee post.
 *
 * @param bool|int $slot The slot number.
 * @return \WP_Post|bool
 */
function get_marquee_post( $slot = false ) {
	return apply_filters( 'get_marquee_content', false, $slot );
}
