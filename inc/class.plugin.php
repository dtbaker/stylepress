<?php
/**
 * Our DtbakerElementorManager class.
 * This handles all our hooks and stuff.
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

/**
 * All the magic happens here.
 *
 * Class DtbakerElementorManager
 */
class DtbakerElementorManager {

	/**
	 * Stores our instance that can (and is) accessed from various places.
	 *
	 * @var DtbakerElementorManager null
	 *
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Grab a static instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return DtbakerElementorManager
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Flag to let us know if the user is currently previewing a site wide style.
	 * Do not render inside content when previewing site wide style.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $previewing_style = false;


	/**
	 * Flag to let us know if we render entire page or overwrite get_header() and get_footer().
	 *
	 * @since 1.0.5
	 *
	 * @var bool
	 */
	public $overwrite_theme_output = false;

	/**
	 * Initializes the plugin and sets all required filters.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'admin_init' ), 20 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'init', array( $this, 'register_custom_post_type' ) );
		add_action( 'init', array( $this, 'register_new_nav_menu' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_css' ) );
		add_action( 'elementor/init', array( $this, 'elementor_init_complete' ) );
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_add_new_widgets' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'editor_scripts' ) );
		add_action( 'wp_print_footer_scripts', array( $this, 'wp_print_footer_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'theme_override_styles' ), 999 );
		add_filter( 'tt_font_get_settings_page_tabs', array( $this, 'tt_font_get_settings_page_tabs' ), 101 );
		add_filter( 'tt_font_get_option_parameters', array( $this, 'tt_font_get_option_parameters' ), 10 );
		add_action( 'elementor/frontend/element/before_render', array( $this, 'section_before_render' ), 10 );
		add_action( 'init', array( $this, 'add_json_overrides' ) );
		add_action( 'init', array( $this, 'elementor_ref' ) );

		add_filter( 'template_include', array( $this, 'template_include' ) );
		add_action( 'get_header', array( $this, 'get_header' ), 999 );
		add_action( 'get_footer', array( $this, 'get_footer' ), 999 );
		add_filter( 'stylepress_rendered_header', array( $this, 'theme_header_filter' ), 999 );
		add_filter( 'stylepress_rendered_footer', array( $this, 'theme_header_filter' ), 999 );
		add_filter( 'elementor/frontend/the_content', array( $this, 'elementor_footer_hack' ), 999 );
	}

	/**
	 * Runs once elementor has completed loading.
	 * This method loads our custom Elementor classes and injects them into the elementor widget_manager
	 * so our widgets appear in the Elementor ui.
	 *
	 * @since 1.0.0
	 */
	public function elementor_init_complete() {

		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			if ( class_exists( 'Elementor\Plugin' ) ) {
				if ( is_callable( 'Elementor\Plugin', 'instance' ) ) {
					$elementor = Elementor\Plugin::instance();
					if ( $elementor && isset( $elementor->elements_manager ) ) {
						if ( method_exists( $elementor->elements_manager, 'add_category' ) ) {
							$elementor->elements_manager->add_category(
								'dtbaker-elementor',
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


	public function elementor_add_new_widgets() {
		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			if ( class_exists( 'Elementor\Plugin' ) ) {
				if ( is_callable( 'Elementor\Plugin', 'instance' ) ) {
					$elementor = Elementor\Plugin::instance();
					if ( isset( $elementor->widgets_manager ) ) {
						if ( method_exists( $elementor->widgets_manager, 'register_widget_type' ) ) {

							// inner content widget.
							$widget_file   = 'plugins/elementor/inner-content.php';
							$template_file = locate_template( $widget_file );
							if ( ! $template_file || ! is_readable( $template_file ) ) {
								$template_file = DTBAKER_ELEMENTOR_PATH . 'widgets/inner-content.php';
							}
							if ( $template_file && is_readable( $template_file ) ) {
								require_once $template_file;
								Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Elementor\Widget_Dtbaker_Inner_Content() );
							}

							// menu plugin widget.
							$widget_file   = 'plugins/elementor/wp-menu.php';
							$template_file = locate_template( $widget_file );
							if ( ! $template_file || ! is_readable( $template_file ) ) {
								$template_file = DTBAKER_ELEMENTOR_PATH . 'widgets/wp-menu.php';
							}
							if ( $template_file && is_readable( $template_file ) ) {
								require_once $template_file;
								Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Elementor\Widget_Dtbaker_WP_Menu() );
							}

							// dynamic field widget.
							$widget_file   = 'plugins/elementor/dynamic-field.php';
							$template_file = locate_template( $widget_file );
							if ( ! $template_file || ! is_readable( $template_file ) ) {
								$template_file = DTBAKER_ELEMENTOR_PATH . 'widgets/dynamic-field.php';
							}
							if ( $template_file && is_readable( $template_file ) ) {
								require_once $template_file;
								Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Elementor\Widget_Dtbaker_Dynamic_Field() );
							}
						}
					}
				}
			}
		}
    }

    public function section_before_render($section){

	    if( 'section' === $section->get_name() ) {
		    $children  = $section->get_children();
		    $has_inner = false;
		    $column_count = 0;
		    foreach ( $children as $child ) {
		        if( 'column' === $child->get_name()){
			        $column_count++;
			        $sub_children = $child->get_children();
			        foreach ( $sub_children as $sub_child ) {
				        if ( 'dtbaker_inner_content' === $sub_child->get_name() ) {
					        $has_inner = true;
				        }
			        }
                }
		    }
		    if ( $has_inner ) {
			    $section->add_render_attribute( 'wrapper', 'class', [
					    'section-dtbaker-has-inner',
					    'section-dtbaker-column-count-' . $column_count,
				    ]
			    );
		    }
	    }
    }

    public function has_permission( $post = false ){
        return current_user_can( 'edit_posts' );
        //current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' )
    }

	/**
	 * This loads a custom "panel" template to the frontend Elementor editor page.
	 * Only when the user is logged in and only when the Elementor editor has loaded.
	 *
	 * @since 1.0.0
	 */
	public function wp_print_footer_scripts() {
		if ( ! is_admin() && $this->has_permission( ) ) {
			if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
				if ( class_exists( 'Elementor\Plugin' ) ) {
					if ( is_callable( 'Elementor\Plugin', 'instance' ) ) {
						$elementor = Elementor\Plugin::instance();
						if ( isset( $elementor->editor ) && $elementor->editor->is_edit_mode() ) {
							include_once DTBAKER_ELEMENTOR_PATH . 'templates/page-panel.php';
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
	 * There are two "modes". We are in the editor and editing the template (loads full-page.php)
	 * Or we are on the frontend and we are rending normal page content (render.php)
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_include The path to the current template file.
	 *
	 * @return string
	 */
	public function template_include( $template_include ) {
		global $post;


		if ( $post && ! empty( $post->ID ) && 'dtbaker_style' === $post->post_type  ) {
            $this->previewing_style = true;
            $template_include       = DTBAKER_ELEMENTOR_PATH . 'templates/full-page.php';
            add_filter( 'body_class', function ( $classes ) use ( $post )  {
                $classes[] = 'dtbaker-elementor-template';
                $classes[] = 'dtbaker-elementor-template-preview';
                if( $post->post_parent && get_post_meta( $post->ID, 'dtbaker_is_component', true ) ){
	                $classes[] = 'dtbaker-elementor-template-component';
                }

                return $classes;
            } );
        } else {
            // check if this particular page has a template set.
            $GLOBALS['our_elementor_template'] = (int) $this->get_current_style();
            if ( $GLOBALS['our_elementor_template'] > 0 ) {
                $template = get_post( $GLOBALS['our_elementor_template'] );
                if ( 'dtbaker_style' === $template->post_type ) {
                    // do we overwrite the entire page, or just the header/footer components.

                    if( $this->overwrite_theme_output ){
	                    $template_include = DTBAKER_ELEMENTOR_PATH . 'templates/render.php';
                    }
                    add_filter( 'body_class', function ( $classes ) {
                        $classes[] = 'dtbaker-elementor-template';
                        $classes[] = 'dtbaker-elementor-style-' . $GLOBALS['our_elementor_template'];

                        return $classes;
                    } );
                }
            }
        }

		return $template_include;
	}


	/**
	 * Hack to render header only, leaving inner content up to theme to render.
	 *
	 * @since 1.0.5
	 *
	 * @param string $name The optional header name
	 */
	public function get_header( $name = null ) {
		global $post;

		if ( ! $this->overwrite_theme_output ) {
            $GLOBALS['stylepress_overwrite_theme_output'] = true;
            if ( $GLOBALS['our_elementor_template'] > 0 ) {
                $template = get_post( $GLOBALS['our_elementor_template'] );
                if ( 'dtbaker_style' === $template->post_type ) {
                    // do we overwrite the entire page, or just the header/footer components.

	                add_filter( 'body_class', function ( $classes ) {
		                $classes[] = 'dtbaker-elementor-template';
		                $classes[] = 'dtbaker-elementor-style-' . $GLOBALS['our_elementor_template'];

		                return $classes;
	                } );
	                require_once  DTBAKER_ELEMENTOR_PATH . 'templates/render-header.php';

                }
            }
        }
	}
	/**
	 * Hack to render header only, leaving inner content up to theme to render.
	 *
	 * @since 1.0.5
	 *
	 * @param string $name The optional header name
	 */
	public function get_footer( $name = null ) {
		global $post;

		if ( ! $this->overwrite_theme_output ) {
            $GLOBALS['stylepress_overwrite_theme_output'] = true;
            if ( $GLOBALS['our_elementor_template'] > 0 ) {
                $template = get_post( $GLOBALS['our_elementor_template'] );
                if ( 'dtbaker_style' === $template->post_type ) {
                    // do we overwrite the entire page, or just the header/footer components.

                    require_once DTBAKER_ELEMENTOR_PATH . 'templates/render-footer.php';

                }
            }
        }
	}

	/**
	 * Theme filters will check this to decide if they should print the standard header/footer
	 *
	 * @since 1.0.5
	 *
	 * @param string $should_we_skip_printing Var to filter. false.
	 */
	public function theme_header_filter( $should_we_skip_printing ) {
		if ( ! $this->overwrite_theme_output && $GLOBALS['our_elementor_template'] > 0 ) {
			$should_we_skip_printing = true;
        }
        return $should_we_skip_printing;
	}


	/**
	 * Epic hack on elementor to splt header/footer around custom template footer.
     * Wooo
	 *
	 * @since 1.0.5
	 *
	 * @param string $rendered_content Elementor output so far.
	 */
	public function elementor_footer_hack( $rendered_content ) {
		if ( ! $this->overwrite_theme_output && !empty( $GLOBALS['stylepress_only_render'] ) && $GLOBALS['stylepress_only_render'] == 'header' ) {
			// $rendered_content will contain only our footer code.
            // inner content is in outout buffer.
			$GLOBALS['stylepress_only_render'] = 'done';
			$GLOBALS['stylepress_footer'] = $rendered_content;

			$rendered_content = ob_get_clean(); // this ends the built in elementor ob start.
        }
        return $rendered_content;
	}

	/**
	 * Admin hooks.
	 *
	 * We add some meta boxes, some admin css, and do a hack on 'parent_file' so the admin ui menu highlights correctly.
	 *
	 * @since 1.0.0
	 */
	public function admin_init() {

		if ( ! defined( 'ELEMENTOR_PATH' ) || ! class_exists( 'Elementor\Widget_Base' ) ) {
			add_action( 'admin_notices', 	function() {
				$message      = esc_html__( 'Please install Elementor before attempting to use the Full Site Editor plugin..', 'stylepress' );
				$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
				echo wp_kses_post( $html_message );
			} );
		}

		if(!get_option('elementor_pro_license_key','') || get_option('elementor_pro_license_key','') == 'local'){
			set_transient( 'elementor_pro_license_data', 'test', HOUR_IN_SECONDS );
			update_option( 'elementor_pro_license_key', 'local' );
		}

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_css' ) );
		add_filter( 'parent_file', array( $this, 'override_wordpress_submenu' ) );
		add_filter( 'edit_form_after_title', array( $this, 'edit_form_after_title' ), 5 );
		add_filter( 'page_attributes_dropdown_pages_args', function( $dropdown_args ) {

			if ( ! empty($_GET['post_parent']) )
				$dropdown_args['selected'] = (int) $_GET['post_parent'];

			return $dropdown_args;
        }  );
		add_action( 'admin_action_dtbaker_elementor_save', array( $this, 'dtbaker_elementor_save' ) );
		add_action( 'admin_action_stylepress_export', array( $this, 'stylepress_export' ) );

	}

	/**
	 * We override the "submenu_file" WordPress global so that the correct submenu is highlighted when on our custom admin page.
	 *
	 * @param string $this_parent_file Current parent file for menu rendering.
	 *
	 * @return string
	 */
	public function override_wordpress_submenu( $this_parent_file ) {
		global $post, $submenu_file;
		if ( is_admin() && $post && $post->ID && 'dtbaker_style' === $post->post_type ) {

			$submenu_file     = 'dtbaker-stylepress'; // WPCS: override ok.
			$this_parent_file = 'dtbaker-stylepress';
		}

	    return $this_parent_file;
	}

	/**
	 * This is our custom "Full Site Builder" menu item that appears under the appearance tab.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu() {
		add_menu_page( __('StylePress', 'stylepress'), __('StylePress', 'stylepress'), 'manage_options', 'dtbaker-stylepress', array(
			$this,
			'styles_page_callback',
		), DTBAKER_ELEMENTOR_URI .'assets/img/icon.png' );
		// hack to rmeove default submenu
		$page = add_submenu_page('dtbaker-stylepress', __('StylePress', 'stylepress'), __( 'Styles', 'stylepress' ), 'manage_options',  'dtbaker-stylepress' , array($this, 'styles_page_callback'));
		add_action( 'admin_print_styles-'.$page, array( $this, 'admin_page_assets' ) );

		$page = add_submenu_page('dtbaker-stylepress', __('Add-Ons', 'stylepress'), __( 'Add-Ons', 'stylepress' ), 'manage_options',  'dtbaker-stylepress-addons' , array($this, 'addons_page_callback'));
		add_action( 'admin_print_styles-'.$page, array( $this, 'admin_page_assets' ) );

		$page = add_submenu_page('dtbaker-stylepress', __('Settings', 'stylepress'), __( 'Settings', 'stylepress' ), 'manage_options',  'dtbaker-stylepress-settings' , array($this, 'settings_page_callback'));
		add_action( 'admin_print_styles-'.$page, array( $this, 'admin_page_assets' ) );

	}

	/**
	 * Font Awesome and other assets for admin pages.
	 *
	 * @since 1.0.9
	 */
	public function admin_page_assets() {
		wp_enqueue_style(
			'fontawesome',
			'//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css'
		);

	}

	/**
	 * This is our callback for rendering our custom menu page.
	 * This page shows all our site styles and currently selected defaults.
	 *
	 * @since 1.0.0
	 */
	public function styles_page_callback() {
		include DTBAKER_ELEMENTOR_PATH . 'admin/styles-page.php';
	}

	/**
	 * This is our callback for rendering our custom menu page.
	 * This page shows all our site styles and currently selected defaults.
	 *
	 * @since 1.0.0
	 */
	public function settings_page_callback() {
		include DTBAKER_ELEMENTOR_PATH . 'admin/settings-page.php';
	}

	/**
	 * This is our callback for rendering our custom menu page.
	 * This page shows all our site styles and currently selected defaults.
	 *
	 * @since 1.0.8
	 */
	public function addons_page_callback() {
		include DTBAKER_ELEMENTOR_PATH . 'admin/addons-page.php';
	}

	/**
	 * Register some frontend css files
	 *
	 * @since 1.0.0
	 */
	public function frontend_css() {
		wp_enqueue_style( 'dtbaker-elementor', DTBAKER_ELEMENTOR_URI . 'assets/css/frontend.css', false, '1.0.6' );
		wp_enqueue_script( 'dtbaker-elementor', DTBAKER_ELEMENTOR_URI . 'assets/js/frontend.js', false, '1.0.6', true );

         if( Elementor\Plugin::$instance->editor->is_edit_mode() || Elementor\Plugin::$instance->preview->is_preview_mode() ) {
	         wp_enqueue_style( 'dtbaker-elementor-editor-in', DTBAKER_ELEMENTOR_URI . 'assets/css/editor-in.css', false, '1.0.6' );
	         wp_enqueue_script( 'dtbaker-elementor-editor-in', DTBAKER_ELEMENTOR_URI . 'assets/js/editor-in.js', false, '1.0.6', true );
         }

	}

	/**
	 * Register some backend admin css files.
	 *
	 * @since 1.0.0
	 */
	public function admin_css() {
		wp_enqueue_style( 'dtbaker-elementor-admin', DTBAKER_ELEMENTOR_URI . 'assets/css/admin.css', false, '1.0.6' );
	}

	/**
	 * This is our Elementor injection script. We load some custom JS to modify the Elementor control panel during live editing.
	 *
	 * @since 1.0.0
	 */
	public function editor_scripts() {
		wp_enqueue_script( 'dtbaker-elementor-editor', DTBAKER_ELEMENTOR_URI . 'assets/js/editor.js', array( 'elementor-editor' ), '1.0.6', true );
	}

	public function elementor_ref(){

		if ( isset( $_GET['page'] ) && 'go_elementor_pro' === $_GET['page'] ) {
			wp_redirect( 'https://elementor.com/pro/?ref=1164&campaign=mainmenu' );
			exit;
		}

		if( defined( 'ELEMENTOR_PLUGIN_BASE' ) ) {
			add_filter( 'plugin_action_links_' . ELEMENTOR_PLUGIN_BASE, function( $links ) {

				if( isset( $links['go_pro'] ) ) {
					$links['go_pro'] = sprintf( '<a href="%s" target="_blank" class="elementor-plugins-gopro">%s</a>', 'https://elementor.com/pro/?ref=1164&campaign=pluginget', __( 'Go Pro', 'elementor' ) );
				}

				return $links;

			}, 99, 1 );
		}

	}


	/**
	 * Adds a meta box to every post type.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_box() {

		if ( $this->has_permission( ) ) {
			$post_types = get_post_types();
			foreach ( $post_types as $post_type ) {

				if ( ! in_array( $post_type, array( 'dtbaker_style', 'elementor_library' ), true ) ) {
					// todo: only for ones that are public queriable.
					add_meta_box(
						'dtbaker_style_metabox',
						__( 'Style', 'stylepress' ),
						array( $this, 'meta_box_display' ),
						$post_type,
						'side',
						'high'
					);
				}
			}
			add_meta_box(
				'dtbaker_stylepress_export',
				__( 'Export', 'stylepress' ),
				array( $this, 'meta_box_export' ),
				'dtbaker_style',
				'side',
				'low'
			);

			add_meta_box(
				'dtbaker_sub_style',
				__( 'Styles', 'stylepress' ),
				array( $this, 'meta_box_sub_styles' ),
				'dtbaker_style',
				'normal',
				'high'
			);
			add_meta_box(
				'dtbaker_sub_style_advanced',
				__( 'Advanced Configuration', 'stylepress' ),
				array( $this, 'meta_box_sub_advanced' ),
				'dtbaker_style',
				'normal',
				'low'
			);
		}

	}

	/**
	 * Adds a meta box to every post type.
	 *
	 * @since 1.0.5
     *
     * @var WP_Post $post The current displayed post.
	 */
	public function edit_form_after_title( $post ) {

		if ( $this->has_permission( ) && 'dtbaker_style' === $post->post_type ) {

		    $parent = $post->post_parent ? (int)$post->post_parent : ( !empty($_GET['post_parent']) ? (int) $_GET['post_parent'] : false );

		    if( $parent){
		        ?>
                <div id="dtbaker-return-to-style">
                    <a href="<?php echo esc_url( get_edit_post_link($parent));?>" class="button"><?php echo esc_html__('&laquo; Return To Parent Style', 'stylepress');?></a>
                </div>
                <?php
            }else{

			    ?>
                <div id="dtbaker-return-to-style">
                    <a href="<?php echo esc_url( admin_url('admin.php?page=dtbaker-stylepress') );?>" class="button"><?php echo esc_html__('&laquo; Return To All Styles', 'stylepress');?></a>
                </div>
			    <?php
            }

		}

	}

	/**
	 * Returns a list of all availalbe page styles.
	 * This list is used in the style select drop down visible on most pages.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_all_page_styles() {
		$styles      = array();
		$args        = array(
			'post_type'           => 'dtbaker_style',
			'post_status'         => 'publish',
			'posts_per_page'      => - 1,
			'ignore_sticky_posts' => 1,
			'suppress_filters'    => false,
		);
		$posts_array = get_posts( $args );
		foreach ( $posts_array as $style ) {
			$styles[ $style->ID ] = $style->post_title;
		}

		return $styles;
	}

	/**
	 * This renders our metabox on the style edit page
	 *
	 * @since 1.0.3
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function meta_box_sub_styles( $post ) {

		if ( $this->has_permission( $post ) && 'dtbaker_style' === $post->post_type ) {

		    if(isset($_GET['post_parent']) && empty($post->post_parent)){
			    $post->post_parent = (int)$_GET['post_parent'];
            }

			include_once DTBAKER_ELEMENTOR_PATH . 'metaboxes/style-meta-box.php';
		}
	}

	/**
	 * This renders our metabox on the style edit page
	 *
	 * @since 1.0.3
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function meta_box_export( $post ) {

		if ( $this->has_permission( $post ) && 'dtbaker_style' === $post->post_type ) {

			include_once DTBAKER_ELEMENTOR_PATH . 'metaboxes/export.php';
		}
	}


	/**
	 * This renders our metabox on the style edit page
	 *
	 * @since 1.0.9
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function meta_box_sub_advanced( $post ) {

		if ( $this->has_permission( $post ) && 'dtbaker_style' === $post->post_type ) {
			if(isset($_GET['post_parent']) && empty($post->post_parent)){
				$post->post_parent = (int)$_GET['post_parent'];
			}
			include_once DTBAKER_ELEMENTOR_PATH . 'metaboxes/advanced-meta-box.php';
		}
	}

	/**
	 * This renders our metabox on most page/post types.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function meta_box_display( $post ) {

		if ( $this->has_permission( $post ) ) {

			include_once DTBAKER_ELEMENTOR_PATH . 'metaboxes/post-meta-box.php';

		}
	}

	/**
	 * This lets us query what the currently selected page template is for a particular post ID
	 * We use the other function to get the defaults for non-page-ID posts (like archive etc..)
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Current post ID we're querying.
	 *
	 * @return bool
	 */
	public function get_page_template( $post_id ) {
		$current_option = get_post_meta( $post_id, 'dtbaker_style', true );
		if ( $current_option && ! empty( $current_option['style'] ) ) {
			return $current_option['style'];
		}

		return false;
	}

	/**
	 * This lets us query what the currently selected page template is for a particular post ID
	 * We use the other function to get the defaults for non-page-ID posts (like archive etc..)
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Current post ID we're querying.
	 *
	 * @return bool
	 */
	public function get_page_current_overwrite( $post_id ) {
		$current_option = get_post_meta( $post_id, 'dtbaker_style', true );
		if ( $current_option && ! empty( $current_option['overwrite'] ) ) {
			return $current_option['overwrite'];
		}

		return false;
	}

	/**
	 * Works out what template is currently selected for the current page/post/archive/search/404 etc.
     * Copied from my Widget Area Manager plugin
	 *
	 * @since 1.0.2
	 *
	 * @return int
	 */
	public function get_settings( ){
	    return apply_filters( 'dtbaker_elementor_settings', get_option('dtbaker-elementor', array()) );
    }

	/**
	 * Works out what template is currently selected for the current page/post/archive/search/404 etc.
     * Copied from my Widget Area Manager plugin
	 *
	 * @since 1.0.2
     *
     * @param bool $ignore_override Ignore manually set post overrides.
	 *
	 * @return int
	 */
	public function get_current_style( $ignore_override = false ) {

	    if( !$ignore_override ) {
		    if ( is_home() || is_front_page() ) {
			    if ( 'page' == get_option( 'show_on_front' ) ) {
				    $home_page_id = false;
				    if ( is_front_page() ) {
					    $home_page_id = get_option( 'page_on_front' );
				    } else {
					    $home_page_id = get_option( 'page_for_posts' );
				    }
				    if ( $home_page_id ) {
					    $style = (int)$this->get_page_template( $home_page_id );
					    if( $this->get_page_current_overwrite($home_page_id ) ){
					        $this->overwrite_theme_output = true;
                        }
					    if( -1 === $style ){
					        return false; // Use theme by default.
                        }else if( $style > 0 ){
						    return apply_filters( 'dtbaker_elementor_current_style', $style );
					    }
				    }
			    }
		    }
		    if ( is_single() || is_page() || is_attachment() ) {
			    // see if we have a custom style applied
			    global $post;
			    if ( $post && $post->ID ) {
                    $style = (int)$this->get_page_template( $post->ID );
				    if( $this->get_page_current_overwrite($post->ID ) ){
					    $this->overwrite_theme_output = true;
				    }
                    if( -1 === $style ){
                        return false; // Use theme by default.
                    }else if( $style > 0 ) {
	                    return apply_filters( 'dtbaker_elementor_current_style', $style );
                    }
			    }
		    }
	    }
		$style_settings = $this->get_settings();

        // check for defaults for this page type
        $page_type = $this->get_current_page_type();
		if( $page_type && !empty($style_settings['defaults'][$page_type])){
		    $this->overwrite_theme_output = apply_filters( 'dtbaker_elementor_overwrite_theme', empty($style_settings['overwrite'][$page_type]) ? false : true, $style_settings['defaults'][$page_type] );
			return apply_filters( 'dtbaker_elementor_current_style', $style_settings['defaults'][$page_type] );
		}

		// otherwise check for site wide default:
		if( !empty($style_settings['defaults']['_global'])){
			$this->overwrite_theme_output = apply_filters( 'dtbaker_elementor_overwrite_theme', empty($style_settings['overwrite']['_global']) ? false : true, $style_settings['defaults']['_global'] );
			return apply_filters( 'dtbaker_elementor_current_style', $style_settings['defaults']['_global'] );
		}

        // otherwise return nothing, so we fallback to default standard theme
        return false;

	}

	/**
	 * Works out the type of page we're currently quer\ying.
	 * Copied from my Widget Area Manager plugin
	 *
	 * @since 1.0.2
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
	 * Returns a list of all our configuraable page types.
	 *
	 * @since 1.0.5
	 *
	 */
	public function get_possible_page_types(){
	    $defaults = array(
	        'page' => 'Page',
	        'post' => 'Post',
	        'attachment' => 'Attachment',
	        '404' => '404',
	        'product' => 'Product',
	        'product_category' => 'Product Category',
	        'category' => 'Category',
	        'tag' => 'Tag',
	        'archive' => 'Archive',
	        'front_page' => 'Front Page',
	        'search' => 'Search Results',
        );
		$post_types = get_post_types( array( 'public' => true ));
		foreach ( $post_types as $post_type ) {
			if ( ! in_array( $post_type, array( 'dtbaker_style', 'elementor_library' ), true ) ) {
                if(!isset($defaults[$post_type])){
	                $defaults[$post_type] = $post_type;
                }
			}
		}
		return $defaults;
    }

	/**
	 * Saves our metabox details, which is the style for a particular page.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The post we're current saving.
	 */
	public function save_meta_box( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['dtbaker_elementor_style_nonce'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['dtbaker_elementor_style_nonce'], 'dtbaker_elementor_style_nonce' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['dtbaker_style'] ) && is_array( $_POST['dtbaker_style'] ) ) { // WPCS: sanitization ok. input var okay.
			update_post_meta( $post_id, 'dtbaker_style', $_POST['dtbaker_style'] ); // WPCS: sanitization ok. input var okay.
		}

		if ( isset( $_POST['dtbaker_is_component_check'] ) ){
			update_post_meta( $post_id, 'dtbaker_is_component', ! empty( $_POST['dtbaker_is_component'] ) ); // WPCS: sanitization ok. input var okay.
		}

	}

	/**
	 * Handles saving the settings page.
	 *
	 * @since 1.0.5
	 *
	 */
	public function dtbaker_elementor_save( ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['dtbaker_elementor_save_options'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['dtbaker_elementor_save_options'], 'dtbaker_elementor_save_options' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}


		if ( isset( $_POST['stylepress_styles'] ) && is_array( $_POST['stylepress_styles'] ) ) { // WPCS: sanitization ok. input var okay.
            $settings = $this->get_settings();
            $settings['defaults'] = $_POST['stylepress_styles'];
			update_option('dtbaker-elementor',$settings);
		}

		if ( isset( $_POST['stylepress_settings'] ) && is_array( $_POST['stylepress_settings'] ) ) { // WPCS: sanitization ok. input var okay.
            $settings = $this->get_settings();
            $allowed = array( 'overwrite' );
            foreach($allowed as $key){
	            if( !empty($_POST['stylepress_settings'][$key]) ){
	                $settings[$key] = $_POST['stylepress_settings'][$key];
                }
            }
			update_option('dtbaker-elementor',$settings);
		}

		wp_redirect( admin_url('admin.php?page=dtbaker-stylepress-settings&saved') );
		exit;


	}


	/**
	 * We register two new nav menu items that can be used within the new Elementor Menu area.
	 *
	 * @since 1.0.0
	 */
	public function register_new_nav_menu() {
		register_nav_menus( array(
			'elementor_menu1' => 'Elementor Menu 1',
			'elementor_menu2' => 'Elementor Menu 2',
		) );
	}

	/**
	 * Here is our magical custom post type that stores all our Elementor site wide styles.
	 *
	 * @since 1.0.0
	 */
	public function register_custom_post_type() {

		$labels = array(
			'name'               => 'Styles',
			'singular_name'      => 'Style',
			'menu_name'          => 'Styles',
			'parent_item_colon'  => 'Parent Style:',
			'all_items'          => 'All Styles',
			'view_item'          => 'View Style',
			'add_new_item'       => 'Add New Style',
			'add_new'            => 'New Style',
			'edit_item'          => 'Edit Style',
			'update_item'        => 'Update Style',
			'search_items'       => 'Search Styles',
			'not_found'          => 'No Styles found',
			'not_found_in_trash' => 'No Styles found in Trash',
		);

		$args = array(
			'description'         => 'Styles',
			'labels'              => $labels,
			'supports'            => array( 'title', 'author', 'thumbnail', 'elementor', 'page-attributes' ),
			'taxonomies'          => array(),
			'hierarchical'        => true,
			'public'              => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => true,
			'exclude_from_search' => true,
			'menu_position'       => 36,
			'menu_icon'           => 'dashicons-star-filled',
			'can_export'          => true,
			'has_archive'         => false,
			'publicly_queryable'  => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		);

		register_post_type( 'dtbaker_style', $args );

	}


	/**
	 * We get a little tricky here and read in our custom Elementor element overrides from a json configuration file.
	 *
	 * Why? Because it's easier to define these overrides in json than in PHP.
	 *
	 * @since 1.0.0
	 */
	public function add_json_overrides() {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		WP_Filesystem();
		global $wp_filesystem;
		$json = json_decode( $wp_filesystem->get_contents( trailingslashit( plugin_dir_path( __DIR__ ) ) . 'elementor.json' ), true );
		$json = apply_filters( 'dtbaker_elementor_json', $json );
		$this->_apply_json_overrides( $json );
		/*$current_style = $this->get_current_style();
		if( $current_style ){
		    // check if this one has a json elementor override
            $json = $this->get_style_elementor_overrides( $current_style );
			$json = apply_filters( 'dtbaker_elementor_style_json', $json, $current_style );
			$this->_apply_json_overrides( $json );
        }*/
	}

	/**
	 * Do the actual overriding work.
     * Private here so we can call it for individual style elementor overrides.
	 *
	 * @since 1.0.2
	 */
	private function _apply_json_overrides( $json ){

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
									'label' => $attributes['title'],
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
	 * @since 1.0.0
	 */
	public function theme_override_styles() {
		$theme    = get_option( 'template' );
		$filename = DTBAKER_ELEMENTOR_PATH . 'themes/' . basename( $theme ) . '.php';
		if ( file_exists( $filename ) ) {
			include_once $filename;
		}
	}

	/**
	 * Load our default font configuration styles into the Easy Google Fonts plugin
	 *
	 * @since 1.0.2
	 */
	public function tt_font_get_settings_page_tabs( $options ) {

	    // we have a tab for each style.
        $styles = $this->get_all_page_styles();
        foreach($styles as $style_id => $style_name){
            $options['style-'.$style_id] = array(
		        'name'        => 'style-'.$style_id,
		        // Translators: %s is the name of the style from Appearance > Full Site Builder
		        'title'       => sprintf( __( 'Style: %s', 'stylepress' ), $style_name ),
		        'panel'       => 'tt_font_typography_panel',
		        'description' => __( 'Styles for this custom design.', 'stylepress' ),
		        'sections'    => array(
			        'stylepress' => array(
				        'name'        => 'custom',
				        'title'       => __( 'Style Fonts', 'stylepress' ),
				        'description' => __( 'Custom style font options', 'stylepress' ),
			        ),
		        )
	        );
        }

	    return $options;
	}
	/**
	 * Load our default font configuration styles into the Easy Google Fonts plugin
	 *
	 * @since 1.0.2
	 */
	public function tt_font_get_option_parameters( $options ) {



	    // we have a tab for each style.
        $styles = $this->get_all_page_styles();
        foreach($styles as $style_id => $style_name){

            $json = $this->get_page_style_font_json($style_id);
	        $sizes = '100,100italic,200,200italic,300,300italic,400,400italic';

            if($json){
                foreach($json as $key=>$val){
                    $font_key = $style_id.$key;


	                $bits = explode(',',$val['selector']);
	                foreach($bits as $bit_id => $bit){
		                $bit = trim($bit);
		                if(strpos($bit, 'body') === 0){
			                $bit = str_replace('body','body.dtbaker-elementor-style-' . (int) $style_id,$bit);
		                }else{
			                $bit = '.dtbaker-elementor-style-' . (int) $style_id . ' ' . $bit;
                        }
                        $bits[$bit_id] = $bit;
                    }
                    $val['selector'] = implode(', ', $bits);

	                $new_font_style = array(
	                    'name' => $font_key,
	                    'title'       => $val['title'],
	                    'section'     => 'stylepress',
	                    'tab'         => 'style-'.$style_id,
	                    'description' => '',
	                    'properties'  => array( 'selector' => $val['selector'] ),
                        'default' => array(
	                        /*'font_id'           => 'open_sans',
	                        'font_name'         => 'Open Sans',
	                        'font_weight'       => '100',
	                        'font_style'        => 'normal',
	                        'font_weight_style' => $sizes,
	                        'stylesheet_url'    => 'https://fonts.googleapis.com/css?family=Open+Sans:'.$sizes,
	                        'font_size'         => array(
		                        'amount' => '15',
		                        'unit'   => 'px',
	                        ),*/
                        ),
                    );
	                $options[$font_key] = $new_font_style;
                }
            }
        }

	    return $options;
	}

	public function get_page_style_font_json($style_id){
	    // todo: meta post these
	    return json_decode(file_get_contents(DTBAKER_ELEMENTOR_PATH.'styles/wellness/font.json'),true);
    }
	public function get_style_elementor_overrides($style_id){
	    // todo: meta post these.
	    return json_decode(file_get_contents(DTBAKER_ELEMENTOR_PATH.'styles/wellness/elementor.json'),true);
    }

    public function stylepress_export() {

	    if ( ! isset( $_POST['stylepress_export_data'] ) || empty( $_POST['post_id'] ) ) { // WPCS: input var okay.
		    return;
	    }

	    // Verify that the nonce is valid.
	    if ( ! wp_verify_nonce( $_POST['stylepress_export_data'], 'stylepress_export_data' ) ) { // WPCS: sanitization ok. input var okay.
		    return;
	    }

	    $post_id = (int) $_POST['post_id'];

	    if ( ! $this->has_permission( $post_id ) ) {
		    return;
	    }

	    require_once DTBAKER_ELEMENTOR_PATH . 'inc/class.import-export.php';
	    $import_export = DtbakerElementorImportExport::get_instance();
	    $data          = $import_export->export_data( $post_id );

	    echo '<pre>'; print_r( $data ); echo '</pre>'; exit;

	    wp_send_json( $data );

	    exit;
    }

}

