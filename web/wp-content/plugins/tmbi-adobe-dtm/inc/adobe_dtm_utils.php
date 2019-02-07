<?php
/**
 * Save original publish date to post types
 */

class Adobe_DTM_Utils {
	const PUBLISH_META_NAME = 'original_publish_date';

	public $registered_post_type = array(
		'post',
		'page',
		'listicle',
		'collection',
		'nicestplace',
		'quiz',
		'video',
		'joke',
		'marquee',
		'project',
		'tip',
	);

	public function __construct() {
		add_action( 'admin_init', array( $this, 'adtm_init' ) );
	}

	public function adtm_init() {
		//add_action( 'transition_post_status', array( $this, 'post_status_transition' ), 10, 3 );
		//get_post_types()
		$this->registered_post_type = get_post_types();
		add_action( 'publish_to_draft', array( $this, 'move_publish_to_draft' ) );
		foreach ( $this->registered_post_type as $post_type ) {
			add_action( 'publish_' . $post_type, array( $this, 'publishing_post' ), 10, 2 );
		}
	}

	/*
	 * Updating the meta tag when publishing the post. it will insert 'self::PUBLISH_META_NAME' at once
	 *
	 * @param int $post_id
	 * @param WP_Post $post post object
	 */
	public function publishing_post( $post_id, $post ) {
		if ( in_array( $post->post_type, $this->registered_post_type ) && ! get_post_meta( $post_id, self::PUBLISH_META_NAME, true ) ) {
			update_post_meta( $post->ID, self::PUBLISH_META_NAME, $post->post_date );
		}
	}

	/*
	 * Updating the meta tag when change the post form publish to draft. it will insert 'self::PUBLISH_META_NAME' at once
	 *
	 * @param int $post_id
	 * @param WP_Post $post post object
	 */
	public function move_publish_to_draft( $post ) {
		if ( in_array( $post->post_type, $this->registered_post_type )
			&& ! get_post_meta( $post->ID, self::PUBLISH_META_NAME, true )
		) {

			update_post_meta( $post->ID, self::PUBLISH_META_NAME, $post->post_date );
		}
	}

	//TODO update while transitioning post status.
	// WP shows modified_date in get_the_date() when post in draft status.
	// WP show post_date in get_the_date when post in publish status
	public function post_status_transition( $new_status, $old_status, $post ) {
		if ( $new_status === 'publish' && $old_status !== 'publish' && in_array( $post->post_type, $this->registered_post_type ) ) {
			update_post_meta( $post->ID, self::PUBLISH_META_NAME, get_the_date( 'Y-m-d' ) );
		}
	}

	public static function get_original_published_date( $post_id = null ) {
		global $post;
		if ( ! $post_id && isset( $post ) ) {
			$post_id = $post->ID;
		}

		if ( ! $post_id ) {
			return false;
		}

		$original_pub_date = get_post_meta( $post_id, self::PUBLISH_META_NAME, true );
		if ( $original_pub_date ) {
			$date = date_create( $original_pub_date );
			return date_format( $date, 'Y-m-d' );
		}

		if ( ! isset( $post ) ) {
			$post = get_post( $post_id );
		}
		$date = date_create( $post->post_date );
		return date_format( $date, 'Y-m-d' );
	}

}
