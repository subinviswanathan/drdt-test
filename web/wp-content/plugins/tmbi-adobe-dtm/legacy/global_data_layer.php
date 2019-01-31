<?php
/**
 * Adds global DTM data
 *
 * @see https://readersdigest.atlassian.net/browse/WPDT-3395
 */

add_filter( 'dtm_data_layer', 'dtm_add_global_data' );
function dtm_add_global_data( $data_layer ) {
	$data_layer['page.theme']    = get_stylesheet();
	$data_layer['page.sitename'] = dtm_get_nickname();
	$data_layer['page.pageName'] = html_entity_decode( strip_tags( wp_title( '|', false, 'right' ) ), ENT_QUOTES );
	$data_layer['page.content.contentName'] = html_entity_decode( strip_tags( wp_title( '|', false, 'right' ) ), ENT_QUOTES );
	$data_layer['ab.testing'] = 'redesign:design_v2';
	return $data_layer;
}
