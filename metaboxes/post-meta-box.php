<?php
/**
 * Metabox showing on all pages and posts.
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

$default_styles = Styles::get_instance()->get_default_styles();
$page_styles    = Styles::get_instance()->get_page_styles( $post->ID );

$page_status = Styles::get_instance()->is_stylpress_enabled( $post );

if ( ! $page_status['enabled'] ) {
	?>

	<label class="screen-reader-text"
	       for="stylepress_page_style"><?php esc_html_e( 'Page Style', 'stylepress' ); ?></label>
	<p>
		<small><?php
			// Translators: The %s is the reason the page has been disabled.
			printf( esc_html__( 'StylePress has been disabled on this page %s.', 'stylepress' ), $page_status['reason'] ); ?>
		</small>
	</p>
	<?php
} else {
	?>

	<label class="screen-reader-text"
	       for="stylepress_page_style"><?php esc_html_e( 'Page Style', 'stylepress' ); ?></label>
	<p>
		<small><?php
			// Translators: The first %s is a link <a href=""> and the second %s is a closing link </a>.
			printf( esc_html__( 'Choose the styles for each section here. Create new styles from the %1$sStylePress%2$s page.', 'stylepress' ), '<a href="' . esc_url( admin_url( 'admin.php?page=stylepress' ) ) . '">', '</a>' ); ?></small>
	</p>
	<?php
	wp_nonce_field( 'stylepress_style_nonce', 'stylepress_style_nonce' );

	$categories = Styles::get_instance()->get_categories();

	foreach ( $categories as $category ) {
		$designs = Styles::get_instance()->get_all_styles( $category['slug'], true );
		if ( $designs ) {
			?>
			<p class="post-attributes-label-wrapper">
				<label class="post-attributes-label"
				       for="stylepress_page_style<?php echo esc_attr( $category['slug'] ); ?>">
					<?php esc_html_e( $category['title'] ) ?>
				</label>
			</p>
			<select name="stylepress_style[<?php echo esc_attr( $category['slug'] ); ?>]"
			        id="stylepress_page_style<?php echo esc_attr( $category['slug'] ); ?>">
				<option value="0"><?php
					// Translators: %s contains the current default style.
					printf( esc_html__( 'Default %s', 'stylepress' ), esc_attr(
						$default_styles[ $category['slug'] ] !== false && isset( $designs[ $default_styles[ $category['slug'] ] ] ) ?
							'(' . $designs[ $default_styles[ $category['slug'] ] ] . ')' :
							'' ) );
					?></option>
				<?php foreach ( $designs as $design_id => $design_name ) {
					?>
					<option
						value="<?php echo esc_attr( $design_id ); ?>"<?php echo isset( $page_styles[ $category['slug'] ] ) && (int) $page_styles[ $category['slug'] ] === (int) $design_id ? ' selected' : ''; ?>>
						<?php echo esc_attr( $design_name ); ?>
					</option>
					<?php
				}
				?>
			</select>
		<?php }
	}

}