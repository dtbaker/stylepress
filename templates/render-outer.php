<?php
/**
 * Layout for previewing our site wide styles
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

ob_start();
?>
<!-- stylepress render outer template begin -->
<?php
if ( ! empty( $GLOBALS['our_elementor_template'] ) && $GLOBALS['our_elementor_template'] > 0 ) {
	$GLOBALS['stylepress_only_render'] = 'all';
	echo Elementor\Plugin::instance()->frontend->get_builder_content( $GLOBALS['our_elementor_template'], false );
} else {
	echo 'Please select a global site style';
}
//if ( ! empty( $GLOBALS['stylepress_manual_inner_content'] ) ) {
//    echo $GLOBALS['stylepress_manual_inner_content'];
//}else{
//	echo 'Failed to render content.';
//}
?>
<!-- stylepress render outer template end -->
<?php
$inner_content = ob_get_clean();

if(empty($GLOBALS['stylepressheader']) || empty($GLOBALS['stylepressfooter'])){
    echo "Sorry this theme is not supported by StylePress. Please see here for details: <a href='https://stylepress.org/elementor/compatible-wordpress-themes/'>https://stylepress.org/elementor/compatible-wordpress-themes/</a>";

	echo $inner_content;

}else{

	echo $GLOBALS['stylepressheader'];
	echo $inner_content;
	echo $GLOBALS['stylepressfooter'];
}
