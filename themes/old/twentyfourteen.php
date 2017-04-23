<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$style = '
	    .dtbaker-elementor-template-std .elementor-page .site {
            max-width: 100%;
			overflow: hidden;
        }
        .dtbaker-elementor-template-std .site::before {
            display: none;
        }
        .dtbaker-elementor-template-std .site-header {
            max-width: 100%;
        }
	';
	wp_add_inline_style( 'twentyfourteen-style', $style );
