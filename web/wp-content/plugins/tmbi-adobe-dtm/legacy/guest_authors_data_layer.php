<?php

/**
 * Adds DTM data for Guest Authors
 *
 * @see https://readersdigest.atlassian.net/browse/WPDT-3395
 */

add_filter( 'dtm_data_layer', 'dtm_add_guest_authors_data', 11 );
function dtm_add_guest_authors_data( $data_layer ) {
	if ( is_single() && class_exists( 'CoAuthorsIterator' ) ) {
		global $post;
		$author_data = new CoAuthorsIterator( $post->ID );
		if ( $author_data->count() > 0 ) {
			$author_data->iterate();
			$author_names = '';
			$author_roles = '';
			do {
				$author_names .= $author_data->current_author->display_name . ',';
				if ( $author_data->current_author->type == 'guest-author' ) {
					$author_roles .= dtm_get_guest_roles( $author_data->current_author->ID );
				} elseif ( $author_data->current_author->roles ) {
					$author_roles .= implode( ', ', $author_data->current_author->roles ) . ',';
				}
			} while ( $author_data->iterate() );

			$data_layer['page.content.author']     = rtrim( $author_names, ',' );
			$data_layer['page.content.authorRole'] = rtrim( $author_roles, ',' );
		}
	}
	return $data_layer;
}

function dtm_get_guest_roles( $user_id ) {
	$coauthor_relation_id = get_post_meta( $user_id, 'cap-author_relation', true );
	$guest_relation_arr   = get_term_by( 'id', $coauthor_relation_id, 'guest_author_roles' );

	if ( ! empty( $guest_relation_arr->name ) ) {
		return trim( $guest_relation_arr->name ) . ',';
	} else {
		return ( 'Guest Author,' );
	}
}