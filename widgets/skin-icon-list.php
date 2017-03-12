<?php
namespace StylePress\Elementor\Skins;

use Elementor\Controls_Manager;
use Elementor\Skin_Base;
use Elementor\Widget_Base;
use Elementor\Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Skin_Dtbaker
 */

class Skin_Icon_List_Dtbaker extends Skin_Base {

	public function get_id() {
		return 'elementor-test-icon-list';
	}

	public function get_title() {
		return __( 'Horizontal Icons', 'elementor-pro' );
	}

	protected function _register_controls_actions() {
		add_action( 'elementor/element/icon-list/section_icon_list/before_section_end', [ $this, 'register_controls' ] );
	}

	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->add_control(
			'vertical_divider',
			[
				'label' => __( 'Vertical Divider', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'elementor' ),
				'label_on' => __( 'On', 'elementor' ),
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'content: ""',
				],
				'separator' => 'before',
			]
		);


		$this->add_control(
			'vertical_divider_style',
			[
				'label' => __( 'Style', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'solid' => __( 'Solid', 'elementor' ),
					'double' => __( 'Double', 'elementor' ),
					'dotted' => __( 'Dotted', 'elementor' ),
					'dashed' => __( 'Dashed', 'elementor' ),
				],
				'default' => 'solid',
				/*'condition' => [
					'vertical_divider' => 'yes',
				],*/
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'border-right-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'vertical_divider_weight',
			[
				'label' => __( 'Weight', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				/*'condition' => [
					'vertical_divider' => 'yes',
				],*/
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'border-right-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'vertical_divider_color',
			[
				'label' => __( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ddd',
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				/*'condition' => [
					'vertical_divider' => 'yes',
				],*/
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'border-right-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'vertical_divider_width',
			[
				'label' => __( 'Height', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'units' => [ '%' ],
				'default' => [
					'unit' => '%',
				],
				/*'condition' => [
					'vertical_divider' => 'yes',
				],*/
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);
	}


	public function render() {
		$settings = $this->parent->get_settings();
		?>
		<ul class="elementor-icon-list-items">
			<?php foreach ( $settings['icon_list'] as $item ) : ?>
				<li class="elementor-icon-list-item" >
					<?php
					if ( ! empty( $item['link']['url'] ) ) {
						$target = $item['link']['is_external'] ? ' target="_blank"' : '';

						echo '<a href="' . $item['link']['url'] . '"' . $target . '>';
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
