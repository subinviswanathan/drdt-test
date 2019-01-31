<?php

add_filter( 'dtm_data_layer_brands_data', 'dtm_get_brands_data' );
/*
 * Get the brand data from different plugins.
 *  - TMBI First Associated Taxonomy ( rd-tmbi-first-published )
 *  - TMBI Brand Attribution Manager ( tmbi-brand-attribution )
 * used get_first_published_brand filter for keep code in same location
 */
function dtm_get_brands_data( $brands_data ) {
	if ( taxonomy_exists( 'tmbi_first_associated' ) ) {
		$post = get_post();
		if ( taxonomy_exists( 'brand' ) ) {
			$brand_names = wp_get_object_terms( $post->ID, 'brand' );
			if ( ! empty( $brand_names[0]->slug ) ) {
				return ( $brand_names[0]->slug );
			}
		} elseif ( $brand_slug_array = apply_filters( 'get_first_published_brand', 'get_brand_data' ) ) {
			return ( $brand_slug_array['term_slug'] );
		}
		 return ( 'no brand' );
	}
}