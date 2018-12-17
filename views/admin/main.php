<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

$title = __( 'Full Site Editor', 'stylepress' );

// Help tab: Previewing and Customizing.

add_thickbox();

$settings   = Settings::get_instance()->get();
$page_types = Settings::get_instance()->get_all_page_types();
$categories = Styles::get_instance()->get_categories();
?>

<?php
if ( isset( $_GET['style_id'] ) ) {
	require_once STYLEPRESS_PATH . 'admin/styles-page-inner.php';
} else {
	?>
	<div class="stylepress__main">

		<div class="wp-clearfix">

			<?php foreach ( $categories as $category ) { ?>
				<div class="stylepress__category">
					<h3 class="stylepress__category-header">
						<span><?php echo esc_html( $category['plural'] ); ?></span>
						<small><?php echo esc_html( $category['description'] ); ?></small>
					</h3>
					<div class="stylepress__category-content">
						<?php
						$designs   = Styles::get_instance()->get_all_styles( $category['slug'] );
						$designs[] = 'asdf';
						$designs[] = 'asdf';
						$designs[] = 'asdf';
						$designs[] = 'asdf';
						$designs[] = 'asdf';
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

									<?php
									/*
									$used        = array();
									$args        = array(
										'post_type'           => 'stylepress_style',
										'post_parent'         => $design_id,
										'post_status'         => 'any',
										'posts_per_page'      => - 1,
										'ignore_sticky_posts' => 1,
									);
									$posts_array = get_posts( $args );

									foreach ( $page_types as $post_type => $post_type_title ) {
										if ( $settings && ! empty( $settings['defaults'][ $post_type ] ) && (int) $settings['defaults'][ $post_type ] === (int) $design_id ) {
											$used[ $post_type ] = $post_type_title;
										}
										// check if any of the child posts are used in this particular post type.
										foreach ( $posts_array as $post_array ) {
											if ( $settings && ! empty( $settings['defaults'][ $post_type ] ) && (int) $settings['defaults'][ $post_type ] === (int) $post_array->ID ) {
												$used[ $post_type ] = $post_type_title;
											}
										}
										// todo: query what custom pages have a different style overview
									}

									?>
									<div class="theme-usage">
										<a href="<?php echo esc_url( admin_url( 'admin.php?page=stylepress-settings' ) ); ?>">
											<?php if ( $used ) { ?>
												<i class="fa fa-check"></i> Style Applied To: <?php echo implode( ', ', $used ); ?>.
											<?php } else { ?>
												<i class="fa fa-times"></i> Style Not Used.
											<?php } ?>
										</a>
									</div>
									<?php */ ?>

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

							<form action="" method="post">
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

			<?php } ?>
		</div>

	</div>

<?php } ?>
