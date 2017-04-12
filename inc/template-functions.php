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

		$current_page_type = DtbakerElementorManager::get_instance()->get_current_page_type();

		if(!isset($GLOBALS['stylepress_template_turtles'])){
			$GLOBALS['stylepress_template_turtles'] = array();
		}

		\DtbakerElementorManager::get_instance()->debug_message("template-functions.php: Rendering from stylepress/render-inner action hook ");

		if(!empty($GLOBALS['stylepress_render_this_template_inside'])){
			// hook here on our header/footer callbacks to strip double rendered content.
			ob_start(); // catches the content from our template below
			add_action('ocean_before_main', function(){
				ob_end_clean();
				ob_start();
				// capture all inner theme output and render it here.
			});
			add_action('ocean_after_main', function(){
				echo ob_get_clean();
				ob_start(); // kill the footer.
			});
			require $GLOBALS['stylepress_render_this_template_inside'];
			ob_end_clean(); // kill the footer.

			return;
		}

		if(count($GLOBALS['stylepress_template_turtles'])){
			\DtbakerElementorManager::get_instance()->debug_message("template-functions.php: Nested inner content for ". $current_page_type .".");

			// save and restore global post entry while we do this.
			if ( isset( $GLOBALS['post'] ) ) {
				$global_post = $GLOBALS['post'];
			}

			if(!empty($GLOBALS['stylepress_post_for_dynamic_fields'])){
				if(is_object($GLOBALS['stylepress_post_for_dynamic_fields'])){
					$GLOBALS['post'] = $GLOBALS['stylepress_post_for_dynamic_fields'];
					setup_postdata($GLOBALS['post']);
				}
			}
			echo '<!-- Start Inner Render Content for ID '.(int)get_the_ID().' --> ';
			// is this page we're trying to edit an elementor page?

			if(!empty($settings['output_type'])){
				switch($settings['output_type']){
					case 'full':
						the_content();
						break;
					case 'raw':
						echo do_shortcode( get_the_content() );
						break;
					case 'excerpt':
						echo do_shortcode( get_the_excerpt() );
						break;
				}
			}else {
				// todo: make these options in the settings array.
				switch ( $current_page_type ) {
					case 'archive':
						echo do_shortcode( get_the_excerpt() );
						break;
					default:
//					echo do_shortcode( get_the_content() );
						the_content();
//					echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( get_the_ID() );
				}
			}

			// Restore global post
			if ( isset( $global_post ) ) {
				$GLOBALS['post'] = $global_post;
				setup_postdata($GLOBALS['post']);
			} else {
				unset( $GLOBALS['post'] );
			}

			echo '<!-- End Inner Render Content --> ';
			return;
		}
		echo '<!-- Start StylePress Render --> ';


		\DtbakerElementorManager::get_instance()->debug_message("template-functions.php: Current page type for inner content style lookup is: $current_page_type ");

		while ( have_posts() ) : the_post();

			global $post;
			$debug_info = "Rendering Post ID <code>".$post->ID."</code> ";

			$GLOBALS['stylepress_post_for_dynamic_fields'] = $post;

			$style_id = $GLOBALS['our_elementor_inner_template'];
			$current_inner_style = (int) DtbakerElementorManager::get_instance()->get_page_inner_style($post->ID);
			if($current_inner_style){
				// override default in loop.
				// hmm this might not work well in output of a blog.
//				$style_id = $current_inner_style;
			}

			if($style_id > 0) {
				$GLOBALS['stylepress_template_turtles'][$style_id] = $style_id;
				\DtbakerElementorManager::get_instance()->debug_message("template-functions.php: Rendering style: $style_id ");
				echo Elementor\Plugin::instance()->frontend->get_builder_content( $style_id, false );
			}else{
				\DtbakerElementorManager::get_instance()->debug_message("template-functions.php: Rendering plain content: $style_id ");
				// todo: handle inner theme output from here.
				the_content();
			}

		endwhile;

		// work out if we have an inner component for this particular post style.
		echo '<!-- End StylePress Render --> ';

	}
}
add_action( 'stylepress/render-inner', 'dtbaker_elementor_page_content', 20 );
