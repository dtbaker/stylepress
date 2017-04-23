<?php

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


require_once DTBAKER_ELEMENTOR_PATH . 'extensions/dynamic-field/widget.dynamic-field.php';

// add dynamic filters onto pre-built elements.
function stylepress_register_dynamic_background( $widget, $args ){
	$widget->add_control(
		'stylepress_enable_dynamic_bg',
		[
			'label' => __( 'Dynamic Image?', 'elementor-pro' ),
			'type' =>  \Elementor\Controls_Manager::SWITCHER,
			'default' => '',
			'label_on' => __( 'Yes', 'elementor' ),
			'label_off' => __( 'No', 'elementor' ),
			'condition' => [
//				'background' => [ 'classic' ],
//				'image[url]!' => '',
			],
		]
	);
	$widget->add_control(
		'stylepress_enable_dynamic_bg_info',
		[
			'label' => __( 'Dynamic background image is now enabled for this widget. See the documentation for more details: <a href="https://stylepress.org/elementor/dynamic-background-image/" target="_blank">stylepress.org/elementor/dynamic-background-image/</a> ' ),
			'type' => \Elementor\Controls_Manager::RAW_HTML,
			'condition' => [
				'stylepress_enable_dynamic_bg!' => '',
			],
		]
	);
}

function stylepress_register_dynamics( $widget, $args ){
	$widget->start_controls_section(
		'section_stylepress_dynamic',
		[
			'label' => __( 'Dynamic Content', 'stylepress' ),
		]
	);


	$widget->add_control(
		'stylepress_enable_dynamic',
		[
			'label' => __( 'Enable Dynamic Content?', 'elementor-pro' ),
			'type' =>  \Elementor\Controls_Manager::SWITCHER,
			'default' => '',
			'label_on' => __( 'Yes', 'elementor' ),
			'label_off' => __( 'No', 'elementor' ),
			'return_value' => 'yes',
			'separator' => 'before',
		]
	);

	ob_start();
	?>
	<br><br>
	<p><strong>Example Dynamic Fields:</strong></p>
	<br><br>
	<ul>
		<?php
		require_once DTBAKER_ELEMENTOR_PATH . 'extensions/dynamic-field/class.dynamic-field.php';
		$dyno_generator = \DtbakerDynamicField::get_instance();
		$available_callbacks = $dyno_generator->get_replace_fields();
		foreach($available_callbacks as $key => $title){ ?>
		<li>{{<?php echo $key;?>}} <span><?php echo $title;?></span></li>
		<?php } ?>
		<li>{{your-custom-field}} <span>Any Custom Field</span></li>
	</ul>
	<?php
	$eg = ob_get_clean();
	$widget->add_control(
		'stylepress_enable_dynamic_information',
		[
			'label' => __( 'Dynamic content is now enabled for this widget. See the documentation for more details: <a href="https://stylepress.org/elementor/dynamic-fields/" target="_blank">stylepress.org/elementor/dynamic-fields/</a> '. $eg ),
			'type' => \Elementor\Controls_Manager::RAW_HTML,
			'condition' => [
				'stylepress_enable_dynamic!' => '',
			],
		]
	);


	$widget->end_controls_section();
}

global $supported_widgets;
$supported_widgets = array(
	'image' => array(
		'section' => 'section_image',
	),
	'heading' => array(
		'section' => 'section_title',
	),
	'button' => array(
		'section' => 'section_button',
	),
	'icon-box' => array(
		'section' => 'section_icon',
	),
	'text-editor' => array(
		'section' => 'section_editor',
	),
);

