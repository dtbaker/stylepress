<?php


defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

// we also add some custom hacks for the dynamic field support to existing elementor features.
add_filter( 'elementor/widget/button/skins_init', function ( $element ) {
	//	require_once DTBAKER_ELEMENTOR_PATH . 'extensions/skins/button-dynamic.php';
	//	$element->add_skin( new StylePress\Elementor\Skins\Skin_StylePressButtonDynamic( $element ) );
} );

add_filter( 'elementor/widget/image/skins_init', function ( $element ) {
	//	require_once DTBAKER_ELEMENTOR_PATH . 'extensions/skins/image-dynamic.php';
	//	$element->add_skin( new StylePress\Elementor\Skins\Skin_StylePressDynamic_Image( $element ) );
} );

// we want a skin for list icon to customize the generated alignment rules.

add_filter( 'elementor/widget/icon-list/skins_init', function ( $element ) {
	require_once DTBAKER_ELEMENTOR_PATH . 'extensions/skins/icon-list.php';
	$element->add_skin( new StylePress\Elementor\Skins\Skin_StylePressIconList( $element ) );
} );

do_action( 'stylepress/elementor/skins' );