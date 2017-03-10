<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

$title = __( 'Full Site Editor', 'stylepress' );

// Help tab: Previewing and Customizing.
if ( $this->has_permission() ) {
	$help_customize =
		'<p>' . __( 'This is help text. I will add some information in here soon.', 'stylepress' ) . '</p>';

	get_current_screen()->add_help_tab( array(
		'id'		=> 'dtbaker-elementor',
		'title'		=> __( 'Editing a Site Style', 'stylepress' ),
		'content'	=> $help_customize,
	) );

	if( isset($_POST['dtbaker_elementor_save']) ) {
		if (
			! isset( $_POST['dtbaker_elementor_save_options'] )
			|| ! wp_verify_nonce( $_POST['dtbaker_elementor_save_options'], 'dtbaker_elementor_save_options' )
		) {

			print 'Sorry, your nonce did not verify.';
			exit;

		} else {


		}
	}


}else{
	die ('No permissions');
}

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:', 'stylepress' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://dtbaker.net/labs/elementor-full-page-site-builder/">Read More on dtbaker.net</a>', 'stylepress' ) . '</p>'
);


add_thickbox();

$styles = DtbakerElementorManager::get_instance()->get_all_page_styles();
$settings = DtbakerElementorManager::get_instance()->get_settings();
$page_types = DtbakerElementorManager::get_instance()->get_possible_page_types();
?>

<div class="wrap">

	<?php require_once DTBAKER_ELEMENTOR_PATH . 'admin/_header.php'; ?>


    <?php if(isset($_GET['saved'])){ ?>
        <div id="message" class="updated notice notice-success is-dismissible"><p>Settings updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
    <?php } ?>

	<?php if(!get_theme_support('stylepress-elementor')){ ?>
        <div class="notice notice-error"><p>Warning: The current theme does not specify <code>stylepress-elementor</code> support. Some functions may not work correctly. Use our recommended theme if you have layout issues.</p></div>
	<?php } ?>

    <div class="notice notice-error"><p>Warning: Most of these settings won't work just yet. Components are disabled. Try the "Global" setting, that should work.</p></div>

	<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
		<input type="hidden" name="action" value="dtbaker_elementor_save" />
		<?php wp_nonce_field( 'dtbaker_elementor_save_options', 'dtbaker_elementor_save_options' ); ?>

		<div class="dtbaker-elementor-instructions">
			<div>
				<div>
					<h3>Outer Styles:</h3>
					<p>Choose which outer style to apply on your entire website.</p>

					<table>
						<thead>
						<tr>
							<th>Type</th>
							<th>Style</th>
							<th>Overwrite</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td>Global</td>
							<td>
								<select name="stylepress_styles[_global]">
									<option value="0">None - Use Normal Theme</option>
									<?php foreach($styles as $style_id => $style){ ?>
										<option value="<?php echo (int)$style_id;?>"<?php echo $settings && !empty($settings['defaults']['_global']) && (int)$settings['defaults']['_global'] === (int)$style_id ? ' selected' : '';?>><?php echo esc_html($style);?></option>
									<?php } ?>
								</select>
							</td>
							<td>
                                <input type="hidden" name="stylepress_settings[overwrite][_do_save_]" value="1">
								<input type="checkbox" name="stylepress_settings[overwrite][_global]" value="1"<?php echo !empty($settings['overwrite']['_global']) ? ' checked':'';?>>
							</td>
						</tr>
						<?php
						foreach ( $page_types as $post_type => $post_type_title ) {
							?>
							<tr>
								<td><?php echo esc_html( $post_type_title);?></td>
								<td>
									<select name="stylepress_styles[<?php echo esc_attr($post_type);?>]">
										<option value="0"> - Use Global Setting - </option>
										<?php foreach ( $styles as $style_id => $style ) { ?>
											<option value="<?php echo (int) $style_id; ?>"<?php echo $settings && ! empty( $settings['defaults'][$post_type] ) && (int) $settings['defaults'][$post_type] === (int) $style_id ? ' selected' : ''; ?>><?php echo esc_html( $style ); ?></option>
										<?php } ?>
									</select>
								</td>
								<td>
									<input type="checkbox" name="stylepress_settings[overwrite][<?php echo esc_attr($post_type);?>]" value="1"<?php echo !empty($settings['overwrite'][$post_type]) ? ' checked':'';?>>
								</td>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>

					<input type="submit" name="save" value="Save Settings" class="button button-primary">
				</div>
				<div>
					<h3>Inner Components:</h3>
					<p>Choose which inner styles to apply to various parts of the site:</p>

					<ul class="dtbaker-elementor-settings">
						<?php
						foreach( array('Blog Grid','Comments','Shop Catalog','Shop Product') as $type){
							?>
							<li>
								<?php echo esc_html( ucwords( str_replace('_',' ',$type)));?> Style: <select name="stylepress_styles[<?php echo esc_attr($type);?>]">
									<option value="0">None - Use Normal Theme</option>
									<?php foreach($styles as $style_id => $style){ ?>
										<option value="<?php echo (int)$style_id;?>"<?php echo $settings && !empty($settings['defaults'][$type]) && (int)$settings['defaults'][$type] === (int)$style_id ? ' selected' : '';?>><?php echo esc_html($style);?></option>
									<?php } ?>
								</select>
							</li>
						<?php } ?>
					</ul>
					<input type="submit" name="save" value="Save Settings" class="button button-primary">
				</div>
				<div>
					<h3>Instructions:</h3>
					<ol>
						<li>Create your "Site Style" in Elementor.</li>
						<li>Choose which styles to apply globally to your site.</li>
						<li>Tick the "overwrite" option if you want the style to overwrite all theme output (test it on and off to see the difference)</li>
						<li>When editing individual pages you can apply a different style in the metabox area.</li>
					</ol>
					<h3>Recommended Plugins:</h3>
					<p>It is recommended to install these plugins to get best results:</p>
					<ol>
						<li><a href="https://elementor.com/pro/?ref=1164&campaign=pluginget" target="_blank">Elementor Pro</a></li>
						<li><a href="https://wordpress.org/plugins/megamenu/" target="_blank">Max Mega Menu</a></li>
						<li><a href="https://wordpress.org/plugins/easy-google-fonts/" target="_blank">Easy Google Fonts</a></li>
					</ol>
					<h3>Recommended Theme:</h3>

					<p>This plugin works best with a basic default theme. If your current theme is causing layout problems please <a href="https://dtbaker.net/labs/stylepress-basic-wordpress-theme/" target="_blank">click here</a> to download our recommended basic theme.</p>
				</div>
			</div>
		</div>
	</form>


</div>
