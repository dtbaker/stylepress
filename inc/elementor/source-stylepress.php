<?php

namespace Elementor\TemplateLibrary;

use Elementor\Core\Base\Document;
use Elementor\Core\Editor\Editor;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Modules\Library\Documents\Library_Document;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor template library local source.
 *
 * Elementor template library local source handler class is responsible for
 * handling local Elementor templates saved by the user locally on his site.
 *
 * @since 1.0.0
 */
class Source_StylePress extends Source_Base {

	/**
	 * Elementor template-library post-type slug.
	 */
	const CPT = \StylePress\Styles::CPT;

	/**
	 * Elementor template-library taxonomy slug.
	 */
	const TAXONOMY_TYPE_SLUG = 'elementor_library_type';

	/**
	 * Elementor template-library category slug.
	 */
	const TAXONOMY_CATEGORY_SLUG = 'elementor_library_category';

	/**
	 * Elementor template-library meta key.
	 * @deprecated 2.3.0 Use \Elementor\Core\Base\Document::TYPE_META_KEY instead
	 */
	const TYPE_META_KEY = '_elementor_template_type';

	/**
	 * Elementor template-library temporary files folder.
	 */
	const TEMP_FILES_DIR = 'elementor/tmp';

	/**
	 * Elementor template-library bulk export action name.
	 */
	const BULK_EXPORT_ACTION = 'elementor_export_multiple_templates';

	const ADMIN_MENU_SLUG = 'edit.php?post_type=elementor_library';

	const ADMIN_SCREEN_ID = 'edit-elementor_library';

	/**
	 * Template types.
	 *
	 * Holds the list of supported template types that can be displayed.
	 *
	 * @access private
	 * @static
	 *
	 * @var array
	 */
	private static $template_types = [];

	/**
	 * Post type object.
	 *
	 * Holds the post type object of the current post.
	 *
	 * @access private
	 *
	 * @var \WP_Post_Type
	 */
	private $post_type_object;

	/**
	 * @return array
	 * @since 2.3.0
	 * @access public
	 * @static
	 */
	public static function get_template_types() {
		return self::$template_types;
	}

	/**
	 * Get local template type.
	 *
	 * Retrieve the template type from the post meta.
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return mixed The value of meta data field.
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 */
	public static function get_template_type( $template_id ) {
		return get_post_meta( $template_id, Document::TYPE_META_KEY, true );
	}

	public static function get_admin_url( $relative = false ) {
		$base_url = self::ADMIN_MENU_SLUG;
		if ( ! $relative ) {
			$base_url = admin_url( $base_url );
		}

		return add_query_arg( 'tabs_group', 'library', $base_url );
	}

	/**
	 * Get local template ID.
	 *
	 * Retrieve the local template ID.
	 *
	 * @return string The local template ID.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_id() {
		return 'stylepress';
	}

	/**
	 * Get local template title.
	 *
	 * Retrieve the local template title.
	 *
	 * @return string The local template title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'StylePress', 'elementor' );
	}

	/**
	 * Register local template data.
	 *
	 * Used to register custom template data like a post type, a taxonomy or any
	 * other data.
	 *
	 * The local template class registers a new `elementor_library` post type
	 * and an `elementor_library_type` taxonomy. They are used to store data for
	 * local templates saved by the user on his site.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_data() {

	}

	/**
	 * Get local templates.
	 *
	 * Retrieve local templates saved by the user on his site.
	 *
	 * @param array $args Optional. Filter templates based on a set of
	 *                    arguments. Default is an empty array.
	 *
	 * @return array Local templates.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_items( $args = [] ) {
		$templates = [];
		foreach ( \StylePress\Styles::get_instance()->get_all_styles( 'demo_content' ) as $post_id => $post_title ) {
			$templates[] = $this->get_item( $post_id );
		}

		return $templates;
	}

	public function save_item( $template_data ) {
		return true;
	}

	public function update_item( $new_data ) {
		return true;
	}

	/**
	 * Get local template.
	 *
	 * Retrieve a single local template saved by the user on his site.
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return array Local template.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_item( $template_id ) {
		$post = get_post( $template_id );
		$parent_style = get_post( $post->post_parent );

		$user = get_user_by( 'id', $post->post_author );

		$page = SettingsManager::get_settings_managers( 'page' )->get_model( $template_id );

		$page_settings = $page->get_data( 'settings' );

		$date = strtotime( $post->post_date );

		$type = self::get_template_type( $post->ID );

		$data = [
			'template_id'     => $post->ID,
			'source'          => 'local', //$this->get_id(),
			'type'            => $type,
			'title'           => $parent_style->post_title .' &raquo; ' . $post->post_title,
			'thumbnail'       => get_the_post_thumbnail_url( $post ),
			'date'            => $date,
			'human_date'      => date_i18n( get_option( 'date_format' ), $date ),
			'author'          => $user->display_name,
			'hasPageSettings' => ! empty( $page_settings ),
			'tags'            => [],
			'export_link'     => $this->get_export_link( $template_id ),
			'url'             => get_permalink( $post->ID ),
		];

		/**
		 * Get template library template.
		 *
		 * Filters the template data when retrieving a single template from the
		 * template library.
		 *
		 * @param array $data Template data.
		 *
		 * @since 1.0.0
		 *
		 */
		$data = apply_filters( 'elementor/template-library/get_template', $data );

