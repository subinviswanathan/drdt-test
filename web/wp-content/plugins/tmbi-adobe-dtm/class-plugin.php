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

}

$adobe_dtm_helper = new RD_Adobe_DTM();
