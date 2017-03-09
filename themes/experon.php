<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$experon = '
		.dtbaker-elementor-template-std #content {
            padding: 0;
        }
		.dtbaker-elementor-template-std #content-core {
            max-width: 100%;
        }
		.dtbaker-elementor-template-std #intro {
			display: none;
		}
	';
	wp_add_inline_style( 'thinkup-style', $experon );
