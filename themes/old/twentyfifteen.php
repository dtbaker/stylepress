<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$style = '
	    body.dtbaker-elementor-template:before {
		    display: none;
	    }
		.dtbaker-elementor-template .site,
		.dtbaker-elementor-template-std .site	{
            max-width: 100%;
			margin: 0;
        }
        .dtbaker-elementor-template .elementor-page	{
            overflow: hidden;
        }
        body.dtbaker-elementor-template-std:before {
		    width: 29.4118%;
	    }		
		.dtbaker-elementor-template-std .site-footer {
            width: 71%;
			margin-left: 29%;
        }
	';
	wp_add_inline_style( 'twentyfifteen-style', $style );
