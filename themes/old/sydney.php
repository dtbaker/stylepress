<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


$sydney = '
		.stylepress-template-std .page-wrap,
        .stylepress-template-std .page-wrap .content-wrapper {
            padding: 0;
			margin: 0;
        }
		.stylepress-template-std .elementor-page .page-wrap .container {
            width: 100%;
	        overflow: hidden;
        }
		.stylepress-template-std .page .entry-header,
        .stylepress-template-std .page .entry-footer {
           display: none;
        }
	';
wp_add_inline_style( 'sydney-style', $sydney );
