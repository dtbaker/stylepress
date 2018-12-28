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
		add_action( 'elementor/element/after_section_end', [ $this, 'after_section_end' ], 10, 3 );
		add_action( 'elementor/widget/print_template', [ $this, 'print_template' ], 10, 2 );
		add_action( 'elementor/widget/before_render_content', [ $this, 'before_render_content' ], 10, 1 );

		// Overwriting page css stuff:
		add_action( 'elementor/frontend/before_render', [ $this, 'before_render' ], 10, 1 );
		//		add_action( 'elementor/element/before_parse_css', [ $this, 'before_parse_css' ], 10, 2 );
		add_action( 'elementor/element/parse_css', [ $this, 'before_parse_css' ], 10, 2 );
	}

	/**
	 * @param $dynamic_css \Elementor\Core\DynamicTags\Dynamic_CSS
	 * @param $element \Elementor\Element_Base $element The element
	 */
	public function before_render( $element ) {
		//		echo "CSS end " . get_class( $element ) . "\n";
		//		print_r($element->get_settings());

	}

	public function is_editing_internal_style_page() {

		$is_style_template = false;
		$post              = get_post();
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

		return $is_style_template;

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

		static $completed_items = [];

		if ( ! isset( $completed_items[ $section->get_name() ] ) ) {

			$completed_items[ $section->get_name() ] = true;

			$section->start_controls_section(
				'stylepress_default_css',
				[
					'label' => __( 'StylePress Default Styles', 'elementor' ),
				]
			);

			// depending on what we're editing.

			if ( $this->is_editing_internal_style_page() ) {

				$section->add_control(
					'default_style_name',
					[
						'label'       => 'Default Style Name',
						'type'        => \Elementor\Controls_Manager::TEXT,
						'default'     => '',
						'label_block' => true,
					]
				);
			}

			$section->end_controls_section();
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
			$debug_text       = '<div class="stylepress-debug">';
			$debug_text       .= '<span>StylePress:</span> &nbsp; ';
			$debug_text       .= '<#
		if ( \'\' !== settings.default_style_name ) {
			print( \'This is the default style for: \' + settings.default_style_name );		
		}else{
			print( \'Warning: No default style name selected for the below element:\' );
		}
		#>
		';
			$template_content = $debug_text . $template_content;
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
			echo '<div class="stylepress-debug">';
			echo '<span>StylePress:</span> &nbsp; ';
			$settings = $widget->get_settings();
			if ( ! empty( $settings['default_style_name'] ) ) {
				echo 'This is the default style for: ' . esc_html( $settings['default_style_name'] );
			} else {
				echo 'Warning: No default style name selected for the below element:';
			}

			echo "</div>";
		}
	}

	/**
	 * @param $dynamic_css \Elementor\Core\DynamicTags\Dynamic_CSS
	 * @param $element \Elementor\Element_Base $element The element
	 */
	public function before_parse_css( $dynamic_css, $element ) {

		// todo: do this when we save one of our global styles:
		//		\Elementor\Plugin::$instance->files_manager->clear_cache();

		//		echo $element->get_name()."\n<br>";
		if ( $element->get_name() == 'heading' ) {
			$element_settings = $element->get_settings();
			$element->set_settings( '_background_background', 'classic' );
			$element->set_settings( '_background_color', '#FF0000' );
			$element_settings['_background_background'] = 'classic';
			$element_settings['_background_color']      = '#FF0000';
			$dynamic_css->add_controls_stack_style_rules(
				$element,
				$element->get_style_controls( null, $element_settings ),
				$element_settings,
				[
					'{{WRAPPER}}'
				],
				[
					".stylepress-heading1",
				]
			);
		}

		return;
	}
}