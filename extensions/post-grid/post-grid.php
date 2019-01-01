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


if ( ! function_exists( 'stylepress_posted_on' ) ) :

	function stylepress_posted_on() {
		$time_string_posted  = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
		$time_string_updated = '<time class="entry-date updated" datetime="%1$s">%2$s</time>';
		$time_string_posted  = sprintf( $time_string_posted,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() )
		);
		$time_string_updated = sprintf( $time_string_updated,
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			esc_html_x( '%s', 'post date', 'stylepress' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string_posted . '</a>'
		);

		$updated_on = sprintf(
			esc_html_x( '%s', 'post date', 'stylepress' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string_updated . '</a>'
		);

		$byline = sprintf(
			esc_html_x( '%s', 'post author', 'stylepress' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span>' . '<span class="updated-on">' . $updated_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.
	}
endif;

if ( ! function_exists( 'stylepress_entry_header' ) ) :

	function stylepress_entry_header() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'stylepress' ) );
			if ( $categories_list ) {
				printf( '<span class="cat-links">' . esc_html__( ' %1$s ', 'stylepress' ) . '</span>', $categories_list ); // WPCS: XSS OK.
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html__( ', ', 'stylepress' ) );
			if ( $tags_list ) {
				printf( '<span class="tags-links">' . esc_html__( ' %1$s', 'stylepress' ) . '</span>', $tags_list ); // WPCS: XSS OK.
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			/* translators: %s: post title */
			comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'stylepress' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
			echo '</span>';
		}
	}
endif;

add_action( 'edit_category', 'stylepress_category_transient_flusher' );
add_action( 'save_post', 'stylepress_category_transient_flusher' );


