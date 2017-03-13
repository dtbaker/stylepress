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

<body <?php body_class('stylepress-editor'); ?>>
<?php

do_action( 'stylepress/before-render' );

?>
<!-- stylepress editor template begin -->
<?php
//do_action( 'stylepress/render-inner' ); // Priority 20 is the_content().
the_content();
?>
<!-- stylepress editor template end -->
<?php
do_action( 'stylepress/after-render' );

wp_footer();
?>
</body>
</html>
