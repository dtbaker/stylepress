<?php
/**
 * Our Backend class.
 *
 * This handles our main admin page
 *
 * @package stylepress
 */

namespace StylePress\Backend;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Backend
 */
class Layout extends \StylePress\Core\Base {

	const PAGE_SLUG = STYLEPRESS_SLUG . '-layout';

	public function add_submenu($top_level_slug){
		$page = add_submenu_page(
			$top_level_slug,
			__( 'Layout', 'stylepress' ),
			__( 'Layout', 'stylepress' ),
			'manage_options',
			self::PAGE_SLUG,
			array(
				$this,
				'layout_page_callback'
			)
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'admin_page_assets' ) );
	}

	public function admin_page_assets() {
		wp_enqueue_style( 'stylepress-admin', STYLEPRESS_URI . 'build/assets/backend.css', false, STYLEPRESS_VERSION );
		wp_register_script( 'stylepress-admin', STYLEPRESS_URI . 'build/assets/backend.js', [], STYLEPRESS_VERSION );
		wp_enqueue_script( 'stylepress-admin' );
	}

	/**
	 * This is our callback for rendering our custom menu page.
	 * This page shows all our site styles and currently selected defaults.
	 *
	 * @since 2.0.0
	 */
	public function layout_page_callback() {
		$this->content = $this->render_template( 'admin/settings.php' );
		$this->header  = $this->render_template( 'admin/header.php' );
		echo $this->render_template( 'wrapper.php' );
	}


	public function stylepress_save() {

		// Check if our nonce is set.
		if ( ! isset( $_POST['stylepress_save_options'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['stylepress_save_options'], 'stylepress_save_options' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}

		$page_types       = Settings::get_instance()->get_all_page_types();
		$categories       = Styles::get_instance()->get_categories();
		$defaults_to_save = [];

		$user_provided_defaults = [];
		$is_advanced_settings   = ! empty( $_POST['stylepress_advanced'] );
		Settings::get_instance()->set( 'stylepress_advanced', $is_advanced_settings );
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
				// store defaults for each page type here.
				foreach ( $categories as $category ) {
					if ( isset( $user_provided_defaults[ $page_type ][ $category['slug'] ] ) ) {
						$chosen_default = $user_provided_defaults[ $page_type ][ $category['slug'] ];
						$valid_answers  = Styles::get_instance()->get_all_styles( $category['slug'], true );
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

		Settings::get_instance()->set( 'stylepress_styles', $defaults_to_save );

		wp_safe_redirect( admin_url( 'admin.php?page=' . self::SETTINGS_PAGE_SLUG . '&saved' ) );
		exit;

	}
}

