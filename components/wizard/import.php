<?php
/**
 * Our Elementor integration class.
 *
 * @package stylepress
 */

namespace StylePress\Wizard;

use StylePress\Core\Base;
use StylePress\Remote_Styles\Remote_Styles;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Plugin
 */
class Import extends Base {
	public function view() {
		$remote_style_slug = isset( $_GET['remote_style_slug'] ) ? $_GET['remote_style_slug'] : false;
		if ( ! $remote_style_slug ) {
			esc_html_e( 'Invalid remote style, please go back and try again', 'stylepress' );
			return false;
		}
		$remote_style_data    = Remote_Styles::get_instance()->get_remote_style_data( $remote_style_slug );
		if ( ! $remote_style_data ) {
			esc_html_e( 'Invalid remote style data, please go back and try again', 'stylepress' );
			return false;
		}
		$is_already_installed    = Remote_Styles::get_instance()->is_remote_style_imported( $remote_style_slug );

		include __DIR__ . '/views/import.php';
	}
}
