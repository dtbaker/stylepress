<?php

namespace Elementor;

defined( 'STYLEPRESS_PATH' ) || exit;

class Stylepress_Inner_Content extends Widget_Base {

	/**
	 * Get Widgets name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'stylepress_inner';
	}

	/**
	 * Get widgets title
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Inner Content', 'stylepress' );
	}

	/**
	 * Get the current icon for display on frontend.
	 * The extra 'stylepress-widget' class is styled differently in frontend.css
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'stylepress-elementor-widget';
	}

	/**
	 * Get available categories for this widget. Which is our own category for page builder options.
	 *
	 * @return array
	 */
	public function get_categories() {
		return [ 'stylepress' ];
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
			'section_stylepress_wp_menu',
			[
				'label' => __( 'Inner Content', 'stylepress' ),
			]
		);


		$this->end_controls_section();

	}


	/**
	 * Render our custom menu onto the page.
	 */
	protected function render() {
		$editing_this_template = false;
		if ( Plugin::$instance->editor->is_edit_mode() || Plugin::$instance->preview->is_preview_mode() ) {
			$post = get_post();
			if ( $post->post_type === \StylePress\Styles::CPT ) {
				$editing_this_template = true;
			}
		}
		if ( ! $editing_this_template ) {
			//$settings = $this->get_settings();
			if ( ! is_404() ) {
				the_post();
				the_content();
			}
		} else {
			$this->content_template();
		}
	}

	/**
	 * This is outputted while rending the page.
	 */
	protected function content_template() {
		?>
		<div class="stylepress-inner-page-content-area">
			Your Page Content Will Appear Here
		</div>
		<?php
	}

}

Plugin::instance()->widgets_manager->register_widget_type( new Stylepress_Inner_Content() );

