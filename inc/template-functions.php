<?php
/**
 * Template Functions
 *
 * @package dtbaker-elementor
 *
 * (just the do_content hook for the elementor widget, maybe more later on)
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

if ( ! function_exists( 'dtbaker_elementor_page_content' ) ) {

	/**
	 * Renderes the_content() from our Elementor widget hook.
	 * Other plugins can hook in before stylepress/render-inner@20 to show content before/after
	 *
	 * @param array $settings Elementor settings from this particular widget. Empty for now but may contain settings down the track.
	 */
	function dtbaker_elementor_page_content( $settings = array() ) {

		if(!empty($GLOBALS['stylepress_rendering_inner'])){
			if(DTBAKER_ELEMENTOR_DEBUG_OUTPUT) echo "<pre>StylePress Debug: \nRendering inner content.</pre>";
			echo '<!-- Start Inner Render Contetn --> ';
			echo do_shortcode( get_the_content() );
			echo '<!-- End Inner Render Contetn --> ';
			return;
		}
		echo '<!-- Start StylePress Render --> ';
		$GLOBALS['stylepress_rendering_inner'] = true;

		$current_page_type = DtbakerElementorManager::get_instance()->get_current_page_type();

		$debug_info = "StylePress Debug: \n";
		$debug_info .= "Current page type: ".$current_page_type ."\n";

		$style_settings = DtbakerElementorManager::get_instance()->get_settings();

		$component_template = false;
		if( is_home() || is_front_page() ){
			// home page or blog output page.
			if ( 'page' == get_option( 'show_on_front' ) && is_front_page() && get_option( 'page_on_front' ) ) {
				$debug_info .= "Showing standard page on front, just run the_content() \n";
			}else{
				$debug_info .= "Showing blog output on front page \n \n";
				// look for a content template to use.
				// we use 'post_summary' for this.
				$component_template = 'post_summary';
			}
		}else if($current_page_type){
			switch($current_page_type){
				case 'post':
					$component_template = 'post_single';
					break;
				case 'page':
					$component_template = 'page_single';
					break;
			}
		}

		if(DTBAKER_ELEMENTOR_DEBUG_OUTPUT) echo '<pre>'.$debug_info.'</pre>';

		while ( have_posts() ) : the_post();

			global $post;
			$debug_info = "Current Post ID is ".$post->ID."\n";
			$GLOBALS['stylepress_post_for_dynamic_fields'] = $post->ID;

			$style_id = false;
			if( $component_template ){
				// loading this component/
				$debug_info .= "Loading compontent ".$component_template."\n";

				if(!empty($style_settings['defaults'][$component_template])){
					$style_id = (int) $style_settings['defaults'][$component_template];
					$debug_info .= "Found style id " . $style_id . "\n";

				}

			}
			if(!$style_id){
				$debug_info .= "No custom style, Running the_content() \n";
			}
			if(DTBAKER_ELEMENTOR_DEBUG_OUTPUT) echo '<pre>'.$debug_info.'</pre>';

			if($style_id) {
				echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $style_id );
			}else{
				the_content();
			}

		endwhile;

		// work out if we have an inner component for this particular post style.
		echo '<!-- End StylePress Render --> ';
		$GLOBALS['stylepress_rendering_inner'] = false;

	}
}
add_action( 'stylepress/render-inner', 'dtbaker_elementor_page_content', 20 );
