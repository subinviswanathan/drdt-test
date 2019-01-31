<?php

class ADTM_AMP {
	public function __construct() {
		add_filter( 'amp_post_template_analytics', array( __CLASS__, 'adtm_amp' ), 11 );
		add_filter( 'the_content', array( __CLASS__, 'toh_the_content_single_article_amp' ), 10 );
	}

	public static function adtm_amp( $analytics ) {
		if ( ! is_array( $analytics ) ) {
			$analytics = array();
		}
		if ( ! class_exists( 'RD_Adobe_DTM' ) ) {
			return ( $analytics );
		}
		if ( ! empty( $analytics['amp-adobe-analytics'] ) ) {
			return $analytics;
		}

		$variables = RD_Adobe_DTM::rd_amp_vars();

		if ( self::is_collection() || self::is_listicle() ) {
			$variables['optional']['c4']   = 'content/index';
			$variables['page_type']['v28'] = 'amp pages listicle';
			$variables['page_type']['c23'] = 'amp pages listicle';
		}
		if ( self::is_post() ) {
			$variables['page_type']['v28'] = 'amp pages article';
			$variables['page_type']['c23'] = 'amp pages article';
		}
		if ( self::is_recipe() ) {
			$variables['page_type']['v28'] = 'amp pages recipe';
			$variables['page_type']['c23'] = 'amp pages recipe';
		}
		if ( self::is_project() ) {
			$variables['page_type']['v28'] = 'amp pages project';
			$variables['page_type']['c23'] = 'amp pages project';
		}
		$basic_vars_string    = RD_Adobe_DTM::get_vars_implode( $variables['basic'] );
		$optional_vars_string = RD_Adobe_DTM::get_vars_implode( $variables['optional'] );
		$page_type_string     = RD_Adobe_DTM::get_vars_implode( $variables['page_type'] );
		$vars                 = array_merge( $variables['basic'], $variables['optional'], $variables['page_type'] );

		$analytics['amp-adobe-analytics'] = array(
			'type'        => 'adobeanalytics',
			'attributes'  => array(),
			'config_data' => array(
				'transport' => array(
					'xhrpost' => false,
					'beacon'  => true,
				),
				'requests'  => array(
					'base'        => 'https://${trackingServer}/b/ss/${accounts}/1/AMP-0.1/s${random}',
					'pageView'    => '${base}?AQB=1&vid=CLIENT_ID(adobe_amp_id)&r=DOCUMENT_REFERRER&' . $basic_vars_string . $optional_vars_string . 'j=amp',
					'buttonClick' => '${base}?AQB=1&vid=CLIENT_ID(adobe_amp_id)&r=DOCUMENT_REFERRER&pageName=${pageName}&j=amp&pe=lnk_o&c21=${linkName}&v26=${linkName}&c20=${linkModule}&v25=${linkModule}&c22=${linkPosition}&v27=${linkPosition}&' . $optional_vars_string . $page_type_string,
				),
				'vars'      => $vars,
				'triggers'  => array(
					'pageLoad' => array(
						'on'      => 'visible',
						'request' => 'pageView',
					),
					'click'    => array(
						'on'       => 'click',
						'selector' => 'a',
						'request'  => 'buttonClick',
					),
				),
			),
		);
		return ( $analytics );
	}

	public static function toh_the_content_single_article_amp( $article_content ) {
		if ( WP_Base::is_toh() && RD_URL_Magick::is_amp_page() && WP_Base::is_post() ) {
			$dom = new DomDocument();
			libxml_use_internal_errors( true );
			$dom->loadHTML( $article_content );
			libxml_use_internal_errors( false );
			foreach ( $dom->getElementsByTagName( 'a' ) as $item ) {
				$analytics_text = '<a data-vars-link-name="' . $item->nodeValue . '" data-vars-link-module="content recirculation" data-vars-link-position="embedded" href="' .$item->getAttribute( 'href' ). '" target="_blank">' .$item->nodeValue. '</a>';
				$article_content = str_replace( html_entity_decode( $dom->saveHTML( $item ) ), $analytics_text, html_entity_decode( $article_content ) );
			}
		}
		return( $article_content );
	}
}
