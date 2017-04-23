<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$edge = '
		.dtbaker-elementor-template-std #content {
            padding: 0;
        }
		.dtbaker-elementor-template-std #content .container {
            max-width: 100%;
        }
		.dtbaker-elementor-template-std .page-header {
            display: none;
        }
		@media only screen and (max-width: 1023px) {
			.dtbaker-elementor-template-std #content .container {
                width: 100%;
            }
		}
	';
wp_add_inline_style( 'edge-style', $edge );
