<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;


$remote_style_id = isset( $_GET['remote_style_id'] ) ? $_GET['remote_style_id'] : 0;
if(!$remote_style_id){
	wp_die('Invalid style ID');
}

$remote_style = Remote_Styles::get_instance()->get_style($remote_style_id);
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
			   href="<?php echo esc_url( admin_url( 'admin.php?page=' . Admin::STYLES_PAGE_SLUG . '&style_id=' . $remote_style_id ) ); ?>">
				<?php esc_html_e( 'Use This Style', 'stylepress' ); ?>
			</a>
		</p>
	</div>
</div>


