<?php
/**
 * Full page template used for rending our custom layouts
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

do_action( 'elementor/full-page/before' );

while ( have_posts() ) : the_post();
	do_action( 'elementor/full-page/inner' ); // Priority 20 is the_content().
endwhile;

do_action( 'elementor/full-page/after' );

wp_footer();
?>
</body>
</html>
