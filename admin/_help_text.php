<?php
/**
 * Our help text for admin pages
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


ob_start();
?>
<h3>Getting Started</h3>
<ol>
	<li>Create your "Site Style" in Elementor from the <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress'));?>">Styles</a> page.</li>
	<li>Choose which Outer Styles to apply to your site using the options below. The Outer Style is the header/sidebar/footer that wraps around your page content.</li>
	<li>Choose which Inner Styles to apply to your site components. The Inner Styles are dynamic layouts that replace the default <code>the_content()</code> output.</li>
	<li>When editing individual pages you can apply a different style to the default, look in the page metabox area.</li>
	<li>Disable Theme CSS if the page layout looks funky (recommended).</li>
	<li>View more help and videos at <a href="https://stylepress.org/help/" target="_blank">https://stylepress.org/help/</a> </li>
</ol>
<?php

$help_customize = ob_get_clean();


get_current_screen()->add_help_tab( array(
	'id'		=> 'stylepress-help',
	'title'		=> __( 'Getting Started', 'stylepress' ),
	'content'	=> $help_customize,
) );


ob_start();
?>
	<h3>Recommended Plugins:</h3>
	<p>It is recommended to install these plugins to get best results:</p>
	<ol>
		<li><a href="https://elementor.com/pro/?ref=1164&campaign=pluginget" target="_blank">Elementor Pro</a></li>
		<li><a href="https://wordpress.org/plugins/megamenu/" target="_blank">Max Mega Menu</a></li>
		<li><a href="https://wordpress.org/plugins/easy-google-fonts/" target="_blank">Easy Google Fonts</a></li>
	</ol>
<?php

$help_customize = ob_get_clean();


get_current_screen()->add_help_tab( array(
	'id'		=> 'stylepress-help-recommended',
	'title'		=> __( 'Recommended Plugins', 'stylepress' ),
	'content'	=> $help_customize,
) );


get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:', 'stylepress' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://stylepress.org" target="_blank">Read More on stylepress.org</a>', 'stylepress' ) . '</p>'
);
