<?php
/**
 * Adds DTM data for Single Posts (of any type)
 *
 * @see https://readersdigest.atlassian.net/browse/WPDT-3395
 */

add_filter( 'dtm_data_layer', 'dtm_add_single_data' );
function dtm_add_single_data( $data_layer ) {
	if ( is_single() ) {
		global $post, $numpages;

		if ( is_page( 'fbia-dax' ) && ! empty( $_GET['contentId'] ) && $_GET['contentId'] > 0  ) {
			$post = get_post( intval( $_GET['contentId'] ) );
			setup_postdata( $post );

			$meta = get_post_meta( $post->ID, '_yoast_wpseo_title', true );
			if ( ! empty( $meta ) ) {
				$page_name = trim( $meta );
			} else {
				$page_name = $post->post_title;
			}

			$data_layer['page.pageName'] = $page_name;
		}

		$source = apply_filters( 'dtm_data_layer_page_content_source', false, $post->ID );
		if ( ! empty( $source ) ) {
			$data_layer['page.content.source'] = $source;
		}

		$brand = apply_filters( 'dtm_data_layer_brands_data', false, $post->ID );
		if ( ! empty( $brand ) ) {
			$data_layer['page.content.tmbiBrand'] = $brand;
		}

		$magazine_issue = apply_filters( 'dtm_data_layer_magazine_issue_data', false, $post->ID );
		if ( ! empty( $magazine_issue ) ) {
			$data_layer['page.content.magazineIssue'] = $magazine_issue;
		}


		$categories = dtm_get_sub_categories( $post->ID );

		$data_layer['page.sitename']                = dtm_get_nickname();
		$data_layer['page.subCategory']             = $categories['subcategory'];
		$data_layer['page.subsubCategory']          = $categories['subsubcategory'];
		$data_layer['page.category.subCategory']    = $categories['subcategory'];
		$data_layer['page.category.subsubCategory'] = $categories['subsubcategory'];
		$data_layer['page.category.pageType']       = dtm_get_categories( $post->ID ); // at page 5.
		$data_layer['page.content.contentName']     = $post->post_title;
		$data_layer['page.content.contentID']       = dtm_get_post_id( $post->post_type, $post->ID );
		$data_layer['page.content.wpContentID']     = dtm_get_wordpress_content_id( $post->post_type, $post->ID );
		$data_layer['page.content.contentType']     = dtm_get_post_type( $post->post_type );
		$data_layer['page.content.category']        = dtm_get_categories( $post->ID );
		$data_layer['page.content.tags']            = dtm_get_tags( $post->ID );
		$data_layer['page.content.contentCost']     = dtm_get_content_cost( $post->ID );
		$data_layer['page.content.publishedDate']   = dtm_get_original_published_date( $post->ID ); // at page 15.
		$data_layer['page.content.modifiedDate']    = get_the_modified_date( 'Y-m-d' ); // at page 15.

		$data_layer['page.content.image.licensorName'] = Image_Credits_DTM::get_image_licensor_name( $post->ID );
		$data_layer['page.content.image.credits']      = Image_Credits_DTM::get_image_credits( $post->ID );
		$data_layer['page.content.author']             = dtm_get_author_name( $post->post_author );
		$data_layer['page.content.authorRole']         = dtm_get_author_roles( $post->post_author );

		$data_layer['page.pageName'] = dtm_get_pagename(
			array(
				dtm_get_nickname(),
				$categories['subcategory'],
				$categories['subsubcategory'],
				dtm_get_post_type( $post->post_type ),
				$post->post_title,
			)
		);
	}
	return $data_layer;
}
