<?php
/**
 * Our Admin class.
 *
 * This handles our main admin page
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Admin
 */
class Admin extends Base {


	/**
	 * Initializes the plugin and sets all required filters.
	 *
	 * @since 2.0.0
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * This is our custom "Full Site Builder" menu item that appears under the appearance tab.
	 *
	 * @since 2.0.0
	 */
	public function admin_menu() {


		add_menu_page( __( 'StylePress', 'stylepress' ), __( 'StylePress', 'stylepress' ), 'manage_options', 'stylepress', array(
			$this,
			'styles_page_callback',
		), STYLEPRESS_URI . 'assets/img/icon.png' );
		// hack to rmeove default submenu
		$page = add_submenu_page( 'stylepress', __( 'StylePress', 'stylepress' ), __( 'Styles', 'stylepress' ), 'manage_options', 'stylepress', array(
			$this,
			'styles_page_callback'
		) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'admin_page_assets' ) );

		$page = add_submenu_page( 'stylepress', __( 'Add-Ons', 'stylepress' ), __( 'Add-Ons', 'stylepress' ), 'manage_options', 'stylepress-addons', array(
			$this,
			'addons_page_callback'
		) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'admin_page_assets' ) );

		$page = add_submenu_page( 'stylepress', __( 'Settings', 'stylepress' ), __( 'Settings', 'stylepress' ), 'manage_options', 'stylepress-settings', array(
			$this,
			'settings_page_callback'
		) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'admin_page_assets' ) );

	}

	/**
	 * Font Awesome and other assets for admin pages.
	 *
	 * @since 2.0.0
	 */
	public function admin_page_assets() {

		wp_enqueue_style( 'font-awesome', STYLEPRESS_URI . 'assets/icons/font-awesome/css/font-awesome.min.css' );

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		wp_register_script( 'stylepress-payments', STYLEPRESS_URI . 'assets/js/payment.js', false, STYLEPRESS_VERSION, true );
		wp_localize_script( 'stylepress-payments', 'stylepress_payment', array(
			'payment_nonce'  => wp_create_nonce( 'payment_nonce' ),
			'hostname'       => get_home_url(),
			'plugin_version' => STYLEPRESS_VERSION,
		) );
		wp_enqueue_script( 'stylepress-payments' );

		wp_enqueue_script( 'stylepress-slider', STYLEPRESS_URI . 'assets/js/omni-slider.js', array( 'jquery' ), STYLEPRESS_VERSION, true );

		require_once STYLEPRESS_PATH . 'views/_help_text.php';

	}

	/**
	 * This is our callback for rendering our custom menu page.
	 * This page shows all our site styles and currently selected defaults.
	 *
	 * @since 2.0.0
	 */
	public function styles_page_callback() {
		$this->content = $this->render_template(
			'admin/main.php', [
			]
		);
		$this->header  = $this->render_template( 'admin/header.php' );
		echo $this->render_template( 'wrapper.php' );
	}

	/**
	 * This is our callback for rendering our custom menu page.
	 * This page shows all our site styles and currently selected defaults.
	 *
	 * @since 2.0.0
	 */
	public function settings_page_callback() {
		include STYLEPRESS_PATH . 'admin/settings-page.php';
	}

	/**
	 * This is our callback for rendering our custom menu page.
	 * This page shows all our site styles and currently selected defaults.
	 *
	 * @since 2.0.0
	 */
	public function addons_page_callback() {
		include STYLEPRESS_PATH . 'admin/addons-page.php';
	}

}

