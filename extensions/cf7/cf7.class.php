<?php

namespace StylePress;

defined( 'STYLEPRESS_PATH' ) || exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // If this file is called directly, abort.

class Premium_Contactform extends Widget_Base {

	public function get_name() {
		return 'premium-contact-form';
	}

	public function get_title() {
		return  __('Contact Form7', 'premium-addons-for-elementor');
	}

	public function get_icon() {
		return 'pa-contact-form';
	}
	public function get_categories() {
		return array( 'premium-elements' );
	}

	// Adding the controls fields for the premium contact form
	// This will controls the animation, colors and background, dimensions etc
	protected function _register_controls() {


		$this->start_controls_section(
			'premium_section_wpcf7_form',
			[
				'label' => __( 'Contact Form', 'premium-addons-for-elementor' )
			]
		);


		$this->add_control(
			'premium_wpcf7_form',
			[
				'label' => __( 'Select Your Contact Form', 'premium-addons-for-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
				'options' => $this->premium_contact_form(),
			]
		);


		$this->end_controls_section();

		$this->start_controls_section('premium_wpcf7_fields',
			[
				'label'     => __('Fields', 'premium-addons-for-elementor'),
			]);

		$this->add_control('premium_wpcf7_fields_heading',
			[
				'label'     => __('Width', 'premium-addons-for-elementor'),
				'type'      => Controls_Manager::HEADING
			]);

		$this->add_responsive_control(
			'premium_elements_input_width',
			[
				'label' => __( 'Input Field', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1200,
					],
					'em' => [
						'min' => 1,
						'max' => 80,
					],
				],
				'default'   => [
					'size'  => 100,
					'unit'  => '%'
				],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-text' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'premium_elements_textarea_width',
			[
				'label' => __( 'Text Area', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1200,
					],
					'em' => [
						'min' => 1,
						'max' => 80,
					],
				],
				'default'   => [
					'size'  => 100,
					'unit'  => '%'
				],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container textarea.wpcf7-textarea' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control('premium_wpcf7_fields_height_heading',
			[
				'label'     => __('Height', 'premium-addons-for-elementor'),
				'type'      => Controls_Manager::HEADING
			]);

		$this->add_responsive_control(
			'premium_elements_input_height',
			[
				'label' => __( 'Input Field', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
					'em' => [
						'min' => 1,
						'max' => 40,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-text' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'premium_elements_textarea_height',
			[
				'label' => __( 'Text Area', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1200,
					],
					'em' => [
						'min' => 1,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container textarea.wpcf7-textarea' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section('premium_wpcf7_button',
			[
				'label'     => __('Button', 'premium-addons-for-elementor'),
			]);

		/*Button Width*/
		$this->add_responsive_control(
			'premium_elements_button_width',
			[
				'label' => __( 'Width', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1200,
					],
					'em' => [
						'min' => 1,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-submit' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		/*Button Height*/
		$this->add_responsive_control(
			'premium_elements_button_height',
			[
				'label' => __( 'Height', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
					'em' => [
						'min' => 1,
						'max' => 40,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-submit' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_contact_form_styles',
			[
				'label' => __( 'Form', 'premium-addons-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);
		$this->add_control(
			'premium_elements_input_background',
			[
				'label' => __( 'Input Field Background', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-text, {{WRAPPER}} .premium-elements-contact-form-container textarea.wpcf7-textarea' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'premium_elements_input_padding',
			[
				'label' => __( 'Padding', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-text, {{WRAPPER}} .premium-elements-contact-form-container textarea.wpcf7-textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'premium_elements_input_border',
				'selector' => '{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-text, {{WRAPPER}} .premium-elements-contact-form-container textarea.wpcf7-textarea',
			]
		);

		$this->add_responsive_control(
			'premium_elements_input_border_radius',
			[
				'label' => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-text, {{WRAPPER}} .premium-elements-contact-form-container textarea.wpcf7-textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'premium_elements_input_margin',
			[
				'label' => __( 'Margin', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-text, {{WRAPPER}} .premium-elements-contact-form-container textarea.wpcf7-textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'premium_elements_input_focus',
			[
				'label' => __( 'Focus Border Color', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-text:focus, {{WRAPPER}} .premium-elements-contact-form-container textarea.wpcf7-textarea:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'premium_elements_input_focus_border_animation',
			[
				'label' => __( 'Focus Border Animation', 'premium-addons-for-elementor' ),
				'type'	=> Controls_Manager::SELECT,
				'options'		=> array(
					'premium_border_animation1'		=> 'On',
					'premium_border_animation2'		=> 'Off',
				),
				'default'       => 'premium_border_animation2',
			]
		);

		$this->add_control(
			'premium_elements_input_focus_border_color',
			[
				'label' => __( 'Focus Line Color', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition'		=> [
					'premium_elements_input_focus_border_animation' => 'premium_border_animation1'
				],
				'selectors' => [
					'{{WRAPPER}} .premium_border_animation1 .wpcf7-span.is-focused::after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'input_button_shadow',
				'selector' => '{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-text, {{WRAPPER}} .premium-elements-contact-form-container textarea.wpcf7-textarea',
			]
		);


		$this->end_controls_section();


		$this->start_controls_section(
			'section_contact_form_typography',
			[
				'label' => __( 'Labels', 'premium-addons-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);


		$this->add_control(
			'premium_elements_heading_default',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Default Typography', 'premium-addons-for-elementor' ),
			]
		);

		$this->add_control(
			'premium_elements_contact_form_color',
			[
				'label' => __( 'Default Font Color', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container, {{WRAPPER}} .premium-elements-contact-form-container label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'premium_elements_contact_form_default_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-elements-contact-form-container',
			]
		);

		$this->add_control(
			'premium_elements_heading_input',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Input Typography', 'premium-addons-for-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'premium_elements_contact_form_field_color',
			[
				'label' => __( 'Input Text Color', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-text, {{WRAPPER}} .premium-elements-contact-form-container textarea.wpcf7-textarea' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'premium_elements_contact_form_field_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-text, {{WRAPPER}} .premium-elements-contact-form-container textarea.wpcf7-textarea',
			]
		);


		$this->add_control(
			'premium_elements_contact_form_placeholder_color',
			[
				'label' => __( 'Placeholder Color', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container ::-webkit-input-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-elements-contact-form-container ::-moz-placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-elements-contact-form-container ::-ms-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_contact_form_submit_button_styles',
			[
				'label' => __( 'Button', 'premium-addons-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'section_title_premium_btn_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-submit',
			]
		);

		$this->add_responsive_control(
			'section_title_premium_btn_padding',
			[
				'label' => __( 'Padding', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->start_controls_tabs( 'premium_elements_button_tabs' );

		$this->start_controls_tab( 'normal', [ 'label' => __( 'Normal', 'premium-addons-for-elementor' ) ] );

		$this->add_control(
			'premium_elements_button_text_color',
			[
				'label' => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-submit' => 'color: {{VALUE}};',
				],
			]
		);



		$this->add_control(
			'premium_elements_button_background_color',
			[
				'label' => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-submit' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'premium_elements_btn_border',
				'selector' => '{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-submit',
			]
		);

		$this->add_responsive_control(
			'premium_elements_btn_border_radius',
			[
				'label' => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-submit' => 'border-radius: {{SIZE}}px;',
				],
			]
		);



		$this->end_controls_tab();

		$this->start_controls_tab( 'premium_elements_hover', [ 'label' => __( 'Hover', 'premium-addons-for-elementor' ) ] );

		$this->add_control(
			'premium_elements_button_hover_text_color',
			[
				'label' => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-submit:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'premium_elements_button_hover_background_color',
			[
				'label' => __( 'Background Color', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-submit:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'premium_elements_button_hover_border_color',
			[
				'label' => __( 'Border Color', 'premium-addons-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-submit:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .premium-elements-contact-form-container input.wpcf7-submit',
			]
		);


		$this->end_controls_section();


	}

	protected function premium_contact_form( ) {

		if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
			return array();
		}

		$forms = \WPCF7_ContactForm::find( array(
			'orderby' => 'title',
			'order'   => 'ASC',
		) );

		if ( empty( $forms ) ) {
			return array();
		}

		$result = array();

		foreach ( $forms as $item ) {
			$key            = sprintf( '%1$s::%2$s', $item->id(), $item->title() );
			$result[ $key ] = $item->title();
		}

		return $result;
	}
	protected function render()  {

		$settings = $this->get_settings();


		if ( ! empty( $settings['premium_wpcf7_form'] ) ) {?>

			<div class="premium-elements-contact-form-container <?php echo $settings['premium_elements_input_focus_border_animation'] ?> ">

				<?php echo do_shortcode( '[contact-form-7 id="' . $settings['premium_wpcf7_form'] . '" ]' );?>
			</div>
			<script>
        jQuery(function($){
          var linput = jQuery( 'body input[type="text"], body input[type="email"], body textarea , body input[type="password"], body input[type="date"], body input[type="number"], body input[type="tel"], body input[type="file"], body input[type="url"]');
          linput.wrap("<span class='wpcf7-span'>")

          var linput = jQuery( 'body input[type="text"], body input[type="email"], body textarea , body input[type="password"], body input[type="date"], body input[type="number"], body input[type="tel"], body input[type="file"], body input[type="url"]');
          linput.on("focus blur", function(){
            $(this).closest(".wpcf7-span").toggleClass("is-focused");
          });
        });
			</script>
			<?php
		}

	}
}