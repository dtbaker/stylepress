<?php
/**
 * Our Plugin class.
 * This handles all our hooks and stuff.
 *
 * @package stylepress
 */

namespace StylePress;

use StylePress\Styles\Cpt;

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
			if ( $post->post_type === Cpt::CPT ) {
				$post_categories = get_the_terms( $post->ID, STYLEPRESS_SLUG . '-cat' );
				$categories      = \StylePress\Styles\Data::get_instance()->get_categories();
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



}

