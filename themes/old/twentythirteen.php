<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$thirteen = '
	    .dtbaker-elementor-template-std .elementor-page .site {
            max-width: 100%;
			overflow: hidden;
        }
        .dtbaker-elementor-template-std .site-header {
            max-width: 100%;
			background-size: 3200px auto;
        }
		.dtbaker-elementor-template-std .navbar {
            max-width: 100%;
            width: 100%;
        }
	';
	wp_add_inline_style( 'twentythirteen-style', $thirteen );
