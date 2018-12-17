<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


$vantage = '
		.stylepress-template-std #main {
            padding: 0;
	        max-width: 100%;
        }

        .stylepress-template-std.responsive.layout-full #main .full-container {
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
        }
	';
wp_add_inline_style( 'vantage-style', $vantage );
