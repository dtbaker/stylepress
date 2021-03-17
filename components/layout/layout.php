<?php
/**
 * Our Backend class.
 *
 * This handles our main admin page
 *
 * @package stylepress
 */

namespace StylePress\Layout;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Backend
 */
class Layout extends \StylePress\Core\Base {

	const PAGE_SLUG = STYLEPRESS_SLUG . '-layout';

	public function __construct() {
		add_action( 'admin_action_stylepress_layout_save', array( $this, 'stylepress_layout_save' ) );
	}

	public function add_submenu($top_level_slug){
		$page = add_submenu_page(
			$top_level_slug,
			__( 'Layout', 'stylepress' ),
			__( 'Layout', 'stylepress' ),
			'manage_options',
			self::PAGE_SLUG,
			array(
				$this,
				'view'
			)
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'admin_page_assets' ) );
	}

	public function admin_page_assets() {
		wp_enqueue_style( 'stylepress-layout', STYLEPRESS_URI . 'build/assets/layout.css', false, STYLEPRESS_VERSION );
		wp_enqueue_script( 'stylepress-layout', STYLEPRESS_URI . 'build/assets/layout.js', [], STYLEPRESS_VERSION );
	}

	public function view() {
		include __DIR__ . '/views/layout.php';
	}

	public function stylepress_layout_save() {

		// Check if our nonce is set.
		if ( ! isset( $_POST['stylepress_layout_save_options'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['stylepress_layout_save_options'], 'stylepress_layout_save_options' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}

		$page_types       = \StylePress\Core\Settings::get_instance()->get_all_page_types();
		$categories       = \StylePress\Styles\Data::get_instance()->get_categories();
		$defaults_to_save = [];

		$user_provided_defaults = [];
		$is_advanced_settings   = ! empty( $_POST['stylepress_advanced'] );
		\StylePress\Core\Settings::get_instance()->set( 'stylepress_advanced', $is_advanced_settings );
		if ( $is_advanced_settings ) {
			if ( isset( $_POST['default_style'] ) && is_array( $_POST['default_style'] ) ) {
				$user_provided_defaults = $_POST['default_style'];
			}
		} else {
			// simple styles.
			if ( isset( $_POST['default_style_simple'] ) && is_array( $_POST['default_style_simple'] ) ) {
				$user_provided_defaults = $_POST['default_style_simple'];
			}
		}
		foreach ( $page_types as $page_type => $page_type_name ) {
			$defaults_to_save[ $page_type ] = [];
			if ( isset( $user_provided_defaults[ $page_type ] ) && is_array( $user_provided_defaults[ $page_type ] ) ) {
				// check if user has disabled this type all together
				if ( isset( $user_provided_defaults[ $page_type ][ '_disabled' ] ) ) {
					$defaults_to_save[ $page_type ][ '_disabled' ] = 'disabled';
				}
				// store defaults for each page type here.
				foreach ( $categories as $category ) {
					if ( isset( $user_provided_defaults[ $page_type ][ $category['slug'] ] ) ) {
						$chosen_default = $user_provided_defaults[ $page_type ][ $category['slug'] ];
						$valid_answers  = \StylePress\Styles\Data::get_instance()->get_all_styles( $category['slug'], true );
						if ( isset( $valid_answers[ $chosen_default ] ) ) {
							$defaults_to_save[ $page_type ][ $category['slug'] ] = $chosen_default;
						}
					}
				}
			}
		}

		foreach ( $defaults_to_save as $default_page_type => $default_styles ) {
			if ( $default_page_type !== '_global' ) {
				$defaults_to_save[ $default_page_type ] = array_merge( $defaults_to_save['_global'], $default_styles );
			}
		}

		\StylePress\Core\Settings::get_instance()->set( 'stylepress_styles', $defaults_to_save );

		wp_safe_redirect( admin_url( 'admin.php?page=' . self::PAGE_SLUG . '&saved' ) );
		exit;
	}
}

