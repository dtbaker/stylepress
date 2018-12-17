<?php
/**
 * Layout for previewing our site wide styles
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

do_action( 'get_header', 'stylepress' );


$categories = Styles::get_instance()->get_categories();

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php

if ( ! empty( $GLOBALS['stylepress_render'] ) ) {

	foreach ( $categories as $category ) {
		if ( isset( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] ) ) {
			if ( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] > 0 ) {
				if(STYLEPRESS_DEBUG_OUTPUT) {
					$template = get_post( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] );

					Plugin::get_instance()->debug_message( 'Rendering template ' . esc_html($template->post_title).' (#'. $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] .') for section ' . $category['slug'] );
				}
				$with_css = false;
				echo \Elementor\Plugin::$instance->frontend->get_builder_content( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ], $with_css );
			}
		}
	}
}
the_content();

do_action( 'get_footer', 'stylepress' );
wp_footer();
?>

</body>
</html>