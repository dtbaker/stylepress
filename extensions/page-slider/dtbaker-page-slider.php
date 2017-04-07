<?php


defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


add_action( 'wp_enqueue_scripts', function() {

	wp_register_script( 'dtbaker_page_slider', DTBAKER_ELEMENTOR_URI . 'extensions/page-slider/js/dtbaker-page-slider.js', array(
		'jquery',
		'jquery-slick'
	), DTBAKER_ELEMENTOR_VERSION );
	wp_enqueue_script( 'dtbaker_page_slider' );
	wp_register_style( 'dtbaker_page_slider', DTBAKER_ELEMENTOR_URI . 'extensions/page-slider/css/page-slider.css', false, DTBAKER_ELEMENTOR_VERSION );
	wp_enqueue_style( 'dtbaker_page_slider' );
});



$widget_file   = DTBAKER_ELEMENTOR_PATH . 'extensions/page-slider/elementor-page-slider.php';
//$template_file = locate_template( $widget_file );
//if ( $template_file && is_readable( $template_file ) ) {
require_once $widget_file;

