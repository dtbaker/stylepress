<?php
/**
 * Layout for previewing our site wide styles
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php


do_action( 'stylepress/before-render' );

if ( ! empty( $GLOBALS['our_elementor_template'] ) ) {
	$GLOBALS['stylepress_only_render'] = 'header';
	echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $GLOBALS['our_elementor_template'] );
} else {
	echo 'Please select a site style';
}