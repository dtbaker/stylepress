<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


$edge = '
		.stylepress-template-std #content {
            padding: 0;
        }
		.stylepress-template-std #content .container {
            max-width: 100%;
        }
		.stylepress-template-std .page-header {
            display: none;
        }
		@media only screen and (max-width: 1023px) {
			.stylepress-template-std #content .container {
                width: 100%;
            }
		}
	';
wp_add_inline_style( 'edge-style', $edge );
