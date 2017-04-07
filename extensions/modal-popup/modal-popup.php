<?php
/**
 * WordPress Nav Menu Widget
 *
 * @package dtbaker-elementor
 */


defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;



add_action( 'wp_enqueue_scripts', function(){
	wp_register_script( 'stylepress-modal-popup', DTBAKER_ELEMENTOR_URI . 'extensions/modal-popup/popup.js', array('jquery') );
	wp_localize_script( 'stylepress-modal-popup', 'stylepress_modal', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	wp_register_style( 'stylepress-modal-button', DTBAKER_ELEMENTOR_URI . 'extensions/modal-popup/popup.css' );
} );

add_filter( 'stylepress_modal_link', function($link, $popup_template, $options = array() ){

	if($popup_template) {
		if ( empty( $GLOBALS['generated_modals'] ) ) {
			$GLOBALS['generated_modals'] = array();
		}
		if ( ! isset( $GLOBALS['generated_modals'][ $popup_template ] ) ) {
			$GLOBALS['generated_modals'][ $popup_template ] = true;
			?>
            <div class="stylepress-modal-pop" id="stylepress-modal-pop-<?php echo (int) $popup_template; ?>">
                <div class="stylepress-modal-inner">
					<?php
					echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $popup_template );
					?>
                </div>
            </div>
			<?php
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			wp_enqueue_style( 'stylepress-modal-button' );
			wp_enqueue_script( 'stylepress-modal-popup' );
			wp_enqueue_script( 'jquery-ui-dialog' );
		}
		$options['id'] = $popup_template;
		$link .= ' data-stylepressmodal="' . htmlspecialchars(json_encode($options), ENT_QUOTES, 'UTF-8') .'"';
	}

	return $link;
}, 10, 3);

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
				echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $modal_id );
				?>
            </div>
			<?php
			exit;
		}
	}
	echo "Sorry something went wrong. Please refresh and try again";
	exit;
}
);

//
function stylepress_modal_button_before_render( $widget ){
    if($widget->get_name() == 'button' || $widget->get_name() == 'icon-box') {
	    $settings = $widget->get_active_settings();
	    if ( ! empty( $settings['dtbaker_modal_content'] ) ) {

		    $popup_template = (int)$settings['dtbaker_modal_content'];
		    $width = '400px';
		    if ( ! empty( $settings['modal_width'] ) ) {
			    $width = $settings['modal_width']['size'] . $settings['modal_width']['unit'];
		    }
		    $widget->add_render_attribute( '_wrapper', 'data-stylepressmodal', json_encode( array(
			    'id'            => $popup_template,
			    'modal_content' => $popup_template,
			    'modal_width'   => $width,
		    ) ) );
		    if ( empty( $GLOBALS['generated_modals'] ) ) {
			    $GLOBALS['generated_modals'] = array();
		    }
		    if ( ! isset( $GLOBALS['generated_modals'][ $popup_template ] ) ) {
			    $GLOBALS['generated_modals'][ $popup_template ] = true;
			    ?>
                <div class="stylepress-modal-pop" id="stylepress-modal-pop-<?php echo $popup_template; ?>">
                    <div class="stylepress-modal-inner">
					    <?php
					    echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $popup_template );
					    ?>
                    </div>
                </div>
			    <?php
			    wp_enqueue_style( 'wp-jquery-ui-dialog' );
			    wp_enqueue_style( 'stylepress-modal-button' );
			    wp_enqueue_script( 'stylepress-modal-popup' );
			    wp_enqueue_script( 'jquery-ui-dialog' );
		    }
	    }
    }
}

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
			'selectors' => [
				'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'border-right-style: {{VALUE}};',
			],
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



}

add_action( 'elementor/element/stylepress_modal_button/section_button/after_section_end', 'stylepress_modal_button_hack' , 10 , 2);

add_action( 'elementor/element/button/section_button/after_section_end', 'stylepress_modal_button_hack' , 10 , 2);
add_action( 'elementor/element/icon-box/section_icon/after_section_end', 'stylepress_modal_button_hack' , 10 , 2);

add_action( 'elementor/frontend/widget/before_render', 'stylepress_modal_button_before_render' , 10 , 1);



/**
 * Creates our custom Elementor widget
 *
 * Class Widget_Dtbaker_WP_Menu
 *
 * @package Elementor
 */
class Widget_Dtbaker_Modal_Button extends \Elementor\Widget_Button {



	public function get_script_depends() {
		return [
			'stylepress-modal-popup',
			'jquery-ui-dialog'
		];
	}

	/**
	 * Get Widgets name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'stylepress_modal_button';
	}

	/**
	 * Get widgets title
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Modal Button', 'stylepress' );
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

	public function before_render() {

		$settings = parent::get_active_settings();
		if(!empty($settings['dtbaker_modal_content'])) {
			$popup_template = $settings['dtbaker_modal_content'];

			$width = '400px';
			if ( ! empty( $settings['modal_width'] ) ) {
				$width = $settings['modal_width']['size'] . $settings['modal_width']['unit'];
			}
			$this->add_render_attribute( '_wrapper', 'data-stylepressmodal', json_encode( array(
				'id'            => $popup_template,
				'modal_content' => $settings['dtbaker_modal_content'],
				'modal_width'   => $width,
			) ) );

			if ( empty( $GLOBALS['generated_modals'] ) ) {
				$GLOBALS['generated_modals'] = array();
			}
			if ( ! isset( $GLOBALS['generated_modals'][ $popup_template ] ) ) {
				$GLOBALS['generated_modals'][ $popup_template ] = true;
				?>
                <div class="stylepress-modal-pop" id="stylepress-modal-pop-<?php echo $popup_template; ?>">
                    <div class="stylepress-modal-inner">
						<?php
						echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $popup_template );
						?>
                    </div>
                </div>
				<?php
				wp_enqueue_style( 'wp-jquery-ui-dialog' );
				wp_enqueue_style( 'stylepress-modal-button' );
			}
		}
		parent::before_render(); // TODO: Change the autogenerated stub
	}


}

//\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widget_Dtbaker_Modal_Button() );

