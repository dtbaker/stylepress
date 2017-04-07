<?php


defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


// we also add some custom hacks for the dynamic field support to existing elementor features.
add_filter( 'elementor/widget/button/skins_init', function($element){
	require_once DTBAKER_ELEMENTOR_PATH . 'extensions/skins/button-dynamic.php';
	$element->add_skin( new StylePress\Elementor\Skins\Skin_StylePressButtonDynamic( $element ) );
});

add_filter( 'elementor/widget/image/skins_init', function($element){
	require_once DTBAKER_ELEMENTOR_PATH . 'extensions/skins/image-dynamic.php';
	$element->add_skin( new StylePress\Elementor\Skins\Skin_StylePressDynamic_Image( $element ) );
});

do_action( 'stylepress/elementor/skins' );