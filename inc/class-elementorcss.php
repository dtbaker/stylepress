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
			$element_settings['_background_color'] = '#FF0000';
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