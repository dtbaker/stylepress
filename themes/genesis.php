<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$genesis = '
		.dtbaker-elementor-template .site-inner,
		.dtbaker-elementor-template-std .site-inner {
            max-width: 100%;
			width: 100%;
            padding: 0;
			margin: 0;			
        }
		.dtbaker-elementor-template .elementor-page,
		.dtbaker-elementor-template-std .elementor-page .site-inner {
            padding-top: 0;
			max-width: 100%;
			width: 100%;
            overflow: hidden;			
        }
		@media only screen and (max-width: 860px) {
			.dtbaker-elementor-template-std .site-inner {
				padding: 0;				
			}
		}
	';
	wp_add_inline_style( 'elementor-frontend', $genesis );
