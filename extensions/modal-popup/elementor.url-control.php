<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * A URL input control. with the ability to set the target of the link to `_blank` to open in a new tab.
 *
 * @param array $default {
 * 		@type string $url         Default empty
 * 		@type bool   $is_external Determine whether to open the url in the same tab or in a new one
 *                                Default empty
 * }
 *
 * @param bool  $show_external 	  Whether to show the 'Is External' button
 *                                Default true
 *
 * @since 1.0.0
 */
class StylePress_Control_URL extends Control_Base_Multiple {

	public function get_type() {
		return 'url';
	}

	public function get_default_value() {
		return [
			'is_external' => '',
			'url' => '',
			'stylepress_template' => '',
			'stylepress_display' => '',
			'stylepress_width' => '500',
		];
	}

	protected function get_default_settings() {
		return [
			'label_block' => true,
			'show_external' => true,
			'stylepress_modal' => false,
		];
	}

	public function content_template() {

		$options = [
			'0' => '- ' . __( 'None', 'elementor' ) . ' -',
		];

		$source = \Elementor\Plugin::instance()->templates_manager->get_source( 'local' );
		$templates = $source->get_items();

		$types = [];

		foreach ( $templates as $template ) {
			$options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
			$types[ $template['template_id'] ] = $template['type'];
		}

		?>
		<div class="elementor-control-field elementor-control-url-external-{{{ data.show_external ? 'show' : 'hide' }}}">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<input type="url" data-setting="url" placeholder="{{ data.placeholder }}" />
				<!-- <button class="elementor-control-stylepress-modal tooltip-target" data-tooltip="<?php _e( 'Create a Modal Popup/Slide-in', 'elementor' ); ?>" title="<?php esc_attr_e( 'Create a Modal Popup/Slide-in', 'elementor' ); ?>">
					<span class="elementor-control-stylepress-modal-button" title="<?php esc_attr_e( 'Create a Modal Popup/Slide-in', 'elementor' ); ?>"><i class="fa fa-window-restore"></i></span>
				</button> -->
				<button class="elementor-control-url-target tooltip-target" data-tooltip="<?php _e( 'Open Link in new Tab', 'elementor' ); ?>" title="<?php esc_attr_e( 'Open Link in new Tab', 'elementor' ); ?>">
					<span class="elementor-control-url-external" title="<?php esc_attr_e( 'New Window', 'elementor' ); ?>"><i class="fa fa-external-link"></i></span>
				</button>
			</div>
		</div>
		<div class="elementor-control-field elementor-stylepress-url-modal-block">
			<label class="elementor-control-stylepress-modal-title"> ... or show a modal window:</label>
			<div class="elementor-control-input-wrapper">
				<select name="stylepress_template" data-setting="stylepress_template">
					<?php foreach($options as $option_id => $option){ ?>
					<option value="<?php echo (int)$option_id;?>"><?php echo esc_html($option);?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="elementor-control-field elementor-stylepress-url-modal-block">
			<label class="elementor-control-stylepress-modal-title">modal style:</label>
			<div class="elementor-control-input-wrapper">
				<select name="stylepress_display" data-setting="stylepress_display">
					<option value="0">Modal</option>
					<option value="1">Slide In</option>
				</select>
			</div>
		</div>
		<div class="elementor-control-field elementor-stylepress-url-modal-block">
			<label class="elementor-control-stylepress-modal-title">modal width:</label>
			<div class="elementor-control-input-wrapper">
				<input type="number" data-setting="stylepress_width" />
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
			<# } #>
		<?php
	}
}
