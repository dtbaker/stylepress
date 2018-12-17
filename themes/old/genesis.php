<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


$genesis = '
		.stylepress-template .site-inner,
		.stylepress-template-std .site-inner {
            max-width: 100%;
			width: 100%;
            padding: 0;
			margin: 0;			
        }
		.stylepress-template .elementor-page,
		.stylepress-template-std .elementor-page .site-inner {
            padding-top: 0;
			max-width: 100%;
			width: 100%;
            overflow: hidden;			
        }
		@media only screen and (max-width: 860px) {
			.stylepress-template-std .site-inner {
				padding: 0;				
			}
		}
	';
wp_add_inline_style( 'elementor-frontend', $genesis );
