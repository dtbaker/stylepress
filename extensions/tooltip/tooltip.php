<?php


defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


add_action( 'wp_enqueue_scripts', function(){
	wp_enqueue_style( 'stylepress-tooltips', DTBAKER_ELEMENTOR_URI . 'extensions/tooltip/tooltip.css' );
	wp_enqueue_script( 'stylepress-tooltips', DTBAKER_ELEMENTOR_URI . 'extensions/tooltip/tlight.js' );
} );
