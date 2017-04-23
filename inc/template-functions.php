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


		\DtbakerElementorManager::get_instance()->debug_message("template-functions.php: Current page type for inner content style lookup is: $current_page_type ");

		if(!empty($GLOBALS['stylepress_render_this_template_inside'])){
			// hook here on our header/footer callbacks to strip double rendered content.

            $theme_hooks = apply_filters('stylepress_theme_hooks',array());

            if(!empty($theme_hooks['before']) && !empty($theme_hooks['after'])){

                ob_start();

                ?><!DOCTYPE html>
                <html <?php language_attributes(); ?> class="no-js">
                <head>
                    <meta charset="<?php bloginfo( 'charset' ); ?>">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <link rel="profile" href="http://gmpg.org/xfn/11">
                    <?php wp_head(); ?>
                </head>

                <body <?php body_class('stylepress-render'); ?>>
                <?php

                $page_type = DtbakerElementorManager::get_instance()->get_current_page_type();
                DtbakerElementorManager::get_instance()->debug_message("render.php: Rendering full page output for page type '$page_type' in render.php using the style: ". (
                    !empty($GLOBALS['our_elementor_template']) ? '<a href="'.get_permalink($GLOBALS['our_elementor_template']).'">' . esc_html(get_the_title($GLOBALS['our_elementor_template'])) .'</a> ' . $GLOBALS['our_elementor_template'] : 'NONE'
                    ).'');

                if(DtbakerElementorManager::get_instance()->removing_theme_css) {
                    DtbakerElementorManager::get_instance()->debug_message( "render.php: Removing the default theme CSS files" );
                }

                do_action( 'stylepress/before-render' );
                $GLOBALS['stylepressheader'] = ob_get_clean();
                ob_start(); // kill the theme header from the below include.
                add_action($theme_hooks['before'], function(){
                    $old_header = ob_get_clean(); // kill the header
                    ob_start(); // capture all inner theme output and render it here.
                });
                add_action($theme_hooks['after'], function(){
                    // we have to break out of the template rendering and continue to render the stylepress footer from here on in.
                    $inner = ob_get_clean(); // capture all inner
                    echo $inner;
                    // render out stylepress footer
                    ob_start();
                    do_action( 'stylepress/after-render' );
                    wp_footer();
                    ?>
                    </body>
                    </html>
                    <?php
                    $GLOBALS['stylepressfooter'] = ob_get_clean();
                    ob_start(); // kill the rest of the theme geneated footer.
                });

                $template = $GLOBALS['stylepress_render_this_template_inside'];
                unset($GLOBALS['stylepress_render_this_template_inside']);

                require $template;
                ob_end_clean(); // kill the footer.
                return;
            }
		}

		echo '<!-- Start StylePress Render --> ';

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
