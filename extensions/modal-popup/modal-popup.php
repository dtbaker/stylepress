<?php
/**
 * WordPress Nav Menu Widget
 *
 * @package dtbaker-elementor
 */


defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


$control_id = \Elementor\Controls_Manager::URL;

$control = $elementor->controls_manager->get_control($control_id);
if($control){
    //StylePress_Control_URL
	require_once DTBAKER_ELEMENTOR_PATH . 'extensions/modal-popup/elementor.url-control.php';

	$class_name = 'Elementor\StylePress_Control_URL';

	$elementor->controls_manager->register_control( $control_id, new $class_name() );
}


add_action( 'wp_enqueue_scripts', function(){
	wp_register_script( 'stylepress-modal-popup', DTBAKER_ELEMENTOR_URI . 'extensions/modal-popup/popup.js', array('jquery') );
	wp_localize_script( 'stylepress-modal-popup', 'stylepress_modal', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	wp_register_style( 'stylepress-modal-button', DTBAKER_ELEMENTOR_URI . 'extensions/modal-popup/popup.css' );
} );

add_filter( 'stylepress_modal_link', function($link, $popup_template, $options = array() ){

	if($popup_template) {

		if(empty($GLOBALS['stylepress_slidein'])){
			$GLOBALS['stylepress_slidein'] = array();
		}
		if(empty($GLOBALS['stylepress_modal_popups'])){
			$GLOBALS['stylepress_modal_popups'] = array();
		}

		if(!empty($options['display']) && $options['display'] == 1){
			$GLOBALS['stylepress_slidein'][$popup_template] = $options;
		}else{
			$GLOBALS['stylepress_modal_popups'][$popup_template] = $options;
		}

		$options['id'] = $popup_template;
		return array( 'key' => 'data-stylepressmodal', 'val' => htmlspecialchars(json_encode($options), ENT_QUOTES, 'UTF-8'));
	}

	return $link;
}, 10, 3);

/*
add_action( 'wp_ajax_stylepress_modal_pop', function(){
	// EDIT: ajax not used any more, we render template straight on page.
	//
	if(!empty($_POST['modal']['id']) && !empty($_POST['modal']['settings'])){
		$modal_id = !empty($_POST['modal']['settings']['dtbaker_modal_content']) ? (int)$_POST['modal']['settings']['dtbaker_modal_content'] : 0;
		$modal_hash = !empty($_POST['modal']['settings']['stylepress_modal_hash']) ? $_POST['modal']['settings']['stylepress_modal_hash'] : 0;
		if($modal_id && $modal_hash && wp_verify_nonce($modal_hash, 'open_modal_' . $modal_id)){
			?>
            <div class="elementor-template">
				<?php
				echo \Elementor\Plugin::instance()->frontend->get_builder_content( $modal_id, false );
				?>
            </div>
			<?php
			exit;
		}
	}
	echo "Sorry something went wrong. Please refresh and try again";
	exit;
}
);*/

//
function stylepress_modal_button_before_render( $widget ) {
	$enabled = array( 'button', 'dtbaker_wp_menu' );
	if ( in_array( $widget->get_name(), $enabled ) ) {
		$settings = $widget->get_active_settings();
		if ( ! empty( $settings['link']['stylepress_template'] ) ) {
			if ( empty( $settings['link']['url'] ) ) {
				$settings['link']['url'] = '#';
				$widget->set_settings( 'link', $settings['link'] );
			}
			$options = array(
				'template' => (int) $settings['link']['stylepress_template'],
				'width'    => (int) $settings['link']['stylepress_width'],
				'display'  => (int) $settings['link']['stylepress_display'],
			);
			$data_attr   = apply_filters( 'stylepress_modal_link', '', $options['template'], $options );
			switch ( $widget->get_name() ) {
				case 'button':
					$widget->add_render_attribute( 'button', $data_attr['key'], $data_attr['val'] );
					break;
				case 'dtbaker_wp_menu':
					$widget->add_render_attribute( 'link', $data_attr['key'], $data_attr['val'] );
					break;
			}
		}
	}
}

/*
function stylepress_modal_button_hack( $widget, $args ){

	$widget->start_controls_section(
		'section_stylepress_modal_settings',
		[
			'label' => __( 'Modal Popup', 'stylepress' ),
		]
	);


	$options = [
		'0' => '— ' . __( 'Choose A Template', 'elementor' ) . ' —',
	];

	$source = \Elementor\Plugin::instance()->templates_manager->get_source( 'local' );
	$templates = $source->get_items();

	$types = [];

	foreach ( $templates as $template ) {
		$options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
		$types[ $template['template_id'] ] = $template['type'];
	}

	$widget->add_control(
		'dtbaker_modal_content',
		[
			'label' => __( 'Choose Modal Content', 'elementor-pro' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '0',
			'options' => $options,
			'types' => $types,
			'label_block'  => 'true',
		]
	);
	$widget->add_control(
		'dtbaker_modal_style',
		[
			'label' => __( 'Display Style', 'elementor-pro' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '0',
			'options' => array(
                '0' => 'Modal Popup',
                '1' => 'Slide In',
            ),
			'label_block'  => 'true',
		]
	);

	$widget->add_control(
		'modal_width',
		[
			'label' => __( 'Width', 'elementor' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => [
				'px' => [
					'min' => 1,
					'max' => 1000,
				],
			],
			'default' => [
				'size' => 500,
				'unit' => 'px',
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-33-separator' => 'width: {{SIZE}}{{UNIT}};',
			],
		]
	);

	$widget->end_controls_section();



}*/


add_action( 'stylepress/before-render' , function(){
	echo '<div id="site-offcanvas-wrap">';
} );
add_action( 'stylepress/after-render' , function(){
	echo '</div>';
	include DTBAKER_ELEMENTOR_PATH . 'extensions/modal-popup/slide-in.php';
} );

add_action( 'stylepress/modal-popups' , function(){
	// if there is no before/after render
	include DTBAKER_ELEMENTOR_PATH . 'extensions/modal-popup/slide-in.php';
} );

//add_action( 'elementor/element/stylepress_modal_button/section_button/after_section_end', 'stylepress_modal_button_hack' , 10 , 2);
//add_action( 'elementor/element/button/section_button/after_section_end', 'stylepress_modal_button_hack' , 10 , 2);
//add_action( 'elementor/element/icon-box/section_icon/after_section_end', 'stylepress_modal_button_hack' , 10 , 2);

add_action( 'elementor/frontend/widget/before_render', 'stylepress_modal_button_before_render' , 10 , 1);



