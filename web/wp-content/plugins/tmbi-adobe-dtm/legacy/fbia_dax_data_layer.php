<?php
/**
 * Adds DTM data for FBIA DAX Page
 *
 * @see https://readersdigest.atlassian.net/browse/WPDT-3555
 * @todo: Move to the plugin that provides the `dbia-dax` page.
 */

add_filter( 'dtm_data_layer', 'dtm_add_fbia_dax_page_data' );
function dtm_add_fbia_dax_page_data( $data_layer ) {
	if ( is_page( 'fbia-dax' ) ) {
		if ( ! empty( $_GET['contentId'] ) && $_GET['contentId'] > 0  ) {
			$content_id = intval( $_GET['contentId'] );
			$post       = get_post( $content_id );
			setup_postdata( $post );

			$meta = get_post_meta( $post->ID, '', true );
			if ( ! empty( $meta['_yoast_wpseo_title'][0] ) ) {
				$page_name = trim( $meta['_yoast_wpseo_title'][0] );
			} else {
				$page_name = $post->post_title;
			}

			$data_layer['page.pageName']                = $page_name;

			// @todo: Review and decouple.
			// $this->set_source( $post->ID );
			$source = apply_filters( 'dtm_data_layer_page_content_source', false );
			if ( ! empty ( $source ) ) {
				$data_layer['page.content.source'] = $source;
			}

			// @todo: Review and decouple.
			// $this->set_tmbi_brand();
			$brand = apply_filters( 'dtm_data_layer_brands_data', false );
			if ( ! empty( $brand ) ) {
				$data_layer['page.content.tmbiBrand'] = $brand;
			}
			// @todo: Review and decouple.
			// $this->set_magazine_issue( $post->ID );
			$magazine_issue = apply_filters( 'dtm_data_layer_magazine_issue_data', false );
			if ( ! empty( $magazine_issue ) ) {
				$data_layer['page.content.magazineIssue'] = $magazineIssue;
			}

			$data_layer['page.category.pageType']       = dtm_get_categories( $post->ID ); // at page 5
			$data_layer['page.content.contentID']       = $post->ID;
			$data_layer['page.content.contentName']     = $post->post_title;
			$data_layer['page.content.contentType']     = dtm_get_post_type( $post->post_type );
			$data_layer['page.content.category']        = dtm_get_categories( $post->ID );
			$data_layer['page.content.tags']            = dtm_get_tags( $post->ID );
			$data_layer['page.content.contentCost']     = dtm_get_content_cost( $post->ID );
			$data_layer['page.content.author']          = dtm_get_author_name( $post->post_author );
			$data_layer['page.content.authorRole']      = dtm_get_author_roles( $post->post_author );
			$data_layer['page.content.publishedDate']   = get_the_date( 'Y-m-d' ); // at page 15
			$data_layer['page.content.modifiedDate']    = get_the_modified_date( 'Y-m-d' ); // at page 15
			if ( $data_layer['page.content.contentType'] == 'listicle' ) {
				$data_layer['page.content.slideShowMulti'] = true;
			}
		}
	}
	return $data_layer;
}