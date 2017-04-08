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

<body <?php body_class('stylepress-render'); ?>>
<?php

$page_type = DtbakerElementorManager::get_instance()->get_current_page_type();
DtbakerElementorManager::get_instance()->debug_message("render.php: Rendering full page output for page type '$page_type' in render.php using the style: ". (
        !empty($GLOBALS['our_elementor_template']) ? '<a href="'.get_permalink($GLOBALS['our_elementor_template']).'">' . esc_html(get_the_title($GLOBALS['our_elementor_template'])) .'</a> ' . $GLOBALS['our_elementor_template'] : 'NONE'
    ).'');

if(DtbakerElementorManager::get_instance()->removing_theme_css) {
	DtbakerElementorManager::get_instance()->debug_message( "render.php: Removing the default theme CSS files" );
}

do_action( 'stylepress/before-render' );


?>
<!-- stylepress render template begin -->
<?php
if ( ! empty( $GLOBALS['our_elementor_template'] ) ) {
	$GLOBALS['stylepress_only_render'] = 'all';
	echo Elementor\Plugin::instance()->frontend->get_builder_content( $GLOBALS['our_elementor_template'], false );
} else {
	echo 'Please select a site style';
}
?>
<!-- stylepress render template end -->
<?php

do_action( 'stylepress/after-render' );


wp_footer();
?>
</body>
</html>
