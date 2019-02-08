<?php
/**
 * IX Header Bidder
 *
 *  @package     IX Header Bidder
 *  Plugin Name: IX Header Bidder.
 *  Version: 1.0.0 .
 *  Description: IX Header Bidder. <a href='https://readersdigest.atlassian.net/browse/WPDT-3432' target='_blank'>Read more at WPDT-3432</a>.
 *  Author: DRDT Team.
 *  Plugin URI: https://readersdigest.atlassian.net/browse/WPDT-3432.
 *  Text Domain: ix-header-bidder.
 */

/**
 * Including file for the settings.
 *
 * @file
 */
require 'inc/class-ix-settings.php';

/**
 *  Class Header Bidder.
 */
class IX_Header_Bidder {
	/**
	 * This will have the value of the script
	 *
	 * @var String
	 */
	private $script_url = '';
	const VERSION       = '1.0.0';

	/**
	 *  Constructor.
	 */
	public function __construct() {
		$ix_settings      = new IX_Settings();
		$this->script_url = $ix_settings->script_url;
		if ( $this->script_url ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 *  Script enqueue.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'ix-header-bidder', $this->script_url, array(), self::VERSION, true );
	}
}

$ix_header = new IX_Header_Bidder();
