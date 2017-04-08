<?php
/**
 * Meta box under styles.
 *
 * @package dtbaker-elementor
 */


defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

// main style first. followed by others.
// component styles next.

wp_nonce_field( 'dtbaker_elementor_style_nonce', 'dtbaker_elementor_style_nonce' );


if( $post->post_parent ){
    // we already editing a child style. Show configuration options instead of sub list.

    ?>
    <input type="hidden" name="dtbaker_is_component_check" value="1">
    <input type="checkbox" name="dtbaker_is_component" value="1" <?php echo isset($_GET['dtbaker_component']) || get_post_meta( $post->ID, 'dtbaker_is_component', true ) ? ' checked' : '';?>> Make this a component.

    <style type="text/css">
        /* todo: move this into a body class and put the style in admin.less */
        .wp-admin.post-type-dtbaker_style #elementor-editor{
            display:inline;
        }
    </style>
    <?php

}else {

    echo 'No settings here yet';

}