<?php
/**
 * Our Settings class.
 *
 * This handles storing our site wide settings.
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Settings
 */
class Templates extends Base {


	public static function get_template_part( $slug, $name, $base_path = '', $args = [] ) {

		$template_path = STYLEPRESS_PATH . $base_path . 'template-parts/' . $slug . '-' . $name . '.php';
		if ( is_file( $template_path ) ) {
			global $wp_query;
			// These are extracted in load_template()
			$wp_query->query_vars['stylepress'] = $args;
			load_template( $template_path, false );
		}

	}

}

