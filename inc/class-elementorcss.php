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
 * Class Styles
 */
class ElementorCSS extends Base {

	public function __construct() {

		// Default styling stuff:
		//		add_action( 'elementor/element/after_section_end', [ $this, 'after_section_end' ], 10, 3 );
		add_action( 'elementor/element/before_section_start', [ $this, 'after_section_end' ], 10, 3 );
		add_action( 'elementor/section/print_template', [ $this, 'print_template' ], 10, 2 );
		add_action( 'elementor/widget/print_template', [ $this, 'print_template' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render_content' ], 10, 1 );
		add_action( 'elementor/widget/before_render_content', [ $this, 'before_render_content' ], 10, 1 );
	}


	public function is_editing_internal_style_page() {

		$is_style_template = false;
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$post = get_post();
			if ( $post->post_type === Styles::CPT ) {
				$post_categories = get_the_terms( $post->ID, STYLEPRESS_SLUG . '-cat' );
				$categories      = Styles::get_instance()->get_categories();
				foreach ( $categories as $category ) {
					foreach ( $post_categories as $post_category ) {
						if ( $post_category->slug === $category['slug'] && ! empty( $category['page_style'] ) ) {
							$is_style_template = true;
						}
					}
				}
			}
		}

		return $is_style_template;

	}


	public function is_editing_internal_content_page() {

		$is_inner_content_page = false;
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$post = get_post();
			if ( $post->post_type === Styles::CPT ) {
				$post_categories = get_the_terms( $post->ID, STYLEPRESS_SLUG . '-cat' );
				$categories      = Styles::get_instance()->get_categories();
				foreach ( $categories as $category ) {
					foreach ( $post_categories as $post_category ) {
						if ( $post_category->slug === $category['slug'] && ! empty( $category['inner'] ) ) {
							$is_inner_content_page = true;
						}
					}
				}
			}
		}

		return $is_inner_content_page;

	}

