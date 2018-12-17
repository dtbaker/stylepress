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
	public function init() {

		// two modes.
		// if we just want the extra widgets and not the full stylepress editor then we load that separately.


		add_action( 'admin_init', array( $this, 'admin_init' ), 20 );
		add_action( 'init', array( $this, 'theme_compatibility' ) );
		add_action( 'wp_ajax_stylepress_purchase_complete', array( $this, 'payment_complete' ) );
		add_action( 'wp_ajax_stylepress_get_css', array( $this, 'editor_get_css' ) );
		add_action( 'wp_ajax_stylepress_save_css', array( $this, 'editor_save_css' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'editor_scripts' ), 99999 );
		add_action( 'wp_print_footer_scripts', array( $this, 'wp_print_footer_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'theme_override_styles' ), 99999 );

		add_filter( 'template_include', array( $this, 'template_include' ), 999 );

		add_action( 'init', array( $this, 'register_new_nav_menu' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_css' ) );
		add_action( 'elementor/init', array( $this, 'elementor_init_complete' ), 40 );
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_add_new_widgets' ) );
		add_action( 'elementor/init', array( $this, 'add_elementor_overrides' ) );

		// stylepress plugin hooks
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

		global $post;

		// If stylepress has been disabled for this particular post then we just use the normal template include.
		// Not sure how to do this for category pages. We'll have to add a taxonomy settings area to each tax.
		$queried_object = get_queried_object();
		var_dump( $queried_object );

		$this->debug_message( 'template_include (start): ' . $template_include );


		$this->debug_message( 'template_include (end): ' . $template_include );

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

			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_meta_box' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_css' ) );
			add_filter( 'edit_form_after_title', array( $this, 'edit_form_after_title' ), 5 );
			add_filter( 'page_attributes_dropdown_pages_args', function ( $dropdown_args ) {
				if ( ! empty( $_GET['post_parent'] ) ) {
					$dropdown_args['selected'] = (int) $_GET['post_parent'];
				}

				return $dropdown_args;
			} );
			add_action( 'admin_action_stylepress_save', array( $this, 'stylepress_save' ) );
			add_action( 'admin_action_stylepress_create', array( $this, 'stylepress_create' ) );
			add_action( 'admin_action_stylepress_export', array( $this, 'stylepress_export' ) );
			add_action( 'admin_action_stylepress_download', array( $this, 'stylepress_download' ) );
			add_action( 'admin_action_stylepress_clone', array( $this, 'stylepress_clone' ) );

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
		// inject adds inline style against 'stylepress'

		if ( $this->has_permission() ) {
			wp_enqueue_style( 'stylepress-css-editor', STYLEPRESS_URI . 'assets/css/frontend-css-editor.css', false, STYLEPRESS_VERSION );

			wp_register_script( 'stylepress-css-editor', STYLEPRESS_URI . 'assets/js/frontend-css-editor.js', false, STYLEPRESS_VERSION, true );
			wp_localize_script( 'stylepress-css-editor', 'stylepress_css', array(
				'nonce'    => wp_create_nonce( 'stylepress_css' ),
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'post_id'  => get_queried_object_id(),
				'style_id' => (int) $this->get_current_style(),
			) );
			wp_enqueue_script( 'stylepress-css-editor' );

		}

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			wp_enqueue_style( 'stylepress-editor-in', STYLEPRESS_URI . 'assets/css/editor-in.css', false, STYLEPRESS_VERSION );
			wp_enqueue_script( 'stylepress-editor-in', STYLEPRESS_URI . 'assets/js/editor-in.js', false, STYLEPRESS_VERSION, true );

		}

	}

	/**
	 * Ajax handler for getting the current page CSS..
	 */
	public function editor_get_css() {
		if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'stylepress_css' ) ) {
			$style_id = ! empty( $_POST['style_id'] ) ? (int) $_POST['style_id'] : 0;
			if ( $style_id ) {
				$post_object = get_post( $style_id );
				// if this is a sub post. we get the parent one.
				if ( $post_object->post_parent ) {
					$post_object = get_post( $post_object->post_parent );
				}
				$advanced = $this->get_advanced( $post_object->ID, false );
				wp_send_json_success( array(
					'style_id' => $post_object->ID,
					'css'      => ! empty( $advanced['css'] ) ? $advanced['css'] : ''
				) );
			}
		}
		wp_send_json_error( '-1' );
	}

	/**
	 * Ajax handler for saving the current page CSS..
	 */
	public function editor_save_css() {

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
	 * Adds a meta box to every post type.
	 *
	 * @since 2.0.0
	 */
	public function add_meta_box() {

		if ( $this->has_permission() ) {
			$post_types = get_post_types();
			foreach ( $post_types as $post_type ) {

				if ( ! in_array( $post_type, array( 'stylepress_style', 'elementor_library' ), true ) ) {
					// todo: only for ones that are public queriable.
					add_meta_box(
						'stylepress_style_metabox',
						__( 'StylePress', 'stylepress' ),
						array( $this, 'meta_box_display' ),
						$post_type,
						'side',
						'high'
					);
				}
			}
			add_meta_box(
				'stylepress_export',
				__( 'Export', 'stylepress' ),
				array( $this, 'meta_box_export' ),
				'stylepress_style',
				'side',
				'low'
			);

			add_meta_box(
				'stylepress_sub_style',
				__( 'StylePress Settings', 'stylepress' ),
				array( $this, 'meta_box_sub_styles' ),
				'stylepress_style',
				'normal',
				'high'
			);
			add_meta_box(
				'stylepress_sub_style_advanced',
				__( 'StylePress Advanced', 'stylepress' ),
				array( $this, 'meta_box_sub_advanced' ),
				'stylepress_style',
				'normal',
				'low'
			);
		}

	}

	/**
	 * Adds a meta box to every post type.
	 *
	 * @since 2.0.0
	 *
	 * @var WP_Post $post The current displayed post.
	 */
	public function edit_form_after_title( $post ) {

		if ( $this->has_permission() && 'stylepress_style' === $post->post_type ) {

			$parent = $post->post_parent ? (int) $post->post_parent : ( ! empty( $_GET['post_parent'] ) ? (int) $_GET['post_parent'] : false );

			if ( $parent ) {
				?>
				<div id="stylepress-return-to-style">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=stylepress&style_id=' . $parent ) ); ?>"
					   class="button"><?php echo esc_html__( '&laquo; Return To Style Page', 'stylepress' ); ?></a>
				</div>
				<?php
			} else {

				?>
				<div id="stylepress-return-to-style">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=stylepress&style_id=' . $post->ID ) ); ?>"
					   class="button"><?php echo esc_html__( '&laquo; Return To Style Page', 'stylepress' ); ?></a>
				</div>
				<div id="stylepress-modify-font">
					<?php
					$url = add_query_arg(
						array(
							'autofocus[panel]' => 'tt_font_typography_panel',
							'url'              => urlencode( get_permalink( $post->ID ) ),
							'return'           => urlencode( get_edit_post_link( $post->ID ) ),
						),
						admin_url( 'customize.php' )
					);
					?>
					<a href="<?php echo esc_url( $url ); ?>"
					   class="button"><?php echo esc_html__( 'Customize Font & Color Defaults', 'stylepress' ); ?></a>
				</div>
				<?php
			}

		}

	}

	/**
	 * Returns a list of all availalbe page styles.
	 * This list is used in the style select drop down visible on most pages.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_all_page_styles() {
		$styles      = array();
		$args        = array(
			'post_type'           => 'stylepress_style',
			'post_status'         => 'publish',
			'posts_per_page'      => - 1,
			'ignore_sticky_posts' => 1,
			'suppress_filters'    => false,
			'order'               => 'ASC',
			'orderby'             => 'title',
		);
		$posts_array = get_posts( $args );
		$children    = array();
		foreach ( $posts_array as $style ) {
			if ( ! $style->post_parent ) {
				$styles[ $style->ID ] = $style->post_title;
			} else if ( ! get_post_meta( $style->ID, 'stylepress_is_component', true ) ) {
				if ( ! isset( $children[ $style->post_parent ] ) ) {
					$children[ $style->post_parent ] = array();
				}
				$children[ $style->post_parent ][ $style->ID ] = $style->post_title;
			}
		}
		// todo: sort alpha:

		$return = array();
		//we're only doing 1 level deep, not themes all the way down, so we don't need recursion here.

		foreach ( $styles as $style_id => $style_name ) {
			$return[ $style_id ] = $style_name;
			if ( isset( $children[ $style_id ] ) ) {
				foreach ( $children[ $style_id ] as $child_style_id => $child_name ) {
					$return[ $child_style_id ] = '&nbsp; &#8627; ' . $child_name;
				}
			}
		}


		return $return;
	}


	/**
	 * Returns a list of all availalbe page styles.
	 * This list is used in the style select drop down visible on most pages.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_all_page_components() {
		$styles      = array();
		$args        = array(
			'post_type'           => 'stylepress_style',
			'post_status'         => 'publish',
			'posts_per_page'      => - 1,
			'ignore_sticky_posts' => 1,
			'suppress_filters'    => false,
			'order'               => 'ASC',
			'orderby'             => 'title',
		);
		$posts_array = get_posts( $args );
		$children    = array();
		foreach ( $posts_array as $style ) {
			if ( ! $style->post_parent ) {
				$styles[ $style->ID ] = $style->post_title;
			} else if ( get_post_meta( $style->ID, 'stylepress_is_component', true ) ) {
				if ( ! isset( $children[ $style->post_parent ] ) ) {
					$children[ $style->post_parent ] = array();
				}
				$children[ $style->post_parent ][ $style->ID ] = $style->post_title;
			}
		}
		// todo: sort alpha:

		$return = array();
		//we're only doing 1 level deep, not themes all the way down, so we don't need recursion here.

		foreach ( $styles as $style_id => $style_name ) {
			//			$return[$style_id] = $style_name;
			if ( isset( $children[ $style_id ] ) ) {
				foreach ( $children[ $style_id ] as $child_style_id => $child_name ) {
					$return[ $child_style_id ] = $style_name . ' &#8611; ' . $child_name;
				}
			}
		}

		return $return;
	}


	/**
	 * Returns a list of all availalbe page styles.
	 * This list is used in the style select drop down visible on most pages.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_downloadable_styles() {

		$styles = get_transient( 'stylepress_downloadable' );
		if ( ! $styles || isset( $_GET['refresh-styles'] ) ) {
			$styles = array();

			// json query to stylepress.org to get a list of available styles.
			$url      = 'https://styleserver.stylepress.org/wp-admin/admin-ajax.php';
			$response = wp_remote_post(
				$url,
				array(
					'body' => array(
						'action'         => 'stylepress_get_available',
						'plugin_version' => STYLEPRESS_VERSION,
						'blog_url'       => get_site_url(),
					),
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$api_response = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( $api_response && ! empty( $api_response['data'] ) ) {
					$styles = $api_response['data'];
					set_transient( 'stylepress_downloadable', $styles, HOUR_IN_SECONDS );

				}
			}
		}
		// look for pay nonces for these
		$purchase = get_option( 'stylepress_purchases', array() );
		if ( ! $purchase ) {
			$purchase = array();
		}
		if ( isset( $_GET['reset-purchases'] ) ) {
			unset( $purchase[ $_GET['reset-purchases'] ] );
			update_option( 'stylepress_purchases', $purchase );
		}
		foreach ( $styles as $style_id => $style ) {
			$styles[ $style_id ]['pay_nonce'] = false;
			if ( ! empty( $purchase[ $style_id ] ) ) {
				// todo: check get_home_url against recorded pament. meh.
				foreach ( $purchase[ $style_id ] as $purchase ) {
					if ( ! empty( $purchase['server']['payment_id'] ) ) {
						$styles[ $style_id ]['pay_nonce'] = $purchase['server']['payment_id'];
					}
				}
			}
		}


		return $styles;
	}

	/**
	 * This renders our metabox on the style edit page
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function meta_box_sub_styles( $post ) {

		if ( $this->has_permission( $post ) && 'stylepress_style' === $post->post_type ) {

			if ( isset( $_GET['post_parent'] ) && empty( $post->post_parent ) ) {
				$post->post_parent = (int) $_GET['post_parent'];
			}

			$this->admin_page_assets();

			include_once STYLEPRESS_PATH . 'metaboxes/style-meta-box.php';
		}
	}

	/**
	 * This renders our metabox on the style edit page
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function meta_box_export( $post ) {

		if ( $this->has_permission( $post ) && 'stylepress_style' === $post->post_type ) {

			include_once STYLEPRESS_PATH . 'metaboxes/export.php';
		}
	}


	/**
	 * This renders our metabox on the style edit page
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function meta_box_sub_advanced( $post ) {

		if ( $this->has_permission( $post ) && 'stylepress_style' === $post->post_type ) {
			if ( isset( $_GET['post_parent'] ) && empty( $post->post_parent ) ) {
				$post->post_parent = (int) $_GET['post_parent'];
			}
			include_once STYLEPRESS_PATH . 'metaboxes/advanced-meta-box.php';
		}
	}

	/**
	 * This renders our metabox on most page/post types.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function meta_box_display( $post ) {

		if ( $this->has_permission( $post ) ) {

			include_once STYLEPRESS_PATH . 'metaboxes/post-meta-box.php';

		}
	}

	/**
	 * This lets us query what the currently selected page template is for a particular post ID
	 * We use the other function to get the defaults for non-page-ID posts (like archive etc..)
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id Current post ID we're querying.
	 *
	 * @return bool
	 */
	public function get_page_template( $post_id ) {
		$current_option = get_post_meta( $post_id, 'stylepress_style', true );
		if ( $current_option && ! empty( $current_option['style'] ) ) {
			return $current_option['style'];
		}

		return false;
	}

	/**
	 * This lets us query what the currently selected page template is for a particular post ID
	 * We use the other function to get the defaults for non-page-ID posts (like archive etc..)
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id Current post ID we're querying.
	 *
	 * @return bool
	 */
	public function get_page_inner_style( $post_id ) {
		$current_option = get_post_meta( $post_id, 'stylepress_style', true );
		if ( $current_option && ! empty( $current_option['inner_style'] ) ) {
			return $current_option['inner_style'];
		}

		return false;
	}


	/**
	 * Works out what template is currently selected for the current page/post/archive/search/404 etc.
	 * Copied from my Widget Area Manager plugin
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	public function get_settings() {
		return apply_filters( 'stylepress_settings', get_option( 'stylepress', array() ) );
	}

	/**
	 * Works out what template is currently selected for the current page/post/archive/search/404 etc.
	 * Copied from my Widget Area Manager plugin
	 *
	 * @since 2.0.0
	 *
	 * @param bool $ignore_override Ignore manually set post overrides.
	 *
	 * @return int
	 */
	public function get_current_style( $ignore_override = false ) {

		$style_settings = $this->get_settings();
		//		if(!empty($style_settings['defaults']['coming_soon']) && !is_user_logged_in()){
		//            return $style_settings['defaults']['coming_soon'];
		//        }

		global $post;
		if ( $post && ! empty( $post->ID ) && 'stylepress_style' === $post->post_type ) {
			// we're previewing a style.
			return $post->ID;
		}

		if ( ! $ignore_override ) {

			if ( is_home() || is_front_page() ) {
				if ( 'page' == get_option( 'show_on_front' ) ) {
					$home_page_id = false;
					if ( is_front_page() ) {
						$home_page_id = get_option( 'page_on_front' );
					} else {
						$home_page_id = get_option( 'page_for_posts' );
					}
					if ( $home_page_id ) {
						$style = (int) $this->get_page_template( $home_page_id );
						if ( STYLEPRESS_OUTER_USE_THEME === $style ) {
							return $style; // Use theme by default.
						} else if ( $style > 0 ) {
							return apply_filters( 'stylepress_current_style', $style );
						}
					}
				}
			}
			if ( is_single() || is_page() || is_attachment() ) {
				// see if we have a custom style applied
				if ( $post && $post->ID ) {
					$style = (int) $this->get_page_template( $post->ID );
					if ( STYLEPRESS_OUTER_USE_THEME === $style ) {
						return $style; // Use theme by default.
					} else if ( $style > 0 ) {
						return apply_filters( 'stylepress_current_style', $style );
					}
				}
			}
		}

		// check for defaults for this page type
		$page_type = $this->get_current_page_type();
		if ( $page_type && ! empty( $style_settings['defaults'][ $page_type ] ) ) {
			return apply_filters( 'stylepress_current_style', $style_settings['defaults'][ $page_type ] );
		}
		// otherwise check for site wide default:
		if ( ! empty( $style_settings['defaults']['_global'] ) ) {
			return apply_filters( 'stylepress_current_style', $style_settings['defaults']['_global'] );
		}

		// otherwise return nothing, so we fallback to default standard theme
		return false;

	}


	/**
	 * Works out what template is currently selected for the current page/post/archive/search/404 etc.
	 * Copied from my Widget Area Manager plugin
	 *
	 * @since 2.0.0
	 *
	 * @param bool $ignore_override Ignore manually set post overrides.
	 *
	 * @return int
	 */
	public function get_current_inner_style( $ignore_override = false ) {

		$style_settings = $this->get_settings();

		global $post;
		if ( $post && ! empty( $post->ID ) && 'stylepress_style' === $post->post_type ) {
			// we're previewing a style.
			return false;
		}

		if ( ! $ignore_override ) {

			if ( is_home() || is_front_page() ) {
				if ( 'page' == get_option( 'show_on_front' ) ) {
					$home_page_id = false;
					if ( is_front_page() ) {
						$home_page_id = get_option( 'page_on_front' );
					} else {
						$home_page_id = get_option( 'page_for_posts' );
					}
					if ( $home_page_id ) {
						$style = (int) $this->get_page_inner_style( $home_page_id );
						if ( STYLEPRESS_INNER_USE_PLAIN === $style || STYLEPRESS_INNER_USE_THEME === $style ) {
							return $style;
						} else if ( $style > 0 ) {
							return apply_filters( 'stylepress_current_inner_style', $style );
						}
					}
				}
			}
			if ( is_single() || is_page() || is_attachment() ) {
				// see if we have a custom style applied
				if ( $post && $post->ID ) {
					$style = (int) $this->get_page_inner_style( $post->ID );
					if ( STYLEPRESS_INNER_USE_PLAIN === $style || STYLEPRESS_INNER_USE_THEME === $style ) {
						return $style; // Use theme by default.
					} else if ( $style > 0 ) {
						return apply_filters( 'stylepress_current_inner_style', $style );
					}
				}
			}
		}

		// check for defaults for this page type
		$page_type = $this->get_current_page_type();

		if ( $page_type ) {
			$settings_key_to_check = $page_type . '_inner';
			if ( is_home() || is_front_page() ) {
				// home page or blog output page.
				if ( 'page' == get_option( 'show_on_front' ) && is_front_page() && get_option( 'page_on_front' ) ) {
					//
				} else if ( $settings_key_to_check != 'archive_inner' ) {
					$settings_key_to_check = 'archive_inner';
					\Plugin::get_instance()->debug_message( "get_current_inner_style(): We're showing blog post output on home page, using inner style $settings_key_to_check instead" );
				}
			}

			if ( $page_type && ! empty( $style_settings['defaults'][ $settings_key_to_check ] ) ) {
				return apply_filters( 'stylepress_current_inner_style', $style_settings['defaults'][ $settings_key_to_check ] );
			}
		}
		// otherwise check for site wide default:
		if ( ! empty( $style_settings['defaults']['_global_inner'] ) ) {
			return apply_filters( 'stylepress_current_inner_style', $style_settings['defaults']['_global_inner'] );
		}

		// otherwise return nothing, so we fallback to default standard theme
		return false;

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
	 * Returns a list of all our configurable componente areas.
	 *
	 * @since 2.0.0
	 *
	 */
	public function get_component_regions() {
		$defaults   = array(
			'post_summary'  => 'Post Summary',
			'post_single'   => 'Post Single',
			'page_single'   => 'Page Single',
			'search_result' => 'Search Result',
			//	        'shop_catalog' => 'Shop Catalog',
			//	        'shop_single' => 'Shop Single',
		);
		$post_types = get_post_types( array( 'public' => true ) );
		foreach ( $post_types as $post_type ) {
			if ( ! in_array( $post_type, array( 'stylepress_style', 'elementor_library', 'attachment' ), true ) ) {
				if ( ! isset( $defaults[ $post_type . '_single' ] ) ) {
					$defaults[ $post_type . '_single' ] = ucwords( str_replace( "_", " ", $post_type ) ) . ' Single';
				}
			}
		}

		return $defaults;
	}

	/**
	 * Saves our metabox details, which is the style for a particular page.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id The post we're current saving.
	 */
	public function save_meta_box( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['stylepress_style_nonce'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['stylepress_style_nonce'], 'stylepress_style_nonce' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['stylepress_style'] ) && is_array( $_POST['stylepress_style'] ) ) { // WPCS: sanitization ok. input var okay.
			update_post_meta( $post_id, 'stylepress_style', $_POST['stylepress_style'] ); // WPCS: sanitization ok. input var okay.
		}


		if ( isset( $_POST['stylepress_advanced'] ) && is_array( $_POST['stylepress_advanced'] ) ) { // WPCS: sanitization ok. input var okay.
			update_post_meta( $post_id, 'stylepress_advanced', $_POST['stylepress_advanced'] ); // WPCS: sanitization ok. input var okay.
		}

		if ( isset( $_POST['stylepress_is_component_check'] ) ) {
			update_post_meta( $post_id, 'stylepress_is_component', empty( $_POST['stylepress_is_component'] ) ? 0 : 1 ); // WPCS: sanitization ok. input var okay.
		}

	}

	/**
	 * Handles saving the settings page.
	 *
	 * @since 2.0.0
	 *
	 */
	public function stylepress_save() {
		// Check if our nonce is set.
		if ( ! isset( $_POST['stylepress_save_options'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['stylepress_save_options'], 'stylepress_save_options' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}


		if ( isset( $_POST['stylepress_styles'] ) && is_array( $_POST['stylepress_styles'] ) ) { // WPCS: sanitization ok. input var okay.
			$settings             = $this->get_settings();
			$settings['defaults'] = $_POST['stylepress_styles'];
			update_option( 'stylepress', $settings );
		}

		if ( isset( $_POST['stylepress_settings'] ) && is_array( $_POST['stylepress_settings'] ) ) { // WPCS: sanitization ok. input var okay.
			$settings = $this->get_settings();
			$allowed  = array( 'remove_css' );
			foreach ( $allowed as $key ) {
				if ( ! empty( $_POST['stylepress_settings'][ $key ] ) ) {
					$settings[ $key ] = $_POST['stylepress_settings'][ $key ];
				}
			}
			update_option( 'stylepress', $settings );
		}

		wp_redirect( admin_url( 'admin.php?page=stylepress-settings&saved' ) );
		exit;


	}

	/**
	 * Handles creating a new style
	 *
	 * @since 2.0.0
	 *
	 */
	public function stylepress_create() {
		// Check if our nonce is set.
		if ( ! isset( $_POST['stylepress_create_options'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['stylepress_create_options'], 'stylepress_create_options' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}


		if ( isset( $_POST['new_style_name'] ) && trim( $_POST['new_style_name'] ) ) { // WPCS: sanitization ok. input var okay.

			$new_style_id = wp_insert_post( array(
				'post_type'    => 'stylepress_style',
				'post_name'    => trim( $_POST['new_style_name'] ),
				'post_title'   => trim( $_POST['new_style_name'] ),
				'post_content' => '', // todo: default style layout here maybe?
				'post_status'  => 'publish',
			) );
			if ( $new_style_id ) {
				wp_redirect( admin_url( 'admin.php?page=stylepress&style_id=' . $new_style_id . '&saved' ) );
				exit;
			}
		}


		wp_redirect( admin_url( 'admin.php?page=stylepress&style_id=new' ) );
		exit;


	}


	/**
	 * We register two new nav menu items that can be used within the new Elementor Menu area.
	 *
	 * @since 2.0.0
	 */
	public function register_new_nav_menu() {
		register_nav_menus( array(
			'elementor_menu1' => 'Elementor Menu 1',
			'elementor_menu2' => 'Elementor Menu 2',
		) );
	}


	/**
	 * We get a little tricky here and read in our custom Elementor element overrides from a json configuration file.
	 *
	 * Why? Because it's easier to define these overrides in json than in PHP.
	 *
	 * @since 2.0.0
	 */
	public function add_elementor_overrides() {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		WP_Filesystem();
		global $wp_filesystem;
		$json = json_decode( $wp_filesystem->get_contents( trailingslashit( plugin_dir_path( __DIR__ ) ) . 'elementor.json' ), true );
		$json = apply_filters( 'stylepress_elementor_json', $json );
		$this->_apply_json_overrides( $json );
		$current_style = (int) $this->get_current_style();
		if ( $current_style > 0 ) {
			// check if this one has a json elementor override
			$json = $this->get_style_elementor_overrides( $current_style );
			$json = apply_filters( 'stylepress_style_json', $json, $current_style );
			$this->_apply_json_overrides( $json );
		}

		require_once STYLEPRESS_PATH . 'extensions/skins/skins.php';


	}

	/**
	 * Do the actual overriding work.
	 * Private here so we can call it for individual style elementor overrides.
	 *
	 * @since 2.0.0
	 */
	private function _apply_json_overrides( $json ) {

		if ( ! is_array( $json ) || ! $json ) {
			return;
		}


		if ( $json && ! empty( $json['attributes'] ) ) {

			add_action( 'elementor/element/before_section_end', function ( $section, $section_id, $args ) use ( $json ) {

				foreach ( $json['attributes'] as $attributes ) {

					if ( ! empty( $attributes['appendto'] ) && $attributes && $section && $section_id === $attributes['appendto'] && method_exists( $section, 'add_control' ) && method_exists( $section, 'start_controls_section' ) && method_exists( $section, 'end_controls_section' ) ) {

						if ( ( ! empty( $attributes['element'] ) && $attributes['element'] === $section->get_name() ) || empty( $attributes['element'] ) ) {

							$section->add_control(
								$attributes['name'],
								[
									'label'        => $attributes['title'],
									'type'         => Elementor\Controls_Manager::SELECT,
									'default'      => $attributes['default'],
									'options'      => $attributes['options'],
									'prefix_class' => $attributes['prefix_class'],
									'label_block'  => true,
								]
							);
						}
					}
				}

			}, 10, 3 );
			add_action( 'elementor/element/after_section_end', function ( $section, $section_id, $args ) use ( $json ) {

				foreach ( $json['attributes'] as $attributes ) {

					if ( ! empty( $attributes['after'] ) && $attributes && $section && $section_id === $attributes['after'] && method_exists( $section, 'add_control' ) && method_exists( $section, 'start_controls_section' ) && method_exists( $section, 'end_controls_section' ) ) {

						if ( ( ! empty( $attributes['element'] ) && $attributes['element'] === $section->get_name() ) || empty( $attributes['element'] ) ) {

							$section->start_controls_section(
								'section_' . $attributes['name'],
								[
									'label' => $attributes['section_title'],
									'tab'   => 'style' === $attributes['tab'] ? Elementor\Controls_Manager::TAB_STYLE : Elementor\Controls_Manager::TAB_CONTENT,
								]
							);

							$section->add_control(
								$attributes['name'],
								[
									'label'        => $attributes['title'],
									'type'         => Elementor\Controls_Manager::SELECT,
									'default'      => $attributes['default'],
									'options'      => $attributes['options'],
									'prefix_class' => $attributes['prefix_class'],
									'label_block'  => true,
								]
							);

							$section->end_controls_section();

						}
					}
				}

			}, 10, 3 );
		}// End if().

	}

	/**
	 * Load some CSS overrides for active theme.
	 * Thanks to WPDevHQ for the list.
	 *
	 * @since 2.0.0
	 */
	public function theme_override_styles() {

		// do we remove theme styles for this current page type?
		// get all styles data
		$settings          = $this->get_settings();
		$current_page_type = $this->get_current_page_type();
		/*global $post;
		if($post->ID && $post->ID) {
			$current_outer_style = $this->get_page_template( $post->ID );
		}else{
			$current_outer_style = !empty($settings['defaults'][$current_page_type]) ? $settings['defaults'][$current_page_type] : false;
        }*/
		$current_outer_style = $this->get_current_style();
		$current_inner_style = $this->get_current_inner_style();

		if ( $current_outer_style != STYLEPRESS_OUTER_USE_THEME && $current_inner_style != STYLEPRESS_INNER_USE_THEME && ! empty( $settings['remove_css'][ $current_page_type ] ) ) {
			$this->removing_theme_css = true;
			global $wp_styles;
			$current_theme                                    = wp_get_theme();
			$remove_slugs                                     = array();
			$remove_slugs[ $current_theme->get_stylesheet() ] = true;
			$remove_slugs[ $current_theme->get_template() ]   = true;

			// don't remove these ones:
			$style_whitelist = apply_filters( 'stylepress-css-whitelist', array(
				'font-awesome',
			) );

			// loop over all of the registered scripts
			foreach ( $wp_styles->registered as $handle => $data ) {
				// remove it
				if ( $data && ! empty( $data->src ) && ! in_array( $handle, $style_whitelist ) ) {
					foreach ( $remove_slugs as $remove_slug => $tf ) {
						// todo: check for custom themes/ folder name here:
						if ( strpos( $data->src, 'themes/' . $remove_slug . '/' ) !== false ) {
							wp_deregister_style( $handle );
							wp_dequeue_style( $handle );
						}
					}
				}
			}

			wp_enqueue_style( 'stylepress-theme-overwrites', STYLEPRESS_URI . 'assets/css/theme-overwrites.css', false, STYLEPRESS_VERSION );
		}

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

	public function stylepress_download() {

		if ( ! isset( $_GET['stylepress_download'] ) || empty( $_GET['slug'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_GET['stylepress_download'], 'stylepress_download' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}

		$slug = $_GET['slug'];

		// see if this slug exists in the available styles to download.
		$designs = $this->get_downloadable_styles();
		if ( ! isset( $designs[ $slug ] ) ) {
			wp_die( __( 'Sorry this style was not found to install.' ), __( 'Style Install Failed.' ), 403 );
		}

		// hit up our server for a copy of this style.
		$url      = 'https://styleserver.stylepress.org/wp-admin/admin-ajax.php';
		$response = wp_remote_post(
			$url,
			array(
				'body' => array(
					'action'         => 'stylepress_download',
					'slug'           => $slug,
					'pay_nonce'      => $designs[ $slug ]['pay_nonce'],
					'plugin_version' => STYLEPRESS_VERSION,
					'blog_url'       => get_site_url(),
				),
			)
		);

		if ( ! is_wp_error( $response ) ) {
			$api_response = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( $api_response && ! empty( $api_response['success'] ) && ! empty( $api_response['data'] ) ) {
				$style_to_import = $api_response['data'];
				require_once STYLEPRESS_PATH . 'inc/class.import-export.php';
				$import_export = StylepressImportExport::get_instance();
				$result        = $import_export->import_data( $style_to_import );
				wp_redirect( admin_url( 'admin.php?page=stylepress-settings&imported' ) );
			} else if ( isset( $api_response['success'] ) && ! $api_response['success'] ) {
				wp_die( sprintf( __( 'Failed to install style: %s ' ), $api_response['data'] ), __( 'Style Install Failed.' ), 403 );
			}
		} else {
			wp_die( __( 'Failed to contact style server. Please try again.' ), __( 'Style Install Failed.' ), 403 );
		}

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
		if ( $post && 'stylepress_style' === $post->post_type ) {

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

	public function payment_complete() {

		if ( ! empty( $_POST['payment']['payment_nonce'] ) && wp_verify_nonce( $_POST['payment']['payment_nonce'], 'payment_nonce' ) ) {
			if ( ! empty( $_POST['server']['slug'] ) ) {
				// we've purchased this slug. store it in options array.
				$purchase = get_option( 'stylepress_purchases', array() );
				if ( ! $purchase ) {
					$purchase = array();
				}

				if ( ! isset( $purchase[ $_POST['server']['slug'] ] ) ) {
					$purchase[ $_POST['server']['slug'] ] = array();
				}
				$purchase[ $_POST['server']['slug'] ][] = array(
					'time'   => time(),
					'server' => $_POST['server'],
				);
				update_option( 'stylepress_purchases', $purchase );
				wp_send_json_success( 'Success' );
			}
		}
		wp_send_json_error( 'Failed to record payment' );

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

