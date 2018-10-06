<?php

namespace StylePress\Elementor\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use Elementor\Controls_Manager;
use Elementor\Skin_Base;
use Elementor\Widget_Base;
use Elementor\Scheme_Color;


/**
 * Class Skin_Dtbaker
 */
class Skin_StylePressIconList extends Skin_Base {

	public function get_id() {
		return 'stylepress-icons-inline';
	}

	public function get_title() {
		return __( 'Inline Layout', 'elementor-pro' );
	}

	protected function _register_controls_actions() {
		add_action( 'elementor/element/icon-list/section_icon_list/before_section_end', [ $this, 'register_controls' ] );
	}


	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;


		$this->parent->update_responsive_control(
			'icon_align',
			[
				'label'                => __( 'Alignment', 'elementor' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'elementor' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'elementor' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'prefix_class'         => 'elementor-align-',
				'selectors'            => [
					// existing rules:
					'{{WRAPPER}} .elementor-icon-list-item, {{WRAPPER}} .elementor-icon-list-item a'                  => 'justify-content: {{VALUE}};',
					// new rules for inline skin:
					'{{WRAPPER}}[data-element_type="icon-list.' . $this->get_id() . '"] ul.elementor-icon-list-items' => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'left'  => 'flex-start',
					'right' => 'flex-end',
				],
			]
		);
	}


	public function render() {

		$settings = $this->parent->get_settings();

		// copied from elementor icon list output:
		?>
		<ul class="elementor-icon-list-items">
			<?php foreach ( $settings['icon_list'] as $index => $item ) : ?>
				<li class="elementor-icon-list-item">
					<?php
					if ( ! empty( $item['link']['url'] ) ) {
						$link_key = 'link_' . $index;

						$this->parent->add_render_attribute( $link_key, 'href', $item['link']['url'] );

						if ( $item['link']['is_external'] ) {
							$this->parent->add_render_attribute( $link_key, 'target', '_blank' );
						}

						if ( $item['link']['nofollow'] ) {
							$this->parent->add_render_attribute( $link_key, 'rel', 'nofollow' );
						}

						echo '<a ' . $this->parent->get_render_attribute_string( $link_key ) . '>';
					}

					if ( $item['icon'] ) : ?>
						<span class="elementor-icon-list-icon">
							<i class="<?php echo esc_attr( $item['icon'] ); ?>"></i>
						</span>
					<?php endif; ?>
					<span class="elementor-icon-list-text"><?php echo $item['text']; ?></span>
					<?php
					if ( ! empty( $item['link']['url'] ) ) {
						echo '</a>';
					}
					?>
				</li>
			<?php
			endforeach; ?>
		</ul>
		<?php
	}

}
