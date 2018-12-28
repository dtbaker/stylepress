<?php
/**
 * Full page template used for rending our custom layouts
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

$categories = Styles::get_instance()->get_categories();

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
$post              = get_post();
if ( $post->post_type === Styles::CPT ) {
	$post_categories = get_the_terms( $post->ID, STYLEPRESS_SLUG . '-cat' );
	$categories      = Styles::get_instance()->get_categories();
	foreach ( $categories as $category ) {
		foreach ( $post_categories as $post_category ) {
			if ( $post_category->slug === $category['slug'] && ! empty( $category['inner'] ) ) {
				$is_inner_template = true;
			}
		}
	}
}
if($is_inner_template){
	?>
	<div>
		INNER TEMPLATE WARNING GOES HERE.
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
