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
	 * Flag to let us know if we render entire page or just overwrite get_header() and get_footer().
	 *
	 * @since 1.0.5
	 *
	 * @var bool
	 */
	public $overwrite_theme_output = true;

	/**
	 * Flag to let us know that theme css is removed.
	 *
	 * @since 1.0.11
	 *
	 * @var bool
	 */
	public $removing_theme_css = false;

	/**
	 * Initializes the plugin and sets all required filters.
	 *
	 * @since 1.0.0
	 */
	public function init() {

	    // two modes.
        // if we just want the extra widgets and not the full stylepress editor then we load that separately.


        add_action( 'admin_init', array( $this, 'admin_init' ), 20 );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'init', array( $this, 'register_custom_post_type' ) );
        add_action( 'init', array( $this, 'theme_compatibility' ) );
        add_action( 'wp_ajax_stylepress_purchase_complete', array( $this, 'payment_complete' ) );
        add_action( 'wp_ajax_stylepress_get_css', array( $this, 'editor_get_css' ) );
        add_action( 'wp_ajax_stylepress_save_css', array( $this, 'editor_save_css' ) );
        add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'editor_scripts' ), 99999 );
        add_action( 'wp_print_footer_scripts', array( $this, 'wp_print_footer_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'theme_override_styles' ), 99999 );
        add_filter( 'tt_font_get_settings_page_tabs', array( $this, 'tt_font_get_settings_page_tabs' ), 101 );
        add_filter( 'tt_font_get_option_parameters', array( $this, 'tt_font_get_option_parameters' ), 10 );
//        add_action( 'elementor/frontend/element/before_render', array( $this, 'section_before_render' ), 10 );

        add_filter( 'template_include', array( $this, 'template_include' ), 999 );

        // nasty old hacks to get inner content working nicely, don't do it like this:
//        add_action( 'get_header', array( $this, 'get_header' ), 999 );
//        add_action( 'get_footer', array( $this, 'get_footer' ), 999 );
//        add_filter( 'stylepress_rendered_header', array( $this, 'theme_header_filter' ), 999 );
//        add_filter( 'stylepress_rendered_footer', array( $this, 'theme_header_filter' ), 999 );
//        add_filter( 'elementor/frontend/the_content', array( $this, 'elementor_footer_hack' ), 999 );



		add_action( 'init', array( $this, 'register_new_nav_menu' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_css' ) );
		add_action( 'elementor/init', array( $this, 'elementor_init_complete' ), 40 );
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_add_new_widgets' ) );
		add_action( 'wp', array( $this, 'add_elementor_overrides' ) );
//		add_action( 'init', array( $this, 'elementor_ref' ) );

		// stylepress plugin hooks
        add_action( 'init', array( $this, 'load_extensions') );
		add_filter( 'nav_menu_item_title', array( $this, 'dropdown_icon'), 10, 4 );


		add_action('wp_before_admin_bar_render', array($this, 'wp_admin_bar'));


	}

	public function show_full_ui(){
	    return !(defined('STYLEPRESS_ONLY_WIDGETS') && STYLEPRESS_ONLY_WIDGETS);
    }

	public function dropdown_icon($title, $item, $args, $depth ) {
		// Build an array with our theme location
		$theme_locations = array(
			'primary',
			'secondary',
			'slideout'
		);

		// Loop through our menu items and add our dropdown icons
		foreach ( $item->classes as $value ) {
			if ( 'menu-item-has-children' === $value  ) {
				$title = $title . '<span role="button" class="dropdown-menu-toggle" aria-expanded="false"></span>';
			}
		}

		// Return our title
		return $title;
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


	public function load_extensions(){

		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			if ( class_exists( 'Elementor\Plugin' ) ) {

				if ( is_callable( 'Elementor\Plugin', 'instance' ) ) {
					$elementor = Elementor\Plugin::instance();
					if ( isset( $elementor->widgets_manager ) ) {
						if ( method_exists( $elementor->widgets_manager, 'register_widget_type' ) ) {

							require_once DTBAKER_ELEMENTOR_PATH . 'extensions/dynamic-field/dynamic-field.php';
							require_once DTBAKER_ELEMENTOR_PATH . 'extensions/email-subscribe/email-subscribe.php';
							require_once DTBAKER_ELEMENTOR_PATH . 'extensions/modal-popup/modal-popup.php';
							require_once DTBAKER_ELEMENTOR_PATH . 'extensions/wp-menu/wp-menu.php';
							require_once DTBAKER_ELEMENTOR_PATH . 'extensions/form/form-fields.php';
							require_once DTBAKER_ELEMENTOR_PATH . 'extensions/tooltip/tooltip.php';
							require_once DTBAKER_ELEMENTOR_PATH . 'extensions/google-maps/google-maps.php';
							require_once DTBAKER_ELEMENTOR_PATH . 'extensions/page-slider/dtbaker-page-slider.php';
							require_once DTBAKER_ELEMENTOR_PATH . 'extensions/woocommerce/woocommerce.php';
							// only works with pro:
							if(defined('ELEMENTOR_PRO_VERSION')) {
								require_once DTBAKER_ELEMENTOR_PATH . 'extensions/stylepress-loop/stylepress-loop.php';
							}

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
     * @since 1.0.8
	 */
	public function elementor_add_new_widgets() {
		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			if ( class_exists( 'Elementor\Plugin' ) ) {

				if ( is_callable( 'Elementor\Plugin', 'instance' ) ) {
					$elementor = Elementor\Plugin::instance();
					if ( isset( $elementor->widgets_manager ) ) {
						if ( method_exists( $elementor->widgets_manager, 'register_widget_type' ) ) {

                            // todo: option these out in 'Add-Ons' section
						    require_once DTBAKER_ELEMENTOR_PATH . 'widgets/inner-content.php';

						}
					}
				}
			}
		}
    }


    public function section_before_render($section){

	    /*if( 'section' === $section->get_name() ) {
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
	    }*/
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
	 * There are two "modes". We are in the editor and editing the template (loads editor.php)
	 * Or we are on the frontend and we are rending normal page content (render.php)
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_include The path to the current template file.
	 *
	 * @return string
	 */
	public function template_include( $template_include ) {

		if(!$this->show_full_ui())return $template_include;
		global $post;

		// whitelist certain templates that don't get overwitten:
        $whitelist = array(
          'plugins/elementor',
        );
        foreach($whitelist as $w){
            if(strpos($template_include, $w)){
                return $template_include;
            }
        }
        $whitelist_post_types = array(
        );
        if( $post && !empty($post->ID) && in_array( $post->post_type, $whitelist_post_types)){
            return $template_include;
        }

		$original_template = $template_include;

		if ( $post && ! empty( $post->ID ) && 'elementor_library' === $post->post_type  ) {
		    return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
		}else if ( $post && ! empty( $post->ID ) && 'dtbaker_style' === $post->post_type  ) {
            $this->previewing_style = true;
            $template_include       = DTBAKER_ELEMENTOR_PATH . 'templates/editor.php';
            add_filter( 'body_class', function ( $classes ) use ( $post )  {
                $classes[] = 'dtbaker-elementor-template';
                $classes[] = 'dtbaker-elementor-template-preview';
                if($post->post_parent){
	                $classes[] = 'dtbaker-elementor-style-' . $post->post_parent;
	                $classes[] = 'dtbaker-elementor-sub-style-' . $post->ID;
                }else{
	                $classes[] = 'dtbaker-elementor-style-' . $post->ID;
                }
                if( $post->post_parent && get_post_meta( $post->ID, 'dtbaker_is_component', true ) ){
	                $classes[] = 'dtbaker-elementor-template-component';
                }

                return $classes;
            } );
        } else {
            // check if this particular page has a template set.
            // we work out the outer and inner template and decide how we should render the page based on that info.

            // outer template. This checks the current page manual overrides, along with the default for the page type.
            $GLOBALS['our_elementor_template'] = (int) $this->get_current_style();
            $GLOBALS['our_elementor_inner_template'] = (int) $this->get_current_inner_style();

            if ( $GLOBALS['our_elementor_template'] > 0 ) {
                $template = get_post( $GLOBALS['our_elementor_template'] );
                if ( 'dtbaker_style' === $template->post_type ) {

                    // success, we've got an outer template to display.
                    // there's two options now:
                    // 1) continue with normal rendered content from stylepress
                    // 2) revert back to theme output for the inner sections ( tricky! )

                    if( $GLOBALS['our_elementor_inner_template'] == STYLEPRESS_INNER_USE_THEME ){
                        //tricky time! stylepress outer + normal theme inner.
                        // will only work for some themes.

                        // we need to render our outer template, then pass the current template into that.
                        $GLOBALS['stylepress_render_this_template_inside'] = $original_template;
	                    $template_include = DTBAKER_ELEMENTOR_PATH . 'templates/render-outer.php';


                    }else if( $GLOBALS['our_elementor_inner_template'] == STYLEPRESS_INNER_USE_PLAIN ){
                        // plain old output, no stylepress wizardty
	                    $template_include = DTBAKER_ELEMENTOR_PATH . 'templates/render.php';
                    }else if( $GLOBALS['our_elementor_inner_template'] > 0 ){
                        // using a stylepress inner layout. and outer layout. easy!
	                    $template_include = DTBAKER_ELEMENTOR_PATH . 'templates/render.php';
                    }else{
                        // using "default" layout which means we pass into render to figure out.
	                    $template_include = DTBAKER_ELEMENTOR_PATH . 'templates/render.php';
                    }
                    add_filter( 'body_class', function ( $classes ) use ($template) {
                        $classes[] = 'dtbaker-elementor-template';
	                    if($template->post_parent){
		                    $classes[] = 'dtbaker-elementor-style-' . $template->post_parent;
		                    $classes[] = 'dtbaker-elementor-sub-style-' . $template->ID;
	                    }else{
		                    $classes[] = 'dtbaker-elementor-style-' . $template->ID;
	                    }
                        return $classes;
                    } );
                }
            }else if($GLOBALS['our_elementor_template'] == STYLEPRESS_OUTER_USE_THEME){

	            add_filter( 'body_class', function ( $classes ) {
		            $classes[] = 'stylepress-outer-inner';
		            return $classes;
	            } );

	            if( $GLOBALS['our_elementor_inner_template'] == STYLEPRESS_INNER_USE_THEME ){
		            // that's fine! go ahead and use defualt inner content.

	            }else if( $GLOBALS['our_elementor_inner_template'] == STYLEPRESS_INNER_USE_PLAIN ){

	                // fine as well, use elementor output.

	            }else if( $GLOBALS['our_elementor_inner_template'] > 0 ){
		            // using a stylepress inner layout. with a default theme outer layout. a bit tricky! hooks!

                    /* theme outer + stylepress inner
  = tricky as well!
  we get two hooks (e.g. ocean_before_main & ocean_after_main )
  on the first hook we render our stylepress output
  then start output buffering and remove everything we capture until the ocean_after_main hook runs.
                    $theme    = get_option( 'template' );
	            $filename = DTBAKER_ELEMENTOR_PATH . 'themes/' . basename( $theme ) . '.css';
	            if ( file_exists( $filename ) ) {
		            wp_enqueue_style( 'stylepress-theme-addons', DTBAKER_ELEMENTOR_URI . 'themes/' . basename( $theme ) . '.css', false, DTBAKER_ELEMENTOR_VERSION );
	            }
                    */

                    $theme_hooks = apply_filters('stylepress_theme_hooks',array());

                    if(!empty($theme_hooks['before']) && !empty($theme_hooks['after'])){
	                    add_action($theme_hooks['before'], function(){ // ocean_before_main
		                    // render our content here.
		                    do_action('stylepress/render-inner');
		                    ob_start();
		                    // capture all output and discard at end below:
	                    });
	                    add_action($theme_hooks['after'], function(){ // ocean_after_main
		                    ob_end_clean();
	                    });
                    }

	            }else{

	            }

            }
        }

		$this->debug_message('template_include: ' . $template_include . ' ' . ( $template_include != $original_template ? ' (changed from: '.$original_template.' )' : ''));

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

		if(!$this->show_full_ui())return;
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

		if(!$this->show_full_ui())return;
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

		if(!$this->show_full_ui())return $should_we_skip_printing;

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

		if(!$this->show_full_ui())return $rendered_content;
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
		    // we need to put it here in admin_init because Elementor might not have loaded in our plugin init area.

			add_action( 'admin_notices', 	function() {
				$message      = esc_html__( 'Please install and activate Elementor before attempting to use the StylePress plugin.', 'stylepress' );
				$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
				echo wp_kses_post( $html_message );
			} );


		}else if($this->show_full_ui()){


//			if ( ! get_option( 'elementor_pro_license_key', '' ) || get_option( 'elementor_pro_license_key', '' ) == 'local' ) {
//				set_transient( 'elementor_pro_license_data', 'test', HOUR_IN_SECONDS );
//				update_option( 'elementor_pro_license_key', 'local' );
//			}

			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_meta_box' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_css' ) );
			add_filter( 'parent_file', array( $this, 'override_wordpress_submenu' ) );
			add_filter( 'edit_form_after_title', array( $this, 'edit_form_after_title' ), 5 );
			add_filter( 'page_attributes_dropdown_pages_args', function ( $dropdown_args ) {

				if ( ! empty( $_GET['post_parent'] ) ) {
					$dropdown_args['selected'] = (int) $_GET['post_parent'];
				}

				return $dropdown_args;
			} );
			add_action( 'admin_action_dtbaker_elementor_save', array( $this, 'dtbaker_elementor_save' ) );
			add_action( 'admin_action_dtbaker_elementor_create', array( $this, 'dtbaker_elementor_create' ) );
			add_action( 'admin_action_stylepress_export', array( $this, 'stylepress_export' ) );
			add_action( 'admin_action_stylepress_download', array( $this, 'stylepress_download' ) );
			add_action( 'admin_action_stylepress_clone', array( $this, 'stylepress_clone' ) );



		}

	}



	public function wp_admin_bar(){
		if($this->show_full_ui() && $this->has_permission() && !is_admin()) {
			$parent_menu = "stylepress_nav";
			$this->add_root_menu(__( 'StylePress'), $parent_menu);
			$current_page_style = (int) $this->get_current_style();
			if($current_page_style > 0) {
				$style_details = get_post( $current_page_style );
				$this->add_sub_menu( sprintf(__('Outer Style: %s'), esc_html($style_details->post_title)) , $parent_menu . 'p', \Elementor\Utils::get_edit_link( $current_page_style ), $parent_menu );
			}
			if(!empty($GLOBALS['stylepress_template_turtles'])){
			    foreach($GLOBALS['stylepress_template_turtles'] as $used_style_id){
				    $style_details = get_post( $used_style_id );
				    $this->add_sub_menu( sprintf(__('Inner Style: %s'), esc_html($style_details->post_title)) , $parent_menu . 'inner'.$used_style_id, \Elementor\Utils::get_edit_link( $used_style_id ), $parent_menu );
                }
			}


			if(!empty($GLOBALS['stylepress_slidein']) || !empty($GLOBALS['stylepress_modal_popups']) || !empty($GLOBALS['stylepress_nav_slideouts'])) {

			    $modal_menu = $parent_menu.'mod';

				$this->add_sub_menu( __('Modals') , $modal_menu, '#', $parent_menu );

				if(!empty($GLOBALS['stylepress_slidein'])) {
					foreach ( $GLOBALS['stylepress_slidein'] as $template_id => $options ) {
					    $post = get_post($template_id);
						$this->add_sub_menu( esc_html($post->post_title) , $modal_menu.$template_id, \Elementor\Utils::get_edit_link($template_id), $modal_menu );
					}
				}
				if(!empty($GLOBALS['stylepress_modal_popups'])) {
					foreach ( $GLOBALS['stylepress_modal_popups'] as $template_id => $options ) {
						$post = get_post($template_id);
						$this->add_sub_menu( esc_html($post->post_title) , $modal_menu.$template_id, \Elementor\Utils::get_edit_link($template_id), $modal_menu );
					}
				}
				if($GLOBALS['stylepress_nav_slideouts']){
					foreach ( $GLOBALS['stylepress_nav_slideouts'] as $template_id => $options ) {
						$post = get_post($template_id);
						$this->add_sub_menu( esc_html($post->post_title) , $modal_menu.$template_id, \Elementor\Utils::get_edit_link($template_id), $modal_menu );
					}
				}
			}

			$page_type = $this->get_current_page_type();
			if($current_page_style > 0) {
				//$this->add_sub_menu( __('Settings: CSS') , $parent_menu . 'c', get_edit_post_link($current_page_style), $parent_menu );
			}
			$this->add_sub_menu(sprintf(__('Settings: Style %s'), ucwords(str_replace('_',' ',$page_type))), $parent_menu.'ni', admin_url('admin.php?page=dtbaker-stylepress-settings&highlight='.$page_type), $parent_menu);
//			$this->add_sub_menu(__('StylePress Settings'), $parent_menu.'w', admin_url('admin.php?page=dtbaker-stylepress-settings'), $parent_menu);
		}
    }

	/**
	 * @param $name
	 * @param $id
	 * @param bool $href
	 * @return mixed
	 * helper function to add a menu to WP header bar
	 * adapted from demo on wp codex
	 */
	public function add_root_menu($name, $id, $href = FALSE) {
		global $wp_admin_bar;
		$wp_admin_bar->add_menu( array(
			'id' => $id,
			'title' => $name,
			'href' => $href ) );
	}
	/**
	 * @param $name
	 * @param $id
	 * @param $link
	 * @param $root_menu
	 * @param bool $meta
	 * @return mixed
	 * helper function to add a menu to WP header bar
	 * adapted from demo on wp codex
	 */
	public function add_sub_menu($name, $id, $link, $root_menu, $meta = FALSE) {
		global $wp_admin_bar;
		$wp_admin_bar->add_menu( array(
			'parent' => $root_menu,
			'id' => $id,
			'title' => $name,
			'href' => $link,
			'meta' => $meta) );

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


		if ( ! defined( 'ELEMENTOR_PATH' ) || ! class_exists( 'Elementor\Widget_Base' ) ) {
		    return;
		}
	    if($this->show_full_ui()) {
		    add_menu_page( __( 'StylePress', 'stylepress' ), __( 'StylePress', 'stylepress' ), 'manage_options', 'dtbaker-stylepress', array(
			    $this,
			    'styles_page_callback',
		    ), DTBAKER_ELEMENTOR_URI . 'assets/img/icon.png' );
		    // hack to rmeove default submenu
		    $page = add_submenu_page( 'dtbaker-stylepress', __( 'StylePress', 'stylepress' ), __( 'Styles', 'stylepress' ), 'manage_options', 'dtbaker-stylepress', array(
			    $this,
			    'styles_page_callback'
		    ) );
		    add_action( 'admin_print_styles-' . $page, array( $this, 'admin_page_assets' ) );

		    $page = add_submenu_page( 'dtbaker-stylepress', __( 'Add-Ons', 'stylepress' ), __( 'Add-Ons', 'stylepress' ), 'manage_options', 'dtbaker-stylepress-addons', array(
			    $this,
			    'addons_page_callback'
		    ) );
		    add_action( 'admin_print_styles-' . $page, array( $this, 'admin_page_assets' ) );

		    $page = add_submenu_page( 'dtbaker-stylepress', __( 'Settings', 'stylepress' ), __( 'Settings', 'stylepress' ), 'manage_options', 'dtbaker-stylepress-settings', array(
			    $this,
			    'settings_page_callback'
		    ) );
		    add_action( 'admin_print_styles-' . $page, array( $this, 'admin_page_assets' ) );
	    }

	}

	/**
	 * Font Awesome and other assets for admin pages.
	 *
	 * @since 1.0.9
	 */
	public function admin_page_assets() {

		wp_enqueue_style('font-awesome', DTBAKER_ELEMENTOR_URI . 'assets/icons/font-awesome/css/font-awesome.min.css' );

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		wp_register_script( 'stylepress-payments', DTBAKER_ELEMENTOR_URI . 'assets/js/payment.js', false, DTBAKER_ELEMENTOR_VERSION, true );
		wp_localize_script( 'stylepress-payments', 'stylepress_payment', array(
		        'payment_nonce' => wp_create_nonce('payment_nonce'),
		        'hostname' => get_home_url(),
		        'plugin_version' => DTBAKER_ELEMENTOR_VERSION,
            ) );
		wp_enqueue_script( 'stylepress-payments' );

		wp_enqueue_script( 'stylepress-slider', DTBAKER_ELEMENTOR_URI . 'assets/js/omni-slider.js', array('jquery'), DTBAKER_ELEMENTOR_VERSION, true );

        require_once DTBAKER_ELEMENTOR_PATH . 'admin/_help_text.php';

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
     * Check if the current theme/plugin/hosting setup supports a particular feature.
     *
	 * @param string $feature Feature name. e.g. theme-inner
	 *
	 * @return bool
	 */
	public function supports( $feature ){
		return count(apply_filters('stylepress_theme_hooks',array()));
//	    return (bool) get_theme_support('stylepress-elementor');
    }

	/**
	 * Register some frontend css files
	 *
	 * @since 1.0.0
	 */
	public function frontend_css() {
		wp_enqueue_style( 'dtbaker-elementor-css', DTBAKER_ELEMENTOR_URI . 'assets/css/frontend.css', false, DTBAKER_ELEMENTOR_VERSION );
		wp_enqueue_script( 'dtbaker-elementor-js', DTBAKER_ELEMENTOR_URI . 'assets/js/frontend.js', false, DTBAKER_ELEMENTOR_VERSION, true );
		// inject adds inline style against 'dtbaker-elementor'
		$this->inject_additional_font_css();

        wp_enqueue_style('font-awesome');//, DTBAKER_ELEMENTOR_URI . 'assets/icons/font-awesome/css/font-awesome.min.css' );


		if($this->show_full_ui() && $this->has_permission()){
			wp_enqueue_style( 'stylepress-css-editor', DTBAKER_ELEMENTOR_URI . 'assets/css/frontend-css-editor.css', false, DTBAKER_ELEMENTOR_VERSION );

			wp_register_script( 'stylepress-css-editor', DTBAKER_ELEMENTOR_URI . 'assets/js/frontend-css-editor.js', false, DTBAKER_ELEMENTOR_VERSION, true );
			wp_localize_script( 'stylepress-css-editor', 'stylepress_css', array(
				'nonce' => wp_create_nonce('stylepress_css'),
				'ajaxurl' => admin_url('admin-ajax.php'),
                'post_id' => get_queried_object_id(),
                'style_id' => (int) $this->get_current_style(),
			) );
			wp_enqueue_script( 'stylepress-css-editor' );

        }

        if( Elementor\Plugin::$instance->editor->is_edit_mode() || Elementor\Plugin::$instance->preview->is_preview_mode() ) {
            wp_enqueue_style( 'dtbaker-elementor-editor-in', DTBAKER_ELEMENTOR_URI . 'assets/css/editor-in.css', false, DTBAKER_ELEMENTOR_VERSION );
            wp_enqueue_script( 'dtbaker-elementor-editor-in', DTBAKER_ELEMENTOR_URI . 'assets/js/editor-in.js', false, DTBAKER_ELEMENTOR_VERSION, true );

        }

	}

	/**
	 * Ajax handler for getting the current page CSS..
	 */
	public function editor_get_css(){
		if(!empty($_POST['nonce']) && wp_verify_nonce($_POST['nonce'],'stylepress_css')){
			$style_id = !empty($_POST['style_id']) ? (int)$_POST['style_id'] : 0;
			if($style_id) {
			    $post_object = get_post($style_id);
			    // if this is a sub post. we get the parent one.
                if($post_object->post_parent){
	                $post_object = get_post($post_object->post_parent);
                }
				$advanced = $this->get_advanced($post_object->ID,false);
				wp_send_json_success( array(
                    'style_id' => $post_object->ID,
                    'css' => !empty($advanced['css']) ? $advanced['css'] : ''
                ) );
			}
		}
		wp_send_json_error('-1');
    }
	/**
	 * Ajax handler for saving the current page CSS..
	 */
	public function editor_save_css(){

    }

	/**
	 * Register some backend admin css files.
	 *
	 * @since 1.0.0
	 */
	public function admin_css() {
		wp_enqueue_style( 'dtbaker-elementor-admin', DTBAKER_ELEMENTOR_URI . 'assets/css/admin.css', false, DTBAKER_ELEMENTOR_VERSION );
	}

	/**
	 * This is our Elementor injection script. We load some custom JS to modify the Elementor control panel during live editing.
	 *
	 * @since 1.0.0
	 */
	public function editor_scripts() {
		if(!$this->show_full_ui())return;
		wp_enqueue_script( 'dtbaker-elementor-editor', DTBAKER_ELEMENTOR_URI . 'assets/js/editor.js', false, DTBAKER_ELEMENTOR_VERSION, true );
		wp_enqueue_style( 'stylepress-elementor-editor', DTBAKER_ELEMENTOR_URI . 'assets/css/editor.css', false, DTBAKER_ELEMENTOR_VERSION);
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
						__( 'StylePress', 'stylepress' ),
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
				__( 'StylePress Settings', 'stylepress' ),
				array( $this, 'meta_box_sub_styles' ),
				'dtbaker_style',
				'normal',
				'high'
			);
			add_meta_box(
				'dtbaker_sub_style_advanced',
				__( 'StylePress Advanced', 'stylepress' ),
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
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=dtbaker-stylepress&style_id=' . $parent ) );?>" class="button"><?php echo esc_html__('&laquo; Return To Style Page', 'stylepress');?></a>
                </div>
                <?php
            }else{

			    ?>
                <div id="dtbaker-return-to-style">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=dtbaker-stylepress&style_id=' . $post->ID ) );?>" class="button"><?php echo esc_html__('&laquo; Return To Style Page', 'stylepress');?></a>
                </div>
                <div id="stylepress-modify-font">
                    <?php
                    $url       = add_query_arg(
	                    array(
                            'autofocus[panel]' => 'tt_font_typography_panel',
		                    'url'    => urlencode( get_permalink( $post->ID ) ),
		                    'return' => urlencode( get_edit_post_link( $post->ID ) ),
	                    ),
	                    admin_url( 'customize.php' )
                    );
                    ?>
                    <a href="<?php echo esc_url( $url );?>" class="button"><?php echo esc_html__('Customize Font & Color Defaults', 'stylepress');?></a>
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
			'order'=> 'ASC',
            'orderby' => 'title',
		);
		$posts_array = get_posts( $args );
		$children = array();
		foreach ( $posts_array as $style ) {
		    if( !$style->post_parent ) {
			    $styles[ $style->ID ] = $style->post_title;
		    }else if ( ! get_post_meta( $style->ID, 'dtbaker_is_component', true ) ) {
		        if(!isset($children[$style->post_parent])){
			        $children[$style->post_parent] = array();
                }
			    $children[$style->post_parent][$style->ID] = $style->post_title;
            }
		}
		// todo: sort alpha:

        $return = array();
		//we're only doing 1 level deep, not themes all the way down, so we don't need recursion here.

        foreach($styles as $style_id => $style_name){
            $return[$style_id] = $style_name;
            if(isset($children[$style_id])){
                foreach($children[$style_id] as $child_style_id => $child_name){
	                $return[$child_style_id] = '&nbsp; &#8627; ' . $child_name;
                }
            }
        }


		return $return;
	}



	/**
	 * Returns a list of all availalbe page styles.
	 * This list is used in the style select drop down visible on most pages.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_all_page_components() {
		$styles      = array();
		$args        = array(
			'post_type'           => 'dtbaker_style',
			'post_status'         => 'publish',
			'posts_per_page'      => - 1,
			'ignore_sticky_posts' => 1,
			'suppress_filters'    => false,
			'order'=> 'ASC',
			'orderby' => 'title',
		);
		$posts_array = get_posts( $args );
		$children = array();
		foreach ( $posts_array as $style ) {
			if ( ! $style->post_parent ) {
				$styles[ $style->ID ] = $style->post_title;
			} else if ( get_post_meta( $style->ID, 'dtbaker_is_component', true ) ) {
				if ( ! isset( $children[ $style->post_parent ] ) ) {
					$children[ $style->post_parent ] = array();
				}
				$children[ $style->post_parent ][ $style->ID ] = $style->post_title;
			}
		}
		// todo: sort alpha:

		$return = array();
		//we're only doing 1 level deep, not themes all the way down, so we don't need recursion here.

		foreach($styles as $style_id => $style_name){
//			$return[$style_id] = $style_name;
			if(isset($children[$style_id])){
				foreach($children[$style_id] as $child_style_id => $child_name){
					$return[$child_style_id] =  $style_name . ' &#8611; ' . $child_name;
				}
			}
		}

		return $return;
	}


	/**
	 * Returns a list of all availalbe page styles.
	 * This list is used in the style select drop down visible on most pages.
	 *
	 * @since 1.0.9
	 *
	 * @return array
	 */
	public function get_downloadable_styles() {

		$styles = get_transient('stylepress_downloadable');
		if(!$styles || isset($_GET['refresh-styles'])) {
			$styles = array();

			// json query to stylepress.org to get a list of available styles.
			$url      = 'https://styleserver.stylepress.org/wp-admin/admin-ajax.php';
			$response = wp_remote_post(
				$url,
				array(
					'body' => array(
						'action' => 'stylepress_get_available',
						'plugin_version'   => DTBAKER_ELEMENTOR_VERSION,
						'blog_url'   => get_site_url(),
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
		$purchase = get_option('stylepress_purchases',array());
		if(!$purchase)$purchase = array();
		if(isset($_GET['reset-purchases'])){
			unset($purchase[$_GET['reset-purchases']]);
			update_option('stylepress_purchases',$purchase);
        }
        foreach($styles as $style_id => $style){
		    $styles[$style_id]['pay_nonce'] = false;
		    if(!empty($purchase[$style_id])){
		        // todo: check get_home_url against recorded pament. meh.
                foreach($purchase[$style_id] as $purchase){
                    if(!empty($purchase['server']['payment_id'])){
	                    $styles[$style_id]['pay_nonce'] = $purchase['server']['payment_id'];
                    }
                }
            }
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

			$this->admin_page_assets();

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
	public function get_page_inner_style( $post_id ) {
		$current_option = get_post_meta( $post_id, 'dtbaker_style', true );
		if ( $current_option && ! empty( $current_option['inner_style'] ) ) {
			return $current_option['inner_style'];
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

		$style_settings = $this->get_settings();
//		if(!empty($style_settings['defaults']['coming_soon']) && !is_user_logged_in()){
//            return $style_settings['defaults']['coming_soon'];
//        }

        global $post;
        if ( $post && ! empty( $post->ID ) && 'dtbaker_style' === $post->post_type){
            // we're previewing a style.
            return $post->ID;
        }

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
					    if( STYLEPRESS_OUTER_USE_THEME === $style ){
					        return $style; // Use theme by default.
                        }else if( $style > 0 ){
						    return apply_filters( 'dtbaker_elementor_current_style', $style );
					    }
				    }
			    }
		    }
		    if ( is_single() || is_page() || is_attachment() ) {
			    // see if we have a custom style applied
			    if ( $post && $post->ID ) {
                    $style = (int)$this->get_page_template( $post->ID );
                    if( STYLEPRESS_OUTER_USE_THEME === $style ){
                        return $style; // Use theme by default.
                    }else if( $style > 0 ) {
	                    return apply_filters( 'dtbaker_elementor_current_style', $style );
                    }
			    }
		    }
	    }

        // check for defaults for this page type
        $page_type = $this->get_current_page_type();
		if( $page_type && !empty($style_settings['defaults'][$page_type])){
			return apply_filters( 'dtbaker_elementor_current_style', $style_settings['defaults'][$page_type] );
		}
		// otherwise check for site wide default:
		if( !empty($style_settings['defaults']['_global'])){
			return apply_filters( 'dtbaker_elementor_current_style', $style_settings['defaults']['_global'] );
		}

        // otherwise return nothing, so we fallback to default standard theme
        return false;

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
	public function get_current_inner_style( $ignore_override = false ) {

		$style_settings = $this->get_settings();

        global $post;
        if ( $post && ! empty( $post->ID ) && 'dtbaker_style' === $post->post_type){
            // we're previewing a style.
            return false;
        }

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
					    $style = (int)$this->get_page_inner_style( $home_page_id );
					    if( STYLEPRESS_INNER_USE_PLAIN === $style ||  STYLEPRESS_INNER_USE_THEME === $style){
					        return $style;
                        }else if( $style > 0 ){
						    return apply_filters( 'dtbaker_elementor_current_inner_style', $style );
					    }
				    }
			    }
		    }
		    if ( is_single() || is_page() || is_attachment() ) {
			    // see if we have a custom style applied
			    if ( $post && $post->ID ) {
                    $style = (int)$this->get_page_inner_style( $post->ID );
				    if( STYLEPRESS_INNER_USE_PLAIN === $style ||  STYLEPRESS_INNER_USE_THEME === $style){
                        return $style; // Use theme by default.
                    }else if( $style > 0 ) {
	                    return apply_filters( 'dtbaker_elementor_current_inner_style', $style );
                    }
			    }
		    }
	    }

        // check for defaults for this page type
        $page_type = $this->get_current_page_type();

        if($page_type) {
	        $settings_key_to_check = $page_type . '_inner';
	        if ( is_home() || is_front_page() ) {
		        // home page or blog output page.
		        if ( 'page' == get_option( 'show_on_front' ) && is_front_page() && get_option( 'page_on_front' ) ) {
			        //
		        } else if ( $settings_key_to_check != 'archive_inner' ) {
			        $settings_key_to_check = 'archive_inner';
			        \DtbakerElementorManager::get_instance()->debug_message( "get_current_inner_style(): We're showing blog post output on home page, using inner style $settings_key_to_check instead" );
		        }
	        }

	        if ( $page_type && ! empty( $style_settings['defaults'][ $settings_key_to_check ] ) ) {
		        return apply_filters( 'dtbaker_elementor_current_inner_style', $style_settings['defaults'][ $settings_key_to_check ] );
	        }
        }
		// otherwise check for site wide default:
		if( !empty($style_settings['defaults']['_global_inner'])){
			return apply_filters( 'dtbaker_elementor_current_inner_style', $style_settings['defaults']['_global_inner'] );
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
	        '_global' => 'Global Defaults',
	        'archive' => 'Archive/Post Summary',
	        'post' => 'Post Single',
	        'page' => 'Page Single',
//	        'attachment' => 'Attachment',
	        '404' => '404',
//	        'product' => 'Product',
//	        'product_category' => 'Product Category',
	        'category' => 'Category',
	        'tag' => 'Tag',
	        'front_page' => 'Front Page',
	        'search' => 'Search Results',
        );

		if(function_exists('WC')){
			// add our own woocommerce entries.
			$defaults['products'] = 'WooCommerce Shop';
			$defaults['product'] = 'WooCommerce Product';
			$defaults['product_category'] = 'WooCommerce Category';
		}

		$post_types = get_post_types( array( 'public' => true ));
		foreach ( $post_types as $post_type ) {
			if ( ! in_array( $post_type, array( 'dtbaker_style', 'elementor_library', 'attachment' ), true ) ) {
                if(!isset($defaults[$post_type])){
                    $data = get_post_type_object($post_type);
	                $defaults[$post_type] = $data->labels->singular_name;
                }
			}
		}


		return $defaults;
    }

	/**
	 * Returns a list of all our configurable componente areas.
	 *
	 * @since 1.0.10
	 *
	 */
	public function get_component_regions(){
	    $defaults = array(
	        'post_summary' => 'Post Summary',
	        'post_single' => 'Post Single',
	        'page_single' => 'Page Single',
	        'search_result' => 'Search Result',
//	        'shop_catalog' => 'Shop Catalog',
//	        'shop_single' => 'Shop Single',
        );
		$post_types = get_post_types( array( 'public' => true ));
		foreach ( $post_types as $post_type ) {
			if ( ! in_array( $post_type, array( 'dtbaker_style', 'elementor_library', 'attachment' ), true ) ) {
				if(!isset($defaults[$post_type.'_single'])){
					$defaults[$post_type.'_single'] = ucwords(str_replace("_"," ",$post_type)) .' Single';
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


		if ( isset( $_POST['stylepress_advanced'] ) && is_array( $_POST['stylepress_advanced'] ) ) { // WPCS: sanitization ok. input var okay.
			update_post_meta( $post_id, 'stylepress_advanced', $_POST['stylepress_advanced'] ); // WPCS: sanitization ok. input var okay.
		}

		if ( isset( $_POST['dtbaker_is_component_check'] ) ){
			update_post_meta( $post_id, 'dtbaker_is_component', empty( $_POST['dtbaker_is_component'] ) ? 0 : 1 ); // WPCS: sanitization ok. input var okay.
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
            $allowed = array( 'remove_css' );
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
	 * Handles creating a new style
	 *
	 * @since 1.0.15
	 *
	 */
	public function dtbaker_elementor_create( ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['dtbaker_elementor_create_options'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['dtbaker_elementor_create_options'], 'dtbaker_elementor_create_options' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}


		if ( isset( $_POST['new_style_name'] ) && trim( $_POST['new_style_name'] ) ) { // WPCS: sanitization ok. input var okay.

            $new_style_id = wp_insert_post(array(
                'post_type' => 'dtbaker_style',
                'post_name' => trim( $_POST['new_style_name'] ),
                'post_title' => trim( $_POST['new_style_name'] ),
                'post_content' => '', // todo: default style layout here maybe?
                'post_status' => 'publish',
            ));
            if($new_style_id) {
	            wp_redirect( admin_url( 'admin.php?page=dtbaker-stylepress&style_id=' . $new_style_id . '&saved' ) );
	            exit;
            }
		}


		wp_redirect( admin_url('admin.php?page=dtbaker-stylepress&style_id=new') );
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

	    if(!$this->show_full_ui())return;
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
	public function add_elementor_overrides() {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		WP_Filesystem();
		global $wp_filesystem;
		$json = json_decode( $wp_filesystem->get_contents( trailingslashit( plugin_dir_path( __DIR__ ) ) . 'elementor.json' ), true );
		$json = apply_filters( 'stylepress_elementor_json', $json );
		$this->_apply_json_overrides( $json );
		$current_style = (int) $this->get_current_style();
		if( $current_style > 0 ){
		    // check if this one has a json elementor override
            $json = $this->get_style_elementor_overrides( $current_style );
			$json = apply_filters( 'dtbaker_elementor_style_json', $json, $current_style );
			$this->_apply_json_overrides( $json );
        }

		require_once DTBAKER_ELEMENTOR_PATH . 'extensions/skins/skins.php';


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
	 * @since 1.0.0
	 */
	public function theme_override_styles() {

		if(!$this->show_full_ui())return;
	    // do we remove theme styles for this current page type?
		// get all styles data
		$settings = $this->get_settings();
		$current_page_type = $this->get_current_page_type();
		/*global $post;
		if($post->ID && $post->ID) {
			$current_outer_style = $this->get_page_template( $post->ID );
		}else{
			$current_outer_style = !empty($settings['defaults'][$current_page_type]) ? $settings['defaults'][$current_page_type] : false;
        }*/
		$current_outer_style = $this->get_current_style();
		$current_inner_style = $this->get_current_inner_style();

        if( $current_outer_style != STYLEPRESS_OUTER_USE_THEME && $current_inner_style != STYLEPRESS_INNER_USE_THEME && !empty($settings['remove_css'][$current_page_type]) ){
            $this->removing_theme_css = true;
	        global $wp_styles;
	        $current_theme = wp_get_theme();
	        $remove_slugs = array();
	        $remove_slugs[$current_theme->get_stylesheet()] = true;
	        $remove_slugs[$current_theme->get_template()] = true;

            // don't remove these ones:
            $style_whitelist = apply_filters( 'stylepress-css-whitelist', array(
                'font-awesome',
            ) );

	        // loop over all of the registered scripts
	        foreach ( $wp_styles->registered as $handle => $data ) {
		        // remove it
                if($data && !empty($data->src) && !in_array( $handle, $style_whitelist ) ) {
	                foreach ( $remove_slugs as $remove_slug => $tf ) {
		                if ( strpos( $data->src, '/' . $remove_slug . '/' ) !== false ) {
			                wp_deregister_style( $handle );
			                wp_dequeue_style( $handle );
		                }
	                }
                }
	        }
	        wp_enqueue_style( 'stylepress-theme-overwrites', DTBAKER_ELEMENTOR_URI . 'assets/css/theme-overwrites.css', false, DTBAKER_ELEMENTOR_VERSION );
        }

	}

	/**
	 * Loads the compatibility with various popular themes.
	 *
	 * @since 1.0.16
	 */
	public function theme_compatibility() {

		if(!$this->show_full_ui())return;

		$theme    = get_option( 'template' );
        if($theme_name = strtolower(basename( $theme ))) {
	        $filename = DTBAKER_ELEMENTOR_PATH . 'themes/' . $theme_name . '/' . $theme_name . '.php';
	        if ( is_readable( $filename ) ) {
                require_once $filename;
	        }
        }
	}

	/**
	 * Load our default font configuration styles into the Easy Google Fonts plugin
	 *
	 * @since 1.0.2
	 */
	public function tt_font_get_settings_page_tabs( $options ) {

		if(!$this->show_full_ui())return $options;
	    // we have a tab for each style.
        $styles = $this->get_all_page_styles();
        foreach($styles as $style_id => $style_name){

            $post = get_post($style_id);
            if($post->post_parent || $post->post_type != 'dtbaker_style')continue;

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

		if(!$this->show_full_ui())return $options;

	    // we have a tab for each style.
        $styles = $this->get_all_page_styles();
        foreach($styles as $style_id => $style_name){

	        $post = get_post($style_id);
	        if($post->post_parent || $post->post_type != 'dtbaker_style')continue;

	        $json = $this->get_page_style_font_json($style_id);
	        $sizes = '100,100italic,200,200italic,300,300italic,400,400italic';

            if($json){
                foreach($json as $key=>$val){
                    $font_key = $style_id.$key;

                    if(empty($val['selector'])){
                        continue;
                    }

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
                        'default' => $val['defaults'],
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
                    );
	                $options[$font_key] = $new_font_style;
                }
            }
        }

	    return $options;
	}

	/**
	 * Inject additional CSS based on font selector attributes.
	 *
	 * @since 1.0.10
	 */
	public function inject_additional_font_css() {

	    $additional_css = '';
		if ( class_exists( 'EGF_Register_Options' ) && is_callable( 'EGF_Register_Options::get_options' ) ) {
			$font_options = EGF_Register_Options::get_options();
			$style_id     = (int) $this->get_current_style();
			if ( $style_id > 0 ) {
				$post = get_post( $style_id );
				if ( $post && ! $post->post_parent && $post->post_type === 'dtbaker_style' ) {
					$json = $this->get_page_style_font_json( $style_id );

					if ( $json ) {
						foreach ( $json as $key => $val ) {
							$font_key = $style_id . $key;

							if ( empty( $val['selector'] ) || empty( $val['inject_additional'] ) ) {
								continue;
							}

							foreach ( $val['inject_additional'] as $additional_selector => $additional_styles ) {

								$bits = explode( ',', $additional_selector );
								foreach ( $bits as $bit_id => $bit ) {
									$bit = trim( $bit );
									if ( strpos( $bit, 'body' ) === 0 ) {
										$bit = str_replace( 'body', 'body.dtbaker-elementor-style-' . (int) $style_id, $bit );
									} else {
										$bit = '.dtbaker-elementor-style-' . (int) $style_id . ' ' . $bit;
									}
									$bits[ $bit_id ] = $bit;
								}
								$additional_selector = implode( ', ', $bits );

								$additional_css .= "\n\n" . $additional_selector . '{';
								foreach($additional_styles as $additional_style){
								    if(!empty($font_options[$font_key][$additional_style])){
								        switch($additional_style){
                                            case 'font_color':
                                                $additional_css .= 'color: '.esc_attr($font_options[$font_key][$additional_style]).';';
                                                break;
                                            case 'font_size':
                                                $additional_css .= 'font-size: '.esc_attr($font_options[$font_key][$additional_style]['amount'].$font_options[$font_key][$additional_style]['unit']).';';
                                                break;
                                        }
                                    }
                                }
								$additional_css .= '}';

							}

						}
					}

					if($additional_css) {
						wp_add_inline_style( 'dtbaker-elementor-css', $additional_css );
					}
				}

			}
		}
	}

	/**
     * Returns advanced details for the current style.
     * These are CSS/Font/Elementor tweaks.
     *
	 * @param int $style_id Current post id of the selected style.
     *
     * @return array
	 */
	public function get_advanced( $style_id, $format = true ){

	    $advanced = get_post_meta( $style_id, 'stylepress_advanced', true );
	    if(!is_array($advanced)){
		    $advanced = array();
        }
        if(empty($advanced['css'])){
	        $advanced['css'] = '/* Add your StylePress CSS here */' . "\n\n";
        }
        if($format) {
	        if ( ! empty( $advanced['font'] ) ) {
		        $advanced['font'] = @json_decode( $advanced['font'], true );
	        }
	        if ( ! empty( $advanced['elementor'] ) ) {
		        $advanced['elementor'] = @json_decode( $advanced['elementor'], true );
	        }
        }
        return apply_filters( 'stylepress_style_advanced', $advanced, $style_id );

    }
	public function get_page_style_font_json($style_id){

		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		WP_Filesystem();
		global $wp_filesystem;
		$json = json_decode( $wp_filesystem->get_contents( trailingslashit( plugin_dir_path( __DIR__ ) ) . 'font.json' ), true );
		$json = apply_filters( 'stylepress_font_json', $json );
		if(!is_array($json))$json=array();

		// and we also want to do some custom stuyff here to match our elementor.json
        // in this case we're adding the custom footer style configuration.
        /*foreach(array('light','mid','dark') as $color){
	        $json['section_'.$color] = array(
	            "title" => ucwords($color). " Section",
                "selector" => '.stylepress-section-color-'.$color,
                'defaults' => array(),
            );
        }*/

	    $advanced = $this->get_advanced($style_id);
	    if($advanced && !empty($advanced['font']) && is_array($advanced['font'])){
            $json = array_merge($json,$advanced['font']);
        }
	    return $json;
    }
	public function get_style_elementor_overrides($style_id){
	    $advanced = $this->get_advanced($style_id);
	    if($advanced && !empty($advanced['elementor']) && is_array($advanced['elementor'])){
            return $advanced['elementor'];
        }
	    return array();
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

	    require_once DTBAKER_ELEMENTOR_PATH . 'inc/class.import-export.php';
	    $import_export = DtbakerElementorImportExport::get_instance();
	    $data          = $import_export->export_data( $post_id );

	    echo '<pre>'; print_r( $data ); echo '</pre>'; exit;

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
	    if(!isset($designs[$slug])){
		    wp_die( __( 'Sorry this style was not found to install.' ), __( 'Style Install Failed.' ), 403 );
        }

	    // hit up our server for a copy of this style.
	    $url      = 'https://styleserver.stylepress.org/wp-admin/admin-ajax.php';
	    $response = wp_remote_post(
		    $url,
		    array(
			    'body' => array(
				    'action' => 'stylepress_download',
				    'slug' => $slug,
				    'pay_nonce' => $designs[$slug]['pay_nonce'],
				    'plugin_version'   => DTBAKER_ELEMENTOR_VERSION,
				    'blog_url'   => get_site_url(),
			    ),
		    )
	    );

	    if ( ! is_wp_error( $response ) ) {
		    $api_response = json_decode( wp_remote_retrieve_body( $response ), true );
		    if ( $api_response && ! empty( $api_response['success'] ) && ! empty( $api_response['data'] ) ) {
			    $style_to_import = $api_response['data'];
			    require_once DTBAKER_ELEMENTOR_PATH . 'inc/class.import-export.php';
			    $import_export = DtbakerElementorImportExport::get_instance();
			    $result          = $import_export->import_data( $style_to_import );
			    wp_redirect(admin_url('admin.php?page=dtbaker-stylepress-settings&imported'));
		    }else if(isset($api_response['success']) && !$api_response['success']){
			    wp_die( sprintf( __( 'Failed to install style: %s ' ), $api_response['data']), __( 'Style Install Failed.' ), 403 );
            }
	    }else{
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

		$post_id = (int)$_GET['post_id'];

		$post = get_post( $post_id );

		/*
		 * if post data exists, create the post duplicate
		 */
		if ($post && 'dtbaker_style' === $post->post_type) {

		    if(!$post->post_parent){
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

			if($new_post_id) {
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

    public function payment_complete(){

	    if(!empty($_POST['payment']['payment_nonce']) && wp_verify_nonce($_POST['payment']['payment_nonce'],'payment_nonce')){
            if(!empty($_POST['server']['slug'])){
                // we've purchased this slug. store it in options array.
                $purchase = get_option('stylepress_purchases',array());
                if(!$purchase)$purchase = array();

	            if(!isset($purchase[$_POST['server']['slug']])) $purchase[$_POST['server']['slug']] = array();
	            $purchase[$_POST['server']['slug']][] = array(
	                'time' => time(),
                    'server' => $_POST['server'],
                );
	            update_option('stylepress_purchases',$purchase);
	            wp_send_json_success('Success');
            }
        }
        wp_send_json_error('Failed to record payment');

    }

    public function debug_message($message){

        if( DTBAKER_ELEMENTOR_DEBUG_OUTPUT && is_user_logged_in() ) {
            echo '<div class="stylepress-debug">';
            echo '<span>StylePress:</span> &nbsp; ';
            echo $message;
            echo "</div>";
        }
    }

}

