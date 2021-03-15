<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

?>
<h1><?php esc_html_e( 'Site Style' ); ?></h1>
<form method="post">
	<p><?php esc_html_e( 'Please choose your site style below. You can import additional styles later on from settings.' ); ?></p>

	<div class="stylepress-setup-wizard__styles">
		<?php
		$current_style_slug    = Remote_Styles::get_instance()->get_chosen_remote_style_slug();
		$current_style_to_save = false;
		foreach ( Remote_Styles::get_instance()->get_all_remote_styles() as $style_slug => $style_data ) {
			if ( $current_style_slug === $style_slug ) {
				$current_style_to_save = $style_slug;
			}
			?>
			<div
				class="stylepress-setup-wizard__style <?php echo $style_slug === $current_style_slug ? 'stylepress-setup-wizard__style--current' : ''; ?>">
				<a href="#" data-style="<?php echo esc_attr( $style_slug ); ?>" class="js-stylepress-style-selector">
					<img src="<?php echo esc_url( $style_data['thumbnail_url'] ); ?>"
					     alt="<?php echo esc_attr( $style_data['title'] ); ?>"/>
					<br/>
					<?php echo esc_html( $style_data['title'] ); ?>
				</a>
			</div>
		<?php } ?>
	</div>

	<input type="hidden" name="new_style" id="new_style" value="<?php echo esc_attr( $current_style_to_save ); ?>"/>

	<p class="envato-setup-actions step">
		<input type="submit" class="button-primary button button-large button-next"
		       value="<?php esc_attr_e( 'Continue' ); ?>" name="save_step"/>
		<?php wp_nonce_field( 'envato-setup' ); ?>
	</p>
</form>
