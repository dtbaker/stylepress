<?php
/**
 * Full page template used for rending our custom layouts
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
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'stylepress-editor' ); ?>>
<?php
do_action( 'stylepress/before-render' );
?>
<!-- stylepress editor template begin -->
<?php
//do_action( 'stylepress/render-inner' ); // Priority 20 is the_content().
$is_inner_template = false;
$current_page_category = false;
$post              = get_post();
if ( $post->post_type === Styles::CPT ) {
	$post_categories = get_the_terms( $post->ID, STYLEPRESS_SLUG . '-cat' );
	$categories      = Styles::get_instance()->get_categories();
	foreach ( $categories as $category ) {
		foreach ( $post_categories as $post_category ) {
			if ( $post_category->slug === $category['slug'] && ! empty( $category['inner'] ) ) {
				$current_page_category = $category;
				$is_inner_template = true;
			}
		}
	}
	?>
	<div class="stylepress__header stylepress__header--editor">
		<div class="stylepress__logo">
			<img alt="StylePress" src="<?php echo esc_url( STYLEPRESS_URI . 'assets/images/logo-stylepress-sml.png' ); ?>">
		</div>
		<div class="stylepress__editor-info">
			<h3><?php echo esc_html($current_page_category['title']);?> Style: <span><?php
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
