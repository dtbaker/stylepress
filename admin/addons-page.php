<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

$title = __( 'AddOns', 'stylepress' );

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

$styles = DtbakerElementorManager::get_instance()->get_all_page_styles();
$settings = DtbakerElementorManager::get_instance()->get_settings();
$page_types = DtbakerElementorManager::get_instance()->get_possible_page_types();
?>

<div class="wrap">

    <?php require_once DTBAKER_ELEMENTOR_PATH . 'admin/_header.php'; ?>

    <?php if(isset($_GET['saved'])){ ?>
        <div id="message" class="updated notice notice-success is-dismissible"><p>Settings updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
    <?php } ?>


    <p>Choose which add-ons to enable for this website:</p>
    (interface coming soon)
    <ul>
        <li>Styled Google Maps</li>
        <li>Insert Page</li>
        <li>Page Slider</li>
        <li>Post Slider</li>
        <li>WooCommerce Product Slider</li>
        <li>Blog Post Output</li>
    </ul>


</div>
