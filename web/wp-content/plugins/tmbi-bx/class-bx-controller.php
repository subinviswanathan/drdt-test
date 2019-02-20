<?php
/**
 *  Bounce Xchange
 *
 *  @package     Bounce Xchange
 * Plugin Name: TMBI Bounce Exchange
 * Version: 1.0.0
 * Description: Adds the BX simple tag to the header <a href='https://readersdigest.atlassian.net/browse/WPDT-3669' target='_blank'>Read more at WPDT-3669 ...</a>
 * Author: Santhosh Kumar MJ
 * Text Domain: bounce-exchange
 * License: BSD(3 Clause)
 * License URI: http://opensource.org/licenses/BSD-3-Clause
 *
 * Copyright (C) 2017, Santhosh Kumar, ness.com, (santhosh.kumar AT ness DOT com)
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 * Neither the name of the {organization} nor the names of its
 * contributors may be used to endorse or promote products derived from
 * this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Including file for the settings.
 *
 * @file
 */
require 'inc/class-bx-settings.php';

/**
 *  Class BX Controller.
 */
class BX_Controller {

	/**
	 * This will have the value of the script id
	 *
	 * @var String
	 */
	private static $script_id = '';
	const FILE_SPEC           = __DIR__;
	const LOCALIZED_LABEL     = 'tmbi_bx';
	const LOADER_SCRIPT       = 'js/smart-tag.js';
	const LOADER_LABEL        = 'tmbi-bx-loader';
	const SCRIPT_ID           = '3125';
	const DEPENDS             = '';
	const VERSION             = '1.0.0';


	/**
	 *  Init.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( __CLASS__, 'remove_bx_xchange' ), 1 );
	}

	/**
	 *  Script enqueue.
	 */
	public static function enqueue_scripts() {
		wp_register_script( self::LOADER_LABEL, plugin_dir_url( __FILE__ ) . self::LOADER_SCRIPT, array(), self::VERSION, true );
		$ad_stack = get_option( 'ad_stack', false );
		// get the script id from ad stack settings.
		if ( $ad_stack['bx_xchange_script_id'] ) {
			self::$script_id = $ad_stack['bx_xchange_script_id'];
		} else {
			self::$script_id = self::SCRIPT_ID;
		}
		$localized_data = array(
			'script_id' => self::$script_id,
		);
		wp_localize_script( self::LOADER_LABEL, self::LOCALIZED_LABEL, $localized_data );
		wp_enqueue_script( self::LOADER_LABEL );
	}

	/**
	 * Remove Bounce Xchange (for ?variant=noads).
	 */
	public static function remove_bx_xchange() {
		$variant = get_query_var( 'variant' );
		if ( 'noads' === $variant ) {
			wp_dequeue_script( self::LOADER_LABEL );
		}
	}
}

add_action( 'init', array( 'BX_Controller', 'init' ) );
