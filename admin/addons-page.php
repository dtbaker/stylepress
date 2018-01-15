<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

$title = __( 'Add-Ons', 'stylepress' );

// Help tab: Previewing and Customizing.
if ( !$this->has_permission() ) {

	die ('No permissions');
}

add_thickbox();

$styles = DtbakerElementorManager::get_instance()->get_all_page_styles();
$settings = DtbakerElementorManager::get_instance()->get_settings();
$page_types = DtbakerElementorManager::get_instance()->get_possible_page_types();
?>

<div class="wrap">

    <?php require_once DTBAKER_ELEMENTOR_PATH . 'admin/_header.php'; ?>

    <?php if(isset($_GET['saved'])){ ?>
        <div id="message" class="updated notice notice-success is-dismissible"><p><?php _e('Settings updated.', 'stylepress'); ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.', 'stylepress'); ?></span></button></div>
    <?php } ?>

    <div class="notice notice-error"><p><?php _e('TODO: Create a toggle button to enable/disable these.', 'stylepress'); ?> </p></div>
    <div class="notice notice-error"><p><?php _e('TODO: Include the rest of these missing plugins into this package.', 'stylepress'); ?> </p></div>

    <h3><?php _e('Current add-ons:', 'stylepress'); ?></h3>
    <ul>
        <li><?php _e('Dynamic Field: enable dynamic fields on text boxes, headings, buttons, images etc..', 'stylepress'); ?></li>
        <li><?php _e('Modal Popup/Slide In: turn any link into a modal popup', 'stylepress'); ?></li>
        <li><?php _e('Menu / Navbar: insert a fixed navbar, create a megamenu layout', 'stylepress'); ?></li>
        <li><?php _e('Page Slider: design each slide in its own Elementor page', 'stylepress'); ?></li>
        <li><?php _e('Styled Google Maps: change the colors of embedded Google Maps', 'stylepress'); ?></li>
        <li><?php _e('Mailchimp Email Subscribe Box', 'stylepress'); ?></li>
        <li><?php _e('Form Fields: Text Description, Date Picker, Toggle Block', 'stylepress'); ?></li>
        <li><?php _e('StylePress Loop: Design your grid layout (currently Elementor Pro only)', 'stylepress'); ?></li>
        <li><?php _e('Tooltip: add a hover tooltip to buttons and links', 'stylepress'); ?></li>
    </ul>

    <h3><?php _e('Pending add-ons:', 'stylepress'); ?></h3>
    <ul>
        <li><?php _e('Insert Page', 'stylepress'); ?></li>
        <li><?php _e('Post Slider', 'stylepress'); ?></li>
        <li><?php _e('WooCommerce Product Slider', 'stylepress'); ?></li>
        <li><?php _e('Blog Post Output', 'stylepress'); ?></li>
        <li><?php _e('Single Image Lightbox', 'stylepress'); ?></li>
        <li><?php _e('Individual Links for Slider Images', 'stylepress'); ?></li>
    </ul>


</div>
