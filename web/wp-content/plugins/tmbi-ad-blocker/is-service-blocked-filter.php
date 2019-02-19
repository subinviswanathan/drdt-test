<?php
/**
 * is_service_blocked filter
 *
 * Adds various ways to check if a particular ad service should be blocked.
 *
 * @package tmbi_ad_blocker
 */

add_filter( 'is_service_blocked', 'tmbi_ad_blocker_variant_noads', 10, 2 );
add_filter( 'is_service_blocked', 'tmbi_ad_blocker_blockservice_url_param', 10, 2 );
add_filter( 'is_service_blocked', 'tmbi_ad_blocker_per_post', 10, 3 );

// Block service using ?variant=noads querystring param
function tmbi_ad_blocker_variant_noads( $blocked, $service ) {
	$variant = get_query_var( 'variant' );
	if ( 'noads' === $variant ) {
		// @todo: Check if service is an ad.
		$blocked = true;
	}
	return $blocked;
}

// Block service using ?blockService[]=$service querystring param
function tmbi_ad_blocker_blockservice_url_param( $blocked, $service ) {
	$blocked_services = [];
	if ( ! empty( $_GET['blockService'] ) ) {
		$blocked_services = is_array( $_GET['blockService'] ) ? $_GET['blockService'] : [ $_GET['blockService'] ];
	}
	if ( ! empty( $service ) && ! empty( $blocked_services ) ) {
		$blocked = in_array( $service, $blocked_services );
	}
	return $blocked;
}

// Block service using CMS controls
function tmbi_ad_blocker_per_post( $blocked, $service, $post_id = false ) {
	// To check the current ID we need to run conditional tags. This is only possible after the `wp` action.
	if ( empty( $post_id ) && did_action( 'wp' ) && is_singular() ) {
		$post_id = get_the_ID();
	}
	if ( ! empty( $post_id ) ) {
		$blocked = $blocked || ( ! empty( get_post_meta( $post_id, 'block_' . $service, true ) ) );
		// Support for old data schema
		if ( $service === 'tb' ) {
			$blocked = $blocked || ( ! empty( get_post_meta( $post_id, 'rd_taboola', true ) ) );
		}
		if ( $service === 'nt' ) {
			$blocked = $blocked || ( ! empty( get_post_meta( $post_id, 'tmbi_nativo', true ) ) );
		}
	}
	return $blocked;
}
