<?php
namespace StylePress\Elementor\Skins;

use Elementor\Controls_Manager;
use Elementor\Skin_Base;
use Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;
use Elementor\Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Skin_Dtbaker
 */

class Skin_StylePressDynamic_Image extends Skin_Base {

	public function get_id() {
		return 'stylepress-dynamic-image';
	}

	public function get_title() {
		return __( 'Dynamic Fields', 'elementor-pro' );
	}

	protected function _register_controls_actions() {
		add_action( 'elementor/element/image/section_image/after_section_start', [ $this, 'register_controls' ] );
	}

	public function get_replace_fields(){

		$fields = array(
			'post_thumbnail' => 'Post Thumbnail',
		);

		return $fields;
	}

	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$available_fields = '<p><strong>StylePress Dynamic Fields:</strong></p>';
		$available_fields .= '<p>You can set this image to be dynamic as per below.</p>';


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

		$this->add_control(
			'stylepress_dynamic_image',
			[
				'label' => __( 'Choose Image', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default As Per Below', 'elementor' ),
					'post_thumbnail' => __( 'Post Thumbnail', 'elementor' ),
				],
				'default' => 'post_thumbnail',
			]
		);
	}


	public function render() {


		$settings = $this->parent->get_settings();

		if($this->get_instance_value('stylepress_dynamic_image') == 'post_thumbnail'){
			require_once DTBAKER_ELEMENTOR_PATH . 'widgets/class.dynamic-field.php';
			$dyno_generator = \DtbakerDynamicField::get_instance();
			$settings['image']['url'] = $dyno_generator->post_thumbnail();
			$link = array(
                'url' => $dyno_generator->permalink()
            );
        }else{
			$link = $this->get_link_url( $settings );
        }

		if ( empty( $settings['image']['url'] ) ) {
			return;
		}

		$has_caption = ! empty( $settings['caption'] );

		$this->parent->add_render_attribute( 'wrapper', 'class', 'elementor-image' );

		if ( ! empty( $settings['shape'] ) ) {
			$this->parent->add_render_attribute( 'wrapper', 'class', 'elementor-image-shape-' . $settings['shape'] );
		}

		if ( $link ) {
			$this->parent->add_render_attribute( 'link', 'href', $link['url'] );

			if ( ! empty( $link['is_external'] ) ) {
				$this->parent->add_render_attribute( 'link', 'target', '_blank' );
			}
		} ?>
        <div <?php echo $this->parent->get_render_attribute_string( 'wrapper' ); ?>>
			<?php
			if ( $has_caption ) : ?>
            <figure class="wp-caption">
				<?php endif;

				if ( $link ) : ?>
                <a <?php echo $this->parent->get_render_attribute_string( 'link' ); ?>>
					<?php endif;

					echo Group_Control_Image_Size::get_attachment_image_html( $settings );

					if ( $link ) : ?>
                </a>
			<?php endif;

			if ( $has_caption ) : ?>
                <figcaption class="widget-image-caption wp-caption-text"><?php echo $settings['caption']; ?></figcaption>
			<?php endif;

			if ( $has_caption ) : ?>
            </figure>
		<?php endif; ?>
        </div>
		<?php
	}

	private function get_link_url( $instance ) {
		if ( 'none' === $instance['link_to'] ) {
			return false;
		}

		if ( 'custom' === $instance['link_to'] ) {
			if ( empty( $instance['link']['url'] ) ) {
				return false;
			}
			return $instance['link'];
		}

		return [
			'url' => $instance['image']['url'],
		];
	}

}
