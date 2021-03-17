<?php
/**
 * Our Plugin class.
 * This handles all our hooks and stuff.
 *
 * @package stylepress
 */

namespace StylePress\Styles;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Styles
 */
class Cpt extends \StylePress\Core\Base {

	const CPT = STYLEPRESS_SLUG . '-style';

	/**
	 * Initializes the plugin and sets all required filters.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_custom_post_type' ) );
		add_filter( 'edit_form_after_title', array( $this, 'edit_form_after_title' ), 5 );
		add_filter( 'use_block_editor_for_post', array( $this, 'use_block_editor_for_post' ), 10, 2 );
	}

	public function use_block_editor_for_post( $use_block_editor, $post ) {
		if ( $post->post_type === self::CPT && $post->post_parent === 0 ) {
			remove_post_type_support( self::CPT, 'editor' );

			return false;
		}

		return $use_block_editor;
	}

	/**
	 * Here is our magical custom post type that stores all our site wide styles.
	 *
	 * @since 2.0.0
	 */
	public function register_custom_post_type() {

		$labels = array(
			'name'               => 'Styles',
			'singular_name'      => 'Style',
			'menu_name'          => 'StylePress',
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
			'supports'            => array(
				'title',
				'author',
				'thumbnail',
				'elementor',
				'page-attributes',
				'revisions',
				'editor'
			),
			'taxonomies'          => array(),
			'hierarchical'        => true,
			'public'              => defined( 'STYLEPRESS_ALLOW_EXPORT' ) && STYLEPRESS_ALLOW_EXPORT,
			'show_in_menu'        => defined( 'STYLEPRESS_ALLOW_EXPORT' ) && STYLEPRESS_ALLOW_EXPORT,
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
			'show_in_rest'        => true,
		);

		register_post_type( self::CPT, $args );

		register_taxonomy(
			STYLEPRESS_SLUG . '-cat',
			self::CPT,
			array(
				'hierarchical' => true,
				'label'        => 'Category',
				'show_in_rest' => true,
			)
		);

	}

	/**
	 * Adds a meta box to every post type.
	 *
	 * @since 2.0.0
	 *
	 * @var \WP_Post $post The current displayed post.
	 */
	public function edit_form_after_title( $post ) {

		if ( self::CPT === $post->post_type ) {

			$parent = $post->post_parent ? (int) $post->post_parent : ( ! empty( $_GET['post_parent'] ) ? (int) $_GET['post_parent'] : false );
			?>
			<div class="stylepress__header">
				<div class="stylepress__logo">
					<img alt="StylePress"
					     src="<?php echo esc_url( STYLEPRESS_URI . 'src/images/logo-stylepress-sml.png' ); ?>">
				</div>
				<div class="stylepress_buttons">
					<a
						href="<?php echo esc_url( admin_url( 'admin.php?page=' . \StylePress\Styles\Styles::PAGE_SLUG . '&style_id=' . ( $parent ? $parent : $post->ID ) ) ); ?>"
						class="button stylepress_buttons--return"><?php echo esc_html__( '&laquo; Return To Style Settings Page', 'stylepress' ); ?></a>
				</div>
			</div>
			<?php
		}

	}


}

