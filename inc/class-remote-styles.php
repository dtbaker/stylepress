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

		add_action( 'after_setup_theme', [ $this, 'load_chosen_style_dependencies' ] );
		add_filter( 'tgmpa_load', array( $this, 'tgmpa_load' ), 10, 1 );
	}

	public function tgmpa_load( $status ) {
		return is_admin() || current_user_can( 'install_themes' );
	}

	public function get_all_styles( $category_slug = false, $include_empty = false, $parent_id = false ) {
		$styles = array();

		$styles = apply_filters( 'stylepress_remote_styles', $styles, $category_slug, $include_empty, $parent_id );

		return $styles;
	}

	public function get_style( $style_id ) {
		return apply_filters( 'stylepress_remote_style', false, $style_id );
	}

	public function set_current_site_style( $new_style ) {
		set_theme_mod( 'stylepress_site_style', $new_style );
	}

	public function get_current_site_style() {
		return get_theme_mod( 'stylepress_site_style' );
	}

	public function load_chosen_style_dependencies() {

		require_once __DIR__ . '/class-tgm-plugin-activation.php';
		$current_style = $this->get_current_site_style();
		if ( $current_style ) {
			$style_data = $this->get_style( $current_style );

			if ( $style_data && ! empty( $style_data['tgmpa'] ) ) {
				if ( ! empty( $style_data['tgmpa'] ) ) {

					$GLOBALS['tgmpa']->init();
					$config = array(
						'id'           => 'stylepress',
						'default_path' => '',
						'menu'         => 'tgmpa-install-plugins',
						'has_notices'  => true,
						'dismissable'  => true,
						'dismiss_msg'  => '',
						'is_automatic' => false,
						'message'      => '',
					);

					tgmpa( $style_data['tgmpa'], $config );
				}
			}
		}


	}
}

