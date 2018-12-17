<?php
/**
 * Plugin Name: StylePress for Elementor
 * Description: Allows you to apply full site layout templates to pages on your website using Elementor.
 * Plugin URI: https://stylepress.org/
 * Author: stylepress
 * Version: 2.0.0
 * Author URI: https://stylepress.net/
 * GitHub Plugin URI: https://github.com/stylepress/stylepress
 * Requires at least:   4.9
 * Tested up to:        5.0.1
 *
 * Text Domain: stylepress
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
 * @package stylepress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 Constants:
*/

// dev stuff by stylepress:
//set_time_limit(2);

/* Set plugin version constant. */
define( 'STYLEPRESS_VERSION', '2.0.0' );

/* Debug output control. */
define( 'STYLEPRESS_DEBUG_OUTPUT', 0 );

/* Set constant path to the plugin directory. */
define( 'STYLEPRESS_SLUG', basename( plugin_dir_path( __FILE__ ) ) );

/* Set constant path to the plugin directory. */
define( 'STYLEPRESS_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/* Set the constant path to the plugin directory URI. */
define( 'STYLEPRESS_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );


add_action( 'plugins_loaded', 'stylepress_load_plugin_textdomain' );

if ( ! version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	add_action( 'admin_notices', 'stylepress_fail_php_version' );
} else {


	define( 'STYLEPRESS_OUTER_USE_THEME', - 1 );
	define( 'STYLEPRESS_INNER_USE_PLAIN', - 1 );
	define( 'STYLEPRESS_INNER_USE_THEME', - 2 );


	/* StylepressManager Class */
	require_once( STYLEPRESS_PATH . 'inc/class.plugin.php' );

	/* Template Functions */
	require_once( STYLEPRESS_PATH . 'inc/template-functions.php' );

	/* Start up our magic */
	StylepressManager::get_instance()->init();


}

/**
 * Load gettext translate for our text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function stylepress_load_plugin_textdomain() {
	load_plugin_textdomain( 'stylepress' );
}

/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @since 1.0.0
 *
 * @return void
 */
if ( ! function_exists( 'stylepress_fail_php_version' ) ) {
	function stylepress_fail_php_version() {
		$message      = esc_html__( 'The StylePress for Elementor plugin requires PHP version 5.4+, plugin is currently NOT ACTIVE.', 'stylepress' );
		$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
		echo wp_kses_post( $html_message );
	}
}

if ( ! defined( 'ELEMENTOR_PARTNER_ID' ) ) {
	define( 'ELEMENTOR_PARTNER_ID', 2114 );
}
