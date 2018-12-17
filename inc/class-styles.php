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
 * Class Styles
 */
class Styles extends Base {


	/**
	 * Initializes the plugin and sets all required filters.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		add_action( 'init', array( $this, 'register_custom_post_type' ) );

	}

	/**
	 * Here is our magical custom post type that stores all our Elementor site wide styles.
	 *
	 * @since 2.0.0
	 */
	public function register_custom_post_type() {

		$labels = array(
			'name'               => 'Styles',
			'singular_name'      => 'Style',
			'menu_name'          => 'Styles',
			'parent_item_colon'  => 'Parent Style:',
			'all_items'          => 'All Styles',
			'view_item'          => 'View Style',
			'add_new_item'       => 'Add New Style',
			'add_new'            => 'New Style',
			'edit_item'          => 'Edit Style',
			'update_item'        => 'Update Style',
			'search_items'       => 'Search Styles',
			'not_found'          => 'No Styles found',
			'not_found_in_trash' => 'No Styles found in Trash',
		);

		$args = array(
			'description'         => 'Styles',
			'labels'              => $labels,
			'supports'            => array( 'title', 'author', 'thumbnail', 'elementor', 'page-attributes' ),
			'taxonomies'          => array(),
			'hierarchical'        => true,
			'public'              => true,
			'show_in_menu'        => STYLEPRESS_DEBUG_OUTPUT ? true : false,
			'show_in_nav_menus'   => true,
			'exclude_from_search' => true,
			'menu_position'       => 36,
			'menu_icon'           => 'dashicons-star-filled',
			'can_export'          => true,
			'has_archive'         => false,
			'publicly_queryable'  => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		);

		register_post_type( STYLEPRESS_SLUG . '-style', $args );

		register_taxonomy(
			STYLEPRESS_SLUG . '-cat',
			STYLEPRESS_SLUG . '-style',
			array(
				'hierarchical' => false,
				'label'        => 'Category',
			)
		);

	}

	public function get_categories() {
		$stylepress_categories   = [];
		$stylepress_categories[] = [
			'order'       => 10,
			'slug'        => 'header',
			'title'       => 'Header',
			'plural'      => 'Headers',
			'description' => 'These show at ..',
		];
		$stylepress_categories[] = [
			'order'       => 20,
			'slug'        => 'hero',
			'title'       => 'Hero',
			'plural'      => 'Heros',
			'description' => 'These show at ..',
		];
		$stylepress_categories[] = [
			'order'       => 30,
			'slug'        => 'content',
			'title'       => 'Content',
			'plural'      => 'Content Area',
			'inner'       => true,
			'description' => 'These show at ..',
		];
		$stylepress_categories[] = [
			'order'       => 40,
			'slug'        => 'footer',
			'title'       => 'Footer',
			'plural'      => 'Footers',
			'description' => 'These show at ..',
		];

		return apply_filters( 'stylepress_categories', $stylepress_categories );
	}

	public function get_all_styles( $category_slug = false ) {
		$styles = array();
		$args   = array(
			'post_type'           => STYLEPRESS_SLUG . '-style',
			'post_status'         => 'publish',
			'posts_per_page'      => - 1,
			'ignore_sticky_posts' => 1,
			'suppress_filters'    => false,
			'order'               => 'ASC',
			'orderby'             => 'title',
			'post_parent'         => 0,
		);
		if ( $category_slug ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => STYLEPRESS_SLUG . '-cat',
					'field'    => 'slug',
					'terms'    => $category_slug,
				)
			);
		}
		$posts_array = get_posts( $args );
		foreach ( $posts_array as $style ) {
			$styles[ $style->ID ] = $style->post_title;
		}

		return $styles;
	}

}

