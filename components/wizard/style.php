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
class Style extends Base {

	public function view() {
		$remote_styles = Remote_Styles::get_instance()->get_all_remote_styles();
		include __DIR__ . '/views/style.php';
	}

}
