<?php

add_filter( 'dtm_data_layer_page_content_source', 'dtm_data_layer_page_content_source', 10, 2 );
function dtm_data_layer_page_content_source( $source, $post_id ) {
	if ( taxonomy_exists( 'source' ) ) {
		$source_data = apply_filters( 'source_line_display_filter', 'get_source_line_display' );
		if ( ! empty( $source_data['name'] ) ) {
			return $source_data['name'];
		}
	}
}
