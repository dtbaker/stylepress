<?php
/**
 * Our Plugin class.
 * This handles all our hooks and stuff.
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Plugin
 */
class Plugin extends Base {
	public function is_editing_internal_content_page() {
		$is_inner_content_page = false;
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$post = get_post();
			if ( $post->post_type === Styles::CPT ) {
				$post_categories = get_the_terms( $post->ID, STYLEPRESS_SLUG . '-cat' );
				$categories      = Styles::get_instance()->get_categories();
				foreach ( $categories as $category ) {
					foreach ( $post_categories as $post_category ) {
						if ( $post_category->slug === $category['slug'] && ! empty( $category['inner'] ) ) {
							$is_inner_content_page = true;
						}
					}
				}
			}
		}

		return $is_inner_content_page;
	}


	public function has_permission( $post = false ) {
		return current_user_can( 'edit_posts' );
		//current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' )
	}


	public function populate_globals() {
		if ( isset( $GLOBALS['stylepress_render'] ) ) {
			return;
		}
		global $post;
		$GLOBALS['stylepress_render'] = [];

		if ( $post && ! empty( $post->ID ) && 'elementor_library' === $post->post_type ) {
			$page_templates_module = \Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' );
			$path                  = $page_templates_module->get_template_path( 'elementor_canvas' );
			if ( is_file( $path ) ) {
				$GLOBALS['stylepress_render']['template'] = $path;
			}
		} else if ( $post && ! empty( $post->ID ) && Styles::CPT === $post->post_type ) {
			$GLOBALS['stylepress_render']['template'] = STYLEPRESS_PATH . 'templates/editor.php';
		}

		$default_styles = Styles::get_instance()->get_default_styles();
		$page_type      = $this->get_current_page_type();
		$these_styles   = isset( $default_styles[ $page_type ] ) ? $default_styles[ $page_type ] : false;

		$queried_object = get_queried_object();
		if ( $these_styles ) {
			// If stylepress has been disabled for this particular post then we just use the normal template include.
			// Not sure how to do this for category pages. We'll have to add a taxonomy settings area to each tax.
			if ( $queried_object && $queried_object instanceof \WP_Post && $queried_object->ID ) {
				$enabled = Styles::get_instance()->is_stylpress_enabled( $post );
				if ( ! $enabled['enabled'] ) {
					$this->debug_message( 'Skipping stylepress template because ' . $enabled['reason'] );
					$GLOBALS['stylepress_render']['template'] = false;
				}
				// todo: confirm our queried object isn't the first blog post in a list of things view.. that would mess it up.
				// We're doing a single object post, should be easy.
				$page_styles = Styles::get_instance()->get_page_styles( $queried_object->ID );
				if ( $page_styles ) {
					foreach ( $page_styles as $category_slug => $chosen_style_id ) {
						if ( $chosen_style_id != 0 ) {
							$these_styles[ $category_slug ] = $chosen_style_id;
						}
					}
				}
			}
		}
		if ( ! isset( $GLOBALS['stylepress_render']['template'] ) ) {
			$GLOBALS['stylepress_render']['template'] = STYLEPRESS_PATH . 'templates/render.php';
		}
		$GLOBALS['stylepress_render']['queried_object'] = $queried_object;
		$GLOBALS['stylepress_render']['page_type']      = $page_type;
		$GLOBALS['stylepress_render']['styles']         = $these_styles;
	}

	public function get_active_style_id() {
		return get_theme_mod( 'stylepress_active_style_id' );
	}

	public function set_active_style_id( $style_id ) {
		set_theme_mod( 'stylepress_active_style_id', $style_id );
	}

	public function get_active_style_data() {
		$active_style_id = $this->get_active_style_id();
		if($active_style_id) {
			return $this->get_style_data( $active_style_id );
		}
	}

	/**
	 * When a remote style is imported it comes with a bunch of data we use for TGM and other things.
	 * This is stored in the stylepress_data post meta object.
	 *
	 * @param $style_id
	 *
	 * @return mixed
	 */
	public function get_style_data( $style_id ) {
		return get_post_meta($style_id, 'stylepress_data', true);
	}

	/**
	 * Works out the type of page we're currently quer\ying.
	 * Copied from my Widget Area Manager plugin
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function get_current_page_type() {
		global $wp_query;
		//print_r($wp_query);
		if ( is_search() ) {
			return 'search';
		} else if ( is_404() ) {
			return '404';
		} else if ( function_exists( 'is_product' ) && is_product() ) {
			return 'product';
		} else if ( function_exists( 'is_product_category' ) && is_product_category() ) {
			return 'product_category';
		} else if ( is_category() ) {
			return 'category';
		} else if ( isset( $wp_query->query_vars ) && isset( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] ) {
			return $wp_query->query_vars['post_type'] . ( is_singular() ? '' : 's' );
		} else if ( isset( $wp_query->query_vars['taxonomy'] ) && $wp_query->query_vars['taxonomy'] ) {
			$current_page_id = $wp_query->query_vars['taxonomy'];
			$value           = get_query_var( $wp_query->query_vars['taxonomy'] );
			if ( $value ) {
				$current_page_id .= '_' . $value;
			}

			return $current_page_id;
		} else if ( isset( $wp_query->is_posts_page ) && $wp_query->is_posts_page ) {
			return 'archive';
		} else if ( is_archive() ) {
			return 'archive';
		} else if ( is_home() || is_front_page() ) {
			return 'front_page';
		} else if ( is_attachment() ) {
			return 'attachment';
		} else if ( is_page() ) {
			return 'page';
		} else if ( is_single() ) {
			return 'post';
		}

		// todo - look for custom taxonomys
		return 'post';
	}


	public function stylepress_export() {

		if ( ! isset( $_GET['stylepress_export_data'] ) || empty( $_GET['post_id'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_GET['stylepress_export_data'], 'stylepress_export_data' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}

		$post_id = (int) $_GET['post_id'];

		if ( ! $this->has_permission( $post_id ) ) {
			return;
		}

		require_once STYLEPRESS_PATH . 'inc/class.import-export.php';
		$import_export = StylepressImportExport::get_instance();
		$data          = $import_export->export_data( $post_id );

		echo '<pre>';
		print_r( $data );
		echo '</pre>';
		exit;

		wp_send_json( $data );

		exit;
	}

	public function stylepress_clone() {

		if ( ! isset( $_GET['stylepress_clone'] ) || empty( $_GET['post_id'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_GET['stylepress_clone'], 'stylepress_clone' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}

		$post_id = (int) $_GET['post_id'];

		$post = get_post( $post_id );

		/*
		 * if post data exists, create the post duplicate
		 */
		if ( $post && Styles::CPT === $post->post_type ) {

			if ( ! $post->post_parent ) {
				$post->post_parent = $post_id; // we're cloaning the parent one, put it underneath itself.
			}
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $post->post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => $post->post_status,
				'post_title'     => '(clone) ' . $post->post_title,
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order
			);

			/*
			 * insert the post by wp_insert_post() function
			 */
			$new_post_id = wp_insert_post( $args );

			if ( $new_post_id ) {
				global $wpdb;
				/*
				 * duplicate all post meta just in two SQL queries
				 */
				$post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id" );
				if ( count( $post_meta_infos ) != 0 ) {
					$sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
					$sql_query_sel = array();
					foreach ( $post_meta_infos as $meta_info ) {
						$meta_key        = $meta_info->meta_key;
						$meta_value      = esc_sql( $meta_info->meta_value );
						$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
					}

					$sql_query .= implode( " UNION ALL ", $sql_query_sel );
					$wpdb->query( $sql_query );
				}

				wp_safe_redirect( get_edit_post_link( $new_post_id, 'edit' ) );
				exit;

			}


		}

		return false;

	}


	public function debug_message( $message ) {

		if ( STYLEPRESS_DEBUG_OUTPUT && is_user_logged_in() ) {
			echo '<div class="stylepress-debug">';
			echo '<span>StylePress:</span> &nbsp; ';
			echo $message;
			echo "</div>";
		}
	}

}

