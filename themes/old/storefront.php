<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


$css = '
		.stylepress-template-std .site-content .col-full {
            max-width: 100%;
            padding: 0;
			margin: 0;
        }
		
		.stylepress-template-std .site-header {
           margin-bottom: 0;
        }
	';
wp_add_inline_style( 'storefront-style', $css );
