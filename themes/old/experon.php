<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


$experon = '
		.stylepress-template-std #content {
            padding: 0;
        }
		.stylepress-template-std #content-core {
            max-width: 100%;
        }
		.stylepress-template-std #intro {
			display: none;
		}
	';
wp_add_inline_style( 'thinkup-style', $experon );
