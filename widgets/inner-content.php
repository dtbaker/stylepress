<?php
/**
 * Inner Content Elementor Widget
 *
 * @package dtbaker-elementor
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Creates our custom Elementor widget
 *
 * Class Widget_Dtbaker_Inner_Content
 *
 * @package Elementor
 */
class Widget_Dtbaker_Inner_Content extends Widget_Base {

	/**
	 * Get Widgets name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'dtbaker_inner_content';
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
	 * We only show this item when we're editing a 'dtbaker_style' post type.
	 *
	 * @return bool
	 */
	public function show_in_panel() {
	    global $post;
	    return 'dtbaker_style' === $post->post_type;
	}

	/**
	 * This registers our controls for the widget. Currently there are none but we may add options down the track.
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_dtbaker_inner_content',
			[
				'label' => __( 'Inner Website Content', 'stylepress' ),
			]
		);


		$this->add_control(
			'output_type',
			[
				'label' => __( 'Output Type', 'stylepress' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'full',
				'options' => [
					'full' => __( 'Full Output - the_content()', 'stylepress' ),
					'raw' => __( 'Raw Output - the_content() without hooks', 'stylepress' ),
					'excerpt' => __( 'Summary Output - the_excerpt()', 'stylepress' ),
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __( 'This will display the inside website content. <br/><br/>i.e. the output from <code>the_content();</code>', 'stylepress' ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);

		$this->end_controls_section();

		do_action( 'dtbaker_inner_content_elementor_controls', $this );

	}

	/**
	 * Render our widget. This is called while public browsing the site and also while editing the site.
	 * So we have to do some trickerly depending on what page we're editing and the current edit mode.
	 */
	protected function render() {
		$settings = $this->get_settings();

		\DtbakerElementorManager::get_instance()->debug_message("inner-content.php: inside render() method. ".get_the_ID());

		if ( \DtbakerElementorManager::get_instance()->previewing_style ) {
			$this->content_template();
		} elseif ( Plugin::$instance->editor->is_edit_mode() || Plugin::$instance->preview->is_preview_mode() ) {

			if ( empty( $GLOBALS['our_elementor_template'] ) ) {
				$this->content_template();
				// we have to display the_content() for elementor editor to work.
				if(Plugin::$instance->editor->is_edit_mode()){
				    // todo; show warning about a missing elementor template .
//                    the_content();
				}
			} else {
				the_content();
//				do_action( 'stylepress/render-inner', $settings ); // Priority 20 is the_content().
			}
		} else {


			/*if( apply_filters('stylepress_rendered_header',false) && !empty( $GLOBALS['stylepress_only_render'] )){

				\DtbakerElementorManager::get_instance()->debug_message("inner-content.php: Now rendering ".$GLOBALS['stylepress_only_render']." from within the inner-content.php render()");

			    // we are splitting header/footer up into multiple renders.
                // haha. Hows your brain going now trying to follow this code???
                // e p i c
                if( $GLOBALS['stylepress_only_render'] == 'header' ){
                    // we only want the header to return now. save our footer render in OB so we can print it later on :)
	                $GLOBALS['stylepress_footer'] = "(stylepress footer)";
                    ob_start();
                }
            }else {*/

				do_action( 'stylepress/render-inner', $settings ); // Priority 20 is the_content().
			/*}*/
		}

	}

	/**
	 * Static content used in Elementor.
	 */
	protected function content_template() {
		?>
		<div class="inner-page-content-area">
		Inner Website Content <br/>Will Display Here
		</div>
		<?php
	}

}


Plugin::instance()->widgets_manager->register_widget_type( new Widget_Dtbaker_Inner_Content() );