<?php
/**
 * Plugin Name: StylePress for Elementor
 * Description: Allows you to apply full site layout templates to pages on your website using Elementor.
 * Plugin URI: https://stylepress.org/
 * Author: dtbaker
 * Version: 1.0.18
 * Author URI: https://dtbaker.net/
 * GitHub Plugin URI: https://github.com/dtbaker/stylepress
 * Requires at least:   4.4
 * Tested up to:        4.7.2
 *
 * Text Domain: dtbaker-elementor
 *
 * Full Site Editor for Elementor is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Elementor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @package dtbaker-elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 Constants:
*/

// dev stuff by dtbaker:
//set_time_limit(2);

/* Set plugin version constant. */
define( 'DTBAKER_ELEMENTOR_VERSION', '1.0.18' );

/* Debug output control. */
define( 'DTBAKER_ELEMENTOR_DEBUG_OUTPUT', 0 );

/* Set constant path to the plugin directory. */
define( 'DTBAKER_ELEMENTOR_SLUG', basename( plugin_dir_path( __FILE__ ) ) );

/* Set constant path to the plugin directory. */
define( 'DTBAKER_ELEMENTOR_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/* Set the constant path to the plugin directory URI. */
define( 'DTBAKER_ELEMENTOR_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );


add_action( 'plugins_loaded', 'dtbaker_elementor_load_plugin_textdomain' );

if ( ! version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	add_action( 'admin_notices', 'dtbaker_elementor_fail_php_version' );
} else {


	define('STYLEPRESS_OUTER_USE_THEME', -1);
	define('STYLEPRESS_INNER_USE_PLAIN', -1);
	define('STYLEPRESS_INNER_USE_THEME', -2);


	/* DtbakerElementorManager Class */
	require_once( DTBAKER_ELEMENTOR_PATH . 'inc/class.plugin.php' );

	/* Template Functions */
	require_once( DTBAKER_ELEMENTOR_PATH . 'inc/template-functions.php' );

	/* Start up our magic */
	DtbakerElementorManager::get_instance()->init();




}

/**
 * Load gettext translate for our text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function dtbaker_elementor_load_plugin_textdomain() {
	load_plugin_textdomain( 'dtbaker-elementor' );
}

/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @since 1.0.0
 *
 * @return void
 */
if( ! function_exists( 'dtbaker_elementor_fail_php_version' ) ) {
	function dtbaker_elementor_fail_php_version() {
		$message      = esc_html__( 'The StylePress for Elementor plugin requires PHP version 5.4+, plugin is currently NOT ACTIVE.', 'stylepress' );
		$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
		echo wp_kses_post( $html_message );
	}
}
