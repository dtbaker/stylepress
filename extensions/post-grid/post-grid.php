<?php

defined( 'STYLEPRESS_PATH' ) || exit;

require_once STYLEPRESS_PATH . 'extensions/post-grid/elementor.post-grid.php';

add_action( 'wp_ajax_stylepress_grid_ajax_tax', function () {
	if ( ! empty( $_POST['admin_nonce'] ) && ! empty( $_POST['post_type'] ) ) {
		$nonce = $_POST['admin_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'stylepress-admin-nonce' ) ) {
			wp_die( 'Not allowed' );
		}
		$post_type     = sanitize_text_field( $_POST['post_type'] );
		$taxonomoies   = get_object_taxonomies( $post_type, 'names' );
		$taxonomy_name = array();
		foreach ( $taxonomoies as $taxonomy ) {
			$taxonomy_name[] = array( 'name' => $taxonomy );

		}
		wp_send_json_success( $taxonomy_name );
	}
	wp_die();

} );

add_action( 'wp_ajax_stylepress_grid_ajax_terms', function () {
	if ( ! empty( $_POST['admin_nonce'] ) && ! empty( $_POST['taxonomy_type'] ) ) {
		$nonce = $_POST['admin_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'stylepress-admin-nonce' ) ) {
			wp_die( 'Not allowed' );
		}
		$taxonomy_type = sanitize_text_field( $_POST['taxonomy_type'] );
		$term_slug     = array();
		$terms         = get_terms( $taxonomy_type );
		foreach ( $terms as $term ) {
			$term_slug[] = array(
				'id'   => $term->term_id,
				'name' => $term->name
			);
		}

		wp_send_json_success( $term_slug );
	}
	wp_die();
} );

add_action( 'pre_get_posts', function ( $query ) {
	if ( $query->get( 'stylepress_grid_query' ) === 'yes' && ! $query->is_main_query() ) {
		//$query->set( 'offset', $query->get( 'stylepress_set_offset' ) );
		$offset = $query->get( 'stylepress_set_offset' );

		//Next, determine how many posts per page you want (we'll use WordPress's settings)
		$post_per_page = $query->get( 'posts_per_page' );

		//Next, detect and handle pagination...
		if ( $query->is_paged ) {

			//Manually determine page query offset (offset + current page (minus one) x posts per page)
			$page_offset = $offset + ( ( $query->query_vars['paged'] - 1 ) * $post_per_page );

			//Apply adjust page offset
			$query->set( 'offset', $page_offset );

		} else {

			//This is the first page. Just use the offset...
			$query->set( 'offset', $offset );

		}
	}
} );

add_filter( 'found_posts', function ( $found_posts, $query ) {
	if ( $query->get( 'stylepress_grid_query' ) == 'yes' && ! $query->is_main_query() ) {
		$offset = $query->get( 'stylepress_set_offset' );

		return $found_posts - $offset;
	}

	return $found_posts;
}, 1, 2 );
