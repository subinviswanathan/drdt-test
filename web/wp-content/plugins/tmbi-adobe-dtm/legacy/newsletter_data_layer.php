<?php
/**
 * Adds DTM data for Newsletter Signup Page - Update Preference
 *
 * @see: https://readersdigest.atlassian.net/browse/WPDT-7266
 * @todo: Move to the plugin that provides the `newslettersignuppage-updatepreference` page and merge with `newsletter_dtm_data` filter.
 */

add_filter( 'dtm_data_layer', 'dtm_add_newsletter_signup_page_data' );
function dtm_add_newsletter_signup_page_data( $data_layer ) {
	if ( is_page( 'newslettersignuppage-updatepreference' ) ) {
		$dtm_data = apply_filters( 'newsletter_dtm_data', array() );
		if ( $dtm_data ) {
			$data_layer['subscription.selectAllEvent'] = $dtm_data['select_all'];
			$data_layer['subscription.signupEvent'] = $dtm_data['signup_event'];
			$data_layer['subscription.signupCount'] = $dtm_data['signup_count'];
			$data_layer['subscription.formName'] = $dtm_data['signup_form'];
		}
	}
	return $data_layer;
}
