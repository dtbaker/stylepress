<?php
/**
 * Custom CSS Support for 3rd party theme
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$style = '	    
.elementor-editor-active .site-content {
	padding: 2.5em 0 0;
}		
.elementor-page .site-content {
	padding: 0;
}
.elementor-page.page:not(.home) #content {
	padding-bottom: 0;
}		
.elementor-page .site-footer {
	margin-top: 0;
}
form.search-form{
    display: flex;
}
form.search-form label{
    display:none;
}
form.search-form .icon-search{
        width: 13px;
        height: 16px;
}
form.search-form .search-field {
	flex:1;
}
';
	wp_add_inline_style( 'dtbaker-elementor-css', $style );
