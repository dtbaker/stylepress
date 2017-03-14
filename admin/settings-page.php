<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

$title = __( 'Full Site Editor', 'stylepress' );

if ( !$this->has_permission() ) {
	die ('No permissions');
}


add_thickbox();

$styles = DtbakerElementorManager::get_instance()->get_all_page_styles();
$components = DtbakerElementorManager::get_instance()->get_all_page_components();
$settings = DtbakerElementorManager::get_instance()->get_settings();
$page_types = DtbakerElementorManager::get_instance()->get_possible_page_types();

?>

<div class="wrap">

	<?php require_once DTBAKER_ELEMENTOR_PATH . 'admin/_header.php'; ?>


    <?php if(isset($_GET['saved'])){ ?>
        <div id="message" class="updated notice notice-success is-dismissible"><p>Settings updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
    <?php } ?>

    <?php if(isset($_GET['imported'])){ ?>
        <div id="message" class="updated notice notice-success is-dismissible"><p><strong>Style Imported!</strong> Your new style has been imported. Please assign it to your site below (hint: Start with "Global" and test from there).</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
    <?php } ?>

	<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
		<input type="hidden" name="action" value="dtbaker_elementor_save" />
		<?php wp_nonce_field( 'dtbaker_elementor_save_options', 'dtbaker_elementor_save_options' ); ?>


		<div class="dtbaker-elementor-instructions">
			<div>
				<div>
					<h3>Configure Website Styles:</h3>
					<p>Choose which styles to apply on this website.  </p>

                    <input type="hidden" name="stylepress_settings[remove_css][_do_save]" value="1">
					<table>
						<thead>
						<tr>
							<th>Page Type</th>
							<th>Outer Style</th>
							<th>Inner Style</th>
							<th>Remove Theme CSS?</th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ( $page_types as $post_type => $post_type_title ) {
							?>
							<tr class="post-type=<?php echo esc_attr($post_type);?>">
								<td><?php echo esc_html( $post_type_title);?></td>
								<td>
									<select name="stylepress_styles[<?php echo esc_attr($post_type);?>]">
                                        <?php if('_global' === $post_type){ ?>
                                            <option value="0"<?php selected( $settings && isset( $settings['defaults'][$post_type] ) ? (int) $settings['defaults'][$post_type] : 0, 0 );?>><?php _e( 'None (Original Theme Output)' ); ?></option>
                                        <?php }else { ?>
                                            <option value="0"<?php selected( $settings && isset( $settings['defaults'][$post_type] ) ? (int) $settings['defaults'][$post_type] : 0, 0 );?>><?php _e( '&nbsp; Use Global Setting Above &#8593; ' ); ?></option>
                                            <option value="-1"<?php selected( $settings && isset( $settings['defaults'][$post_type] ) ? (int) $settings['defaults'][$post_type] : 0, -1 );?>><?php _e( 'None (Original Theme Output)' ); ?></option>
	                                        <?php
                                        }
                                        foreach ( $styles as $style_id => $style ) { ?>
											<option value="<?php echo (int) $style_id; ?>"<?php echo $settings && ! empty( $settings['defaults'][$post_type] ) && (int) $settings['defaults'][$post_type] === (int) $style_id ? ' selected' : ''; ?>><?php echo esc_html( $style ); ?></option>
										<?php } ?>
									</select>
								</td>
								<td>
                                    <?php $inner_post_type = $post_type.'_inner'; ?>
									<select name="stylepress_styles[<?php echo esc_attr($inner_post_type);?>]">
                                        <?php if('_global_inner' === $inner_post_type){ ?>
                                            <option value="0"<?php selected( $settings && isset( $settings['defaults'][$inner_post_type] ) ? (int) $settings['defaults'][$inner_post_type] : 0, 0 );?>><?php _e( 'None - just show the_content()' ); ?></option>
                                        <?php }else { ?>
                                            <option value="0"<?php selected( $settings && isset( $settings['defaults'][$inner_post_type] ) ? (int) $settings['defaults'][$inner_post_type] : 0, 0 );?>><?php _e( '&nbsp; Use Global Setting Above &#8593; ' ); ?></option>
                                            <option value="-1"<?php selected( $settings && isset( $settings['defaults'][$inner_post_type] ) ? (int) $settings['defaults'][$inner_post_type] : 0, -1 );?>><?php _e( 'None - just show the_content()' ); ?></option>
	                                        <?php
                                        }
                                        if($this->supports( 'theme-inner' )) {
	                                        ?>
                                            <option value="-2"<?php selected( $settings && isset( $settings['defaults'][ $inner_post_type ] ) ? (int) $settings['defaults'][ $inner_post_type ] : 0, - 2 ); ?>><?php _e( 'Use Theme Default Inner Output' ); ?></option>
	                                        <?php
                                        }
                                        foreach ( $components as $style_id => $style ) { ?>
											<option value="<?php echo (int) $style_id; ?>"<?php echo $settings && ! empty( $settings['defaults'][$inner_post_type] ) && (int) $settings['defaults'][$inner_post_type] === (int) $style_id ? ' selected' : ''; ?>><?php echo esc_html( $style ); ?></option>
										<?php } ?>
									</select>
								</td>
								<td style="text-align: center">
                                    <input type="checkbox" name="stylepress_settings[remove_css][<?php echo esc_attr($post_type);?>]" value="1"<?php echo checked( !empty($settings['remove_css'][$post_type]) ? $settings['remove_css'][$post_type] : 0, 1);?>>
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
                    <h3>Inner &amp; Outer Styles Explained:</h3>
                    <p>The Outer Style is your Header/Footer/Sidebars.<br/> The Inner Style is everything on the inside of the design.</p>
                    <img src="<?php echo esc_url( DTBAKER_ELEMENTOR_URI . 'assets/img/inner-outer-graphic-light.png' );?>">
                </div>
			</div>
		</div>

        <div class="dtbaker-elementor-instructions">
			<div>


				<div>

                    <h3>Coming Soon Page:</h3>
                    <p>The coming soon feature can be used to hide your website from anyone who is not logged in. Enable this while you are developing your website.</p>
                    <p>Remember to turn this off when you launch your website :)</p>

                    <div>
                        Page to Display: <select name="stylepress_styles[coming_soon]">
                            <option value=""> - Disabled - </option>
							<?php
							foreach ( $styles as $style_id => $style ) { ?>
                                <option value="<?php echo (int) $style_id; ?>"<?php echo $settings && ! empty( $settings['defaults']['coming_soon'] ) && (int) $settings['defaults']['coming_soon'] === (int) $style_id ? ' selected' : ''; ?>><?php echo esc_html( $style ); ?></option>
							<?php } ?>
                        </select>
                    </div>



                    <input type="submit" name="save" value="Save Settings" class="button button-primary">
				</div>
			</div>
		</div>

	</form>


</div>
