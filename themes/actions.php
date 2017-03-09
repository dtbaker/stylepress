<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$style = '
    .dtbaker-elementor-template .main-content-area .main,
    .dtbaker-elementor-template-std .main-content-area .main  {
        margin: 0 auto;
        width: 100%;
        max-width: 100%;
    }
';
if ( is_child_theme() || ! is_admin() ) {
	wp_add_inline_style( 'actions-child-style', $style );
} else {
	wp_add_inline_style( 'actions-style', $style );
}
