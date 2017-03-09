<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$style = '	    
		body.dtbaker-elementor-template.elementor-page,
		body.dtbaker-elementor-template-std.elementor-page {
			background: transparent;
		}
		.dtbaker-elementor-template-std .site {
			margin: 0;
		}
		.dtbaker-elementor-template .elementor-page,
        .dtbaker-elementor-template-std .elementor-page {
	        overflow: hidden;
        }
		.dtbaker-elementor-template .full-width,
        .dtbaker-elementor-template-std .full-width {
	        width: 100%;
        }
        .dtbaker-elementor-template .site-inner,
        .dtbaker-elementor-template-std .site-inner {
            max-width: 100%;
        }
        .dtbaker-elementor-template .site-content,
        .dtbaker-elementor-template-std .site-content {
            padding: 0;
        }
        .dtbaker-elementor-template header#masthead,
        .dtbaker-elementor-template footer#colophon,
        .dtbaker-elementor-template-std header#masthead,
        .dtbaker-elementor-template-std footer#colophon {
	        margin: 0 auto;
	        max-width: 1320px;
        } 
        .dtbaker-elementor-template .entry-content,
        .dtbaker-elementor-template-std .entry-content {
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
			.dtbaker-elementor-template .entry-content,
            .dtbaker-elementor-template-std .entry-content	{
	            margin-right: auto;
	            margin-left: auto;
	        }
        }
        @media screen and (min-width: 44.375em) {	        
			.dtbaker-elementor-template .entry-content,
            .dtbaker-elementor-template-std .entry-content {
	            margin-right: auto;;
	        }
        }
	';
	wp_add_inline_style( 'twentysixteen-style', $style );
