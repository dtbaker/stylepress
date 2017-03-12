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
	 * Other plugins can hook in before elementor/full-page/inner@20 to show content before/after
	 *
	 * @param array $settings Elementor settings from this particular widget. Empty for now but may contain settings down the track.
	 */
	function dtbaker_elementor_page_content( $settings = array() ) {

		global $post;
//		echo $post->ID . ' = '. DtbakerElementorManager::get_instance()->get_current_page_type();

		if( is_home() || is_front_page() ){
			// home page or blog output page.
			if ( 'page' == get_option( 'show_on_front' ) && is_front_page() && get_option( 'page_on_front' ) ) {
//				echo "Showing standard page on front, continue to the_content() below:";
			}else{
//				echo "Showing blog output below: ";
				// look for a content template to use.

			}
		}else {

			switch ( DtbakerElementorManager::get_instance()->get_current_page_type() ) {

			}
		}

		// work out if we have an inner component for this particular post style.


		the_content();
	}
}
add_action( 'elementor/full-page/inner', 'dtbaker_elementor_page_content', 20 );
