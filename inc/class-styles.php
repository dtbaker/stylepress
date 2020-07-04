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

	const CPT = STYLEPRESS_SLUG . '-style';

	/**
	 * Initializes the plugin and sets all required filters.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_custom_post_type' ) );
		add_filter( 'edit_form_after_title', array( $this, 'edit_form_after_title' ), 5 );
		add_filter( 'elementor/init', array( $this, 'include_our_styles_in_elementor_popup' ), 10, 2 );
		add_filter( 'elementor/template-library/create_new_dialog_types', array( $this, 'dont_allow_new' ), 10, 2 );

	}

	public function dont_allow_new( $types ) {
		unset( $types['stylepress'] );

		return $types;
	}

	public function include_our_styles_in_elementor_popup( $option_value ) {
		require_once __DIR__ . '/elementor/source-stylepress.php';
		\Elementor\Plugin::$instance->templates_manager->register_source( '\Elementor\TemplateLibrary\Source_StylePress' );

		require_once __DIR__ . '/elementor/stylepress-document.php';
		\Elementor\Plugin::$instance->documents
			->register_document_type( 'stylepress', \Elementor\Modules\Library\Documents\Stylepress_Document::get_class_full_name() );
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
			'supports'            => array( 'title', 'author', 'thumbnail', 'elementor', 'page-attributes', 'revisions' ),
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
		);

		register_post_type( self::CPT, $args );

		register_taxonomy(
			STYLEPRESS_SLUG . '-cat',
			self::CPT,
			array(
				'hierarchical' => false,
				'label'        => 'Category',
			)
		);

	}

	public function get_categories() {
		$stylepress_categories   = [];
		$stylepress_categories[] = [
			'order'           => 10,
			'slug'            => 'header',
			'title'           => 'Header',
			'plural'          => 'Headers',
			'description'     => 'These are the header designs for this style.',
			'global_selector' => true,
			'render_section'  => true,
		];
		$stylepress_categories[] = [
			'order'           => 20,
			'slug'            => 'hero',
			'title'           => 'Hero',
			'plural'          => 'Heros',
			'description'     => 'These are the hero designs for this style.',
			'global_selector' => true,
			'render_section'  => true,
		];
		$stylepress_categories[] = [
			'order'           => 30,
			'slug'            => 'page_body',
			'title'           => 'Page Body',
			'plural'          => 'Page Body',
			'inner'           => true,
			'description'     => 'These are the page body layouts for this style.',
			'global_selector' => true,
			'render_section'  => true,
		];
		$stylepress_categories[] = [
			'order'           => 40,
			'slug'            => 'footer',
			'title'           => 'Footer',
			'plural'          => 'Footers',
			'description'     => 'These are the footer designs for this style.',
			'global_selector' => true,
			'render_section'  => true,
		];
		$stylepress_categories[] = [
			'order'                  => 50,
			'slug'                   => 'theme_styles',
			'title'                  => 'Theme Style',
			'plural'                 => 'Theme Styles',
			'description'            => 'These are global theme styles that apply to all elements on the page (i.e. link color).',
			'global_selector'        => true,
			'render_section'         => false,
			'is_elementor_kit_style' => true,
		];
		$stylepress_categories[] = [
			'order'           => 60,
			'slug'            => 'demo_content',
			'title'           => 'Demo Content',
			'plural'          => 'Demo Content',
			'description'     => 'These are demo content that can be inserted through out the website.',
			'global_selector' => false,
			'render_section'  => false,
		];

		return apply_filters( 'stylepress_categories', $stylepress_categories );
	}

	public function get_all_styles( $category_slug = false, $include_empty = false, $parent_id = false ) {
		$styles = array();
		$args   = array(
			'post_type'           => self::CPT,
			'post_status'         => 'publish',
			'posts_per_page'      => - 1,
			'ignore_sticky_posts' => 1,
			'suppress_filters'    => false,
			'order'               => 'ASC',
			'orderby'             => 'title',
		);
		if ( $parent_id !== false ) {
			$args['post_parent'] = (int) $parent_id;
		}
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
			if ( $parent_id === false && $style->post_parent ) {
				$parent            = get_post( $style->post_parent );
				$style->post_title = $parent->post_title . ' > ' . $style->post_title;
			}
			$styles[ $style->ID ] = $style->post_title;
		}

		if ( $include_empty ) {
			$styles = [ - 1 => '(Blank)' ] + $styles;
		}

		return $styles;
	}

	/**
	 * Returns a URL used to edit a particular design.
	 *
	 * @param int $design_id the design we want to edit.
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function get_design_edit_url( $design_id ) {
		// defaul to Elementor, but we want to support other page builders down the track.
		if ( class_exists( '\Elementor\Plugin' ) ) {
			if ( is_callable( '\Elementor\Plugin', 'instance' ) ) {
				return \Elementor\Plugin::$instance->documents->get( $design_id )->get_edit_url();
			}
		}

		return get_edit_post_link( $design_id, 'edit' );
	}

	/**
	 * This lets us query what the currently selected page template is for a particular post ID
	 * We use the other function to get the defaults for non-page-ID posts (like archive etc..)
	 *
	 * @param int $post_id Current post ID we're querying.
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function get_page_styles( $post_id ) {
		$current_option = get_post_meta( $post_id, 'stylepress_style', true );

		if ( ! is_array( $current_option ) ) {
			$current_option = [];
		}

		return $current_option;
	}

	/**
	 * Works out what template is currently selected for the current page/post/archive/search/404 etc.
	 * Copied from my Widget Area Manager plugin
	 *
	 * @return int
	 * @since 2.0.0
	 *
	 */
	public function get_default_styles() {

		$style_settings = Settings::get_instance()->get( 'stylepress_styles' );
		if ( ! $style_settings || ! is_array( $style_settings ) ) {
			$style_settings = [];
		}

		$categories = Styles::get_instance()->get_categories();
		foreach ( $categories as $category ) {
			if ( ! isset( $style_settings[ $category['slug'] ] ) ) {
				$style_settings[ $category['slug'] ] = false;
			}
		}

		return $style_settings;

	}


	// Todo: we need this for categories and 404 etc..
	public function is_stylpress_enabled( $post ) {
		if ( $post && $post->ID ) {
			$template = get_post_meta( $post->ID, '_wp_page_template', true );
			if ( $template && $template !== 'default' ) {
				return [
					'enabled' => false,
					'reason'  => 'StylePress disabled due to a custom template assigned to this page. Please remove the custom page template if you wish to use StylePress on this page.',
				];
			}
		}

		return [
			'enabled' => true,
		];
	}


	public function something() {

		global $post;

		if ( ! $ignore_override ) {

			if ( is_home() || is_front_page() ) {
				if ( 'page' == get_option( 'show_on_front' ) ) {
					$home_page_id = false;
					if ( is_front_page() ) {
						$home_page_id = get_option( 'page_on_front' );
					} else {
						$home_page_id = get_option( 'page_for_posts' );
					}
					if ( $home_page_id ) {
						$style = (int) $this->get_page_template( $home_page_id );
						if ( STYLEPRESS_OUTER_USE_THEME === $style ) {
							return $style; // Use theme by default.
						} else if ( $style > 0 ) {
							return apply_filters( 'stylepress_current_style', $style );
						}
					}
				}
			}
			if ( is_single() || is_page() || is_attachment() ) {
				// see if we have a custom style applied
				if ( $post && $post->ID ) {
					$style = (int) $this->get_page_template( $post->ID );
					if ( STYLEPRESS_OUTER_USE_THEME === $style ) {
						return $style; // Use theme by default.
					} else if ( $style > 0 ) {
						return apply_filters( 'stylepress_current_style', $style );
					}
				}
			}
		}

		// check for defaults for this page type
		$page_type = Plugin::get_instance()->get_current_page_type();
		if ( $page_type && ! empty( $style_settings['defaults'][ $page_type ] ) ) {
			return apply_filters( 'stylepress_current_style', $style_settings['defaults'][ $page_type ] );
		}
		// otherwise check for site wide default:
		if ( ! empty( $style_settings['defaults']['_global'] ) ) {
			return apply_filters( 'stylepress_current_style', $style_settings['defaults']['_global'] );
		}

		// otherwise return nothing, so we fallback to default standard theme
		return false;

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
						href="<?php echo esc_url( admin_url( 'admin.php?page=' . Backend::STYLES_PAGE_SLUG . '&style_id=' . ( $parent ? $parent : $post->ID ) ) ); ?>"
						class="button stylepress_buttons--return"><?php echo esc_html__( '&laquo; Return To Style Settings Page', 'stylepress' ); ?></a>
				</div>
			</div>
			<?php
		}

	}


}

