<?php

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


add_action( 'wp_enqueue_scripts', function(){
	wp_enqueue_script( 'stylepress-formmods', DTBAKER_ELEMENTOR_URI . 'extensions/form/frontend.js' );
	wp_enqueue_style( 'stylepress-formstyles', DTBAKER_ELEMENTOR_URI . 'extensions/form/form.css' );
} );


add_action( 'elementor/editor/before_enqueue_scripts', function(){
	wp_enqueue_script( 'stylepress-forms', DTBAKER_ELEMENTOR_URI . 'extensions/form/form.js', false, DTBAKER_ELEMENTOR_VERSION, true );
}, 99999 );


//do_action( 'elementor_pro/forms/render_field/' . $item['field_type'],  $item, $item_index, $this );


add_action('elementor_pro/forms/render_field/stylepress-datepicker',function($item, $item_index, $widget){
	wp_enqueue_script( 'jquery-ui-datepicker' );

	// You need styling for the datepicker. For simplicity I've linked to Google's hosted jQuery UI CSS.
	wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
	wp_enqueue_style( 'jquery-ui' );
	$widget->add_render_attribute( 'input' . $item_index, 'class', 'elementor-field-textual stylepress-datepicker' );
	$widget->add_render_attribute( 'input' . $item_index, 'type', 'text', true );
	$widget->add_render_attribute( 'input' . $item_index, 'placeholder', 'Choose Date', true );
	echo '<input size="1" ' . $widget->get_render_attribute_string( 'input' . $item_index ) . '>';
}, 10, 3);




add_filter('elementor_pro/forms/render/item',function($item, $item_index, $widget){
	if ( 'stylepress-description' === $item['field_type'] || 'stylepress-togglestart' === $item['field_type']  || 'stylepress-toggleend' === $item['field_type'] ) {
		$item['field_labelold'] = $item['field_label'];
		$item['field_label']    = '';
	}
	return $item;
}, 10, 3);

add_action('elementor_pro/forms/render_field/stylepress-description',function($item, $item_index, $widget){
	?>
	<div class="stylepress-form-description">
        <?php echo !empty($item['field_labelold']) ? do_shortcode($item['field_labelold']) : ''; ?>
	</div>
	<?php
}, 10, 3);

add_action('elementor_pro/forms/render_field/stylepress-togglestart',function($item, $item_index, $widget){
    $bits = explode('|',$item['field_labelold']);

	?>
	<div class="stylepress-form-togglescontrol">

        <input type="checkbox" class="stylepress-form-togglecheck" value="1" id="stylepress-formtoggle-<?php echo $item_index;?>">
        <label for="stylepress-formtoggle-<?php echo $item_index;?>" class="stylepress-form-togglelabel"><span class="on"><?php echo $bits[0];?></span><span class="off"><?php echo $bits[1];?></span> </label>
        <div class="stylepress-form-toggleinner">
            <div>
<!-- start -->
	<?php
}, 10, 3);

add_action('elementor_pro/forms/render_field/stylepress-toggleend',function($item, $item_index, $widget){
	?>
    <!-- end-->
	    </div>
	    </div>
    </div>
	<?php
}, 10, 3);


add_filter('elementor_pro/forms/field_types',function($fields) {

	$fields['stylepress-datepicker']  = 'DatePicker';
	$fields['stylepress-description'] = 'Description Block';
	$fields['stylepress-togglestart'] = 'Toggle Start';
	$fields['stylepress-toggleend']   = 'Toggle End';

	return $fields;
});

//

add_action( 'elementor/element/form/section_form_options/before_section_end', function( $widget ){


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
		'stylepress_email_autoreply',
		[
			'label' => __( 'Choose Autoreply Template', 'elementor-pro' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '0',
			'options' => $options,
			'types' => $types,
			'label_block'  => 'true',
		]
	);


} , 10 , 2);