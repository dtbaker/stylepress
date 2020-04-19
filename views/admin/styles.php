<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

?>

<div class="stylepress__styles">
	<div class="stylepress__category">
		<h3 class="stylepress__category-header">
			<span>Your Styles</span>
			<small>These are the page styles which can be applied to your website pages.
			</small>
		</h3>
		<div class="stylepress__category-content">
			<?php
			$category = 'styles';
			$designs  = Styles::get_instance()->get_all_styles( $category );
			foreach ( $designs as $design_id => $design ) {
				?>
				<div class="stylepress__style">
					<div class="stylepress__style-inner">
						<a
							href="<?php echo esc_url( admin_url( 'admin.php?page=' . Backend::STYLES_PAGE_SLUG . '&style_id=' . $design_id ) ); ?>"
							class="stylepress__style-thumb"
							style="background-image: url(<?php if ( has_post_thumbnail( $design_id ) ) {
								echo esc_url( get_the_post_thumbnail( $design_id, 'full' ) );
							} else {
								echo esc_url( STYLEPRESS_URI . 'src/images/wp-theme-thumb-logo-sml.jpg' );
							} ?>);">
						</a>
						<h3 class="stylepress__style-name"><?php echo esc_html( $design ); ?></h3>
						<div class="stylepress__style-action">
							<a class="button button-primary"
							   href="<?php echo esc_url( admin_url( 'admin.php?page=' . Backend::STYLES_PAGE_SLUG . '&style_id=' . $design_id ) ); ?>">
								<?php esc_html_e( 'Open Style', 'stylepress' ); ?>
							</a>
						</div>
					</div>

				</div>
			<?php } ?>
			<?php if ( defined( 'STYLEPRESS_ALLOW_CREATION' ) && STYLEPRESS_ALLOW_CREATION ) { ?>
				<div class="stylepress__style stylepress__style--new" tabindex="0">

					<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
						<input type="hidden" name="new_style_category" value="<?php echo esc_attr( $category ); ?>"/>
						<input type="hidden" name="new_style_parent" value="0"/>
						<input type="hidden" name="action" value="stylepress_new_style"/>
						<?php wp_nonce_field( 'stylepress_new_style', 'stylepress_new_style' ); ?>

						<div class="stylepress__style-inner">

							<div
								class="stylepress__style-thumb"
								style="background-image: url(<?php echo esc_url( STYLEPRESS_URI . 'src/images/wp-theme-thumb-logo-sml.jpg' ); ?>);">
							</div>

							<h3 class="stylepress__style-name">
								<input type="text" class="stylepress__style-name-input" name="new_style_name"
								       placeholder="<?php esc_attr_e( 'Enter New Style Name', 'stylepress' ); ?>">
							</h3>

							<div class="stylepress__style-action">
								<input class="button button-primary"
								       type="submit" value="<?php esc_attr_e( 'Create New', 'stylepress' ); ?>">
							</div>
						</div>
					</form>
				</div>
			<?php } ?>


		</div>
	</div>

	<?php if ( defined( 'STYLEPRESS_ALLOW_IMPORT' ) && STYLEPRESS_ALLOW_IMPORT ) { ?>
		<div class="stylepress__category">
			<h3 class="stylepress__category-header">
				<span>Available Styles</span>
				<small>These are the available default styles, choose one to import into the website.
				</small>
			</h3>
			<div class="stylepress__category-content">
				<?php
				$category = 'styles';
				$designs  = Remote_Styles::get_instance()->get_all_styles( $category );
				foreach ( $designs as $design_id => $design ) {
					?>
					<div class="stylepress__style">
						<div class="stylepress__style-inner">
							<a
								href="<?php echo esc_url( admin_url( 'admin.php?page=' . Backend::STYLES_PAGE_SLUG . '&remote_style_id=' . $design_id ) ); ?>"
								class="stylepress__style-thumb"
								style="background-image: url(<?php echo esc_url( $design['thumbnail_url'] ); ?>);">
							</a>
							<h3 class="stylepress__style-name"><?php echo esc_html( $design['title'] ); ?></h3>
							<div class="stylepress__style-action">
								<a class="button button-primary"
								   href="<?php echo esc_url( admin_url( 'admin.php?page=' . Backend::STYLES_PAGE_SLUG . '&remote_style_id=' . $design_id ) ); ?>">
									<?php esc_html_e( 'Preview Style', 'stylepress' ); ?>
								</a>
							</div>
						</div>

					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

</div>
