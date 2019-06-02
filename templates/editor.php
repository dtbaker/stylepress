<?php
/**
 * Special template used when editing back end StylePress styles.
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php
	$categories = Styles::get_instance()->get_categories();
	foreach ( $categories as $category ) {
		if ( ! empty( $category['page_style'] ) ) {
			$styles = Styles::get_instance()->get_all_styles( $category['slug'] );
			foreach ( $styles as $style_id => $style_name ) {
				$page_classes_template = get_post( $style_id );
				ElementorCSS::get_instance()->render_css_header( $page_classes_template );
			}
		}
	}
	wp_head(); ?>
</head>
<body <?php body_class( 'stylepress-editor' ); ?>>
<?php
do_action( 'stylepress/before-render' );
?>
<!-- stylepress editor template begin -->
<?php
//do_action( 'stylepress/render-inner' ); // Priority 20 is the_content().
$is_inner_template     = false;
$is_style_template     = false;
$current_page_category = false;
$post                  = get_post();
$post_categories       = get_the_terms( $post->ID, STYLEPRESS_SLUG . '-cat' );
setup_postdata( $post );
foreach ( $categories as $category ) {
	foreach ( $post_categories as $post_category ) {
		if ( $post_category->slug === $category['slug'] ) {
			$current_page_category = $category;
			if ( ! empty( $category['inner'] ) ) {
				$is_inner_template = true;
			}
			if ( ! empty( $category['page_style'] ) ) {
				$is_style_template = true;
			}
		}
	}
}
if ( $is_style_template ) {
	?>
	<style>
		.elementor-element-edit-mode {
			margin-top: 60px;
		}
	</style>
	<?php
}
if ( $is_style_template ) {
	?>
	<div class="stylepress__header stylepress__header--editor">
		<div class="stylepress__logo">
			<img alt="StylePress" src="<?php echo esc_url( STYLEPRESS_URI . 'assets/images/logo-stylepress-sml.png' ); ?>">
		</div>
		<div class="stylepress__editor-info">
			<h3><?php echo esc_html( $current_page_category['title'] ); ?>: <span><?php
					if ( $post->post_parent ) {
						$parent = get_post( $post->post_parent );
						echo esc_html( $parent->post_title ) . ' > ';
					}
					echo esc_html( $post->post_title ); ?></span></h3>
				<div class="stylepress__editor-infotext">
					This page lets you configure default styles that can be used on elements. Help Link / More Details Here.
				</div>
				<div class="stylepress__default-components">
					<p>To style these default components please click the "Settings" button (bottom left) and go to "Styles".</p>
					<fieldset class="stylepress__default-component">
						<label>Links</label>
						<div class="stylepress__default-variations elementor-text-editor">
							<div class="stylepress__default-variation">
								<a href="https://stylepress.org" target="_blank" class="stylepress__preview-a-normal">Normal Link</a>
							</div>
							<div class="stylepress__default-variation">
								<a href="https://stylepress.org" target="_blank" class="stylepress__preview-a-hover">Hover Link</a>
							</div>
							<div class="stylepress__default-variation">
								<a href="https://stylepress.org" target="_blank" class="stylepress__preview-a-active">Active Link</a>
							</div>
						</div>
					</fieldset>
				</div>
		</div>
	</div>
	<?php
}else{
	?>
	<div class="stylepress__header stylepress__header--editor">
		<div class="stylepress__logo">
			<img alt="StylePress" src="<?php echo esc_url( STYLEPRESS_URI . 'assets/images/logo-stylepress-sml.png' ); ?>">
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
}
the_content();
?>
<!-- stylepress editor template end -->
<?php
do_action( 'stylepress/after-render' );

wp_footer();
?>
</body>
</html>
