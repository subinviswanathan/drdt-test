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

		$variables = self::rd_amp_vars();

		$pages_post_type = get_post_type();
		if ( get_post_type() === 'listicle' || get_post_type() === 'collection' ) {
			$variables['optional']['c4'] = 'content/index';

			$pages_post_type = 'listicle';
		}

		$variables['page_type']['v28'] = 'amp pages ' . $pages_post_type;
		$variables['page_type']['c23'] = 'amp pages ' . $pages_post_type;

		$basic_vars_string    = self::get_vars_implode( $variables['basic'] );
		$optional_vars_string = self::get_vars_implode( $variables['optional'] );
		$page_type_string     = self::get_vars_implode( $variables['page_type'] );
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
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
			$dom = new DomDocument();
			libxml_use_internal_errors( true );
			$dom->loadHTML( $article_content );
			libxml_use_internal_errors( false );
			foreach ( $dom->getElementsByTagName( 'a' ) as $item ) {
				$analytics_text = '<a data-vars-link-name="' . $item->nodeValue . '" data-vars-link-module="content recirculation" data-vars-link-position="embedded" href="' . $item->getAttribute( 'href' ) . '" target="_blank">' . $item->nodeValue . '</a>';
				$article_content = str_replace( html_entity_decode( $dom->saveHTML( $item ) ), $analytics_text, html_entity_decode( $article_content ) );
			}
		}
		return( $article_content );
	}

	/**
	 * Google Accelerated Mobile Pages variables
	 * @return array
	 */
	public static function rd_amp_vars() {
		global $post;

		$categories = dtm_get_sub_categories( $post->ID );
		$pagename   = dtm_get_pagename(
			array(
				dtm_get_nickname(),
				$categories['subcategory'],
				$categories['subsubcategory'],
				dtm_get_post_type( $post->post_type ),
				$post->post_title,
			)
		);

		$vars = array(
			'basic'    => array(
				'trackingServer' => 'trustedmediabrands.sc.omtrdc.net',
				'accounts'       => self::get_server_account(),
				'pageName'       => strtolower( $pagename ),
				'g'              => self::get_page_url(),
				'ch'             => dtm_get_site_section(),
				'events'         => self::get_events(),
			),
			'optional' => array_filter(
				array(
					'v1'  => strtolower( $pagename ),
					'v2'  => self::get_page_url(),
					'c4'  => dtm_get_categories( $post->ID ),
					'c5'  => $_SERVER['HTTP_HOST'],
					'c7'  => dtm_get_post_type( $post->post_type ),
					'c8'  => dtm_get_post_id( $post->post_type, $post->ID ),
					'c9'  => strtolower( $post->post_title ),
					'v12' => dtm_get_post_type( $post->post_type ),
					'v13' => dtm_get_post_id( $post->post_type, $post->ID ),
					'v14' => strtolower( $post->post_title ),
					'c15' => dtm_get_tags( $post->ID ),
					'v20' => dtm_get_tags( $post->ID ),
					'c47' => dtm_get_wordpress_content_id( $post->post_type, $post->ID ),
					'c59' => dtm_get_wordpress_content_id( $post->post_type, $post->ID ),
				)
			),
		);

		return( $vars );
	}

	public static function get_vars_implode( $vars ) {
		$var_string = '';
		foreach ( $vars as $key => $var ) {
			$var_string .= $key . '=${' . $key . '}&';
		}
		return( $var_string );
	}

	/**
	 * event1 => default pageload event
	 * event32 => google amp view
	 * event41 => single page slideshow/listicle
	 * event42 => listicle event
	 *
	 * @return string $event
	 */
	public static function get_events() {
		$event = 'event1,event32';
		// currently amp only supports listview
		if ( get_post_type() === 'listicle' || get_post_type() === 'collection' ) {
			$event .= ',event41,event42';
		}
		return( $event );
	}


	public static function get_server_account() {
		$server_account = get_option( RD_DTM_Settings::AMP_SERVER_ACCOUNT );
		if ( ! $server_account ) {
			$server_account = 'tmbrandsdev';
		}
		return( $server_account );
	}

	public static function get_page_url() {
		$page_url = ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		return( $page_url );
	}
}
