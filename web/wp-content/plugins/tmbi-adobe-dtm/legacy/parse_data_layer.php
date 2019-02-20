<?php

add_filter( 'dtm_data_layer', 'adtm_parse_data_layer_config', 100 );
/*
 * Functions borrowed from Adobe DTM for Wordpress Plugin
 * get data array with dot notation and return as array
 */
function adtm_parse_data_layer_config( $config ) {

	if ( is_array( $config ) && count( $config ) > 0 ) {
		$data_layer = array();

		foreach ( $config as $key => $value ) {
			if ( isset( $data_layer[$key] ) ) {
				if ( is_array( $data_layer[$key] ) && count( $data_layer[$key] ) === count( $data_layer[$key], COUNT_RECURSIVE ) ) {
					$data_layer[$key][] = $value;
				} else {
					$data_layer[$key] = $value;
				}
			} else {
				$data_layer = array_merge_recursive( $data_layer, adtm_create_element( $key, $value ) );
			}
		}
	} else {
		$data_layer = $config;
	}
	return( $data_layer );
}

// recursive function to construct an object from dot-notation
function adtm_create_element( $key, $value ) {
	$element = array();
	$key = (string) $key;
	// if the key is a property
	if ( strpos( $key, '.' ) !== false ) {
		/**
		 * extract the first part with the name of the object, however
		 * explode() will return FALSE we should be careful to check this
		 */
		$list = explode( '.', $key );
		// the rest of the key
		$sub_key = substr_replace( $key, '', 0, strlen( $list[0] ) + 1 );
		// create the object if it doesnt exist
		if ( $list !== false && ! array_key_exists( $list[0], $element ) ) {
			$element[$list[0]] = array();
		}
		// if the key is not empty, create it in the object
		if ( $sub_key !== '' && $list !== false ) {
			$element[$list[0]] = adtm_create_element( $sub_key, $value );
		}
	} else { // just normal key.
		$element[$key] = $value;
	}
	return( $element );
}
