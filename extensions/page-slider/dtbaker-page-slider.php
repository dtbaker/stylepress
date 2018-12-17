<?php


defined( 'STYLEPRESS_PATH' ) || exit;


add_action( 'wp_enqueue_scripts', function () {

	wp_register_script( 'stylepress_page_slider', STYLEPRESS_URI . 'extensions/page-slider/js/stylepress-page-slider.js', array(
		'jquery',
		'jquery-slick'
	), STYLEPRESS_VERSION, true );
	wp_enqueue_script( 'stylepress_page_slider' );
	wp_register_style( 'stylepress_page_slider', STYLEPRESS_URI . 'extensions/page-slider/css/page-slider.css', false, STYLEPRESS_VERSION );
	wp_enqueue_style( 'stylepress_page_slider' );
} );


$widget_file = STYLEPRESS_PATH . 'extensions/page-slider/elementor-page-slider.php';
//$template_file = locate_template( $widget_file );
//if ( $template_file && is_readable( $template_file ) ) {
require_once $widget_file;

