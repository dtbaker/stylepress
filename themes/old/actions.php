<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


$style = '
    .stylepress-template .main-content-area .main,
    .stylepress-template-std .main-content-area .main  {
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
