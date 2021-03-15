<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

if ( get_option( 'stylepress_setup_wizard_complete', false ) ) {
	?>
	<h1><?php printf( esc_html__( 'Welcome to the setup wizard for %s.' , 'stylepress'), wp_get_theme() ); ?></h1>
	<p><?php esc_html_e( 'It looks like you have already run the setup wizard.', 'stylepress' ); ?></p>
	<p><?php esc_html_e( 'You are welcome to run it a second time if you missed something first time round', 'stylepress' ); ?></p>
	<?php
} else {
	?>
	<h1><?php printf( esc_html__( 'Welcome to the setup wizard for %s.', 'stylepress' ), wp_get_theme() ); ?></h1>
	<p><?php printf( esc_html__( 'Thank you for choosing the %s theme from ThemeForest. This quick setup wizard will help you configure your new website. This wizard will install the required WordPress plugins, default content, logo and tell you a little about Help &amp; Support options. It should only take 5 minutes.', 'stylepress' ), wp_get_theme() ); ?></p>
	<?php
}
