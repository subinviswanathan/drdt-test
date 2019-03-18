<?php
/**
 * TMBI Salesforece DMP
 *
 * @package TMBI Salesforece DMP
 * This is for the author details.
 */

/**
 * Function to retrieve the author details
 *
 * @return array()
 */
function get_the_authors() {
	global $post;
	$post_id = $post->ID;
	// Read the Post Author's Name.
	// If Co-Author plugin is active get the list of authors else get the author.
	$author_list = array();
	if ( function_exists( 'get_coauthors' ) ) {
		$authors = get_coauthors( $post_id );
		foreach ( $authors as $author ) {
			$author_list[] = array(
				'author_image' => get_author_image(
					$author->ID,
					$author->type,
					array(
						'size'   => 75,
						'scheme' => 'https',
					)
				),
				'author_name'  => $author->display_name,
				'author_desc'  => $author->description,
				'author_url'   => get_author_posts_url( $author->ID, $author->user_nicename ),
			);
		}
	}
	if ( count( $author_list ) === 0 ) {
		$creator_display_name = get_the_author_meta( 'display_name' );
		$author_desc          = get_the_author_meta( 'description' );
		$author_url           = get_author_posts_url( get_the_author_meta( 'ID' ) );
		$author_list[]        = array(
			'author_image' => get_author_image( get_the_author_meta( 'ID' ), 'wp_author', '75' ),
			'author_name'  => $creator_display_name,
			'author_desc'  => $author_desc,
			'author_url'   => $author_url,
		);
	}
	return $author_list;
}
/**
 * Function to retrieve the author image
 *
 * @param number $author_id the author ID.
 * @param string $author_type the author type.
 * @param string $size the size.
 * @return string
 */
function get_author_image( $author_id, $author_type, $size ) {
	if ( isset( $author_type ) && 'guest-author' === $author_type ) {
		if ( ! has_post_thumbnail( $author_id ) ) {
			return get_avatar_url(
				$author_id,
				array(
					'size'   => $size,
					'scheme' => 'https',
				)
			);
		}
		$thumbnail_size   = array( $size, $size );
		$author_thumbnail = get_the_post_thumbnail_url( $author_id, $thumbnail_size );
		if ( $author_thumbnail ) {
			return $author_thumbnail;
		}
	} else {
		return get_avatar_url(
			$author_id,
			array(
				'size'   => $size,
				'scheme' => 'https',
			)
		);
	}
	return '';
}
