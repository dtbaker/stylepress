<?php
/**
 * Template for purchase popup
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

$current_user = wp_get_current_user();
$name = trim( $current_user->user_firstname .' '.$current_user->user_lastname );
if(!$name)$name = $current_user->display_name;

?>
<script type="text/template" id="tmpl-stylepress-payment-popup">
<div class="stylepress-payment-pop">
	<div class="stylepress-payment-popup">
        <div class="loadingbar"></div>
		<div class="form-row">
			<label for="card-name">
				Your Name
			</label>
			<input type="text" size="50" class="name text-field" name="name" placeholder="Your Name" value="<?php echo esc_attr($name);?>">
		</div>
		<div class="form-row">
			<label for="card-email">
				Your Email
			</label>
			<input type="email" size="50" class="email text-field" name="email" placeholder="Your Email" value="<?php echo esc_attr($current_user->user_email);?>">
		</div>
		<div class="form-row">
			<label for="stylepress-card-amount">
				Payment Amount = <span id="stylepress-amount-update"></span> USD
			</label>
            <div id="stylepress-card-amount" class="wrap"></div>
            <input type="hidden" name="amount" class="stylepress-card-amount-value" value="1">
		</div>
		<div class="form-row">
			<label for="card-element">
				Credit or Debit Card
			</label>
			<div class="stripe-card-element">
			</div>
			<!-- Used to display form errors -->
			<div id="card-errors"></div>
		</div>

		<button class="button button-primary stylepress-final-purchase-button">Purchase Style</button>
	</div>
</div>

</script>