function stylepress_dynamic_before_render( $widget ) {
	global $supported_widgets;

	// check for background image support.
	if( $widget->get_name() === 'section' || $widget->get_name() === 'column' ) {
		$settings = $widget->get_active_settings();
		if ( ! empty( $settings['stylepress_enable_dynamic_bg'] ) && $settings['stylepress_enable_dynamic_bg'] === 'yes' ) {
			require_once DTBAKER_ELEMENTOR_PATH . 'extensions/dynamic-field/class.dynamic-field.php';
			$dyno_generator = \DtbakerDynamicField::get_instance();
			$image_url = $dyno_generator->post_thumbnail();
			if($image_url){
				$widget->add_render_attribute( '_wrapper', 'style', 'background-image: url("' . esc_url($image_url) . '") !important;' );
			}
		}
	}
	if( isset($supported_widgets[ $widget->get_name() ]) ) {
		$settings = $widget->get_active_settings();
		if(!empty($settings['stylepress_enable_dynamic']) && $settings['stylepress_enable_dynamic'] === 'yes'){
			// do a find/replace on the settings
			$fields = array();
			$do_link = false;
			switch($widget->get_name()){
				case 'heading':
					$fields = array(
						'title',
					);
					$do_link = true;
					break;
				case 'text-editor':
					$fields = array(
						'editor',
					);
					$do_link = true;
					break;
				case 'image':
					$fields = array(
					);
					$do_link = true;
					break;
				case 'button':
					$fields = array(
						'text',
					);
					$do_link = true;
					break;
				case 'icon-box':
					$fields = array(
						'editor',
					);
					$do_link = true;
					break;
			}
			require_once DTBAKER_ELEMENTOR_PATH . 'extensions/dynamic-field/class.dynamic-field.php';
			$dyno_generator = \DtbakerDynamicField::get_instance();
//			$available_callbacks = $dyno_generator->get_replace_fields();

			foreach($fields as $field){
				if(!empty($settings[$field])){
					// find/replace time.
					if( preg_match_all('#\{\{([a-z_-]+)\}\}#imsU', $settings[$field], $matches)){
						foreach($matches[1] as $key=>$replace_field){
//							if( isset($available_callbacks[$replace_field])){
								$replace = $dyno_generator->get_field($replace_field);
								$settings[$field] = str_replace('{{' . $replace_field . '}}', $replace, $settings[$field]);
//							}
						}
					}
					$widget->set_settings($field, $settings[$field]);

				}
			}
			// replace the [link][url] link, usually this is just {{permalink}}
			if($do_link && !empty($settings['link']['url'])){
				if( preg_match_all('#\{\{([a-z_-]+)\}\}#imsU', $settings['link']['url'], $matches)){
					foreach($matches[1] as $key=>$replace_field){
						$replace = $dyno_generator->get_field($replace_field);
						$settings['link']['url'] = str_replace('{{' . $replace_field . '}}', $replace, $settings['link']['url']);
					}
				}
				$widget->set_settings('link', $settings['link']);
			}


			// dynamic image support?
			switch($widget->get_name()) {
				case 'image':
					$image_url = $dyno_generator->post_thumbnail();
					$image_id = $dyno_generator->post_thumbnail_id();
					if($image_url && $image_id) {
						$settings['image']['id'] = $image_id;
						$settings['image']['url'] = $image_url;
						$widget->set_settings('image', $settings['image']);
					}
					break;
			}

		}

		/*
//		print_r($settings);
		//$widget->set_settings($key,$val);
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
						echo \Elementor\Plugin::instance()->frontend->get_builder_content( $popup_template, false );
						?>
					</div>
				</div>
				<?php
				wp_enqueue_style( 'wp-jquery-ui-dialog' );
				wp_enqueue_style( 'stylepress-modal-button' );
				wp_enqueue_script( 'stylepress-modal-popup' );
				wp_enqueue_script( 'jquery-ui-dialog' );
			}
		}*/
	}
}

foreach($supported_widgets as $widget_name => $widget_options) {
	add_action( 'elementor/element/'.$widget_name.'/'.$widget_options['section'].'/after_section_end', 'stylepress_register_dynamics', 10, 2 );
}
add_action( 'elementor/frontend/widget/before_render', 'stylepress_dynamic_before_render' , 10 , 1);
add_action( 'elementor/frontend/element/before_render', 'stylepress_dynamic_before_render' , 10 , 1);

// dynamic background image on certain elements.
add_action( 'elementor/element/section/section_background/before_section_end', 'stylepress_register_dynamic_background', 10, 2 );