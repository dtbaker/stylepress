<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


$style = '	    
		.entry-header {
			background-color: #fff;	
		}		
		.entry-header .grid-container {
			padding: 10px 10px;
		}
	';
wp_add_inline_style( 'generate-style', $style );