		return $data;
	}

	/**
	 * Get template data.
	 *
	 * Retrieve the data of a single local template saved by the user on his site.
	 *
	 * @param array $args Custom template arguments.
	 *
	 * @return array Local template data.
	 * @since 1.5.0
	 * @access public
	 *
	 */
	public function get_data( array $args ) {
		$db = Plugin::$instance->db;

		$template_id = $args['template_id'];

		// TODO: Validate the data (in JS too!).
		if ( ! empty( $args['display'] ) ) {
			$content = $db->get_builder( $template_id );
		} else {
			$document = Plugin::$instance->documents->get( $template_id );
			$content  = $document ? $document->get_elements_data() : [];
		}

		if ( ! empty( $content ) ) {
			$content = $this->replace_elements_ids( $content );
		}

		$data = [
			'content' => $content,
		];

		if ( ! empty( $args['with_page_settings'] ) ) {
			$page = SettingsManager::get_settings_managers( 'page' )->get_model( $args['template_id'] );

			$data['page_settings'] = $page->get_data( 'settings' );
		}

		return $data;
	}

	/**
	 * Delete local template.
	 *
	 * Delete template from the database.
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return \WP_Post|\WP_Error|false|null Post data on success, false or null
	 *                                       or 'WP_Error' on failure.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function delete_template( $template_id ) {
		if ( ! current_user_can( $this->post_type_object->cap->delete_post, $template_id ) ) {
			return new \WP_Error( 'template_error', __( 'Access denied.', 'elementor' ) );
		}

		return wp_delete_post( $template_id, true );
	}

	/**
	 * Export local template.
	 *
	 * Export template to a file.
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return \WP_Error WordPress error if template export failed.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function export_template( $template_id ) {
		wp_die( 'Sorry not implemented' );
	}


	/**
	 * Block template frontend
	 *
	 * Don't display the single view of the template library post type in the
	 * frontend, for users that don't have the proper permissions.
	 *
	 * Fired by `template_redirect` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function block_template_frontend() {
		if ( is_singular( self::CPT ) && ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			wp_safe_redirect( site_url(), 301 );
			die;
		}
	}

	/**
	 * Is template library supports export.
	 *
	 * whether the template library supports export.
	 *
	 * Template saved by the user locally on his site, support export by default
	 * but this can be changed using a filter.
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return bool Whether the template library supports export.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function is_template_supports_export( $template_id ) {
		$export_support = true;

		/**
		 * Is template library supports export.
		 *
		 * Filters whether the template library supports export.
		 *
		 * @param bool $export_support Whether the template library supports export.
		 *                             Default is true.
		 * @param int  $template_id Post ID.
		 *
		 * @since 1.0.0
		 *
		 */
		$export_support = apply_filters( 'elementor/template_library/is_template_supports_export', $export_support, $template_id );

		return $export_support;
	}


	/**
	 * Get template export link.
	 *
	 * Retrieve the link used to export a single template based on the template
	 * ID.
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return string Template export URL.
	 * @since 2.0.0
	 * @access private
	 *
	 */
	private function get_export_link( $template_id ) {
		// TODO: BC since 2.3.0 - Use `$ajax->create_nonce()`
		/** @var \Elementor\Core\Common\Modules\Ajax\Module $ajax */
		// $ajax = Plugin::$instance->common->get_component( 'ajax' );

		return add_query_arg(
			[
				'action'         => 'elementor_library_direct_actions',
				'library_action' => 'export_template',
				'source'         => $this->get_id(),
				'_nonce'         => wp_create_nonce( 'elementor_ajax' ),
				'template_id'    => $template_id,
			],
			admin_url( 'admin-ajax.php' )
		);
	}

	/**
	 * On template save.
	 *
	 * Run this method when template is being saved.
	 *
	 * Fired by `save_post` action.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post The current post object.
	 *
	 * @since 1.0.1
	 * @access public
	 *
	 */
	public function on_save_post( $post_id, \WP_Post $post ) {
		if ( self::CPT !== $post->post_type ) {
			return;
		}

		if ( self::get_template_type( $post_id ) === 'stylepress' ) { // It's already with a type
			return;
		}

		// Don't save type on import, the importer will do it.
		if ( did_action( 'import_start' ) ) {
			return;
		}

		$this->save_item_type( $post_id, 'stylepress' );
	}

	/**
	 * Save item type.
	 *
	 * When saving/updating templates, this method is used to update the post
	 * meta data and the taxonomy.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $type Item type.
	 *
	 * @since 1.0.1
	 * @access private
	 *
	 */
	private function save_item_type( $post_id, $type ) {
		update_post_meta( $post_id, Document::TYPE_META_KEY, $type );
		wp_cache_flush();
	}


	/**
	 * Maybe render blank state.
	 *
	 * When the template library has no saved templates, display a blank admin page offering
	 * to create the very first template.
	 *
	 * Fired by `manage_posts_extra_tablenav` action.
	 *
	 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function maybe_render_blank_state( $which ) {
		global $post_type;

		if ( self::CPT !== $post_type || 'bottom' !== $which ) {
			return;
		}

		global $wp_list_table;

		$total_items = $wp_list_table->get_pagination_arg( 'total_items' );

		if ( ! empty( $total_items ) || ! empty( $_REQUEST['s'] ) ) {
			return;
		}

		$inline_style = '#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom .actions, .wrap .subsubsub { display:none;}';

		$current_type = get_query_var( 'elementor_library_type' );

		$document_types = Plugin::instance()->documents->get_document_types();

		if ( empty( $document_types[ $current_type ] ) ) {
			return;
		}

		// TODO: Better way to exclude widget type.
		if ( 'widget' === $current_type ) {
			return;
		}

		if ( empty( $current_type ) ) {
			$counts = (array) wp_count_posts( self::CPT );
			unset( $counts['auto-draft'] );
			$count = array_sum( $counts );

			if ( 0 < $count ) {
				return;
			}

			$current_type = 'template';

			$inline_style .= '#elementor-template-library-tabs-wrapper {display: none;}';
		}

		$current_type_label = $this->get_template_label_by_type( $current_type );
		?>
		<style type="text/css"><?php echo $inline_style; ?></style>
		<div class="elementor-template_library-blank_state">
			<div class="elementor-blank_state">
				<i class="eicon-folder"></i>
				<h2>
					<?php
					/* translators: %s: Template type label. */
					printf( __( 'No Templates Yet', 'elementor' ), $current_type_label );
					?>
				</h2>
				<a class="elementor-button elementor-button-success"
				   href="<?php echo esc_url( admin_url( 'admin.php?page=stylepress-styles' ) ); ?>">
					<?php
					printf( __( 'Open StylePress', 'elementor' ), $current_type_label );
					?>
				</a>
			</div>
		</div>
		<?php
	}


	/**
	 * Get template label by type.
	 *
	 * Retrieve the template label for any given template type.
	 *
	 * @param string $template_type Template type.
	 *
	 * @return string Template label.
	 * @since 2.0.0
	 * @access private
	 *
	 */
	private function get_template_label_by_type( $template_type ) {
		$document_types = Plugin::instance()->documents->get_document_types();

		if ( isset( $document_types[ $template_type ] ) ) {
			$template_label = call_user_func( [ $document_types[ $template_type ], 'get_title' ] );
		} else {
			$template_label = ucwords( str_replace( [ '_', '-' ], ' ', $template_type ) );
		}

		/**
		 * Template label by template type.
		 *
		 * Filters the template label by template type in the template library .
		 *
		 * @param string $template_label Template label.
		 * @param string $template_type Template type.
		 *
		 * @since 2.0.0
		 *
		 */
		$template_label = apply_filters( 'elementor/template-library/get_template_label_by_type', $template_label, $template_type );

		return $template_label;
	}

	/**
	 * Filter template types in admin query.
	 *
	 * Update the template types in the main admin query.
	 *
	 * Fired by `parse_query` action.
	 *
	 * @param \WP_Query $query The `WP_Query` instance.
	 *
	 * @since 2.4.0
	 * @access public
	 *
	 */
	public function admin_query_filter_types( \WP_Query $query ) {
		if ( empty( $query->query_vars['elementor_library_type'] ) || $query->query_vars['elementor_library_type'] !== 'stylepress' ) {
			return;
		}

		$query->query_vars['post_type']  = \StylePress\Styles::CPT;
		$query->query_vars['meta_key']   = '';
		$query->query_vars['meta_value'] = '';

	}

	/**
	 * Add template library actions.
	 *
	 * Register filters and actions for the template library.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function add_actions() {
		if ( is_admin() ) {
			add_action( 'save_post', [ $this, 'on_save_post' ], 3, 2 );

			add_action( 'parse_query', [ $this, 'admin_query_filter_types' ] );

			// Template type column.
			add_action( 'manage_' . self::CPT . '_posts_columns', [ $this, 'admin_columns_headers' ] );
			add_action( 'manage_' . self::CPT . '_posts_custom_column', [ $this, 'admin_columns_content' ], 10, 2 );

			if ( $this->is_current_screen() ) {
				add_filter( 'the_title', [ $this, 'add_stylepress_parent_suffix' ], 11, 2 );
				add_action( 'manage_elementor_library_posts_columns', [ $this, 'admin_columns_headers' ] );
				add_action( 'manage_elementor_library_posts_custom_column', [ $this, 'admin_columns_content' ], 10, 2 );
			}

			// Show blank state.
			add_action( 'manage_posts_extra_tablenav', [ $this, 'maybe_render_blank_state' ] );
		}

		add_action( 'template_redirect', [ $this, 'block_template_frontend' ] );
	}

	public function add_stylepress_parent_suffix($title, $post_id){
		$post = get_post($post_id);
		$parent = get_post($post->post_parent);
		if($parent ){
			return $parent->post_title .' &raquo; ' . $title;
		}
		return $title;
	}

	/**
	 * @since 2.0.6
	 * @access public
	 */
	public function admin_columns_content( $column_name, $post_id ) {
		if ( 'elementor_library_type' === $column_name ) {
			/** @var Document $document */
			$document = Plugin::$instance->documents->get( $post_id );

			if ( $document && $document instanceof Library_Document ) {
				$document->print_admin_column_type();
			}
		}
		if ( 'stylepress_type' === $column_name ) {
			$stylepress_type = get_the_terms( $post_id, STYLEPRESS_SLUG . '-cat' );
			if ( $stylepress_type ) {
				if ( $stylepress_type[0]->slug === 'styles' ) {
					echo 'Main Style';
				} else {
					$categories  = \StylePress\Styles::get_instance()->get_categories();
					$found_match = false;
					foreach ( $categories as $category ) {
						if ( $category['slug'] === $stylepress_type[0]->slug ) {
							echo esc_html( $category['title'] );
							$found_match = true;
							break;
						}
					}
					if ( ! $found_match ) {
						echo 'Unknown';
					}
				}
			}
		}
	}

	/**
	 * @since 2.0.6
	 * @access public
	 */
	public function admin_columns_headers( $posts_columns ) {
		// Replace original column that bind to the taxonomy - with another column.
		unset( $posts_columns['taxonomy-elementor_library_type'] );

		$offset = 2;

		$posts_columns = array_slice( $posts_columns, 0, $offset, true ) + [
				'stylepress_type'        => __( 'StylePress Category', 'elementor' ),
				'elementor_library_type' => __( 'Elementor Type', 'elementor' ),
			] + array_slice( $posts_columns, $offset, null, true );

		return $posts_columns;
	}

	private function is_current_screen() {
		return isset( $_GET['elementor_library_type'] ) && $_GET['elementor_library_type'] === 'stylepress';
	}

	/**
	 * Template library local source constructor.
	 *
	 * Initializing the template library local source base by registering custom
	 * template data and running custom actions.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct();

		$this->add_actions();
	}
}
