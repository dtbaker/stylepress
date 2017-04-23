<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$sydney = '
		.dtbaker-elementor-template-std .page-wrap,
        .dtbaker-elementor-template-std .page-wrap .content-wrapper {
            padding: 0;
			margin: 0;
        }
		.dtbaker-elementor-template-std .elementor-page .page-wrap .container {
            width: 100%;
	        overflow: hidden;
        }
		.dtbaker-elementor-template-std .page .entry-header,
        .dtbaker-elementor-template-std .page .entry-footer {
           display: none;
        }
	';
	wp_add_inline_style( 'sydney-style', $sydney );
