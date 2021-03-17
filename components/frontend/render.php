<?php
/**
 * Our Frontend class.
 *
 * @package stylepress
 */

namespace StylePress\Frontend;

use StylePress\Logging\Debug;
use StylePress\Styles\Cpt;
use StylePress\Styles\Data;

defined( 'STYLEPRESS_VERSION' ) || exit;

class Render extends \StylePress\Core\Base {

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
	 * @param string $template_include The path to the current template file.
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function template_include( $template_include ) {

		$this->populate_globals();

		if ( ! empty( $GLOBALS['stylepress_render']['template'] ) ) {
			return $GLOBALS['stylepress_render']['template'];
		}

		Debug::get_instance()->debug_message( 'Sorry no styles found for this page type' );

		return $template_include;
	}


	/**
	 * Works out the type of page we're currently quer\ying.
	 * Copied from my Widget Area Manager plugin
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function get_current_page_type() {
		global $wp_query;
		//		print_r($wp_query->query_vars);
		if ( is_search() ) {
			return 'search';
		} else if ( is_404() ) {
			return '404';
		} else if ( function_exists( 'is_product' ) && is_product() ) {
			return 'product';
		} else if ( function_exists( 'is_product_category' ) && is_product_category() ) {
			return 'product_category';
		} else if ( is_category() ) {
			return 'category';
		} else if ( isset( $wp_query->query_vars ) && ! empty( $wp_query->query_vars['post_type'] ) && ! is_array( $wp_query->query_vars['post_type'] ) ) {
			// Elementor sets an array as post_type since the introduction of landing pages.
			$post_type = $wp_query->query_vars['post_type'];

			return $post_type . ( is_singular() ? '' : 's' );
		} else if ( ! empty( $wp_query->query_vars['taxonomy'] ) ) {
			$current_page_id = $wp_query->query_vars['taxonomy'];
			$value           = get_query_var( $wp_query->query_vars['taxonomy'] );
			if ( $value ) {
				$current_page_id .= '_' . $value;
			}

			return $current_page_id;
		} else if ( isset( $wp_query->is_posts_page ) && $wp_query->is_posts_page ) {
			return 'archive';
		} else if ( is_archive() ) {
			return 'archive';
		} else if ( is_home() || is_front_page() ) {
			return 'front_page';
		} else if ( is_attachment() ) {
			return 'attachment';
		} else if ( is_page() ) {
			return 'page';
		} else if ( is_single() ) {
			return 'post';
		}

		// todo - look for custom taxonomys
		return 'post';
	}


	public function populate_globals() {
		if ( isset( $GLOBALS['stylepress_render'] ) ) {
			return;
		}
		global $post;
		$GLOBALS['stylepress_render'] = [];

		// TODO: remove integration with Elementor here
		if ( $post && ! empty( $post->ID ) && 'elementor_library' === $post->post_type ) {
			$page_templates_module = \Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' );
			$path                  = $page_templates_module->get_template_path( 'elementor_canvas' );
			if ( is_file( $path ) ) {
				$GLOBALS['stylepress_render']['template'] = $path;
			}
		} else if ( $post && ! empty( $post->ID ) && Cpt::CPT === $post->post_type ) {
			// User is editing one of our stylepress templates, use a special template so we can show some stuff
			// Really only useful in Elementor or when previewing the template on the frontend.
			$GLOBALS['stylepress_render']['template'] = __DIR__ . '/views/editor.php';
		}

		$default_styles = Data::get_instance()->get_default_styles();
		$page_type      = $this->get_current_page_type();
		$these_styles   = isset( $default_styles[ $page_type ] ) ? $default_styles[ $page_type ] : false;

		$queried_object = get_queried_object();
		if ( $these_styles ) {
			// If stylepress has been disabled for this particular post then we just use the normal template include.
			// Not sure how to do this for category pages. We'll have to add a taxonomy settings area to each tax.
			// It can be disabled because a custom template is chosen, or via the disabled flag in advanced layouts.
			if ( ! empty( $these_styles['_disabled'] ) ) {
				Debug::get_instance()->debug_message( 'Skipping stylepress because post type is flagged as disabled in layout settings.' );
				$GLOBALS['stylepress_render']['template'] = false;
			} else if ( $queried_object && $queried_object instanceof \WP_Post && $queried_object->ID ) {
				$enabled = Data::get_instance()->is_stylpress_enabled( $post );
				if ( ! $enabled['enabled'] ) {
					Debug::get_instance()->debug_message( 'Skipping stylepress template because ' . $enabled['reason'] );
					$GLOBALS['stylepress_render']['template'] = false;
				}
				// todo: confirm our queried object isn't the first blog post in a list of things view.. that would mess it up.
				// We're doing a single object post, should be easy.
				$page_styles = Data::get_instance()->get_page_styles( $queried_object->ID );
				if ( $page_styles ) {
					foreach ( $page_styles as $category_slug => $chosen_style_id ) {
						if ( $chosen_style_id != 0 ) {
							$these_styles[ $category_slug ] = $chosen_style_id;
						}
					}
				}
			}
		}
		if ( ! isset( $GLOBALS['stylepress_render']['template'] ) ) {
			$GLOBALS['stylepress_render']['template'] = __DIR__ . '/views/render.php';
		}
		$GLOBALS['stylepress_render']['queried_object'] = $queried_object;
		$GLOBALS['stylepress_render']['page_type']      = $page_type;
		$GLOBALS['stylepress_render']['styles']         = $these_styles;
	}


	/**
	 * Register some frontend css files
	 *
	 * @since 2.0.0
	 */
	public function frontend_css() {
		wp_enqueue_style( 'stylepress-css', STYLEPRESS_URI . 'build/assets/frontend.css', false, STYLEPRESS_VERSION );

		wp_register_script( 'stylepress-js', STYLEPRESS_URI . 'build/assets/frontend.js', false, STYLEPRESS_VERSION, true );
		wp_localize_script( 'stylepress-js', 'stylepress_frontend', array(
				'ajaxurl'      => admin_url( 'admin-ajax.php' ),
				'public_nonce' => wp_create_nonce( 'stylepress-public-nonce' ),
			)
		);
		wp_enqueue_script( 'stylepress-js' );

		if ( \StylePress\Elementor\Integration::is_in_edit_mode() ) {
			// This loads extra scripts into the editor iframe only in edit mode. Used for the styling of the helper text at the top of the edit iframe.
			wp_enqueue_style( 'stylepress-editor-in', STYLEPRESS_URI . 'build/assets/frontend-edit.css', false, STYLEPRESS_VERSION );
			wp_enqueue_script( 'stylepress-editor-in', STYLEPRESS_URI . 'build/assets/frontend-edit.js', false, STYLEPRESS_VERSION, true );
		}
	}

	public function render_content( $post_id ) {
		if ( post_password_required( $post_id ) ) {
			return;
		}

		// TODO: remove reliance on Elementor like this
		if ( \StylePress\Elementor\Integration::is_post_built_with_elementor( $post_id ) ) {
			$with_css = false;
			echo \Elementor\Plugin::$instance->frontend->get_builder_content( $post_id, $with_css );
		} else {
			echo apply_filters( 'the_content', get_the_content( null, null, $post_id ) );
		}
	}
}

