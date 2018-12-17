<?php

defined( 'STYLEPRESS_PATH' ) || exit;

isset( $theme_name ) || exit;

// $theme_name is sanatised in class.plugin.php

$current_dir = STYLEPRESS_PATH . 'themes/' . $theme_name . '/';

if ( is_readable( $current_dir . $theme_name . '.css' ) ) {
	add_action( 'wp_enqueue_scripts', function () {
		wp_enqueue_style( 'stylepress-theme-addons', STYLEPRESS_URI . 'themes/generatepress/generatepress.css', false, STYLEPRESS_VERSION );
	} );

}

add_filter( 'stylepress_theme_hooks', function ( $hooks ) {
	$hooks['before'] = 'generate_after_header';
	$hooks['after']  = 'generate_before_footer';

	return $hooks;
} );