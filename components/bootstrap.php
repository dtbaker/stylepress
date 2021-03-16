<?php
/**
 * StylePress: Bootstrap File
 *
 * This starts things up. Registers the SPL and starts up some classes.
 *
 * @package stylepress
 * @since 2.0.0
 */

namespace StylePress;

use StylePress\Frontend\Render;

defined( 'STYLEPRESS_VERSION' ) || exit;

spl_autoload_register(
	function ( $class ) {
		$prefix = __NAMESPACE__;
		$base_dir = __DIR__;
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}
		$relative_class = strtolower( substr( $class, $len + 1 ) );
		$file           = $base_dir . DIRECTORY_SEPARATOR . str_replace( [ '\\', '_' ], [
				'/',
				'-'
			], $relative_class ) . '.php';
		if ( file_exists( $file ) ) {
			require_once $file;
		} else {
			wp_die( esc_html( 'Filename: ' . $file . ' ' . basename( $file ) . ' missing.' ) );
		}
	}
);

// Needed for the CPT registration:
Styles\Cpt::get_instance();

if ( is_admin() ) {
	// Adds the menu items to the nav area:
	Backend\Ui::get_instance();
}else if ( wp_doing_ajax() ) {
	// Registers our ajax callbacks
	Wizard\Ajax::get_instance();
}else{
	// Frontend requests, load the template modification classes
	Render::get_instance();
}

//Plugin::get_instance();
//Elementor\Integration::get_instance();
//Styles::get_instance();
//Frontend::get_instance();
//if ( is_admin() || wp_doing_ajax() ) {
//	Backend::get_instance();
//	Wizard::get_instance();
//	Remote_Styles::get_instance();
//}
