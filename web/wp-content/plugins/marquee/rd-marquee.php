<?php
/*
Plugin Name: RD Marquee Post Type
Version: 0.2.3
Description: Adds a custom post type for marquee posts.
Author: Oomph, Inc.
Author URI: http://www.oomphinc.com
Text Domain: rdnap
*/

require_once 'legacy/wp-forms-api/wp-forms-api.php';
require_once 'legacy/wp-cpt-base/wp-cpt-base.php';
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
