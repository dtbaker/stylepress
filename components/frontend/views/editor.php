<?php
/**
 * Special template used when editing back end StylePress styles.
 *
 * @package stylepress
 */

namespace StylePress;

use StylePress\Styles\Data;

defined( 'STYLEPRESS_VERSION' ) || exit;

$categories = Data::get_instance()->get_categories();
$post = get_post();
$is_inner_template = false;
$current_page_category = false;
$post_categories = get_the_terms( $post->ID, STYLEPRESS_SLUG . '-cat' );

if($post_categories) {
	foreach ( $categories as $category ) {
		foreach ( $post_categories as $post_category ) {
			if ( $post_category->slug === $category['slug'] ) {
				$current_page_category = $category;
				if ( ! empty( $category['inner'] ) ) {
					$is_inner_template = true;
				}
			}
		}
	}
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php

	// We override the Elementor default active kit here based on the current page selection:
//	$elementor_template_type = get_post_meta( $post->ID, '_elementor_template_type', true );
//	if ( $elementor_template_type === 'kit' ) {
//		add_action( 'pre_option_elementor_active_kit', function ( $kit_id ) use ( $post ) {
//			if ( $post && $post->ID ) {
//				$kit_id = $post->ID;
//			}
//
//			return $kit_id;
//		} );
//	} else {
//		// we're editing another type of non kit page
//		add_action( 'pre_option_elementor_active_kit', function ( $kit_id ) use ( $current_page_category ) {
//			$default_styles = Styles::get_instance()->get_default_styles();
//			if ( $default_styles && ! empty( $default_styles['_global'] ) && ! empty( $default_styles['_global']['theme_styles'] ) ) {
//				$kit_id = $default_styles['_global']['theme_styles'];
//			}
//
//			return $kit_id;
//		} );
//	}
	wp_head(); ?>
</head>
<body <?php body_class( 'stylepress-editor' ); ?>>
<?php
do_action( 'stylepress/before-render' );
?>
<!-- stylepress editor template begin -->
<?php
//do_action( 'stylepress/render-inner' ); // Priority 20 is the_content().
setup_postdata( $post );
?>
<div class="stylepress__header stylepress__header--editor">
	<div class="stylepress__logo">
		<img alt="StylePress" src="<?php echo esc_url( STYLEPRESS_URI . 'src/images/logo-stylepress-sml.png' ); ?>">
	</div>
	<div class="stylepress__editor-info">
		<h3><?php echo esc_html( $current_page_category['title'] ); ?> Style: <span><?php
				if ( $post->post_parent ) {
					$parent = get_post( $post->post_parent );
					echo esc_html( $parent->post_title ) . ' > ';
				}
				echo esc_html( $post->post_title ); ?></span></h3>
		<?php
		if ( $is_inner_template ) {
			?>
			<div class="stylepress__editor-infotext--warning">
				<strong>Important:</strong> Please add at least one <strong>Inner Content</strong> widget to the page.
			</div>
			<?php
		}
		?>
	</div>
</div>
<?php
the_content();
?>
<!-- stylepress editor template end -->
<?php
do_action( 'stylepress/after-render' );

wp_footer();
?>
</body>
</html>
