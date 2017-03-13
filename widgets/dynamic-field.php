<?php
/**
 * WordPress Nav Menu Widget
 *
 * @package dtbaker-elementor
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

/**
 * Creates our custom Elementor widget
 *
 * Class Widget_Dtbaker_WP_Menu
 *
 * @package Elementor
 */
class Widget_Dtbaker_Dynamic_Field extends Widget_Base {

	/**
	 * Get Widgets name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'dtbaker_dynamic';
	}

	/**
	 * Get widgets title
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Dynamic Field', 'stylepress' );
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

	/**
	 * This registers our controls for the widget. Currently there are none but we may add options down the track.
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_dtbaker_wp_menu',
			[
				'label' => __( 'Dynamic Field', 'stylepress' ),
			]
		);

		$this->add_control(
			'desc',
			[
				'label' => __( 'Choose from the available dynamic fields below. You can even mix HTML into the output if you need to create something more advanced.', 'stylepress' ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);

		$dynamic_select = array(
			'' => esc_html__( ' - choose - ', 'stylepress' ),
		);

		$dynamic_select = array_merge( $dynamic_select, $this->get_dynamic_fields( true ) );


		$this->add_control(
			'dynamic_field_value',
			[
				'label'   => esc_html__( 'Choose Field', 'stylepress' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $dynamic_select,
			]
		);

		$this->add_control(
			'field_preview',
			[
				'label'   => esc_html__( 'Code', 'stylepress' ),
				'type'    => Controls_Manager::RAW_HTML,
				'separator' => 'none',
				'show_label' => false,
				'raw' => '<div id="dtbaker-dynamic-code"></div>',
			]
		);

		$this->add_control(
			'dynamic_html',
			[
                'label' => __( 'Optional HTML Output', 'stylepress' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => '',
                'placeholder' => __( 'Combine dynamic fields with HTML code here.', 'stylepress' ),
                'title' => __( 'Combine dynamic fields with HTML code here.', 'stylepress' ),
                'rows' => 10,
			]
		);

		$this->end_controls_section();

		do_action( 'dtbaker_wp_menu_elementor_controls', $this );

	}

	public function get_dynamic_fields($flat = false){

	    $fields = array();


	    // general.
	    $fields[] = array(
            'code' => 'page_title',
            'name' => 'Page Title',
        );
	    $fields[] = array(
            'code' => 'search_query',
            'name' => 'Search Query',
        );

	    // woocommerce.
		$fields[] = array(
			'code' => 'product_title',
			'name' => 'Product Title',
		);


		if($flat) {
		    $all = array();
			foreach ( $fields as $field ) {
				$all[ $field['code'] ] = $field['name'];
			}
			return $all;
		}

	    return $fields;
    }

	/**
	 * Render our custom menu onto the page.
	 */
	protected function render() {
		$settings = $this->get_settings();
		$callback = false;
		$available_callbacks = $this->get_dynamic_fields(true);
		if( $settings && !empty($settings['dynamic_field_value']) ){
		    $callback = '{{'.$settings['dynamic_field_value'].'}}';
        }
		if( $settings && !empty($settings['dynamic_html']) ){
		    $callback = $settings['dynamic_html'];
        }
        if($callback) {
	        require_once DTBAKER_ELEMENTOR_PATH . 'widgets/class.dynamic-field.php';
	        $dyno_generator = \DtbakerDynamicField::get_instance();

	        if( preg_match_all('#\{\{([a-z_]+)\}\}#imsU', $callback, $matches)){
	            foreach($matches[1] as $key=>$field){
	                if( isset($available_callbacks[$field])){
	                    $replace = $dyno_generator->$field();
	                    $callback = str_replace('{{' . $field . '}}', $replace, $callback);
                    }
                }
            }
        }
        echo $callback;

	}

	/**
	 * This is outputted while rending the page.
	 */
	protected function content_template() {
		?>
		<div class="dtbaker-wp-menu-content-area">
		{{Dynamic Field Here}}
		</div>
		<?php
	}

}


Plugin::instance()->widgets_manager->register_widget_type( new Widget_Dtbaker_Dynamic_Field() );