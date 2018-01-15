<?php
/**
 * Our help text for admin pages
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


ob_start();
?>
<h3><?php _e('Getting Started', 'stylepress'); ?></h3>
<ol>
	<li><?php _e('Create your "Site Style" in Elementor from the ', 'stylepress'); ?><a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress'));?>"><?php _e('Styles', 'stylepress'); ?></a> <?php _e('page.', 'stylepress'); ?></li>
	<li><?php _e('Choose which Outer Styles to apply to your site using the options below. The Outer Style is the header/sidebar/footer that wraps around your page content.', 'sylepress'); ?></li>
	<li><?php _e('Choose which Inner Styles to apply to your site components. The Inner Styles are dynamic layouts that replace the default <code>the_content()</code> output.', 'stylepress'); ?></li>
	<li><?php _e('When editing individual pages you can apply a different style to the default, look in the page metabox area.', 'stylepress'); ?></li>
	<li><?php _e('Disable Theme CSS if the page layout looks funky (recommended).', 'stylepress'); ?></li>
	<li><?php _e('View more help and videos at', 'stylepress'); ?> <a href="https://stylepress.org/help/" target="_blank">https://stylepress.org/help/</a> </li>
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
	<h3><?php _e('Recommended Plugins:', 'stylepress'); ?></h3>
	<p><?php _e('It is recommended to install these plugins to get best results:', 'stylepress'); ?></p>
	<ol>
		<li><a href="https://elementor.com/pro/?ref=1164&campaign=pluginget" target="_blank"><?php _e('Elementor Pro', 'stylepress'); ?></a></li>
		<li><a href="https://wordpress.org/plugins/megamenu/" target="_blank"><?php _e('Max Mega Menu', 'stylepress'); ?></a></li>
		<li><a href="https://wordpress.org/plugins/easy-google-fonts/" target="_blank"><?php _e('Easy Google Fonts', 'stylepress'); ?></a></li>
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
