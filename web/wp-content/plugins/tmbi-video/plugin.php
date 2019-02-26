<?php
/*
Plugin Name: RD Video Post Type
Version: 0.0.9
Description: Add the video custom post type <a href='https://readersdigest.atlassian.net/browse/WPDT-3302' target='_blank'>Read more at WPDT-3302 ...</a>.
Author: Mikel King
Text Domain: video-post-type
License: BSD(3 Clause)
License URI: http://opensource.org/licenses/BSD-3-Clause
*/

/**
 * define video plugin version
 */
define( 'VIDEO_PLUGIN_VER', '2.0.0' );

// Import Video CPT Class.
require_once 'inc/class-video-cpt.php';

/**
 * Register and initialize Video Post Type
 */
new Video_CPT();


// Add Video CPT Setting class.
require_once 'inc/class-video-cpt-setting.php';

/**
 * Additional helper setting for Video CPT
 */
new Video_CPT_Setting();


/**
 * Include shortcode files
 */
require_once 'inc/class-video-shortcode.php';
new Video_Shortcode();


// Add Video Secondary Player class.
require_once 'inc/class-video-secondary-player.php';

/**
 * Secondary Player settings
 */
new Video_Secondary_Player();


// Add Brightcove player class
require_once 'inc/provider/class-tmbi-brightcove-player.php';

/**
 * Initialize Brightcove player
 */
new TMBI_Brightcove_Player();

// Add Dailymotion player class.
require_once 'inc/provider/class-tmbi-dailymotion-player.php';
new TMBI_Dailymotion_Player();

// Add JW Player class.
require_once 'inc/provider/class-tmbi-jw-player.php';
new TMBI_JW_Player();

// Add JW Player Options class.
require_once 'inc/provider/class-tmbi-jw-player-options.php';
new TMBI_JW_Player_Options();


