<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

//$page_types = Settings::get_instance()->get_all_page_types();
$categories      = Styles::get_instance()->get_categories();
$parent_style_id = isset( $_GET['style_id'] ) ? (int) $_GET['style_id'] : 0;
$parent_style    = false;
if ( $parent_style_id ) {
	$parent_style = get_post( $parent_style_id );
}
if ( ! $parent_style || $parent_style->post_type !== Styles::CPT ) {
	wp_die( 'Invalid parent style' );
}
?>

<div class="stylepress__main">
	<div class="stylepress__summary">
		<h3>Current Style: <?php echo esc_html( $parent_style->post_title ); ?></h3>
		<img src="<?php if ( has_post_thumbnail( $parent_style->ID ) ) {
			echo esc_url( get_the_post_thumbnail( $parent_style->ID, 'full' ) );
		} else {
			echo esc_url( STYLEPRESS_URI . 'assets/images/wp-theme-thumb-logo-sml.jpg' );
		} ?>">
		<p>Below are a list of designs included within this style.</p>
	</div>
	<?php foreach ( $categories as $category ) { ?>
		<div class="stylepress__category">
			<a name="cat-<?php echo esc_attr( $category['slug'] ); ?>"></a>
			<h3 class="stylepress__category-header">
				<span><?php echo esc_html( $category['plural'] ); ?></span>
				<small><?php echo esc_html( $category['description'] ); ?></small>
			</h3>
			<div class="stylepress__category-content">
				<?php
				$designs = Styles::get_instance()->get_all_styles( $category['slug'], false, $parent_style_id );
				//						$designs[] = 'asdf';
				foreach ( $designs as $design_id => $design ) {
					?>
					<div class="stylepress__style">
						<div class="stylepress__style-inner">
							<a href="<?php echo esc_url( Styles::get_instance()->get_design_edit_url( $design_id ) ); ?>"
							   class="stylepress__style-thumb"
							   style="background-image: url(<?php if ( has_post_thumbnail( $design_id ) ) {
								   echo esc_url( get_the_post_thumbnail( $design_id, 'full' ) );
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
								   href="<?php echo esc_url( Styles::get_instance()->get_design_edit_url( $design_id ) ); ?>">
									<?php esc_html_e( 'Edit Style', 'stylepress' ); ?>
								</a>
							</div>
						</div>

					</div>
				<?php } ?>
				<div class="stylepress__style stylepress__style--new" tabindex="0">

					<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
						<input type="hidden" name="new_style_category" value="<?php echo esc_attr( $category['slug'] ); ?>"/>
						<input type="hidden" name="new_style_parent" value="<?php echo (int) $parent_style->ID; ?>"/>
						<input type="hidden" name="action" value="stylepress_new_style"/>
						<?php wp_nonce_field( 'stylepress_new_style', 'stylepress_new_style' ); ?>

						<div class="stylepress__style-inner">

							<div
								class="stylepress__style-thumb"
								style="background-image: url(<?php echo esc_url( STYLEPRESS_URI . 'assets/images/wp-theme-thumb-logo-sml.jpg' ); ?>);">
							</div>

							<h3 class="stylepress__style-name">
								<input type="text" class="stylepress__style-name-input" name="new_style_name"
								       placeholder="<?php printf( esc_attr( 'Enter New %s Name', 'stylepress' ), $category['title'] ); ?>">
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
