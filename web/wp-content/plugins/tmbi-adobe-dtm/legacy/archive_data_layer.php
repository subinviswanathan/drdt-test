<?php
/**
 * Adds DTM data for Archive pages
 *
 * @see https://readersdigest.atlassian.net/browse/WPDT-4879
 * @see https://readersdigest.atlassian.net/browse/WPDT-3395
 */

add_filter( 'dtm_data_layer', 'dtm_add_archive_data' );
function dtm_add_archive_data( $data_layer ) {
	if ( is_archive() ) {
		$data_layer['page.category.pageType'] = get_the_archive_title();

		$data_layer['page.content.contentType'] = 'hubs';

		$categories = dtm_get_archive_categories();

		// Assign only 3 deep categories.
		$sub_category    = array();
		$sub_category[0] = isset( $categories[0] ) ? $categories[0] : '';
		$sub_category[1] = isset( $categories[1] ) ? $categories[1] : '';
		$sub_category[2] = isset( $categories[2] ) ? $categories[2] : '';

		$data_layer['page.subCategory']                = $sub_category[0];
		$data_layer['page.subsubCategory']             = $sub_category[1];
		$data_layer['page.subsubsubCategory']          = $sub_category[2];
		$data_layer['page.category.subCategory']       = $sub_category[0];
		$data_layer['page.category.subsubCategory']    = $sub_category[1];
		$data_layer['page.category.subsubsubCategory'] = $sub_category[2];

		array_unshift( $categories, dtm_get_nickname() );

		$data_layer['page.pageName'] = dtm_get_pagename( $categories );
	}
	return $data_layer;
}

/**
 * Get archive categories from queried variable
 *
 * @return array $term_array
 */
function dtm_get_archive_categories() {
	$term_array = array();

	if ( is_archive() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$parent_terms = get_ancestors( $term->term_id, $term->taxonomy );
			foreach ( array_reverse( $parent_terms ) as $parent_term ) {
				$term_array[] = get_term( $parent_term )->slug;
			}
			array_push( $term_array, $term->slug );
		}
	}
	return $term_array;
}
