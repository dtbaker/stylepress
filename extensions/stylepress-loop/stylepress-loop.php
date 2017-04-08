<?php


/**
 * Class dtbaker_Widget_Google_Map and dtbaker_Shortcode_Google_Map
 * Easily create a Google Map on any WordPress post/page (with an insert map button).
 * Easily create a Google Map in any Widget Area.
 * Author: dtbaker@gmail.com
 * Copyright 2014
 */


defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


add_action( 'wp_enqueue_scripts', function(){


	wp_register_style( 'stylepress-loop', DTBAKER_ELEMENTOR_URI . 'extensions/stylepress-loop/stylepress-loop.css' );

	if( isset($_GET['elementor']) || isset($_GET['elementor-preview'])) { //\Elementor\Plugin::$instance->editor->is_edit_mode()){
		wp_enqueue_style('stylepress-loop');
	}

} );

$widget_file   = DTBAKER_ELEMENTOR_PATH . 'extensions/stylepress-loop/widget.stylepress-loop.php';
//$template_file = locate_template( $widget_file );
//if ( $template_file && is_readable( $template_file ) ) {
require_once $widget_file;

