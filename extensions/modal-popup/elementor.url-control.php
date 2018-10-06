<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * A URL input control. with the ability to set the target of the link to `_blank` to open in a new tab.
 *
 * @param array $default {
 *
 * @type string $url Default empty
 * @type bool   $is_external Determine whether to open the url in the same tab or in a new one
 *                                Default empty
 * }
 *
 * @param bool  $show_external Whether to show the 'Is External' button
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
			'is_external'         => '',
			'url'                 => '',
			'nofollow'            => '',
			'stylepress_template' => '',
			'stylepress_display'  => '',
			'stylepress_width'    => '500',
		];
	}

	protected function get_default_settings() {
		return [
			'label_block'      => true,
			'show_external'    => true,
			'stylepress_modal' => false,
		];
	}

	public function content_template() {

		$control_uid = $this->get_control_uid();

		$more_input_control_uid = $this->get_control_uid( 'more-input' );

		$is_external_control_uid = $this->get_control_uid( 'is_external' );

		$nofollow_control_uid = $this->get_control_uid( 'nofollow' );

		$popup_modal_control_uid         = $this->get_control_uid( 'stylepress_template' );
		$popup_modal_width_control_uid   = $this->get_control_uid( 'stylepress_width' );
		$popup_modal_display_control_uid = $this->get_control_uid( 'stylepress_display' );


		$options = [
			'0' => '- ' . __( 'None', 'elementor' ) . ' -',
		];

		$source    = \Elementor\Plugin::instance()->templates_manager->get_source( 'local' );
		$templates = $source->get_items();

		$types = [];

		foreach ( $templates as $template ) {
			$options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
			$types[ $template['template_id'] ]   = $template['type'];
		}

		?>


		<div class="elementor-control-field elementor-control-url-external-{{{ data.show_external ? 'show' : 'hide' }}}">
			<label for="<?php echo $control_uid; ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<input id="<?php echo $control_uid; ?>" type="url" class="elementor-input" data-setting="url"
				       placeholder="{{ data.placeholder }}"/>
				<label for="<?php echo $more_input_control_uid; ?>" class="elementor-control-url-more tooltip-target"
				       data-tooltip="<?php _e( 'Link Options', 'elementor' ); ?>">
					<i class="fa fa-cog"></i>
				</label>
				<input id="<?php echo $more_input_control_uid; ?>" type="checkbox" class="elementor-control-url-more-input">
				<div class="elementor-control-url-more-options">
					<div class="elementor-control-url-option">
						<input id="<?php echo $is_external_control_uid; ?>" type="checkbox"
						       class="elementor-control-url-option-input" data-setting="is_external">
						<label
							for="<?php echo $is_external_control_uid; ?>"><?php echo __( 'Open in new window', 'elementor' ); ?></label>
					</div>
					<div class="elementor-control-url-option">
						<input id="<?php echo $nofollow_control_uid; ?>" type="checkbox" class="elementor-control-url-option-input"
						       data-setting="nofollow">
						<label for="<?php echo $nofollow_control_uid; ?>"><?php echo __( 'Add nofollow', 'elementor' ); ?></label>
					</div>
					<div class="elementor-control-url-option">
						<label
							for="<?php echo $popup_modal_control_uid; ?>"><?php echo __( 'Show a modal window:', 'elementor' ); ?></label>
						<select name="stylepress_template" id="<?php echo $popup_modal_control_uid; ?>"
						        data-setting="stylepress_template">
							<?php foreach ( $options as $option_id => $option ) { ?>
								<option value="<?php echo (int) $option_id; ?>"><?php echo esc_html( $option ); ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="elementor-control-url-option">
						<label
							for="<?php echo $popup_modal_display_control_uid; ?>"><?php echo __( 'Modal Style:', 'elementor' ); ?></label>
						<select name="stylepress_display" id="<?php echo $popup_modal_display_control_uid; ?>"
						        data-setting="stylepress_display">
							<option value="0">Modal</option>
							<option value="1">Slide In</option>
						</select>
					</div>
					<div class="elementor-control-url-option">
						<label
							for="<?php echo $popup_modal_width_control_uid; ?>"><?php echo __( 'Modal Width:', 'elementor' ); ?></label>
						<input type="number" data-setting="stylepress_width" id="<?php echo $popup_modal_width_control_uid; ?>"/>
					</div>

				</div>
			</div>
		</div>


		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>

		<?php
	}
}
