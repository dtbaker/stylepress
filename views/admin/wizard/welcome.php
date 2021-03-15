<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

if ( get_option( 'stylepress_setup_wizard_complete', false ) ) {
	?>
	<h1><?php printf( esc_html__( 'Welcome to the setup wizard for %s.' ), wp_get_theme() ); ?></h1>
	<p><?php esc_html_e( 'It looks like you have already run the setup wizard. Below are some options: ' ); ?></p>
	<ul>
		<li>
			<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
			   class="button-primary button button-next button-large"><?php esc_html_e( 'Run Setup Wizard Again' ); ?></a>
		</li>
	</ul>
	<p class="envato-setup-actions step">
		<a
			href="<?php echo esc_url( admin_url( '' ) ); ?>"
			class="button button-large"><?php esc_html_e( 'Cancel' ); ?></a>
	</p>
	<?php
} else {
	?>
	<h1><?php printf( esc_html__( 'Welcome to the setup wizard for %s.' ), wp_get_theme() ); ?></h1>
	<p><?php printf( esc_html__( 'Thank you for choosing the %s theme from ThemeForest. This quick setup wizard will help you configure your new website. This wizard will install the required WordPress plugins, default content, logo and tell you a little about Help &amp; Support options. It should only take 5 minutes.' ), wp_get_theme() ); ?></p>
	<p class="envato-setup-actions step">
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
		   class="button-primary button button-large button-next"><?php esc_html_e( 'Let\'s Go!' ); ?></a>
		<a
			href="<?php echo esc_url( admin_url( '' ) ); ?>"
			class="button button-large"><?php esc_html_e( 'Not right now' ); ?></a>
	</p>
	<?php
}
