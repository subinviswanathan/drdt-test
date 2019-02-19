<?php
/**
 * Plugin Name: TMBI Ad blocker
 * Description: Block ads from the TMBI Ad Stack in various ways
 * Author: Facundo Farias
 * Author URI: https://facundofarias.com.ar
 * Version: 1.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tmbiadblocker
 */

defined( 'ABSPATH' ) || exit;

/*
Usage:

From ad-related plugins, register the service like this to get CMS controls to block/unblock per-post:
```
	add_filter( 'ad_services', function( $services ) {
		$services['tb'] => 'Taboola';
		return $services;
	});
```

When enqueueing scripts or doing ad stuff, check like this:
```
	if ( ! apply_filters( 'is_service_blocked', false, 'tb' ) ) {
		do_ad_stuff();
	}
```
* NOTE: For reliability, specially with CMS controls (that depend on is_single), checks should happen _after_ the `wp` hook. *
*/


require_once( 'is-service-blocked-filter.php' );
