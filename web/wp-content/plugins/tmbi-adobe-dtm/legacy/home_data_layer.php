<?php
/**
 * Adds DTM data for Homepage
 *
 * @see https://readersdigest.atlassian.net/browse/WPDT-3395
 */

add_filter( 'dtm_data_layer', 'dtm_add_homepage_data' );
function dtm_add_homepage_data( $data_layer ) {
	if ( is_home() ) {
		$data_layer['page.category.pageType'] = 'Homepage'; // at page 5
		$data_layer['page.pageName']          = dtm_get_pagename( array( dtm_get_nickname(), 'Homepage' ) );
		$data_layer['page.content.contentType'] = 'homepage';
	}
	return $data_layer;
}
