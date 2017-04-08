<?php


defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


// this filter will only fire on shop pages.
add_filter('woocommerce_template_loader_files',function($search_files, $template_file){
	// check if we have a special template file just for this one.

	$file = basename($template_file);
	if($file && file_exists( DTBAKER_ELEMENTOR_PATH . 'extensions/woocommerce/templates/'.$file)){
		// hacky hack hack.
		$plugin_slug_dir = str_replace(WP_CONTENT_DIR, '', WP_PLUGIN_DIR);
		$search_files[] = '../..'.$plugin_slug_dir.'/' . DTBAKER_ELEMENTOR_SLUG . '/extensions/woocommerce/templates/'.$file;
	}

	return $search_files;
}, 5, 2);

add_action('stylepress/render-inner', function(){

	if(function_exists('WC') && class_exists('WC_Template_Loader')) {
		if( $template_file = WC_Template_Loader::template_loader('') ) {
			// this will only fire on shop pages.
			if( file_exists($template_file)) {

				\DtbakerElementorManager::get_instance()->debug_message("woocommerce.php: including a WooCommerce template ( ".basename($template_file)." )");

				remove_action( 'stylepress/render-inner', 'dtbaker_elementor_page_content', 20 );

				// we undo generatepress (and potentially other) theme damage:

				global $wp_filter;

				$comment_filters = array ();
				$out = '';

				foreach ( $wp_filter as $key => $val )
				{
					if ( FALSE !== strpos( $key, 'woocommerce' ) )
					{
						$comment_filters[$key][] = var_export( $val, TRUE );
					}
				}

				foreach ( $comment_filters as $name => $arr_vals )
				{
					$out .= "<h2 id=$name>$name</h2><pre>" . implode( "\n\n", $arr_vals ) . '</pre>';
				}
//				echo $out;

				remove_action( 'woocommerce_before_main_content', 'stylepress_woocommerce_start', 10);
				remove_action( 'woocommerce_after_main_content', 'stylepress_woocommerce_end', 10);
				remove_action( 'woocommerce_before_main_content', 'generatepress_woocommerce_start', 10);
				remove_action( 'woocommerce_after_main_content', 'generatepress_woocommerce_end', 10);
				// undo twentyseventeen damage:
				if(class_exists('WC_Twenty_Seventeen')) {
					remove_action( 'woocommerce_before_main_content', array( 'WC_Twenty_Seventeen', 'output_content_wrapper' ), 10 );
					remove_action( 'woocommerce_after_main_content', array( 'WC_Twenty_Seventeen', 'output_content_wrapper_end' ), 10 );
					remove_filter( 'woocommerce_enqueue_styles', array( 'WC_Twenty_Seventeen', 'enqueue_styles' ) );
				}

				add_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
				add_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);


				include $template_file;
			}

		}
	}
}, 5);