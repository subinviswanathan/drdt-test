<?php

class Marquee_Functions {
	/**
	 * get marquee content
	 * @return \WP_Post|bool
	 */
	public static function get_marquee_content( $slot = false ) {
		if ( $slot == 1 ) {
			$key = '_marquee_featured';
		} else {
			$key = '_marquee_featured_' . $slot;
		}
		$args = array(
			'post_type' => 'marquee',
			'posts_per_page' => 1,
			'post_status' => 'publish',
			'orderby' => 'modified',
		);

		if ( is_home() ) {

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
		if ( $marquee_post = get_posts( $args ) ) {
			return $marquee_post[0];
		}

		return false;
	}
}
add_filter( 'get_marquee_content' , array( 'Marquee_Functions', 'get_marquee_content' ) );
