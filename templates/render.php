<?php
/**
 * This is used in the back end editor, and the front end display.
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

do_action( 'get_header', 'stylepress' );

$categories = Styles::get_instance()->get_categories();

$page_classes_template = false;

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php
	// pull in the default page classes
	if ( ! empty( $GLOBALS['stylepress_render'] ) ) {
		foreach ( $categories as $category ) {
			if ( ! empty( $category['page_style'] ) ) {
				if ( isset( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] ) ) {
					if ( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] > 0 ) {
						$page_classes_template = get_post( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] );
						ElementorCSS::get_instance()->render_css_header( $page_classes_template );
					}
				}
			}
		}
	}
	wp_head();
	?>
</head>
<body <?php body_class(); ?>>
<?php
if ( $page_classes_template ) {
	Plugin::get_instance()->debug_message( 'Using default page classes:  ' . esc_html( $page_classes_template->post_title ) . ' (#' . $page_classes_template->ID . ')' );
}

do_action( 'stylepress/before-render' );
if ( ! empty( $GLOBALS['stylepress_render'] ) ) {
	foreach ( $categories as $category ) {
		if ( ! empty( $category['page_style'] ) ) {
			continue;
		}
		if ( STYLEPRESS_DEBUG_OUTPUT ) {
			if ( isset( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] ) ) {
				if ( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] > 0 ) {
					$template = get_post( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] );
					Plugin::get_instance()->debug_message( 'Rendering template ' . esc_html( $template->post_title ) . ' (#' . $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] . ') for section ' . $category['slug'] );
				} else {
					Plugin::get_instance()->debug_message( 'Blank template chosen for section ' . $category['slug'] );
				}
			} else {
				Plugin::get_instance()->debug_message( 'No template chosen for section ' . $category['slug'] );
			}
		}
		if ( isset( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] ) ) {
			if ( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ] > 0 ) {
				$with_css = false;
				echo \Elementor\Plugin::$instance->frontend->get_builder_content( $GLOBALS['stylepress_render']['styles'][ $category['slug'] ], $with_css );
			}
		}
		if ( ! empty( $category['inner'] ) && empty( $GLOBALS['stylepress_render']['has_done_inner_content'] ) ) {
			the_post();
			the_content();
		}
	}
}

do_action( 'stylepress/after-render' );
do_action( 'get_footer', 'stylepress' );
wp_footer();
?>

</body>
</html>