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

	$args        = array(
		'post_type'           => 'dtbaker_style',
		'post_parent'         => $post->ID,
		'post_status'         => 'any',
		'posts_per_page'      => - 1,
		'ignore_sticky_posts' => 1,
	);
	$posts_array = get_posts( $args );

	$styles = $components = array();

	$post->post_title = $post->post_title . ' (Main)';
	$styles[]         = $post;

	foreach ( $posts_array as $post_array ) {
		if ( get_post_meta( $post_array->ID, 'dtbaker_is_component', true ) ) {
			$components[] = $post_array;
		} else {
			$styles[] = $post_array;
		}

	}


	$settings = DtbakerElementorManager::get_instance()->get_settings();
	$page_types = DtbakerElementorManager::get_instance()->get_possible_page_types();

	?>
    <div id="stylepress-styles-metabox" class="stylepress-metabox dtbaker-elementor-browser">

        <h3>
            <a class="button button-primary"
               href="<?php echo esc_url( admin_url( 'post-new.php?post_type=dtbaker_style&post_parent=' . (int) $post->ID ) ); ?>"><?php esc_html_e( 'New', 'stylepress' ); ?></a>
            <span>Outer Styles:</span>
            <small>These styles can surround your existing website content.</small>
        </h3>
        <div class="inner-wrap">
            <ul>
                <?php foreach ( $styles as $style ) {
                    ?>
                    <li>
                        <div class="stylebox inner-style" tabindex="0">
                            <?php if ( has_post_thumbnail( $style->ID ) ) { ?>
                                <a href="<?php echo esc_url( \Elementor\Utils::get_edit_link( $style->ID ) ); ?>" class="thumb">
                                    <?php echo get_the_post_thumbnail( $style->ID, 'full' ); ?>
                                </a>
                            <?php }else{ ?>

                                <a href="<?php echo esc_url( \Elementor\Utils::get_edit_link( $style->ID ) );?>" class="thumb">
                                    <img src="<?php echo esc_url( DTBAKER_ELEMENTOR_URI . 'assets/img/wp-theme-thumb-logo-sml.jpg' );?>">
                                </a>
                            <?php }

                            $used = array();
                            foreach($page_types as $post_type => $post_type_title){
	                            if($settings && ! empty( $settings['defaults'][$post_type] ) && (int) $settings['defaults'][$post_type] === (int) $style->ID){
		                            $used[$post_type] = $post_type_title;
	                            }
	                            if($settings && ! empty( $settings['defaults'][$post_type] ) && (int) $settings['defaults'][$post_type.'_inner'] === (int) $style->ID){
		                            $used[$post_type.'_inner'] = $post_type_title .' Inner';
	                            }
                            }

                            ?>
                            <div class="theme-usage">
                                <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress-settings'));?>">
	                                <?php if ( $used ){ ?>
                                        <i class="fa fa-check"></i> Style Applied To: <?php echo implode(', ',$used); ?>.
	                                <?php }else{ ?>
                                        <i class="fa fa-times"></i> Style Not Used.
                                    <?php } ?>
                                </a>
                            </div>

                            <h3 class="design-name">
                                <?php if( $post->ID != $style->ID ) { ?>
                                    <a href="<?php echo esc_url( get_edit_post_link( $style->ID ) ); ?>"><?php echo esc_html( $style->post_title ); ?></a>
                                <?php }else{ ?>
                                    <?php echo esc_html( $style->post_title ); ?>
                                <?php } ?>
                            </h3>

                            <div class="theme-actions">
                                <a class="button button" href="<?php print wp_nonce_url(admin_url('admin.php?action=stylepress_clone&post_id=' . (int)$style->ID), 'stylepress_clone', 'stylepress_clone');?>"><?php esc_html_e( 'Clone', 'stylepress' ); ?></a>
                                <a class="button button" href="<?php echo esc_url( get_permalink( $style->ID ) );?>"><?php esc_html_e( 'Preview', 'stylepress' ); ?></a>
                                <a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo esc_url( \Elementor\Utils::get_edit_link( $style->ID ) ); ?>"><?php esc_html_e( 'Edit', 'stylepress' ); ?></a>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <h3>
            <a class="button button-primary"
               href="<?php echo esc_url( admin_url( 'post-new.php?post_type=dtbaker_style&dtbaker_component=1&post_parent=' . (int) $post->ID ) ); ?>"><?php esc_html_e( 'New', 'stylepress' ); ?></a>
            <span>Inner Components:</span>
            <small>These styles can be used for your inner components (blog summary, comments, etc...)</small>
        </h3>
        <div class="inner-wrap">
            <ul>
                <?php foreach ( $components as $style ) {
                    ?>
                    <li>
                        <div class="stylebox" tabindex="0">
                            <?php if ( has_post_thumbnail( $style->ID ) ) { ?>
                                <a href="<?php echo esc_url( \Elementor\Utils::get_edit_link( $style->ID ) ); ?>" class="thumb">
                                    <?php echo get_the_post_thumbnail( $style->ID, 'full' ); ?>
                                </a>
                            <?php }else{ ?>

                                <a href="<?php echo esc_url( \Elementor\Utils::get_edit_link( $style->ID ) );?>" class="thumb">
                                    <img src="<?php echo esc_url( DTBAKER_ELEMENTOR_URI . 'assets/img/wp-theme-thumb-logo-sml.jpg' );?>">
                                </a>
                            <?php }

                            $used = array();
                            foreach($page_types as $post_type => $post_type_title){
	                            if($settings && ! empty( $settings['defaults'][$post_type] ) && (int) $settings['defaults'][$post_type] === (int) $style->ID){
		                            $used[$post_type] = $post_type_title;
	                            }
	                            if($settings && ! empty( $settings['defaults'][$post_type] ) && (int) $settings['defaults'][$post_type.'_inner'] === (int) $style->ID){
		                            $used[$post_type.'_inner'] = $post_type_title .' Inner';
	                            }
                            }

                            ?>
                            <div class="theme-usage">
                                <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress-settings'));?>">
			                        <?php if ( $used ){ ?>
                                        <i class="fa fa-check"></i> Style Applied To: <?php echo implode(', ',$used); ?>.
			                        <?php }else{ ?>
                                        <i class="fa fa-times"></i> Style Not Used.
			                        <?php } ?>
                                </a>
                            </div>

                            <h3 class="design-name">
		                        <?php if( $post->ID != $style->ID ) { ?>
                                    <a href="<?php echo esc_url( get_edit_post_link( $style->ID ) ); ?>"><?php echo esc_html( $style->post_title ); ?></a>
		                        <?php }else{ ?>
			                        <?php echo esc_html( $style->post_title ); ?>
		                        <?php } ?>
                            </h3>

                            <div class="theme-actions">
                                <a class="button button" href="<?php print wp_nonce_url(admin_url('admin.php?action=stylepress_clone&post_id=' . (int)$style->ID), 'stylepress_clone', 'stylepress_clone');?>"><?php esc_html_e( 'Clone', 'stylepress' ); ?></a>
                                <a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo esc_url( \Elementor\Utils::get_edit_link( $style->ID ) ); ?>"><?php esc_html_e( 'Edit', 'stylepress' ); ?></a>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>


    </div>

	<?php

}