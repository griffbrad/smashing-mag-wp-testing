<?php

/**
 * Plugin Name: Smashing Mag Automated Testing Example
 * Description: A plugin that illustrates automated testing concepts in WordPress.
 * Author: Brad Griffith
 * Author URI: http://bradg.net
 * Version: 1.0
 */

require_once __DIR__ . '/lib/SmashingBase.php';

$base = new SmashingBase();

add_action( 'admin_init', array( $base, 'admin_init' ) );

add_action( 'wp_dashboard_setup', array( $base, 'dashboard_setup' ) );
