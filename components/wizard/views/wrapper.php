<?php

namespace StylePress\Wizard;

defined( 'STYLEPRESS_VERSION' ) || exit;

?>
<div
	class="stylepress-setup-wizard"
	data-controller="wizard"
	data-wizard-step-value="<?php echo esc_attr( $this->current_step ) ;?>"
	data-wizard-next-url-value="<?php echo esc_attr( $this->get_next_step_link() ) ;?>"
	data-wizard-prev-url-value="<?php echo esc_attr( $this->get_prev_step_link() ) ;?>"
	data-wizard-ajax-endpoint-value="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ) ;?>"
	data-wizard-ajax-nonce-value="<?php echo wp_create_nonce('stylepress-wizard-process') ;?>"
>
	<ol class="stylepress-setup-wizard__steps">
		<?php foreach ( $output_steps as $step_key => $step ) {
			if ( $step_key === 'introduction' ) {
				continue;
			}
			$step_classname = '';
			if( $step_key === $this->current_step ) $step_classname = 'active';
			else if( array_search( $this->current_step, $step_keys ) > array_search( $step_key, $step_keys ) ) $step_classname = 'done';
			?>
			<li class="<?php echo esc_attr($step_classname);?>">
				<?php echo esc_html( $step['name'] ); ?>
			</li>
		<?php } ?>
	</ol>

	<div class="stylepress-setup-wizard__message">
		<div data-wizard-target="errorMessage"></div>
	</div>
	<div class="stylepress-setup-wizard__content">
		<?php
		$this->step_output();
		?>
	</div>
	<div class="stylepress-setup-wizard__actions">
		<a
			href="<?php echo esc_url( $this->get_prev_step_link() ); ?>"
			data-wizard-target="backButton"
			class="button button-large"
		>
			<?php esc_html_e( 'Back' ); ?>
		</a>

		<input
			type="button"
			data-wizard-target="nextButton"
			class="button-primary button button-large button-next"
			value="<?php esc_attr_e( 'Continue &raquo;', 'stylepress' ); ?>"
			name="save_step"
		/>

		<?php wp_nonce_field( 'stylepress' ); ?>
	</div>
</div>
