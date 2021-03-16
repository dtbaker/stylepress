<?php
/**
 * Our Plugin class.
 * This handles all our hooks and stuff.
 *
 * @package stylepress
 */

namespace StylePress\Remote_Styles;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Styles
 */
class Remote_Styles extends \StylePress\Core\Base {

	public function get_all_remote_styles() {
		$styles = array();

		$styles = apply_filters( 'stylepress_remote_styles', $styles );

		return $styles;
	}

	public function get_remote_style_data( $remote_style_slug ) {
		return apply_filters( 'stylepress_remote_style_data', false, $remote_style_slug );
	}

	public function is_remote_style_imported( $remote_style_slug ) {
		// todo:
		return false;
	}
}

