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
$designs    = Styles::get_instance()->get_all_styles();
?>

<?php
if ( isset( $_GET['style_id'] ) ) {
	require_once STYLEPRESS_PATH . 'admin/styles-page-inner.php';
} else {
	?>
	<div class="stylepress-browser">

		<div class="wp-clearfix">

			<h3 class="stylepress-header">
				<div class="buttons">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=stylepress&style_id=new' ) ); ?>"
					   class="button button-primary">Create New Style</a>
				</div>
				<span>Your Styles</span>
				<small>These are all available website styles. You can set defaults below, or customize on a per page basis.
				</small>
			</h3>

			<div class="stylepress-item-wrapper">
				<?php

				if ( ! $designs ) {
					?>
					<p>None yet! Create your own or install from the list below.</p>
					<p>&nbsp;</p>
					<?php
				}

				foreach ( $designs as $design_id => $design ) :
					$post = get_post( $design_id );
					if ( $post->post_parent ) {
						continue;
					}
					?>
					<div class="design stylebox" tabindex="0">
						<?php if ( has_post_thumbnail( $design_id ) ) { ?>
							<a
								href="<?php echo esc_url( admin_url( 'admin.php?page=stylepress&style_id=' . $design_id ) ); ?>"
								class="thumb">
								<?php echo get_the_post_thumbnail( $design_id, 'full' ); ?>
							</a>
						<?php } else { ?>
							<a
								href="<?php echo esc_url( admin_url( 'admin.php?page=stylepress&style_id=' . $design_id ) ); ?>"
								class="thumb">
								<img
									src="<?php echo esc_url( STYLEPRESS_URI . 'assets/img/wp-theme-thumb-logo-sml.jpg' ); ?>">
							</a>
						<?php } ?>

						<?php
						// find out where it's applied, if anywhere.
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

						<h3 class="design-name"><?php echo esc_html( $design ); ?></h3>

						<div class="theme-actions">
							<!--						<a class="button button" href="#" onclick="alert('Coming soon');">-->
							<?php //esc_html_e( 'Copy', 'stylepress' );
							?><!--</a>-->
							<a class="button button-primary"
							   href="<?php echo esc_url( admin_url( 'admin.php?page=stylepress&style_id=' . $design_id ) ); ?>"><?php esc_html_e( 'Edit Style', 'stylepress' ); ?></a>
						</div>

					</div>
				<?php endforeach; ?>
			</div>
		</div>

	</div>

<?php } ?>
