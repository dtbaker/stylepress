<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


$thirteen = '
	    .stylepress-template-std .elementor-page .site {
            max-width: 100%;
			overflow: hidden;
        }
        .stylepress-template-std .site-header {
            max-width: 100%;
			background-size: 3200px auto;
        }
		.stylepress-template-std .navbar {
            max-width: 100%;
            width: 100%;
        }
	';
wp_add_inline_style( 'twentythirteen-style', $thirteen );
