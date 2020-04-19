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

defined( 'STYLEPRESS_VERSION' ) || exit;

spl_autoload_register(
	function ( $class ) {
		$prefix   = __NAMESPACE__;
		$base_dir = __DIR__;
		$len      = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}
		$relative_class = strtolower( substr( $class, $len + 1 ) );
		$relative_class = 'class-' . $relative_class;
		$file           = $base_dir . DIRECTORY_SEPARATOR . str_replace( [ '\\', '_' ], [ '/', '-' ], $relative_class ) . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		} else {
			wp_die( esc_html( 'Filename: ' . $file.' '.basename( $file ) . ' missing.' ) );
		}
	}
);

Plugin::get_instance();
Styles::get_instance();
Frontend::get_instance();
if(is_admin()){
	Backend::get_instance();
}
