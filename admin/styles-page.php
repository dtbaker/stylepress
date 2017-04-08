<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

$title = __( 'Full Site Editor', 'stylepress' );

// Help tab: Previewing and Customizing.
if ( !$this->has_permission() ) {

    die ('No permissions');
}


add_thickbox();

$settings = DtbakerElementorManager::get_instance()->get_settings();
$page_types = DtbakerElementorManager::get_instance()->get_possible_page_types();
$designs = DtbakerElementorManager::get_instance()->get_all_page_styles();
$downloadable = DtbakerElementorManager::get_instance()->get_downloadable_styles();
?>

<div class="wrap">

	<?php require_once DTBAKER_ELEMENTOR_PATH . 'admin/_header.php';

	if(isset($_GET['style_id'])){
	    require_once DTBAKER_ELEMENTOR_PATH . 'admin/styles-page-inner.php';
    }else{
	    ?>
        <div class="dtbaker-elementor-browser">

            <div class="wp-clearfix">

                <h3 class="stylepress-header">
                    <div class="buttons">
    <!--                    <a href="--><?php //echo esc_url( admin_url( 'post-new.php?post_type=dtbaker_style' ) ); ?><!--" class="button">Import</a>-->
                        <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress&style_id=new') ); ?>"
                           class="button button-primary">Create New Style</a>
                    </div>
                    <span>Your Styles</span>
                    <small>These are your website styles. A style can be applied to your website from the <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress-settings'));?>">Settings</a> page.</small>
                </h3>

                <div class="stylepress-item-wrapper">
                    <?php

                    if(!$designs){
                        ?>
                        <p>None yet! Create your own or install from the list below.</p>
                        <p>&nbsp;</p>
                        <?php
                    }

                    foreach ( $designs as $design_id => $design ) :
                        $post = get_post($design_id);
                        if($post->post_parent)continue;
                        ?>
                        <div class="design stylebox" tabindex="0">
                            <?php if ( has_post_thumbnail( $design_id ) ) { ?>
                                <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress&style_id=' . $design_id) );?>" class="thumb">
                                    <?php echo get_the_post_thumbnail( $design_id, 'full' );?>
                                </a>
                            <?php }else{ ?>
                                <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress&style_id=' . $design_id) );?>" class="thumb">
                                    <img src="<?php echo esc_url( DTBAKER_ELEMENTOR_URI . 'assets/img/wp-theme-thumb-logo-sml.jpg' );?>">
                                </a>
                            <?php } ?>

                            <?php
                            // find out where it's applied, if anywhere.
                            $used = array();
                            $args        = array(
                                'post_type'           => 'dtbaker_style',
                                'post_parent'         => $design_id,
                                'post_status'         => 'any',
                                'posts_per_page'      => -1,
                                'ignore_sticky_posts' => 1,
                            );
                            $posts_array = get_posts( $args );

                            foreach($page_types as $post_type => $post_type_title){
                                if($settings && ! empty( $settings['defaults'][$post_type] ) && (int) $settings['defaults'][$post_type] === (int) $design_id){
                                    $used[$post_type] = $post_type_title;
                                }
                                // check if any of the child posts are used in this particular post type.
                                foreach ( $posts_array as $post_array ) {
                                    if($settings && ! empty( $settings['defaults'][$post_type] ) && (int) $settings['defaults'][$post_type] === (int) $post_array->ID){
                                        $used[$post_type] = $post_type_title;
                                    }
                                }
                                // todo: query what custom pages have a different style overview
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

                            <h3 class="design-name"><?php echo esc_html( $design ); ?></h3>

                            <div class="theme-actions">
                                <!--						<a class="button button" href="#" onclick="alert('Coming soon');">--><?php //esc_html_e( 'Copy', 'stylepress' ); ?><!--</a>-->
                                <a class="button button-primary" href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress&style_id=' . $design_id) ); ?>"><?php esc_html_e( 'Edit Style', 'stylepress' ); ?></a>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>


            <div class="wp-clearfix">
                <h3 class="stylepress-header">
                    <span>Available Styles</span>
                    <small>These site styles can be installed and then edited with Elementor.</small>
                </h3>

                <div class="stylepress-item-wrapper">
                    <?php

                    foreach ( $downloadable as $design_slug => $design ) :

                        $type = !empty($design['cost']) ? 'paid' : 'free';
                        $has_purchased = false;
                        if( 'paid' === $type && !empty($design['pay_nonce'])){
                            $has_purchased = true;
                        }else{

                        }
                        ?>
                        <div class="design stylebox stylepress-<?php echo esc_attr($type); if($has_purchased) echo ' stylepress-purchased'?>" tabindex="0">
                            <a href="<?php echo esc_url( $design['demo'] );?>" class="thumb" target="_blank">
                                <img src="<?php echo esc_html($design['thumb']);?>">
                            </a>
                            <div class="theme-usage style-description">
                                <?php echo wp_kses_post( !empty($design['included']) ? $design['included'] : '' );?>
                            </div>

                            <h3 class="design-name"><?php echo esc_html( $design['title'] ); ?>
                            <small>v<?php echo esc_html($design['version']);?><?php
                                if($has_purchased){
                                    echo ' - purchased';
                                }else if($type == 'free'){
                                    echo ' - free';
                                }
                                ?></small></h3>

                            <div class="theme-actions">
                                <a class="button" href="<?php echo esc_url( $design['demo'] ); ?>" target="_blank"><?php esc_html_e( 'Preview', 'stylepress' ); ?></a>
                                <?php if('paid' === $type && !$has_purchased){ ?>
                                    <a class="button button-primary button-stylepress-pay" href="<?php echo esc_url( wp_nonce_url(admin_url('admin.php?action=stylepress_download&slug=' . $design_slug), 'stylepress_download', 'stylepress_download') ); ?>" data-stylename="<?php echo esc_attr( $design['title'] );?>" data-styleslug="<?php echo esc_attr( $design_slug );?>" data-stylecost="<?php echo esc_attr( $design['cost'] );?>"><?php esc_html_e( 'Purchase', 'stylepress' ); ?></a>
                                <?php }else{ ?>
                                    <a class="button button-primary" href="<?php echo esc_url( wp_nonce_url(admin_url('admin.php?action=stylepress_download&slug=' . $design_slug), 'stylepress_download', 'stylepress_download') ); ?>"><?php esc_html_e( 'Install', 'stylepress' ); ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php endforeach; ?>


                </div>
            </div>
        </div>

        <div class="theme-overlay"></div>

    <?php } ?>
</div>

<?php require_once DTBAKER_ELEMENTOR_PATH .'admin/payment-modal.php';
