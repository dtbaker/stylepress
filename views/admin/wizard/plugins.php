<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

?>
<h1><?php esc_html_e( 'Required Plugins' ); ?></h1>
<form method="post">
	<?php
	$plugins = $this->get_plugins();
	if ( count( $plugins['all'] ) ) {
		?>
		<p><?php esc_html_e( 'Your website needs a few essential plugins. The following plugins will be installed or updated:' ); ?></p>
		<ul class="stylepress-wizard-plugins">
			<?php foreach ( $plugins['all'] as $slug => $plugin ) { ?>
				<li data-slug="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $plugin['name'] ); ?>
					<span>
            <?php
            $keys = array();
            if ( isset( $plugins['install'][ $slug ] ) ) {
	            $keys[] = 'Installation';
            }
            if ( isset( $plugins['update'][ $slug ] ) ) {
	            $keys[] = 'Update';
            }
            if ( isset( $plugins['activate'][ $slug ] ) ) {
	            $keys[] = 'Activation';
            }
            echo implode( ' and ', $keys ) . ' required';
            ?>
          </span>
					<div class="spinner"></div>
				</li>
			<?php } ?>
		</ul>
		<?php
	} else {
		echo '<p><strong>' . esc_html_e( 'Good news! All plugins are already installed and up to date. Please continue.' ) . '</strong></p>';
	} ?>

	<p><?php esc_html_e( 'You can add and remove plugins later on from within WordPress.' ); ?></p>

	<p class="stylepress-actions step">
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
		   class="button-primary button button-large button-next"
		   data-callback="install_plugins"><?php esc_html_e( 'Continue' ); ?></a>
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
		   class="button button-large button-next"><?php esc_html_e( 'Skip this step' ); ?></a>
		<?php wp_nonce_field( 'stylepress' ); ?>
	</p>
</form>
