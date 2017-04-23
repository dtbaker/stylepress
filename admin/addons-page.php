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
        <div id="message" class="updated notice notice-success is-dismissible"><p>Settings updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
    <?php } ?>

    <div class="notice notice-error"><p>TODO: Create a toggle button to enable/disable these. </p></div>
    <div class="notice notice-error"><p>TODO: Include the rest of these missing plugins into this package. </p></div>

    <h3>Current add-ons:</h3>
    <ul>
        <li>Dynamic Field: enable dynamic fields on text boxes, headings, buttons, images etc..</li>
        <li>Modal Popup/Slide In: turn any link into a modal popup</li>
        <li>Menu / Navbar: insert a fixed navbar, create a megamenu layout</li>
        <li>Page Slider: design each slide in its own Elementor page</li>
        <li>Styled Google Maps: change the colors of embedded Google Maps</li>
        <li>Mailchimp Email Subscribe Box</li>
        <li>Form Fields: Text Description, Date Picker, Toggle Block</li>
        <li>StylePress Loop: Design your grid layout (currently Elementor Pro only)</li>
        <li>Tooltip: add a hover tooltip to buttons and links</li>
    </ul>

    <h3>Pending add-ons:</h3>
    <ul>
        <li>Insert Page</li>
        <li>Post Slider</li>
        <li>WooCommerce Product Slider</li>
        <li>Blog Post Output</li>
        <li>Single Image Lightbox</li>
        <li>Individual Links for Slider Images</li>
    </ul>


</div>
