<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


$style = '
	    body.stylepress-template:before {
		    display: none;
	    }
		.stylepress-template .site,
		.stylepress-template-std .site	{
            max-width: 100%;
			margin: 0;
        }
        .stylepress-template .elementor-page	{
            overflow: hidden;
        }
        body.stylepress-template-std:before {
		    width: 29.4118%;
	    }		
		.stylepress-template-std .site-footer {
            width: 71%;
			margin-left: 29%;
        }
	';
wp_add_inline_style( 'twentyfifteen-style', $style );
