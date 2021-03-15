<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;
?>

<h1>Help and Support</h1>
<p>This theme comes with 6 months item support from purchase date (with the option to extend this period).
	This license allows you to use this theme on a single website. Please purchase an additional license to
	use this theme on another website.</p>
<p>Item Support can be accessed from <a href="http://dtbaker.net/envato/" target="_blank">http://dtbaker.net/envato/</a>
	and includes:</p>
<ul>
	<li>Availability of the author to answer questions</li>
	<li>Answering technical questions about item features</li>
	<li>Assistance with reported bugs and issues</li>
	<li>Help with bundled 3rd party plugins</li>
</ul>

<p>Item Support <strong>DOES NOT</strong> Include:</p>
<ul>
	<li>Customization services (this is available through <a
			href="http://studiotracking.envato.com/aff_c?offer_id=4&aff_id=1564&source=DemoInstall"
			target="_blank">Envato Studio</a>)
	</li>
	<li>Installation services (this is available through <a
			href="http://studiotracking.envato.com/aff_c?offer_id=4&aff_id=1564&source=DemoInstall"
			target="_blank">Envato Studio</a>)
	</li>
	<li>Help and Support for non-bundled 3rd party plugins (i.e. plugins you install yourself later on)</li>
</ul>
<p>More details about item support can be found in the ThemeForest <a
		href="http://themeforest.net/page/item_support_policy" target="_blank">Item Support Polity</a>. </p>
<p class="stylepress-actions step">
	<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
	   class="button button-primary button-large button-next"><?php esc_html_e( 'Agree and Continue' ); ?></a>
	<?php wp_nonce_field( 'stylepress' ); ?>
</p>
