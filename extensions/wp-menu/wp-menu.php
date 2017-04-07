<?php

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


add_action( 'wp_enqueue_scripts', function(){
	wp_enqueue_style( 'stylepress-nav-menu', DTBAKER_ELEMENTOR_URI . 'extensions/wp-menu/menu.css', false );
	wp_enqueue_script( 'stylepress-nav-menu', DTBAKER_ELEMENTOR_URI . 'extensions/wp-menu/navigation.js', array('jquery') );
} );


// todo: option these out in 'Add-Ons' section
require_once DTBAKER_ELEMENTOR_PATH . 'extensions/wp-menu/widget.wp-menu.php';
