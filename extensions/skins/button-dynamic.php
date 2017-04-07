<?php

namespace StylePress\Elementor\Skins;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Controls_Manager;
use Elementor\Skin_Base;
use Elementor\Widget_Base;
use Elementor\Scheme_Color;


/**
 * Class Skin_Dtbaker
 */

class Skin_StylePressButtonDynamic extends Skin_Base {

	public function get_id() {
		return 'elementor-test-icon-list';
	}

	public function get_title() {
		return __( 'Dynamic Fields', 'elementor-pro' );
	}

	protected function _register_controls_actions() {
		add_action( 'elementor/element/button/section_button/before_section_end', [ $this, 'register_controls' ] );
	}

	public function get_replace_fields(){

		$fields = array(
			'permalink' => 'Post Permalink',
			'post_title' => 'Post Title',
		);

		return $fields;
	}

	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$available_fields = '<p><strong>StylePress Dynamic Fields:</strong></p>';
		$available_fields .= '<p>These fields can be used in the "Text" and "Link" attributes. Make sure you set the "Skin" above to "Dynamic Field" for these to work.</p>';
		$available_fields .= '<ul>';
		foreach($this->get_replace_fields() as $field => $desc) {
			$available_fields .= '<li>{{'.$field.'}}</li>';
		}
		$available_fields .= '</ul>';

		$this->add_control(
			'stylepress_dynamic_fields',
			[
				'raw' => $available_fields,
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'content_classes' => 'stylepress-elementor-description',
				/*'condition' => [
					'stylepress_dynamic_field' => 'yes',
				],*/
			]
		);
	}


	public function render() {
		$settings = $this->parent->get_settings();

		require_once DTBAKER_ELEMENTOR_PATH . 'widgets/class.dynamic-field.php';
		$dyno_generator = \DtbakerDynamicField::get_instance();
		$available_callbacks = $this->get_replace_fields();

		if(!empty($settings['link']['url'])){
			// check for fields to replace.
			if( preg_match_all('#\{\{([a-z_]+)\}\}#imsU', $settings['link']['url'], $matches)){
				foreach($matches[1] as $key=>$field){
					if( isset($available_callbacks[$field])){
						$replace = $dyno_generator->$field();
						$settings['link']['url'] = str_replace('{{' . $field . '}}', $replace, $settings['link']['url']);
					}
				}
			}
		}
		if(!empty($settings['text'])){
			// check for fields to replace.
			if( preg_match_all('#\{\{([a-z_]+)\}\}#imsU', $settings['text'], $matches)){
				foreach($matches[1] as $key=>$field){
					if( isset($available_callbacks[$field])){
						$replace = $dyno_generator->$field();
						$settings['link']['url'] = str_replace('{{' . $field . '}}', $replace, $settings['text']);
					}
				}
			}
		}


		$this->parent->add_render_attribute( 'wrapper', 'class', 'elementor-button-wrapper' );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->parent->add_render_attribute( 'button', 'href', $settings['link']['url'] );
			$this->parent->add_render_attribute( 'button', 'class', 'elementor-button-link' );

			if ( ! empty( $settings['link']['is_external'] ) ) {
				$this->parent->add_render_attribute( 'button', 'target', '_blank' );
			}
		}

		$this->parent->add_render_attribute( 'button', 'class', 'elementor-button' );

		if ( ! empty( $settings['size'] ) ) {
			$this->parent->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['size'] );
		}

		if ( $settings['hover_animation'] ) {
			$this->parent->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}

		$this->parent->add_render_attribute( 'content-wrapper', 'class', 'elementor-button-content-wrapper' );
		$this->parent->add_render_attribute( 'icon-align', 'class', 'elementor-align-icon-' . $settings['icon_align'] );
		$this->parent->add_render_attribute( 'icon-align', 'class', 'elementor-button-icon' );
		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'wrapper' ); ?>>
			<a <?php echo $this->parent->get_render_attribute_string( 'button' ); ?>>
				<span <?php echo $this->parent->get_render_attribute_string( 'content-wrapper' ); ?>>
					<?php if ( ! empty( $settings['icon'] ) ) : ?>
						<span <?php echo $this->parent->get_render_attribute_string( 'icon-align' ); ?>>
							<i class="<?php echo esc_attr( $settings['icon'] ); ?>"></i>
						</span>
					<?php endif; ?>
					<span class="elementor-button-text"><?php echo $settings['text']; ?></span>
				</span>
			</a>
		</div>
		<?php
	}

}
