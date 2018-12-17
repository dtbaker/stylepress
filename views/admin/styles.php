<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

//$page_types = Settings::get_instance()->get_all_page_types();
$categories = Styles::get_instance()->get_categories();
?>

<div class="stylepress__styles">
		<div class="stylepress__category">
			<h3 class="stylepress__category-header">
				<span>Styles</span>
				<small>These are default page styles that you can apply to your pages. Choose settings once and apply to entire website.</small>
			</h3>
			<div class="stylepress__category-content">
				<?php
				$category = 'styles';
				$designs = Styles::get_instance()->get_all_styles( $category );
				//						$designs[] = 'asdf';
				foreach ( $designs as $design_id => $design ) {
					?>
					<div class="stylepress__style">
						<div class="stylepress__style-inner">
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=stylepress&style_id=' . $design_id ) ); ?>"
							   class="stylepress__style-thumb"
							   style="background-image: url(<?php if ( has_post_thumbnail( $design_id ) ) {
								   echo get_the_post_thumbnail( $design_id, 'full' );
							   } else {
								   echo esc_url( STYLEPRESS_URI . 'assets/images/wp-theme-thumb-logo-sml.jpg' );
							   } ?>);">
							</a>
							<h3 class="stylepress__style-name"><?php echo esc_html( $design ); ?></h3>
							<div class="stylepress__style-action">
								<a class="button button-primary"
								   href="<?php echo esc_url( admin_url( 'admin.php?page=stylepress&style_id=' . $design_id ) ); ?>">
									<?php esc_html_e( 'Edit Style', 'stylepress' ); ?>
								</a>
							</div>
						</div>

					</div>
				<?php } ?>
				<div class="stylepress__style stylepress__style--new" tabindex="0">

					<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
						<input type="hidden" name="new_style_category" value="<?php echo esc_attr( $category ); ?>"/>
						<input type="hidden" name="action" value="stylepress_new_style"/>
						<?php wp_nonce_field( 'stylepress_new_style', 'stylepress_new_style' ); ?>

						<div class="stylepress__style-inner">

							<div
								class="stylepress__style-thumb"
								style="background-image: url(<?php echo esc_url( STYLEPRESS_URI . 'assets/images/wp-theme-thumb-logo-sml.jpg' ); ?>);">
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


			</div>
		</div>

</div>
