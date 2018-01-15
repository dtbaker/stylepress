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
				<?php _e('Your Name', 'stylepress'); ?>
			</label>
			<input type="text" size="50" class="name text-field" name="name" placeholder="<?php _e('Your Name', 'stylepress'); ?>" value="<?php echo esc_attr($name);?>">
		</div>
		<div class="form-row">
			<label for="card-email">
				<?php _e('Your Email', 'stylepress'); ?>
			</label>
			<input type="email" size="50" class="email text-field" name="email" placeholder="<?php _e('Your Email', 'stylepress'); ?>" value="<?php echo esc_attr($current_user->user_email);?>">
		</div>
		<div class="form-row">
			<label for="stylepress-card-amount">
				<?php _e('Payment Amount', 'stylepress'); ?> = <span id="stylepress-amount-update"></span> USD
			</label>
            <div id="stylepress-card-amount" class="wrap"></div>
            <input type="hidden" name="amount" class="stylepress-card-amount-value" value="1">
		</div>
		<div class="form-row">
			<label for="card-element">
				<?php _e('Credit or Debit Card', 'stylepress'); ?>
			</label>
			<div class="stripe-card-element">
			</div>
			<!-- Used to display form errors -->
			<div id="card-errors"></div>
		</div>

		<button class="button button-primary stylepress-final-purchase-button"><?php _e('Purchase Style', 'stylepress'); ?></button>
	</div>
</div>

</script>