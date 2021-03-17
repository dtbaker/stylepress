<?php
/**
 * Our Settings class.
 *
 * This handles storing our site wide settings.
 *
 * @package stylepress
 */

namespace StylePress\Core;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Settings
 */
class Permissions extends Base {
	public function can_run_setup_wizard() {
		return current_user_can('manage_options');
	}

	public function can_edit_post_meta_boxes($post = false) {
		return current_user_can('edit_posts');
	}
}

