<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package stylepress
 */

defined( 'STYLEPRESS_PATH' ) || exit;


$style = '	    
		body.stylepress-template.elementor-page,
		body.stylepress-template-std.elementor-page {
			background: transparent;
		}
		.stylepress-template-std .site {
			margin: 0;
		}
		.stylepress-template .elementor-page,
        .stylepress-template-std .elementor-page {
	        overflow: hidden;
        }
		.stylepress-template .full-width,
        .stylepress-template-std .full-width {
	        width: 100%;
        }
        .stylepress-template .site-inner,
        .stylepress-template-std .site-inner {
            max-width: 100%;
        }
        .stylepress-template .site-content,
        .stylepress-template-std .site-content {
            padding: 0;
        }
        .stylepress-template header#masthead,
        .stylepress-template footer#colophon,
        .stylepress-template-std header#masthead,
        .stylepress-template-std footer#colophon {
	        margin: 0 auto;
	        max-width: 1320px;
        } 
        .stylepress-template .entry-content,
        .stylepress-template-std .entry-content {
	        margin-right: auto;
	        margin-left: auto;
        }
        @media screen and (min-width: 56.875em) {
	        .admin-bar .anchor-menu {
		        top: 20px;
	        }	
			.admin-bar .anchor-menu-fixed.anchor-menu, .admin-bar .anchor-menu-fixed.elementor-widget-wp-widget-nav_menu {
				top: 54px !important;
			}			
			.stylepress-template .entry-content,
            .stylepress-template-std .entry-content	{
	            margin-right: auto;
	            margin-left: auto;
	        }
        }
        @media screen and (min-width: 44.375em) {	        
			.stylepress-template .entry-content,
            .stylepress-template-std .entry-content {
	            margin-right: auto;;
	        }
        }
	';
wp_add_inline_style( 'twentysixteen-style', $style );
