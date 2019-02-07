<?php
/*
Plugin Name: RD Adobe DTM
Version: 1.3.9
Description: Adobe DTM <a href='https://readersdigest.atlassian.net/browse/WPDT-3395' target='_blank'>Read more at WPDT-3395</a>
Author: Jey
Plugin URI: https://readersdigest.atlassian.net/browse/WPDT-3395
Text Domain: rd-adobe-dtm
*/

require_once 'legacy.php';

require 'inc/rd-dtm-settings.php';
require 'inc/class-image-credits-dtm.php';
require 'inc/adobe_dtm_utils.php';
require 'inc/amp.php';

class RD_Adobe_DTM {
	const VERSION         = '2.0.0';
	const PRIORITY        = '5';
	const PROCESSOR_SLUG  = 'rd_adobe_dtm';
	const JS_FILE         = 'js/adobe_dtm.js';
	const FILE_SPEC       = __FILE__;
	const DATA_LAYER_NAME = 'digitalData';

	private $adobe_dtm_slug;
	public $depends         = array( 'jquery' );
	public $data_layer      = array();
	public $data_layer_json = '';


	/**
	 * RD_Adobe_DTM constructor.
	 */
	public function __construct() {

		if ( class_exists( 'RD_DTM_Settings' ) ) {
			$rd_dtm_settings      = new RD_DTM_Settings();
			$this->adobe_dtm_slug = $rd_dtm_settings::RD_DTM_SLUG;
		}
		new Image_Credits_DTM();
		new ADTM_AMP();

		/*
		 * Instantiate Adobe DTM utils class
		 */
		new Adobe_DTM_Utils();

		add_action( 'wp_head', array( $this, 'page_header' ), static::PRIORITY );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

	}

	/**
	 * Enqueuing script to process tagging
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		if ( ! get_option( RD_DTM_Settings::RD_DTM_SLUG ) ) {
			return;
		}

		// Madatory for all sites.
		wp_register_script(
			self::PROCESSOR_SLUG,
			plugins_url( self::JS_FILE, __FILE__ ),
			$this->depends,
			self::VERSION,
			true
		);
		wp_enqueue_script( self::PROCESSOR_SLUG );

	}

	/**
	 * Enqueue Adobe Code and associated data
	 */
	public function page_header() {

		if ( ! get_option( RD_DTM_Settings::RD_DTM_SLUG ) ) {
			return;
		}

		$parsed_array = apply_filters( 'dtm_data_layer', array() );

		wp_register_script(
			'adobe-dtm-js',
			get_option( RD_DTM_Settings::RD_DTM_SLUG ),
			array(),
			self::VERSION,
			false
		);
		wp_enqueue_script( 'adobe-dtm-js' );

		wp_localize_script( 'adobe-dtm-js', self::DATA_LAYER_NAME, $parsed_array );

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

$adobe_dtm_helper = new RD_Adobe_DTM();
