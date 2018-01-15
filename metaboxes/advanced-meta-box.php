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
    <?php _e('(nothing here yet)', 'stylepress'); ?>
    <?php

}else {


    $current_settings = $this->get_settings();
    $advanced = $this->get_advanced($post->ID,false);


	?>
    <div id="stylepress-advanced-metabox" class="stylepress-metabox dtbaker-elementor-browser">

        <h3><?php _e('Font Rules:', 'stylepress'); ?> </h3>
        <div class="inner-wrap">
        <p><?php _e('This is a font configuration file for the Easy Google Fonts plugin. This lets you configure site wide styles from the Apperance > Customize > Typography window.', 'stylepress'); ?></p>
        <textarea class="advanced-settings" name="stylepress_advanced[font]"><?php echo esc_textarea(!empty($advanced['font']) ? $advanced['font'] : '');?></textarea>
        </div>

        <h3><?php _e('Elementor Overrides:', 'stylepress'); ?> </h3>
        <div class="inner-wrap">
        <p><?php _e('This is a json Elementor configuration file for advanced Elementor tweaks. Adding things like new drop downs to existing widgets etc...', 'stylepress'); ?></p>
        <textarea class="advanced-settings" name="stylepress_advanced[elementor]"><?php echo esc_textarea(!empty($advanced['elementor']) ? $advanced['elementor'] : '');?></textarea>
        </div>

        <h3><?php _e('Style CSS:', 'stylepress'); ?> </h3>
            <div class="inner-wrap">
        <p><?php _e('We try to avoid writing manual CSS, but sometimes it is needed. These CSS rules will be applied to any page that uses one of these styles.', 'stylepress'); ?></p>
        <textarea class="advanced-settings" name="stylepress_advanced[css]"><?php echo esc_textarea(!empty($advanced['css']) ? $advanced['css'] : '');?></textarea>
            </div>

    </div>

	<?php

}