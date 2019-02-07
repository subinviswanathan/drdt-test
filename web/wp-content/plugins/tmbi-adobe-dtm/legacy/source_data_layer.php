<?php

add_filter( 'dtm_data_layer_page_content_source', 'dtm_data_layer_page_content_source' );
function dtm_data_layer_page_content_source( $source ) {
	if ( taxonomy_exists( 'source' ) ) {
		$source_data = apply_filters( 'source_line_display_filter', 'get_source_line_display' );
		if ( ! empty( $source_data['name'] ) ) {
			return $source_data['name'];
		}
	}
}
