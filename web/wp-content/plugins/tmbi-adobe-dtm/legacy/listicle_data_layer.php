<?php
/**
 * Adds DTM data for Listicles
 *
 * @see https://readersdigest.atlassian.net/browse/WPDT-3812
 * @todo: Move to the Listicle plugin/template.
 */

add_filter( 'dtm_data_layer', 'dtm_add_listicle_data' );
function dtm_add_listicle_data( $data_layer ) {
	if ( is_singular( 'listicle' ) ) {
		global $post;
		// @todo: check global $page, $numpages;
		$data_layer['page.content.listicleEvent'] = true;
		$page = (int) get_query_var( 'page' );
		if ( $page ) {
			$data_layer['page.content.cardNo'] = $page;
			$data_layer['page.content.slideShowMulti'] = true;
		} else {
			$data_layer['page.content.slideShowSingle'] = true;

			$content_pages = explode( '<!--nextpage-->', $post->post_content );
			$numpages      = count( $content_pages );
			if ( $numpages ) {
				$data_layer['page.content.slideTotal']  = $numpages;
			}
		}
	}
	return $data_layer;
}
