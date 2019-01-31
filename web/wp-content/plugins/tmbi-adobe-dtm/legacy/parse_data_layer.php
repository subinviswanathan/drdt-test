<?php

add_filter( 'dtm_data_layer', 'adtm_parse_data_layer_config', 100 );
/*
 * Functions borrowed from Adobe DTM for Wordpress Plugin
 * get data array with dot notation and return as array
 */
function adtm_parse_data_layer_config( $config ) {

	if ( is_array( $config ) && count( $config ) > 0 ) {
		$dataLayer = array();

		foreach ( $config as $key => $value ) {
			if ( isset( $dataLayer[$key] ) ) {
				if ( is_array( $dataLayer[$key] ) && count( $dataLayer[$key] ) === count( $dataLayer[$key], COUNT_RECURSIVE ) ) {
					$dataLayer[$key][] = $value;
				} else {
					$dataLayer[$key] = $value;
				}
			} else {
				$dataLayer = array_merge_recursive( $dataLayer, adtm_create_element( $key, $value ) );
			}
		}
	} else {
		$dataLayer = $config;
	}
	return( $dataLayer );
}

// recursive function to construct an object from dot-notation
function adtm_create_element( $key, $value ) {
	$element = array();
	$key = ( string ) $key;
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
	}
	// just normal key
	else {
		$element[$key] = $value;
	}
	return( $element );
}