<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$vantage = '
		.dtbaker-elementor-template-std #main {
            padding: 0;
	        max-width: 100%;
        }

        .dtbaker-elementor-template-std.responsive.layout-full #main .full-container {
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
        }
	';
	wp_add_inline_style( 'vantage-style', $vantage );
