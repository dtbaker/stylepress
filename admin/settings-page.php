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


        <h3 class="stylepress-header">
            <span>Configure Website Styles</span>
            <small>Choose which styles to apply on various parts of your website. See the help menu above for more details.</small>
        </h3>


		<div class="dtbaker-elementor-instructions">
			<div>
				<div>

                    <input type="hidden" name="stylepress_settings[remove_css][_do_save]" value="1">
					<table class="widefat striped">
						<thead>
						<tr>
							<th>Page Type</th>
                            <th>Outer Style <small>(Header/Footer)</small></th>
                            <th>Inner Style <small>(Page + CPT Layouts)</small></th>
							<th>Remove Theme CSS?</th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ( $page_types as $post_type => $post_type_title ) {
							?>
							<tr class="post-type-<?php echo esc_attr($post_type); echo !empty($_GET['highlight']) && $post_type === $_GET['highlight'] ? ' highlightstyle': '';?>">
								<td><?php echo esc_html( $post_type_title);?></td>
								<td>
									<select name="stylepress_styles[<?php echo esc_attr($post_type);?>]">
                                        <?php if('_global' === $post_type){ ?>
                                            <option value="<?php echo STYLEPRESS_OUTER_USE_THEME;?>"<?php selected( $settings && isset( $settings['defaults'][$post_type] ) ? (int) $settings['defaults'][$post_type] : 0, STYLEPRESS_OUTER_USE_THEME );?>><?php _e( 'None (Original Theme Output)' ); ?></option>
                                        <?php }else { ?>
                                            <option value="0"<?php selected( $settings && isset( $settings['defaults'][$post_type] ) ? (int) $settings['defaults'][$post_type] : 0, 0 );?>><?php _e( '&nbsp; (default) ' ); ?></option>
                                            <option value="<?php echo STYLEPRESS_OUTER_USE_THEME;?>"<?php selected( $settings && isset( $settings['defaults'][$post_type] ) ? (int) $settings['defaults'][$post_type] : 0, STYLEPRESS_OUTER_USE_THEME );?>><?php _e( 'None (Original Theme Output)' ); ?></option>
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
                                            <option value="0"<?php selected( $settings && isset( $settings['defaults'][$inner_post_type] ) ? (int) $settings['defaults'][$inner_post_type] : 0, 0 );?>><?php _e( '&nbsp; (default)' ); // just show the_content() ?></option>
                                        <?php }else { ?>
                                            <option value="0"<?php selected( $settings && isset( $settings['defaults'][$inner_post_type] ) ? (int) $settings['defaults'][$inner_post_type] : 0, 0 );?>><?php _e( '&nbsp; (default) ' ); ?></option>
                                            <option value="<?php echo STYLEPRESS_INNER_USE_PLAIN;?>"<?php selected( $settings && isset( $settings['defaults'][$inner_post_type] ) ? (int) $settings['defaults'][$inner_post_type] : 0, STYLEPRESS_INNER_USE_PLAIN );?>><?php _e( 'None - just show the_content()' ); ?></option>
	                                        <?php
                                        }
                                        if($this->supports( 'theme-inner' )) {
	                                        ?>
                                            <option value="<?php echo STYLEPRESS_INNER_USE_THEME;?>"<?php selected( $settings && isset( $settings['defaults'][ $inner_post_type ] ) ? (int) $settings['defaults'][ $inner_post_type ] : 0, STYLEPRESS_INNER_USE_THEME ); ?>><?php _e( 'Use Theme Default Inner Output' ); ?></option>
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

                    <p>
                        <input type="submit" name="save" value="Save Settings" class="button button-primary">
                    </p>
				</div>

                <div>
                    <div style="text-align: center">
                    <p><strong>Need help?</strong>
                        <small>
                    <br/>
                        The "Outer" style is generally the same on every page of the site. <br/>
                        It contains your logo, header, footer and sidebars. <br/>
                        The "Inner" style is can be different for posts, pages and CPT's. <br/>
                        It contains a page title area and other dynamic fields.
                        </small>
                    </p>
                        <p>&nbsp;</p>
                    <div>
                        <img src="<?php echo esc_url( DTBAKER_ELEMENTOR_URI . 'assets/img/inner-outer-graphic-light.png' );?>">
                    </div>
                    </div>
                </div>
			</div>
		</div>




	</form>


</div>
