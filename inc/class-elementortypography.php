<?php

namespace StylePress;

use Elementor\Core\Base\Module;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Core\Settings\Manager;

defined( 'STYLEPRESS_VERSION' ) || exit;

class ElementorTypography extends Base {

	public function __construct() {
		add_action( 'elementor/element/after_section_end', [ $this, 'register_link_typography' ], 10, 2 );
		add_action( 'elementor/element/after_section_end', [ $this, 'register_body_and_paragraph_typography' ], 10, 2 );
		add_action( 'elementor/element/after_section_end', [ $this, 'register_heading_typography' ], 10, 2 );
		add_action( 'elementor/element/after_section_end', [ $this, 'register_typography_sizes' ], 10, 2 );
		add_action( 'elementor/element/after_section_end', [ $this, 'register_text_sizes' ], 10, 2 );
		add_action( 'elementor/element/after_section_end', [ $this, 'register_columns_gap' ], 10, 2 );
//		add_action( 'elementor/element/after_section_end', [ $this, 'register_styling_settings' ], -9999, 2 );
//		add_action( 'elementor/element/after_section_end', [ $this, 'register_tools' ], 10, 2 );

		add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_preview_scripts' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ], 999 );

		add_action( 'elementor/element/before_section_end', [ $this, 'update_padding_control_selector' ], 10, 2 );

	}

	/**
	 * Update selector for padding, so it doesn't conflict with column gaps.
	 *
	 * @param Controls_Stack $control_stack Control Stack.
	 * @param array          $args Arguments.
	 */
	public function update_padding_control_selector( Controls_Stack $control_stack, $args ) {
		$control = $control_stack->get_controls( 'padding' );

		// Exit early if $control_stack dont have the image_size control.
		if ( empty( $control ) || ! is_array( $control ) ) {
			return;
		}

		if ( 'section_advanced' === $control['section'] ) {
			if ( isset( $control['selectors']['{{WRAPPER}} > .elementor-element-populated'] ) ) {
				$control['selectors'] = [
					'{{WRAPPER}} > .elementor-element-populated.elementor-element-populated' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				];

				$control_stack->update_control( 'padding', $control );
			}
		}
	}

