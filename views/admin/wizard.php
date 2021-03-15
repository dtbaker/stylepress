<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

$wizard       = Wizard::get_instance();
$output_steps = $wizard->get_steps();
$step_keys    = array_keys( $output_steps );
?>
<div
	class="stylepress-setup-wizard"
	data-controller="wizard-manager"
	data-wizard-manager-step-value="<?php echo esc_attr( $wizard->current_step ) ;?>"
	data-wizard-manager-next-url-value="<?php echo esc_attr( $wizard->get_next_step_link() ) ;?>"
	data-wizard-manager-prev-url-value="<?php echo esc_attr( $wizard->get_prev_step_link() ) ;?>"
	data-wizard-manager-ajax-endpoint-value="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ) ;?>"
	data-wizard-manager-ajax-nonce-value="<?php echo wp_create_nonce('stylepress-wizard-process') ;?>"
>
	<ol class="stylepress-setup-wizard__steps">
		<?php foreach ( $output_steps as $step_key => $step ) {
			if ( $step_key === 'introduction' ) {
				continue;
			}
			$step_classname = '';
			if( $step_key === $wizard->current_step ) $step_classname = 'active';
			else if( array_search( $wizard->current_step, $step_keys ) > array_search( $step_key, $step_keys ) ) $step_classname = 'done';
			?>
			<li class="<?php echo esc_attr($step_classname);?>">
				<?php echo esc_html( $step['name'] ); ?>
			</li>
		<?php } ?>
	</ol>

	<div class="stylepress-setup-wizard__message">
		<div data-wizard-manager-target="errorMessage"></div>
	</div>
	<div class="stylepress-setup-wizard__content">
		<?php
		$wizard->step_output();
		?>
	</div>
	<div class="stylepress-setup-wizard__actions">
		<a
			href="<?php echo esc_url( $wizard->get_prev_step_link() ); ?>"
			data-wizard-manager-target="backButton"
			class="button button-large"><?php esc_html_e( 'Back' ); ?></a>

		<input type="button"
		       data-wizard-manager-target="nextButton"
		       class="button-primary button button-large button-next"
		       value="<?php esc_attr_e( 'Continue &raquo;', 'stylepress' ); ?>"
		       name="save_step"/>
		<?php wp_nonce_field( 'stylepress' ); ?>
	</div>
</div>
