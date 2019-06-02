<?php
/**
 * Our Plugin class.
 * This handles all our hooks and stuff.
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Styles
 */
class Remote_Styles extends Base {

	/**
	 * Initializes the plugin and sets all required filters.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

	}

	public function get_all_styles( $category_slug = false, $include_empty = false, $parent_id = false ) {
		$styles = array();

		$styles = apply_filters('stylepress_remote_styles',$styles,$category_slug,$include_empty,$parent_id);

		return $styles;
	}

	public function get_style($style_id){
		return apply_filters('stylepress_remote_style',false,$style_id);
	}
}

