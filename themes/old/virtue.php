<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$virtue = '
		.dtbaker-elementor-template .headerclass,
        .dtbaker-elementor-template .footerclass {
            display: none;
        }
        .dtbaker-elementor-template .contentclass,
        .dtbaker-elementor-template-std .contentclass {
            padding-bottom: 0;
        }
		.dtbaker-elementor-template .contentclass {
            padding-top: 0;
        }
	';
	wp_add_inline_style( 'kadence_theme', $virtue );
