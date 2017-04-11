<?php


if(!empty($GLOBALS['stylepress_slidein'])) {

	wp_enqueue_style( 'wp-jquery-ui-dialog' );
	wp_enqueue_style( 'stylepress-modal-button' );
	wp_enqueue_script( 'stylepress-modal-popup' );
	wp_enqueue_script( 'jquery-ui-dialog' );

	echo '<div class="offcanvas-slideins">';

	foreach ( $GLOBALS['stylepress_slidein'] as $template_id => $options ) {
        $width = ! empty( $options['width'] ) ? (int) $options['width'] : 400;
        ?>
        <section class="stylepress_slide_in_menu right" data-size="<?php echo $width; ?>"
                 style="width: <?php echo $width; ?>px;" data-id="<?php echo (int) $template_id; ?>"
                 tabindex="5000">
            <a href="/" target="_self" class="close_sidebar"></a>
            <div>
                <?php
                echo \Elementor\Plugin::instance()->frontend->get_builder_content( $template_id, false );
                ?>
            </div>
        </section>
        <?php
	}
	echo '</div>';
}

if(!empty($GLOBALS['stylepress_modal_popups'])) {

	wp_enqueue_style( 'wp-jquery-ui-dialog' );
	wp_enqueue_style( 'stylepress-modal-button' );
	wp_enqueue_script( 'stylepress-modal-popup' );
	wp_enqueue_script( 'jquery-ui-dialog' );


	foreach($GLOBALS['stylepress_modal_popups'] as $template_id => $options) {
        ?>
        <div class="stylepress-modal-pop" id="stylepress-modal-pop-<?php echo (int) $template_id; ?>">
            <div class="stylepress-modal-inner">
                <?php
                echo \Elementor\Plugin::instance()->frontend->get_builder_content( $template_id, false );
                ?>
            </div>
        </div>
        <?php
    }

} ?>