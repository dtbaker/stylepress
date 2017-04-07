<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class StylePress_Google_Map extends Widget_Base {

	public function get_name() {
		return 'stylepress-google-map';
	}

	public function get_title() {
		return esc_html__( 'Styled Google Map', 'elementor' );
	}

	public function get_script_depends(){
	    return ['googlemaps'];
    }
	/**
	 * Get the current icon for display on frontend.
	 * The extra 'dtbaker-elementor-widget' class is styled differently in frontend.css
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'dtbaker-stylepress-elementor-widget';
	}


	/**
	 * Get available categories for this widget. Which is our own category for page builder options.
	 *
	 * @return array
	 */
	public function get_categories() {
		return [ 'dtbaker-elementor' ];
	}

	/**
	 * We always show this item in the panel.
	 *
	 * @return bool
	 */
	public function show_in_panel() {
		return true;
	}

	protected function _register_controls() {



		$this->start_controls_section(
			'section_stylepress_map',
			[
				'label' => __( 'Styled Google Map', 'stylepress' ),
			]
		);

		$this->add_control(
			'desc',
			[
				'label' => __( 'Enter your Google Map settings below.'  ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);



		$this->add_control(
			'address',
			[
				'label' => __( 'Address', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Sydney, Australia',
			]
		);

		$this->add_control(
			'height',
			[
				'label' => __( 'Map Height', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '400',
			]
		);


		$zooms =array();
		for($x=1;$x<=20;$x++){
		    $zooms[$x] = $x;
        }
		$this->add_control(
			'zoom',
			[
				'label'   => esc_html__( 'Map Zoom Level', 'stylepress' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '15',
				'options' => $zooms,
			]
		);


		$this->add_control(
			'enlarge_button',
			[
				'label'   => esc_html__( 'Show Buttons', 'stylepress' ),
				'type' => Controls_Manager::SELECT,
                'default' => 1,
                'options' => array(
                    1 => 'Yes',
                    0 => 'No',
                )
			]
		);


		$this->add_control(
			'innercontent',
			[
				'label'   => esc_html__( 'Map Popup Text', 'stylepress' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => '',
			]
		);



		$this->add_control(
			'style',
			[
				'label' => sprintf( __( 'Choose a style from <a href="%s" target="_blank">https://snazzymaps.com</a> and paste the "JavaScript Style Array" below..', 'stylepress' ), 'https://snazzymaps.com/' ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);


		$this->add_control(
			'mapstyle',
			[
				'label'   => esc_html__( 'JavaScript Style Array', 'stylepress' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => '',
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'section_stylepress_google_map_buttons',
			[
				'label' => __( 'Button Style', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_responsive_control(
			'menu_align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress_map_buttons' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_background',
			[
				'label' => __( 'Background', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f8f8f8',
				'selectors' => [
					'{{WRAPPER}} .stylepress_map_buttons a' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'menu_background_hover',
			[
				'label' => __( 'Background (hover)', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#eaeaea',
				'selectors' => [
					'{{WRAPPER}} .stylepress_map_buttons a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'font_color',
			[
				'label' => __( 'Font Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .stylepress_map_buttons a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'font_color_hover',
			[
				'label' => __( 'Font Color (Hover)', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .stylepress_map_buttons a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .stylepress_map_buttons a',
			]
		);

		$this->end_controls_section();


		do_action( 'dtbaker_wp_menu_elementor_controls', $this );
	}

	protected function render() {

		$instance = $this->get_settings();

        $shortcode = '[stylepress_google_map ';
        foreach(\stylepress_dtbaker_Shortcode_Google_Map::get_instance()->fields as $field){
            $value = isset($instance[$field['name']]) ? $instance[$field['name']] : $field['default'];
            if($field['name'] == 'mapstyle'){
                $value = str_replace('[','&#91;',$value);
                $value = str_replace(']','&#93;',$value);
	            $value = preg_replace('#\s+#',' ', $value );
            }else if($field['name'] == 'innercontent'){

            }
	        $shortcode .= ' '.$field['name'].'="' . esc_attr( $value ) . '" ';

        }
        $shortcode .= ']';
//        echo str_replace('[sty','',$shortcode);
        $html = do_shortcode($shortcode);

        echo apply_filters('stylepress_google_map_elementor_render', $html, $instance);

	}

	protected function content_template() {
		?>
		<div class="elementor-dtbaker-google-map-wrapper elementor-google-map">
			The Google Map Will Appear Here
		</div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new StylePress_Google_Map() );