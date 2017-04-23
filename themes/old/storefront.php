<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$css = '
		.dtbaker-elementor-template-std .site-content .col-full {
            max-width: 100%;
            padding: 0;
			margin: 0;
        }
		
		.dtbaker-elementor-template-std .site-header {
           margin-bottom: 0;
        }
	';
	wp_add_inline_style( 'storefront-style', $css );