	/**
	 * Get public name for control.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'agwp-controls';
	}

	/**
	 * Register Heading typography controls.
	 *
	 * @param Controls_Stack $element Controls object.
	 * @param string         $section_id Section ID.
	 */
	public function register_heading_typography( Controls_Stack $element, $section_id ) {
		if ( 'section_page_style' !== $section_id ) {
			return;
		}

		$element->start_controls_section(
			'stylepress_headings_typography',
			[
				'label' => __( 'Headings Typography', 'stylepress' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'stylepress_headings_typography_description',
			[
				'raw'             => __( 'These settings apply to all Headings in your layout. You can still override individual values at each element.', 'stylepress' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$default_fonts = Manager::get_settings_managers( 'general' )->get_model()->get_settings( 'elementor_default_generic_fonts' );

		if ( $default_fonts ) {
			$default_fonts = ', ' . $default_fonts;
		}

		$element->add_control(
			'stylepress_default_heading_font_family',
			[
				'label'     => __( 'Default Headings Font', 'stylepress' ),
				'type'      => Controls_Manager::FONT,
				'default'   => $this->get_default_value( 'stylepress_default_heading_font_family' ),
				'selectors' => [
					'h1, h2, h3, h4, h5, h6' => 'font-family: "{{VALUE}}"' . $default_fonts . ';',
				],
			]
		);

		for ( $i = 1; $i < 7; $i++ ) {
			$element->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'           => 'stylepress_heading_' . $i,
					/* translators: %s: Heading 1-6 type */
					'label'          => sprintf( __( 'Heading %s', 'stylepress' ), $i ),
					'selector'       => "body h{$i}, body .elementor-widget-heading h{$i}.elementor-heading-title",
					'scheme'         => Scheme_Typography::TYPOGRAPHY_1,
					'fields_options' => $this->get_default_typography_values( 'stylepress_heading_' . $i ),
				]
			);
		}

		$element->end_controls_section();
	}

	/**
	 * Register Body and Paragraph typography controls.
	 *
	 * @param Controls_Stack $element Controls object.
	 * @param string         $section_id Section ID.
	 */
	public function register_link_typography( Controls_Stack $element, $section_id ) {
		if ( 'section_page_style' !== $section_id ) {
			return;
		}

		$element->start_controls_section(
			'stylepress_link_typography_section',
			[
				'label' => __( 'Link Styles', 'stylepress' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		foreach([
			['Normal','normal',''],
			['Hover','hover',':hover'],
			['Active','active',':active'],
		] as $link_type) {
			$element->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'           => 'stylepress_link_' . $link_type[1],
					'label'          => __( $link_type[0] .' Link Typography', 'stylepress' ),
					'selector'       => '.elementor-text-editor a, .stylepress__preview-a-' . $link_type[1],
					'scheme'         => Scheme_Typography::TYPOGRAPHY_3,
					'fields_options' => $this->get_default_typography_values( 'stylepress_link_' . $link_type[1] ),
				]
			);

			$element->add_control(
				'stylepress_link_' . $link_type[1] .'_color',
				[
					'label'     => __( $link_type[0] .' Link Color', 'stylepress' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'.elementor-text-editor a, .stylepress__preview-a-' . $link_type[1] . $link_type[2] => 'color: {{VALUE}};',
					],
				]
			);
		}

		$element->end_controls_section();
	}

	/**
	 * Register Body and Paragraph typography controls.
	 *
	 * @param Controls_Stack $element Controls object.
	 * @param string         $section_id Section ID.
	 */
	public function register_body_and_paragraph_typography( Controls_Stack $element, $section_id ) {
		if ( 'section_page_style' !== $section_id ) {
			return;
		}

		$element->start_controls_section(
			'stylepress_body_and_paragraph_typography',
			[
				'label' => __( 'Body Typography', 'stylepress' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'stylepress_body',
				'label'          => __( 'Body Typography', 'stylepress' ),
				'selector'       => 'body',
				'scheme'         => Scheme_Typography::TYPOGRAPHY_3,
				'fields_options' => $this->get_default_typography_values( 'stylepress_body' ),
			]
		);

		$element->end_controls_section();
	}

	/**
	 * Register typography sizes controls.
	 *
	 * @param Controls_Stack $element Controls object.
	 * @param string         $section_id Section ID.
	 */
	public function register_typography_sizes( Controls_Stack $element, $section_id ) {
		if ( 'section_page_style' !== $section_id ) {
			return;
		}

		$element->start_controls_section(
			'stylepress_typography_sizes',
			[
				'label' => __( 'Heading Sizes', 'stylepress' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$settings = [
			[ 'small', __( 'Small', 'stylepress' ), 15 ],
			[ 'medium', __( 'Medium', 'stylepress' ), 19 ],
			[ 'large', __( 'Large', 'stylepress' ), 29 ],
			[ 'xl', __( 'XL', 'stylepress' ), 39 ],
			[ 'xxl', __( 'XXL', 'stylepress' ), 59 ],
		];

		foreach ( $settings as $setting ) {
			$element->add_control(
				'toggle_heading_size_' . $setting[0],
				[
					'label'        => $setting[1],
					'type'         => Controls_Manager::POPOVER_TOGGLE,
					'return_value' => 'yes',
				]
			);

			$element->start_popover();

			$element->add_responsive_control(
				'stylepress_size_' . $setting[0],
				[
					'label'           => __( 'Font Size', 'stylepress' ),
					'type'            => Controls_Manager::SLIDER,
					'desktop_default' => $this->get_default_value( 'stylepress_size_' . $setting[0], true ),
					'tablet_default'  => $this->get_default_value( 'stylepress_size_' . $setting[0] . '_tablet', true ),
					'mobile_default'  => $this->get_default_value( 'stylepress_size_' . $setting[0] . '_mobile', true ),
					'size_units'      => [ 'px', 'em', 'rem', 'vw' ],
					'range'           => [
						'px' => [
							'min' => 1,
							'max' => 200,
						],
						'vw' => [
							'min'  => 0.1,
							'max'  => 10,
							'step' => 0.1,
						],
					],
					'responsive'      => true,
					'selectors'       => [
						"body .elementor-widget-heading h1.elementor-heading-title.elementor-size-{$setting[0]}," .
						"body .elementor-widget-heading h2.elementor-heading-title.elementor-size-{$setting[0]}," .
						"body .elementor-widget-heading h3.elementor-heading-title.elementor-size-{$setting[0]}," .
						"body .elementor-widget-heading h4.elementor-heading-title.elementor-size-{$setting[0]}," .
						"body .elementor-widget-heading h5.elementor-heading-title.elementor-size-{$setting[0]}," .
						"body .elementor-widget-heading h6.elementor-heading-title.elementor-size-{$setting[0]}"
						=> 'font-size: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$element->add_responsive_control(
				'stylepress_heading_size_lh_' . $setting[0],
				[
					'label'      => __( 'Line Height', 'stylepress' ),
					'type'       => Controls_Manager::SLIDER,
					'responsive' => true,
					'size_units' => [ 'px', 'em' ],
					'range'      => [
						'px' => [
							'min' => 1,
							'max' => 200,
						],
					],
					'selectors'  => [
						"body .elementor-widget-heading h1.elementor-heading-title.elementor-size-{$setting[0]}," .
						"body .elementor-widget-heading h2.elementor-heading-title.elementor-size-{$setting[0]}," .
						"body .elementor-widget-heading h3.elementor-heading-title.elementor-size-{$setting[0]}," .
						"body .elementor-widget-heading h4.elementor-heading-title.elementor-size-{$setting[0]}," .
						"body .elementor-widget-heading h5.elementor-heading-title.elementor-size-{$setting[0]}," .
						"body .elementor-widget-heading h6.elementor-heading-title.elementor-size-{$setting[0]}"
						=> 'line-height: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$element->end_popover();
		}

		$element->end_controls_section();
	}

	/**
	 * Register text sizes controls.
	 *
	 * @param Controls_Stack $element Controls object.
	 * @param string         $section_id Section ID.
	 */
	public function register_text_sizes( Controls_Stack $element, $section_id ) {
		if ( 'section_page_style' !== $section_id ) {
			return;
		}

		$element->start_controls_section(
			'stylepress_text_sizes',
			[
				'label' => __( 'Text Sizes', 'stylepress' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$settings = [
			[ 'small', __( 'Small', 'stylepress' ), 15 ],
			[ 'medium', __( 'Medium', 'stylepress' ), 19 ],
			[ 'large', __( 'Large', 'stylepress' ), 29 ],
			[ 'xl', __( 'XL', 'stylepress' ), 39 ],
			[ 'xxl', __( 'XXL', 'stylepress' ), 59 ],
		];

		foreach ( $settings as $setting ) {
			$element->add_control(
				'toggle_text_size' . $setting[0],
				[
					'label'        => $setting[1],
					'type'         => Controls_Manager::POPOVER_TOGGLE,
					'return_value' => 'yes',
				]
			);

			$element->start_popover();

			$element->add_responsive_control(
				'stylepress_text_size_' . $setting[0],
				[
					'label'           => __( 'Font Size', 'stylepress' ),
					'type'            => Controls_Manager::SLIDER,
					'desktop_default' => $this->get_default_value( 'stylepress_size_' . $setting[0], true ),
					'tablet_default'  => $this->get_default_value( 'stylepress_size_' . $setting[0] . '_tablet', true ),
					'mobile_default'  => $this->get_default_value( 'stylepress_size_' . $setting[0] . '_mobile', true ),
					'size_units'      => [ 'px', 'em', 'rem', 'vw' ],
					'range'           => [
						'px' => [
							'min' => 1,
							'max' => 200,
						],
						'vw' => [
							'min'  => 0.1,
							'max'  => 10,
							'step' => 0.1,
						],
					],
					'responsive'      => true,
					'selectors'       => [
						"body .elementor-widget-heading .elementor-heading-title.elementor-size-{$setting[0]}:not(h1):not(h2):not(h3):not(h4):not(h5):not(h6)"
						=> 'font-size: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$element->add_responsive_control(
				'stylepress_text_size_lh_' . $setting[0],
				[
					'label'      => __( 'Line Height', 'stylepress' ),
					'type'       => Controls_Manager::SLIDER,
					'responsive' => true,
					'size_units' => [ 'px', 'em' ],
					'range'      => [
						'px' => [
							'min' => 1,
							'max' => 200,
						],
					],
					'selectors'  => [
						"body .elementor-widget-heading .elementor-heading-title.elementor-size-{$setting[0]}:not(h1):not(h2):not(h3):not(h4):not(h5):not(h6)"
						=> 'line-height: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$element->end_popover();
		}

		$element->end_controls_section();
	}

	/**
	 * Register Columns gaps controls.
	 *
	 * @param Controls_Stack $element Controls object.
	 * @param string         $section_id Section ID.
	 */
	public function register_columns_gap( Controls_Stack $element, $section_id ) {
		if ( 'section_page_style' !== $section_id ) {
			return;
		}

		$gaps = [
			'default'  => __( 'Default Padding', 'stylepress' ),
			'narrow'   => __( 'Narrow Padding', 'stylepress' ),
			'extended' => __( 'Extended Padding', 'stylepress' ),
			'wide'     => __( 'Wide Padding', 'stylepress' ),
			'wider'    => __( 'Wider Padding', 'stylepress' ),
		];

		$element->start_controls_section(
			'stylepress_column_gaps',
			[
				'label' => __( 'Column Gaps', 'stylepress' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'stylepress_column_gaps_description',
			[
				'raw'             => __( 'Set the default values of the column gaps. Based on Elementor&apos;s default sizes.', 'stylepress' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		foreach ( $gaps as $key => $label ) {
			$element->add_responsive_control(
				'stylepress_column_gap_' . $key,
				[
					'label'           => $label,
					'type'            => Controls_Manager::DIMENSIONS,
					'desktop_default' => $this->get_default_value( 'stylepress_column_gap_' . $key, true ),
					'tablet_default'  => $this->get_default_value( 'stylepress_column_gap_' . $key . '_tablet', true ),
					'mobile_default'  => $this->get_default_value( 'stylepress_column_gap_' . $key . '_mobile', true ),
					'size_units'      => [ 'px', 'em', '%' ],
					'selectors'       => [
						"body .elementor-column-gap-{$key} > .elementor-row > .elementor-column > .elementor-element-populated"
						=> 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);
		}

		$element->end_controls_section();
	}


	/**
	 * Enqueue Google fonts.
	 *
	 * @return void
	 */
	public function enqueue_preview_scripts() {
		$post_id = get_the_ID();

		// Get the page settings manager.
		$page_settings_manager = Manager::get_settings_managers( 'page' );
		$page_settings_model   = $page_settings_manager->get_model( $post_id );

		$keys = apply_filters(
			'analog/elementor/typography/keys',
			[
				'stylepress_heading_1',
				'stylepress_heading_2',
				'stylepress_heading_3',
				'stylepress_heading_4',
				'stylepress_heading_5',
				'stylepress_heading_6',
				'stylepress_default_heading',
				'stylepress_body',
				'stylepress_paragraph',
			]
		);

		$font_families = [];

		foreach ( $keys as $key ) {
			$font_families[] = $page_settings_model->get_settings( $key . '_font_family' );
		}

		// Remove duplicate and null values.
		$font_families = \array_unique( \array_filter( $font_families ) );

		if ( count( $font_families ) ) {
			wp_enqueue_style(
				'stylepress_typography_fonts',
				'https://fonts.googleapis.com/css?family=' . implode( ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic|', $font_families ),
				[],
				get_the_modified_time( 'U', $post_id )
			);
		}
	}

	/**
	 * Enqueue preview script.
	 *
	 * @return void
	 */
	public function enqueue_editor_scripts() {
		return;
		$script_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script(
			'stylepress_typography_script',
			stylepress_PLUGIN_URL . "inc/elementor/js/ang-typography{$script_suffix}.js",
			[
				'jquery',
				'editor',
			],
			stylepress_VERSION,
			true
		);
	}

	/**
	 * Get default value for specific control.
	 *
	 * @param string $key Setting ID.
	 * @param bool   $is_array Whether provided key includes set of array.
	 *
	 * @return array|string
	 */
	public function get_default_value( $key, $is_array = false ) {

		$recently_imported = get_post_meta( get_the_ID(), '_elementor_page_settings', true );
		return ( $is_array ) ? [] : '';
	}

	/**
	 * Get default values for Typography group control.
	 *
	 * @param string $key Setting ID.
	 *
	 * @return array
	 */
	public function get_default_typography_values( $key ) {

		$recently_imported = get_post_meta( get_the_ID(), '_elementor_page_settings', true );
		return [
			'typography'            => [
				'default' => $this->get_default_value( $key . '_typography', true ),
			],
			'font_size'             => [
				'default' => $this->get_default_value( $key . '_font_size', true ),
			],
			'font_size_tablet'      => [
				'default' => $this->get_default_value( $key . '_font_size_tablet', true ),
			],
			'font_size_mobile'      => [
				'default' => $this->get_default_value( $key . '_font_size_mobile', true ),
			],
			'line_height'           => [
				'default' => $this->get_default_value( $key . '_line_height', true ),
			],
			'line_height_mobile'    => [
				'default' => $this->get_default_value( $key . '_line_height_mobile', true ),
			],
			'line_height_tablet'    => [
				'default' => $this->get_default_value( $key . '_line_height_tablet', true ),
			],
			'letter_spacing'        => [
				'default' => $this->get_default_value( $key . '_letter_spacing', true ),
			],
			'letter_spacing_mobile' => [
				'default' => $this->get_default_value( $key . '_letter_spacing_mobile', true ),
			],
			'letter_spacing_tablet' => [
				'default' => $this->get_default_value( $key . '_letter_spacing_tablet', true ),
			],
			'font_family'           => [
				'default' => $this->get_default_value( $key . '_font_family', true ),
			],
			'font_weight'           => [
				'default' => $this->get_default_value( $key . '_font_weight', true ),
			],
			'text_transform'        => [
				'default' => $this->get_default_value( $key . '_text_transform', true ),
			],
			'font_style'            => [
				'default' => $this->get_default_value( $key . '_font_style', true ),
			],
			'text_decoration'       => [
				'default' => $this->get_default_value( $key . '_text_decoration', true ),
			],
		];
	}

	/**
	 * Return text formatter for displaying tooltip.
	 *
	 * @param string $text Tooltip Text.
	 *
	 * @return string
	 */
	public function get_tooltip( $text ) {
		return ' <span class="hint--top-right hint--medium" aria-label="' . $text . '"><i class="fa fa-info-circle"></i></span>';
	}

}
