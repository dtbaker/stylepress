<?php


defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


add_action( 'wp_enqueue_scripts', function(){
	wp_enqueue_style( 'stylepress-tooltips', DTBAKER_ELEMENTOR_URI . 'extensions/tooltip/tooltip.css' );
	wp_enqueue_script( 'stylepress-tooltips', DTBAKER_ELEMENTOR_URI . 'extensions/tooltip/tlight.js' );
} );




global $supported_tooltip_widgets;
$supported_tooltip_widgets = array(
	/*'image' => array(
		'section' => 'section_image',
	),
	'heading' => array(
		'section' => 'section_title',
	),*/
	'button' => array(
		'section' => 'section_button',
	),
	/*'icon-box' => array(
		'section' => 'section_icon',
	),*/
);

foreach($supported_tooltip_widgets as $widget_name => $widget_options) {
	add_action( 'elementor/element/'.$widget_name.'/'.$widget_options['section'].'/after_section_end', 'stylepress_register_tooltip', 10, 2 );
}


function stylepress_register_tooltip( $widget, $args ){
	$widget->start_controls_section(
		'section_stylepress_tooltip',
		[
			'label' => __( 'Tooltip', 'stylepress' ),
		]
	);


	$widget->add_control(
		'stylepress_enable_tooltip',
		[
			'label' => __( 'Enable Tooltip?', 'elementor-pro' ),
			'type' =>  \Elementor\Controls_Manager::SWITCHER,
			'default' => '',
			'label_on' => __( 'Yes', 'elementor' ),
			'label_off' => __( 'No', 'elementor' ),
			'return_value' => 'yes',
			'separator' => 'before',
		]
	);

	$widget->add_control(
		'stylepress_tooltip_text',
		[
			'label' => __( 'Tooltip Text' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'condition' => [
				'stylepress_enable_tooltip!' => '',
			],
		]
	);

	$widget->add_control(
		'stylepress_tooltip_position',
		[
			'label' => __( 'Tooltip Position' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 's',
			'options' => array(
				's' => 'Top',
				'n' => 'Bottom',
				'w' => 'Right',
				'e' => 'Left',
				'sw' => 'Top Left',
				'se' => 'Top Right',
				'nw' => 'Bottom Left',
				'ne' => 'Bottom Right',
			),
			'condition' => [
				'stylepress_enable_tooltip!' => '',
			],
		]
	);


	$widget->end_controls_section();
}


function stylepress_tooltip_before_render( $widget ) {
	global $supported_tooltip_widgets;


	if( isset($supported_tooltip_widgets[ $widget->get_name() ]) ) {
		$settings = $widget->get_active_settings();
		if(!empty($settings['stylepress_enable_tooltip']) && $settings['stylepress_enable_tooltip'] === 'yes'){
			// do a find/replace on the settings
			switch($widget->get_name()) {
				case 'button':
					$widget->add_render_attribute( 'button', 'data-tlite', $settings['stylepress_tooltip_text'] );
					$widget->add_render_attribute( 'button', 'class', 'tooltip-' . $settings['stylepress_tooltip_position'] );
					break;
			}
		}
	}
}


add_action( 'elementor/frontend/widget/before_render', 'stylepress_tooltip_before_render' , 10 , 1);