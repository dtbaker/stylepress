<?php
/**
 * Layout for previewing our site wide styles
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


if ( ! empty( $GLOBALS['stylepress_footer'] ) ) {
	echo $GLOBALS['stylepress_footer'];
}

do_action( 'stylepress/after-render' );

wp_footer();
?>
</body>
</html>
