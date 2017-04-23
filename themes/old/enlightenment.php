<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$style = '
		.elementor-body .site {
			overflow-x: visible;
		}
		.elementor-body .navbar-fixed-top {
			z-index: 1;
		}
	';
	wp_add_inline_style( 'elementor-frontend', $style );
