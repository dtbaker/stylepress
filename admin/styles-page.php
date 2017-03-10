<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

$title = __( 'Full Site Editor', 'stylepress' );

// Help tab: Previewing and Customizing.
if ( $this->has_permission() ) {
	$help_customize =
		'<p>' . __( 'This is help text. I will add some information in here soon.', 'stylepress' ) . '</p>';

	get_current_screen()->add_help_tab( array(
		'id'		=> 'dtbaker-elementor',
		'title'		=> __( 'Editing a Site Style', 'stylepress' ),
		'content'	=> $help_customize,
	) );

	if( isset($_POST['dtbaker_elementor_save']) ) {
		if (
			! isset( $_POST['dtbaker_elementor_save_options'] )
			|| ! wp_verify_nonce( $_POST['dtbaker_elementor_save_options'], 'dtbaker_elementor_save_options' )
		) {

			print 'Sorry, your nonce did not verify.';
			exit;

		} else {


		}
	}


}else{
    die ('No permissions');
}

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:', 'stylepress' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://dtbaker.net/labs/elementor-full-page-site-builder/">Read More on dtbaker.net</a>', 'stylepress' ) . '</p>'
);


add_thickbox();

?>

<div class="wrap">


	<?php require_once DTBAKER_ELEMENTOR_PATH . 'admin/_header.php'; ?>


	<div class="dtbaker-elementor-browser">
		<div class="wp-clearfix">

            <h3 class="stylepress-header">
                <div class="buttons">
<!--                    <a href="--><?php //echo esc_url( admin_url( 'post-new.php?post_type=dtbaker_style' ) ); ?><!--" class="button">Import</a>-->
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=dtbaker_style' ) ); ?>"
                       class="button button-primary">Create New Style</a>
                </div>
                <span>Your Styles</span>
                <small>These are your website styles. A style can be applied to your website from the <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress-settings'));?>">Settings</a> page.</small>
            </h3>

			<?php

			$designs = DtbakerElementorManager::get_instance()->get_all_page_styles();
			if(!$designs){
			    ?>
                <p>None yet! Create your own.</p>
                <?php
            }

			foreach ( $designs as $design_id => $design ) :
				$post = get_post($design_id);
				if($post->post_parent)continue;
				?>
                <div class="design stylebox" tabindex="0">
					<?php if ( has_post_thumbnail( $design_id ) ) { ?>
                        <a href="<?php echo esc_url( get_edit_post_link( $design_id ) );?>" class="thumb">
							<?php echo get_the_post_thumbnail( $design_id, 'full' );?>
                        </a>
					<?php }else{ ?>
                        <a href="<?php echo esc_url( get_edit_post_link( $design_id ) );?>" class="thumb">
                            <img src="<?php echo esc_url( DTBAKER_ELEMENTOR_URI . 'assets/img/wp-theme-thumb-logo-sml.jpg' );?>">
                        </a>
					<?php } ?>

                    <h3 class="design-name"><?php echo esc_html( $design ); ?></h3>

                    <div class="theme-actions">
                        <!--						<a class="button button" href="#" onclick="alert('Coming soon');">--><?php //esc_html_e( 'Copy', 'stylepress' ); ?><!--</a>-->
                        <a class="button button-primary" href="<?php echo esc_url( get_edit_post_link( $design_id ) ); ?>"><?php esc_html_e( 'Edit Style', 'stylepress' ); ?></a>
                    </div>
                </div>
			<?php endforeach; ?>
		</div>

        <div class="wp-clearfix">
            <h3 class="stylepress-header">
                <span>Available Styles</span>
                <small>These are styles that can be downloaded from StylePress.org - Once downloaded these styles can be edited to suit your needs.</small>
            </h3>

	        <?php

	        $designs = DtbakerElementorManager::get_instance()->get_downloadable_styles();

	        foreach ( $designs as $design_slug => $design ) :
		        ?>
                <div class="design stylebox" tabindex="0">
                    <a href="<?php echo esc_url( $design['demo'] );?>" class="thumb" target="_blank">
				        <img src="<?php echo esc_html($design['thumb']);?>">
                    </a>

                    <h3 class="design-name"><?php echo esc_html( $design['title'] ); ?></h3>

                    <div class="theme-actions">
                        <a class="button" href="<?php echo esc_url( $design['demo'] ); ?>" target="_blank"><?php esc_html_e( 'Preview', 'stylepress' ); ?></a>
                        <a class="button button-primary" href="<?php echo esc_url( wp_nonce_url(admin_url('admin.php?action=stylepress_download&slug=' . $design_slug), 'stylepress_download', 'stylepress_download') ); ?>"><?php esc_html_e( 'Install', 'stylepress' ); ?></a>
                    </div>
                </div>
	        <?php endforeach; ?>


        </div>
	</div>

	<div class="theme-overlay"></div>
</div>
