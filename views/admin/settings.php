<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

$default_styles = Styles::get_instance()->get_default_styles();
$page_types     = Settings::get_instance()->get_all_page_types();
$categories     = Styles::get_instance()->get_categories();

?>

<?php if ( isset( $_GET['saved'] ) ) { ?>
	<div id="message" class="updated notice notice-success is-dismissible"><p>Settings updated.</p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	</div>
<?php } ?>

<p>This is the default style settings page. Here you can choose the default styles that will apply in each section of
	your website.</p>

<?php
$edit_links = $manage_links = [];
?>
<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
	<input type="hidden" name="action" value="stylepress_save"/>
	<?php wp_nonce_field( 'stylepress_save_options', 'stylepress_save_options' ); ?>

	<label for="stylepress_advanced"><?php esc_html_e( 'Advanced View', 'stylepress' ); ?></label>
	<input type="checkbox" name="stylepress_advanced" id="stylepress_advanced"
	       value="1"<?php checked( 1, Settings::get_instance()->get( 'stylepress_advanced' ) ); ?>>

	<div class="stylepress__defaults stylepress__defaults--basic">

		<div class="stylepress__defaults-page">
			<div class="stylepress__defaults-pagesection stylepress-chrome">
				<div class="stylepress-chrome-row">
					<div class="stylepress-chrome-column stylepress-chrome-left">
						<span class="stylepress-chrome-dot" style="background:#ED594A;"></span>
						<span class="stylepress-chrome-dot" style="background:#FDD800;"></span>
						<span class="stylepress-chrome-dot" style="background:#5AC05A;"></span>
					</div>
					<div class="stylepress-chrome-column stylepress-chrome-middle">
						<input type="text" value="<?php echo esc_attr( get_home_url() ); ?>" disabled
						       class="stylepress-chrome-addr">
					</div>
					<div class="stylepress-chrome-column stylepress-chrome-right">
						<div>
							<span class="stylepress-chrome-bar"></span>
							<span class="stylepress-chrome-bar"></span>
							<span class="stylepress-chrome-bar"></span>
						</div>
					</div>
				</div>
			</div>
			<?php
			$page_type = '_global';
			foreach ( $categories as $category ) {
				if ( $category['global_selector'] ) {
					?>
					<div class="stylepress__defaults-pagesection">
						<label
							for="default-basic-<?php echo esc_attr( $category['slug'] ); ?>"><?php echo esc_html( $category['title'] ); ?>
						</label>
						<select
							class="stylepress__default-select js-stylepress-select"
							name="default_style_simple[<?php echo esc_attr( $page_type ); ?>][<?php echo esc_attr( $category['slug'] ); ?>]">
							<option value=""> - choose a style -</option>
							<?php
							$styles = Styles::get_instance()->get_all_styles( $category['slug'], true );
							foreach ( $styles as $design_id => $design ) {
								if ( $design_id > 0 ) {
									$edit_links[ $design_id ] = Styles::get_instance()->get_design_edit_url( $design_id );
								}
								?>
								<option
									value="<?php echo (int) $design_id; ?>"
									<?php selected( $design_id, isset( $default_styles[ $page_type ] ) && ! empty( $default_styles[ $page_type ][ $category['slug'] ] ) ? $default_styles[ $page_type ][ $category['slug'] ] : false ); ?>>
									<?php echo esc_attr( $design ); ?>
								</option>
								<?php
							} ?>
						</select>
						<span class="stylepress__default-link js-stylepress-link"></span>
						<?php if ( defined( 'STYLEPRESS_ALLOW_CREATION' ) && STYLEPRESS_ALLOW_CREATION ) { ?>
							<span class="stylepress__manage-link">
							 / <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Backend::STYLES_PAGE_SLUG ) ); ?>">
								Add New
							</a>
						</span>
						<?php } ?>
					</div>
					<?php
				}
			}
			?>
		</div>

		<input class="button button-primary"
		       type="submit" value="<?php esc_attr_e( 'Save Settings', 'stylepress' ); ?>">

	</div>
	<div class="stylepress__defaults stylepress__defaults--advanced">

		<table class="widefat striped">
			<thead>
			<tr>
				<th>Page Type</th>
				<?php foreach ( $categories as $category ) {
					if ( $category['global_selector'] ) { ?>
						<th>Default <?php echo $category['title']; ?></th>
					<?php }
				} ?>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $page_types as $page_type => $page_type_name ) { ?>
				<tr class="stylepress-row<?php echo esc_attr( $page_type ); ?>">
					<td>
						<?php esc_html_e( $page_type_name ); ?>
					</td>
					<?php foreach ( $categories as $category ) {
						if ( $category['global_selector'] ) {
							?>
							<td>
								<select
									class="stylepress__default-select js-stylepress-select"
									name="default_style[<?php echo esc_attr( $page_type ); ?>][<?php echo esc_attr( $category['slug'] ); ?>]">
									<option value="">Choose <?php echo esc_attr( $category['title'] ); ?> </option>
									<?php
									$styles = Styles::get_instance()->get_all_styles( $category['slug'], true );
									foreach ( $styles as $design_id => $design ) { ?>
										<option
											value="<?php echo (int) $design_id; ?>"
											<?php selected( $design_id, isset( $default_styles[ $page_type ] ) && ! empty( $default_styles[ $page_type ][ $category['slug'] ] ) ? $default_styles[ $page_type ][ $category['slug'] ] : false ); ?>>
											<?php echo esc_attr( $design ); ?>
										</option>
										<?php
									} ?>
								</select>
							</td>
						<?php }
					} ?>
				</tr>

			<?php } ?>
			</tbody>
		</table>

		<input class="button button-primary"
		       type="submit" value="<?php esc_attr_e( 'Save Settings', 'stylepress' ); ?>">

	</div>

	<script type="text/javascript">
    jQuery(function ($) {
      var edit_links = <?php echo json_encode( $edit_links );?>;
      $('.js-stylepress-select').change(function () {
        var design_id = $(this).val();
        var $edit = $(this).parent().find('.js-stylepress-link');
        if ($edit.length > 0) {
          $edit.html('');
          if (design_id !== '') {
            if (typeof edit_links[design_id] !== 'undefined') {
              $edit.html('<a href="' + edit_links[design_id] + '" target="_blank">Edit</a>');
            }
          }
        }
      }).change();
    });
	</script>

</form>
