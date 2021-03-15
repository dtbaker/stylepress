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

	public function get_all_remote_styles() {
		$styles = array();

		$styles = apply_filters( 'stylepress_remote_styles', $styles );

		return $styles;
	}

	public function get_chosen_remote_style_slug() {
		return get_theme_mod( 'stylepress_remote_style_slug' );
	}

	public function set_chosen_remote_style_slug( $style_slug ) {
		set_theme_mod( 'stylepress_remote_style_slug', $style_slug );
	}

	public function get_remote_style_data( $remote_style_slug ) {
		return apply_filters( 'stylepress_remote_style_data', false, $remote_style_slug );
	}

	public function get_current_remote_style_data() {
		$current_style_slug = $this->get_chosen_remote_style_slug();
		if ( $current_style_slug ) {
			return $this->get_remote_style_data( $current_style_slug );
		}

		return false;
	}

	public function load_chosen_style_dependencies() {
		if ( is_admin() ) {
			// todo: move this into main plugin and run once we import a remote style locally.
			require_once __DIR__ . '/tgm-plugin-activation.php';
			$style_data = $this->get_current_remote_style_data();
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

