<?php
/**
 * Our Frontend class.
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

class Frontend extends Base {

	/**
	 * Initializes the plugin and sets all required filters.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'template_include' ), 999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_css' ) );
	}


	/**
	 * Filter on the template_include path.
	 * This can overwrite our site wide template for every page of the website.
	 * This is where the magic happens! :)
	 *
	 * If the user has disabled stylepress for a particular item then we just render default.
	 *
	 * @since 2.0.0
	 *
	 * @param string $template_include The path to the current template file.
	 *
	 * @return string
	 */
	public function template_include( $template_include ) {

		Plugin::get_instance()->populate_globals();

		if ( ! empty( $GLOBALS['stylepress_render']['template'] ) ) {
			return $GLOBALS['stylepress_render']['template'];
		}

		Plugin::get_instance()->debug_message( 'Sorry no styles found for this page type' );

		return $template_include;
	}


	/**
	 * Register some frontend css files
	 *
	 * @since 2.0.0
	 */
	public function frontend_css() {
		wp_enqueue_style( 'stylepress-css', STYLEPRESS_URI . 'assets/frontend.css', false, STYLEPRESS_VERSION );

		wp_register_script( 'stylepress-js', STYLEPRESS_URI . 'assets/frontend.js', false, STYLEPRESS_VERSION, true );
		wp_localize_script( 'stylepress-js', 'stylepress_frontend', array(
				'ajaxurl'      => admin_url( 'admin-ajax.php' ),
				'public_nonce' => wp_create_nonce( 'stylepress-public-nonce' ),
			)
		);
		wp_enqueue_script( 'stylepress-js' );


		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			// This loads extra scripts into the editor iframe only in edit mode. Used for the styling of the helper text at the top of the edit iframe.
			wp_enqueue_style( 'stylepress-editor-in', STYLEPRESS_URI . 'assets/frontend-edit.css', false, STYLEPRESS_VERSION );
			wp_enqueue_script( 'stylepress-editor-in', STYLEPRESS_URI . 'assets/frontend-edit.js', false, STYLEPRESS_VERSION, true );

		}

	}


}

