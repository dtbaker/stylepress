<?php
/**
 * Meta box under styles.
 *
 * @package stylepress
 */


defined( 'STYLEPRESS_PATH' ) || exit;

// main style first. followed by others.
// component styles next.

wp_nonce_field( 'stylepress_style_nonce', 'stylepress_style_nonce' );


if ( $post->post_parent ) {
	// we already editing a child style. Show configuration options instead of sub list.

	?>
	<input type="hidden" name="stylepress_is_component_check" value="1">
	<input type="checkbox" name="stylepress_is_component"
	       value="1" <?php echo isset( $_GET['stylepress_component'] ) || get_post_meta( $post->ID, 'stylepress_is_component', true ) ? ' checked' : ''; ?>> Make this a component.

	<style type="text/css">
		/* todo: move this into a body class and put the style in admin.less */
		.wp-admin.post-type-stylepress_style #elementor-editor {
			display: inline;
		}
	</style>
	<?php

} else {

	echo 'No settings here yet';

}