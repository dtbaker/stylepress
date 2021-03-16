<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package stylepress
 */

namespace StylePress\Styles;

defined( 'STYLEPRESS_VERSION' ) || exit;

$remote_style_slug = isset( $_GET['remote_style_slug'] ) ? $_GET['remote_style_slug'] : 0;
if(!$remote_style_slug){
	wp_die('Invalid style ID');
}

$remote_style = \StylePress\Remote_Styles\Remote_Styles::get_instance()->get_remote_style_data($remote_style_slug);
if(!$remote_style){
	wp_die('Invalid style');
}

?>

<div class="stylepress__main">
	<div class="stylepress__summary">
		<h3>Style: <?php echo esc_html( $remote_style['title'] ); ?></h3>
		<img src="<?php echo esc_url($remote_style['thumbnail_url']); ?>">
		<p>
			<a class="button button-primary"
			   href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG . '&remote_style_slug=' . $remote_style_slug .'&import_step=1' ) ); ?>">
				<?php esc_html_e( 'Import This Style (link to wizard step)', 'stylepress' ); ?>
			</a>
		</p>
	</div>
</div>


