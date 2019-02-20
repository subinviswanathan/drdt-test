<?php

add_filter( 'dtm_data_layer_magazine_issue_data', 'dtm_magazine_issue_date', 10, 2 );

function dtm_get_magazine_issue_date( $post_id ) {
	if ( taxonomy_exists( 'mag_issue_date' ) ) {
		$magazine_issue = wp_get_object_terms( $post_id, 'mag_issue_date', true );
		if ( ! empty( $magazine_issue[0]->slug ) ) {
			return $magazine_issue[0]->slug;
		}
	}
}

function dtm_magazine_issue_date( $magazine_issue_data, $post_id ) {
	$magazine_issue_date = dtm_get_magazine_issue_date( $post_id );
	return $magazine_issue_date;
}