	/**
	 * Adds our default styles selector to the styles page.
	 *
	 * @since 2.0.0
	 *
	 * @param $section
	 * @param $section_id
	 * @param $args
	 */
	public function after_section_end( $section, $section_id, $args ) {

		$which_tab_to_add_to = 'style';
		if( $section->get_name() === 'divider'){
			$which_tab_to_add_to = 'content';
			if(empty($args['tab'])){
				$args['tab'] = 'content';
			}
		}

		if ( ! $args || empty( $args['tab'] ) || $args['tab'] !== $which_tab_to_add_to ) {
			return;
		}
		static $completed_items = [];

		$widget_name = $section->get_name();
		if ( $widget_name == 'column' ) {
			return;
		}
		if ( ! isset( $completed_items[ $widget_name ] ) ) {

			$completed_items[ $widget_name ] = true;

			if ( $this->is_editing_internal_style_page() ) {
				$section->start_controls_section(
					'stylepress_default_css',
					[
						'label' => __( 'StylePress Default Styles', 'stylepress' ),
						'tab'   => $which_tab_to_add_to,
					]
				);

				$section->add_control(
					'stylepress_default_description',
					[
						'raw'             => __( 'Choose a name for this style. You will be able to select this style when building your pages. If you name this style "default" then it will be selected by default for new page elements.', 'stylepress' ),
						'type'            => \Elementor\Controls_Manager::RAW_HTML,
						'content_classes' => 'elementor-descriptor',
					]
				);

				$section->add_control(
					'default_style_name',
					[
						'label'       => 'Default Style Name',
						'type'        => \Elementor\Controls_Manager::TEXT,
						'default'     => '',
						'label_block' => true,
					]
				);

				$section->end_controls_section();
			} else {

				Plugin::get_instance()->populate_globals();

				$default_style_post_ids = [];

				$categories = Styles::get_instance()->get_categories();

				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
					if ( empty( $GLOBALS['stylepress_render']['styles'] ) ) {
						$post = get_post();
						if ( $post->post_type === Styles::CPT ) {
							foreach ( $categories as $category ) {
								if ( ! empty( $category['page_style'] ) ) {
									$styles = Styles::get_instance()->get_all_styles( $category['slug'] );
									foreach ( $styles as $style_id => $style_name ) {
										$default_style_post_ids[] = $style_id;
									}
								}
							}
						}
					}
				}

				if ( ! empty( $GLOBALS['stylepress_render'] ) && ! empty( $GLOBALS['stylepress_render']['styles'] ) ) {
					foreach ( $GLOBALS['stylepress_render']['styles'] as $category_slug => $category_post_id ) {
						foreach ( $categories as $category ) {
							if ( ! empty( $category['page_style'] ) && $category['slug'] === $category_slug ) {
								$default_style_post_ids[] = $category_post_id;
							}
						}
					}
				}

				// Now that we've got our default style post ids, we want to grab the names of any defined styles from that post.
				$defined_style_names = [];
				foreach ( $default_style_post_ids as $default_style_post_id ) {

					$document = \Elementor\Plugin::$instance->documents->get( $default_style_post_id );
					if ( ! $document ) {
						continue;
					}
					$data = $document->get_elements_data();
					if ( empty( $data ) ) {
						continue;
					}

					\Elementor\Plugin::$instance->db->iterate_data( $data, function ( $element ) use ( $widget_name, & $defined_style_names ) {

						if ( ! empty( $element['elType'] ) && $element['elType'] === $widget_name && ! empty( $element['settings']['default_style_name'] ) ) {
							$defined_style_names[] = $element['settings']['default_style_name'];
						}
						if ( ! empty( $element['widgetType'] ) && $element['widgetType'] === $widget_name && ! empty( $element['settings']['default_style_name'] ) ) {
							$defined_style_names[] = $element['settings']['default_style_name'];
						}
					} );

				}
				if ( $defined_style_names ) {
					$section->start_controls_section(
						'stylepress_default_css',
						[
							'label' => __( 'StylePress Default Styles', 'stylepress' ),
							'tab'   => $which_tab_to_add_to,
						]
					);

					$section->add_control(
						'stylepress_default_description',
						[
							'raw'             => __( 'Choose which default style to use on this element. Default styles can be chosen from the StylePress WordPress menu.', 'stylepress' ),
							'type'            => \Elementor\Controls_Manager::RAW_HTML,
							'content_classes' => 'elementor-descriptor',
						]
					);

					$options           = [
						'' => 'No Default Styles'
					];
					$default_selection = '';
					foreach ( $defined_style_names as $defined_style_name ) {
						if ( ! $default_selection && strtolower( $defined_style_name ) == 'default' ) {
							$default_selection = $this->sanitise_class_name( $defined_style_name, $widget_name );
						}
						$options[ $this->sanitise_class_name( $defined_style_name, $widget_name ) ] = $defined_style_name;
					}
					// find out which style has been applied to this current page view.
					$section->add_control(
						'default_style_name',
						[
							'label'       => 'Choose Default Style',
							'type'        => \Elementor\Controls_Manager::SELECT,
							'options'     => $options,
							'default'     => $default_selection,
							'label_block' => true,
						]
					);
					$section->end_controls_section();
				}
			}
		}
	}

	/**
	 * Outputs our helper text before the default css.
	 *
	 * @since 2.0.0
	 *
	 * @param $template_content
	 * @param $widget
	 */
	public function print_template( $template_content, $widget ) {

		// $template_content = apply_filters( "elementor/{$element_type}/print_template", $template_content, $this );

		if ( $this->is_editing_internal_style_page() && $template_content ) {
			$debug_text       = '<div class="stylepress-inline-style">';
			$debug_text       .= '<div class="stylepress-debug">';
			$debug_text       .= '<span>StylePress:</span> &nbsp; ';
			$debug_text       .= '<#
		if ( \'\' !== settings.default_style_name ) {
			print( \'This ' . $widget->get_name() . ' style is called: <strong>\' + settings.default_style_name + \'</strong>\' );		
			if ( \'default\' == settings.default_style_name ) {
				print( \'. This style will be used for every new ' . $widget->get_name() . ' added to the page.\' );
			}
		}else{
			print( \'Warning: This ' . $widget->get_name() . ' element does not have a default style name. If you want this style to be available please enter a name.\' );
		}
		#>
		</div>
		</div>
		';
			$template_content = $debug_text . $template_content;
		} else {
			$template_content = "<# view.\$el.removeClass (function (index, className) { return (className.match (/(^|\s)stylepress-\S+/g) || []).join(' '); });
			if ( '' !== settings.default_style_name ) { view.\$el.addClass(settings.default_style_name); } #> " . $template_content;
		}

		return $template_content;
	}

	/**
	 * Outputs our helper text before the default css.
	 *
	 * @since 2.0.0
	 *
	 * @param $widget
	 */
	public function before_render_content( $widget ) {
		if ( $this->is_editing_internal_style_page() ) {
			echo '<div class="stylepress-inline-style">';
			echo '<div class="stylepress-debug">';
			echo '<span>StylePress:</span> &nbsp; ';
			$settings = $widget->get_settings();
			if ( ! empty( $settings['default_style_name'] ) ) {
				echo 'This ' . $widget->get_name() . ' style is called: <strong>' . esc_html( $settings['default_style_name'] ) . '</strong>';
				if ( $settings['default_style_name'] == 'default' ) {
					echo '. This style will be used for every new ' . $widget->get_name() . ' added to the page.';
				}
			} else {
				echo 'Warning: This ' . $widget->get_name() . ' element does not have a default style name. If you want this style to be available please enter a name.';
			}
			echo "</div>";
			echo "</div>";
		} else {
			$settings = $widget->get_settings();
			if ( ! empty( $settings['default_style_name'] ) ) {
				$widget->add_render_attribute( '_wrapper', 'class', [
						$settings['default_style_name'],
					]
				);
			}
		}
	}

	/**
	 * Outputs our helper text before the default css.
	 *
	 * @since 2.0.0
	 *
	 * @param $widget
	 */
	public function sanitise_class_name( $default_style_name, $widget_name ) {
		return 'stylepress-' . $widget_name . '-' . strtolower( preg_replace( '#[^a-zA-Z0-9_-]#', '', $default_style_name ) );
	}


	/**
	 * Outputs our helper text before the default css.
	 *
	 * @since 2.0.0
	 *
	 * @param $post
	 */
	public function render_css_header( $post ) {

		$css = new \Elementor\Core\Files\CSS\Post( $post->ID );

		$document = \Elementor\Plugin::$instance->documents->get( $post->ID );
		if ( ! $document ) {
			return;
		}
		$data = $document->get_elements_data();
		if ( empty( $data ) ) {
			return;
		}

		$css_contents = $css->get_content();
		$css_contents = str_replace( '.elementor-' . $post->ID . ' ', ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ? '#elementor' : '' ) . '.elementor-' . get_the_ID() . ' ', $css_contents );
		if ( ! empty( $data ) ) {
			\Elementor\Plugin::$instance->db->iterate_data( $data, function ( $element ) use ( &$css_contents ) {
				if ( ! empty( $element['settings'] ) && ! empty( $element['settings']['default_style_name'] ) && ! empty( $element['widgetType'] ) ) {
					$css_contents = str_replace( '.elementor-element.elementor-element-' . $element['id'], '.elementor-element.' . $this->sanitise_class_name( $element['settings']['default_style_name'], $element['widgetType'] ), $css_contents );
				}
				if ( ! empty( $element['settings'] ) && ! empty( $element['settings']['default_style_name'] ) && ! empty( $element['elType'] ) ) {
					$css_contents = str_replace( '.elementor-element.elementor-element-' . $element['id'], '.elementor-element.' . $this->sanitise_class_name( $element['settings']['default_style_name'], $element['elType'] ), $css_contents );
				}
			} );
		}
		echo '<style>' . $css_contents . '</style>'; // XSS ok.

		\Elementor\Plugin::$instance->frontend->print_fonts_links();

	}
}