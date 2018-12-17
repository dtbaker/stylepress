<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


$virtue = '
		.stylepress-template .headerclass,
        .stylepress-template .footerclass {
            display: none;
        }
        .stylepress-template .contentclass,
        .stylepress-template-std .contentclass {
            padding-bottom: 0;
        }
		.stylepress-template .contentclass {
            padding-top: 0;
        }
	';
wp_add_inline_style( 'kadence_theme', $virtue );
