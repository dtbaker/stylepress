<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

?>
<h1><?php esc_html_e( 'Site Style' ); ?></h1>
<form method="post">
	<p><?php esc_html_e( 'Please choose your site style below. You can import additional styles later on from settings.' ); ?></p>
	<div class="stylepress-setup-wizard__styles" data-controller="wizard-style">
		<?php
		foreach ( Remote_Styles::get_instance()->get_all_remote_styles() as $style_slug => $style_data ) {
			?>
			<div class="stylepress-setup-wizard__style">
				<div data-wizard-style-target="styleSelector" data-action="click->wizard-style#setStyle" data-style="<?php echo esc_attr( $style_slug ); ?>">
					<img src="<?php echo esc_url( $style_data['thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $style_data['title'] ); ?>"/>
					<br/>
					<?php echo esc_html( $style_data['title'] ); ?>
				</div>
			</div>
		<?php } ?>
	</div>
</form>
