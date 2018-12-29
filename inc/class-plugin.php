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
 * Class Plugin
 */
class Plugin extends Base {


	/**
	 * Initializes the plugin and sets all required filters.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		add_filter( 'template_include', array( $this, 'template_include' ), 999 );

		add_action( 'admin_init', array( $this, 'admin_init' ), 20 );
		add_action( 'init', array( $this, 'theme_compatibility' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'editor_scripts' ), 99999 );
		add_action( 'wp_print_footer_scripts', array( $this, 'wp_print_footer_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_css' ) );
		add_action( 'elementor/init', array( $this, 'elementor_init_complete' ), 40 );
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_add_new_widgets' ) );
		add_action( 'init', array( $this, 'load_extensions' ) );
		add_action( 'widgets_init', [ $this, 'load_widgets' ] );
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
					if ( $elementor && isset( $elementor->elements_manager ) ) {
						if ( method_exists( $elementor->elements_manager, 'add_category' ) ) {
							$elementor->elements_manager->add_category(
								'stylepress',
								[
									'title' => 'StylePress',
									'icon'  => 'font'
								],
								1
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

							require_once STYLEPRESS_PATH . 'extensions/inner-content/inner-content.php';

							return;
							require_once STYLEPRESS_PATH . 'extensions/dynamic-field/dynamic-field.php';
							require_once STYLEPRESS_PATH . 'extensions/email-subscribe/email-subscribe.php';
							require_once STYLEPRESS_PATH . 'extensions/modal-popup/modal-popup.php';
							require_once STYLEPRESS_PATH . 'extensions/wp-menu/wp-menu.php';
							require_once STYLEPRESS_PATH . 'extensions/form/form-fields.php';
							require_once STYLEPRESS_PATH . 'extensions/tooltip/tooltip.php';
							require_once STYLEPRESS_PATH . 'extensions/google-maps/google-maps.php';
							require_once STYLEPRESS_PATH . 'extensions/page-slider/stylepress-page-slider.php';
							require_once STYLEPRESS_PATH . 'extensions/woocommerce/woocommerce.php';
							// only works with pro:
							if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
								require_once STYLEPRESS_PATH . 'extensions/stylepress-loop/stylepress-loop.php';
							} else {
								require_once STYLEPRESS_PATH . 'extensions/shortcode/shortcode.php';
							}

							do_action( 'stylepress_init_extensions' );
						}
					}
				}
			}
		}

	}

	public function load_widgets() {
		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			if ( class_exists( '\Elementor\Plugin' ) ) {
				if ( is_callable( '\Elementor\Plugin', 'instance' ) ) {
					require_once STYLEPRESS_PATH . 'extensions/widget/widget.php';
					register_widget( "stylepress_template_widget" );
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

							// todo: option these out in 'Add-Ons' section
							require_once STYLEPRESS_PATH . 'widgets/inner-content.php';

						}
					}
				}
			}
		}
	}


	public function has_permission( $post = false ) {
		return current_user_can( 'edit_posts' );
		//current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' )
	}

	/**
	 * This loads a custom "panel" template to the frontend Elementor editor page.
	 * Only when the user is logged in and only when the Elementor editor has loaded.
	 *
	 * @since 2.0.0
	 */
	public function wp_print_footer_scripts() {
		if ( ! is_admin() && $this->has_permission() ) {
			if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
				if ( class_exists( '\Elementor\Plugin' ) ) {
					if ( is_callable( '\Elementor\Plugin', 'instance' ) ) {
						$elementor = \Elementor\Plugin::instance();
						if ( isset( $elementor->editor ) && $elementor->editor->is_edit_mode() ) {
							include_once STYLEPRESS_PATH . 'templates/page-panel.php';
						}
					}
				}
			}
		}
	}

	public function populate_globals() {

		if ( isset( $GLOBALS['stylepress_render'] ) ) {
			return;
		}
		global $post;
		$GLOBALS['stylepress_render'] = [];

		if ( $post && ! empty( $post->ID ) && 'elementor_library' === $post->post_type ) {
			$page_templates_module = \Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' );
			$path                  = $page_templates_module->get_template_path( 'elementor_canvas' );
			if ( is_file( $path ) ) {
				$GLOBALS['stylepress_render']['template'] = $path;
			}
		} else if ( $post && ! empty( $post->ID ) && Styles::CPT === $post->post_type ) {
			$GLOBALS['stylepress_render']['template'] = STYLEPRESS_PATH . 'templates/editor.php';
		}

		$default_styles = Styles::get_instance()->get_default_styles();
		$page_type      = $this->get_current_page_type();
		$these_styles   = isset( $default_styles[ $page_type ] ) ? $default_styles[ $page_type ] : false;

		$queried_object = get_queried_object();
		if ( $these_styles ) {
			// If stylepress has been disabled for this particular post then we just use the normal template include.
			// Not sure how to do this for category pages. We'll have to add a taxonomy settings area to each tax.
			if ( $queried_object && $queried_object instanceof \WP_Post && $queried_object->ID ) {
				$enabled = Styles::get_instance()->is_stylpress_enabled( $post );
				if ( ! $enabled['enabled'] ) {
					$this->debug_message( 'Skipping stylepress template because ' . $enabled['reason'] );
					$GLOBALS['stylepress_render']['template'] = false;
				}
				// todo: confirm our queried object isn't the first blog post in a list of things view.. that would mess it up.
				// We're doing a single object post, should be easy.
				$page_styles = Styles::get_instance()->get_page_styles( $queried_object->ID );
				if ( $page_styles ) {
					foreach ( $page_styles as $category_slug => $chosen_style_id ) {
						if ( $chosen_style_id != 0 ) {
							if ( isset( $these_styles[ $category_slug ] ) ) {
								$these_styles[ $category_slug ] = $chosen_style_id;
							}
						}
					}
				}
			}
		}
		if ( ! isset( $GLOBALS['stylepress_render']['template'] ) ) {
			$GLOBALS['stylepress_render']['template'] = STYLEPRESS_PATH . 'templates/render.php';
		}
		$GLOBALS['stylepress_render']['queried_object'] = $queried_object;
		$GLOBALS['stylepress_render']['page_type']      = $page_type;
		$GLOBALS['stylepress_render']['styles']         = $these_styles;
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

		$this->populate_globals();

		if ( ! empty( $GLOBALS['stylepress_render']['template'] ) ) {
			return $GLOBALS['stylepress_render']['template'];
		}

		$this->debug_message( 'Sorry no styles found for this page type' );

		return $template_include;
	}


	/**
	 * Admin hooks.
	 *
	 * We add some meta boxes, some admin css, and do a hack on 'parent_file' so the admin ui menu highlights correctly.
	 *
	 * @since 2.0.0
	 */
	public function admin_init() {


		if ( ! defined( 'ELEMENTOR_PATH' ) || ! class_exists( '\Elementor\Widget_Base' ) ) {
			// we need to put it here in admin_init because Elementor might not have loaded in our plugin init area.

			add_action( 'admin_notices', function () {
				$message      = esc_html__( 'Please install and activate the latest version of Elementor before attempting to use the StylePress plugin.', 'stylepress' );
				$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
				echo wp_kses_post( $html_message );
			} );


		} else {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_css' ) );

		}

	}


	/**
	 * Register some frontend css files
	 *
	 * @since 2.0.0
	 */
	public function frontend_css() {
		wp_enqueue_style( 'stylepress-css', STYLEPRESS_URI . 'assets/css/frontend.css', false, STYLEPRESS_VERSION );
		wp_enqueue_script( 'stylepress-js', STYLEPRESS_URI . 'assets/js/frontend.js', false, STYLEPRESS_VERSION, true );

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_enqueue_style( 'stylepress-editor-in', STYLEPRESS_URI . 'assets/css/editor-in.css', false, STYLEPRESS_VERSION );
			wp_enqueue_script( 'stylepress-editor-in', STYLEPRESS_URI . 'assets/js/editor-in.js', false, STYLEPRESS_VERSION, true );

		}

	}


	/**
	 * Register some backend admin css files.
	 *
	 * @since 2.0.0
	 */
	public function admin_css() {
		wp_enqueue_style( 'stylepress-admin', STYLEPRESS_URI . 'assets/css/admin.css', false, STYLEPRESS_VERSION );
	}

	/**
	 * This is our Elementor injection script. We load some custom JS to modify the Elementor control panel during live editing.
	 *
	 * @since 2.0.0
	 */
	public function editor_scripts() {
		wp_enqueue_script( 'stylepress-editor', STYLEPRESS_URI . 'assets/js/editor.js', false, STYLEPRESS_VERSION, true );
		wp_enqueue_style( 'stylepress-elementor-editor', STYLEPRESS_URI . 'assets/css/editor.css', false, STYLEPRESS_VERSION );
	}

	/**
	 * Works out the type of page we're currently quer\ying.
	 * Copied from my Widget Area Manager plugin
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_current_page_type() {
		global $wp_query;
		//print_r($wp_query);
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
		} else if ( isset( $wp_query->query_vars ) && isset( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] ) {
			return $wp_query->query_vars['post_type'] . ( is_singular() ? '' : 's' );
		} else if ( isset( $wp_query->query_vars['taxonomy'] ) && $wp_query->query_vars['taxonomy'] ) {
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

	/**
	 * Loads the compatibility with various popular themes.
	 *
	 * @since 2.0.0
	 */
	public function theme_compatibility() {

		$theme = get_option( 'template' );
		if ( $theme_name = strtolower( basename( $theme ) ) ) {
			$filename = STYLEPRESS_PATH . 'themes/' . $theme_name . '/' . $theme_name . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;
			}
		}
	}


	public function stylepress_export() {

		if ( ! isset( $_GET['stylepress_export_data'] ) || empty( $_GET['post_id'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_GET['stylepress_export_data'], 'stylepress_export_data' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}

		$post_id = (int) $_GET['post_id'];

		if ( ! $this->has_permission( $post_id ) ) {
			return;
		}

		require_once STYLEPRESS_PATH . 'inc/class.import-export.php';
		$import_export = StylepressImportExport::get_instance();
		$data          = $import_export->export_data( $post_id );

		echo '<pre>';
		print_r( $data );
		echo '</pre>';
		exit;

		wp_send_json( $data );

		exit;
	}

	public function stylepress_clone() {

		if ( ! isset( $_GET['stylepress_clone'] ) || empty( $_GET['post_id'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_GET['stylepress_clone'], 'stylepress_clone' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}

		$post_id = (int) $_GET['post_id'];

		$post = get_post( $post_id );

		/*
		 * if post data exists, create the post duplicate
		 */
		if ( $post && Styles::CPT === $post->post_type ) {

			if ( ! $post->post_parent ) {
				$post->post_parent = $post_id; // we're cloaning the parent one, put it underneath itself.
			}
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $post->post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => $post->post_status,
				'post_title'     => '(clone) ' . $post->post_title,
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order
			);

			/*
			 * insert the post by wp_insert_post() function
			 */
			$new_post_id = wp_insert_post( $args );

			if ( $new_post_id ) {
				global $wpdb;
				/*
				 * duplicate all post meta just in two SQL queries
				 */
				$post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id" );
				if ( count( $post_meta_infos ) != 0 ) {
					$sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
					$sql_query_sel = array();
					foreach ( $post_meta_infos as $meta_info ) {
						$meta_key        = $meta_info->meta_key;
						$meta_value      = esc_sql( $meta_info->meta_value );
						$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
					}

					$sql_query .= implode( " UNION ALL ", $sql_query_sel );
					$wpdb->query( $sql_query );
				}

				wp_redirect( get_edit_post_link( $new_post_id, 'edit' ) );
				exit;

			}


		}

		return false;

	}


	public function debug_message( $message ) {

		if ( STYLEPRESS_DEBUG_OUTPUT && is_user_logged_in() ) {
			echo '<div class="stylepress-debug">';
			echo '<span>StylePress:</span> &nbsp; ';
			echo $message;
			echo "</div>";
		}
	}

}

