<?php
/**
 * Our Elementor integration class.
 *
 * @package stylepress
 */

namespace StylePress\Wizard;

use StylePress\Core\Base;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Plugin
 */
class Complete extends Base {

	public function view() {
		include __DIR__ . '/views/support.php';
		include __DIR__ . '/views/ready.php';
	}

}
