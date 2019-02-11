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

	/**
	 * get actual post of marquee content
	 * @param $marquee_post
	 *
	 * @return mixed
	 */
	public static function get_actual_post( $marquee_post ) {
		$support_post_type = array( 'post', 'listicle', 'collection', 'project' );

		$content_id_meta = get_post_meta( $marquee_post, '_marquee_content_id', true );

		if ( $content_id_meta ) {
			$actual_post = get_post( $content_id_meta );
		} else {
			$marquee_permalink = get_permalink( $marquee_post->ID );
			$parsed_url = parse_url( $marquee_permalink );

			$path = $parsed_url['path'];
			$path = explode( '?', $path )[0];
			$path = trim( $path, '/' );
			$path = preg_replace( '~(\/\d+$)~', '', $path );
			$path = preg_replace( '~(\/view-all$)~', '', $path );
			$parse_path = explode( '/', $path );
			$path = end( $parse_path );
			$actual_post = get_page_by_path( $path, OBJECT, $support_post_type );
		}
		return $actual_post;
	}
}
add_filter( 'get_marquee_content' , array( 'Marquee_Functions', 'get_marquee_content' ) );
