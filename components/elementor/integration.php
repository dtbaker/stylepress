<?php
/**
 * Our Elementor integration class.
 *
 * @package stylepress
 */

namespace StylePress\Elementor;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Plugin
 */
class Integration extends \StylePress\Core\Base {

	/**
	 * Initializes the plugin and sets all required filters.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		add_action( 'elementor/init', array( $this, 'elementor_init_complete' ), 40 );
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_add_new_widgets' ) );
		//add_action( 'init', array( $this, 'load_extensions' ) );
		add_filter( 'elementor/init2', array( $this, 'include_our_styles_in_elementor_popup' ), 10, 2 );
		add_filter( 'elementor/template-library/create_new_dialog_types', array( $this, 'dont_allow_new' ), 10, 2 );
	}

	public static function is_elementor_active() {
		return class_exists( '\Elementor\Plugin' ) && is_callable( '\Elementor\Plugin', 'instance' );
	}

	public static function is_post_built_with_elementor($post_id) {
		return self::is_elementor_active() && \Elementor\Plugin::$instance->db->is_built_with_elementor( $post_id );
	}

	public static function edit_url_for_design( $design_id ){
		return \Elementor\Plugin::$instance->documents->get( $design_id )->get_edit_url();
	}

	public function dont_allow_new( $types ) {
		unset( $types['stylepress'] );

		return $types;
	}

	public function include_our_styles_in_elementor_popup( $option_value ) {
		require_once __DIR__ . '/source-stylepress.php';
		\Elementor\Plugin::$instance->templates_manager->register_source( '\Elementor\TemplateLibrary\Source_StylePress' );

		require_once __DIR__ . '/stylepress-document.php';
		\Elementor\Plugin::$instance->documents
			->register_document_type( 'stylepress', \Elementor\Modules\Library\Documents\Stylepress_Document::get_class_full_name() );
	}

	/**
	 * Runs once elementor has completed loading.
	 * This method loads our custom Elementor classes and injects them into the elementor widget_manager
	 * so our widgets appear in the Elementor ui.
	 *
	 * @since 2.0.0
	 */
	public function elementor_init_complete() {

		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( '\Elementor\Widget_Base' ) ) {
			if ( class_exists( '\Elementor\Plugin' ) ) {
				if ( is_callable( '\Elementor\Plugin', 'instance' ) ) {
					$elementor = \Elementor\Plugin::instance();

					// We have to enqueue styles on all pages, even non elementor pages, so global styles work.
					// reference: wp-content/plugins/elementor/includes/frontend.php:209
					add_action( 'wp_enqueue_scripts', [ $elementor->frontend, 'enqueue_styles' ] );

					if ( $elementor && isset( $elementor->elements_manager ) ) {
						if ( method_exists( $elementor->elements_manager, 'add_category' ) ) {
							$elementor->elements_manager->add_category(
								'stylepress',
								[
									'title' => 'StylePress',
									'icon'  => 'eicon-font'
								]
							);
						}
					}
				}
			}
		}
	}


	public function load_extensions() {

		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			if ( class_exists( '\Elementor\Plugin' ) ) {

				if ( is_callable( '\Elementor\Plugin', 'instance' ) ) {
					$elementor = \Elementor\Plugin::instance();
					if ( isset( $elementor->widgets_manager ) ) {
						if ( method_exists( $elementor->widgets_manager, 'register_widget_type' ) ) {

							//require_once STYLEPRESS_PATH . 'extensions/woocommerce/woocommerce.php';
							do_action( 'stylepress_init_extensions' );
						}
					}
				}
			}
		}

	}

	/**
	 * Adds our new widgets to the Elementor widget area.
	 *
	 * @since 2.0.0
	 */
	public function elementor_add_new_widgets() {
		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			if ( class_exists( '\Elementor\Plugin' ) ) {

				if ( is_callable( '\Elementor\Plugin', 'instance' ) ) {
					$elementor = \Elementor\Plugin::instance();
					if ( isset( $elementor->widgets_manager ) ) {
						if ( method_exists( $elementor->widgets_manager, 'register_widget_type' ) ) {

							require_once STYLEPRESS_PATH . 'extensions/inner-content/inner-content.php';
							do_action( 'stylepress_init_widgets' );

						}
					}
				}
			}
		}
	}

}
