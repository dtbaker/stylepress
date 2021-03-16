<?php
/**
 * Our Backend class.
 *
 * This handles our main admin page
 *
 * @package stylepress
 */

namespace StylePress\Backend;

use StylePress\Styles\Cpt;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Backend
 */
class Ui extends \StylePress\Core\Base {

	/**
	 * Initializes the plugin and sets all required filters.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'add_meta_boxes', array( $this, 'meta_box_add' ) );
		add_action( 'save_post', array( $this, 'meta_box_save' ) );
	}

	/**
	 * This is our custom "Full Site Builder" menu item that appears under the appearance tab.
	 *
	 * @since 2.0.0
	 */
	public function admin_menu() {
		$top_level_slug = \StylePress\Wizard\Wizard::get_instance()->add_top_level_menu();

		if($top_level_slug) {
			\StylePress\Layout\Layout::get_instance()->add_submenu($top_level_slug);
			\StylePress\Styles\Styles::get_instance()->add_submenu($top_level_slug);
		}
	}

	/**
	 * Adds a meta box to every post type.
	 *
	 * @since 2.0.0
	 */
	public function meta_box_add() {
		if(\StylePress\Core\Permissions::get_instance()->can_edit_post_meta_boxes()) {
			$post_types = get_post_types();
			foreach ( $post_types as $post_type ) {
				if ( ! in_array( $post_type, array( Cpt::CPT, 'elementor_library' ), true ) ) {
					add_meta_box(
						'stylepress_style_metabox',
						__( 'StylePress', 'stylepress' ),
						array( $this, 'meta_box_display' ),
						$post_type,
						'side',
						'high'
					);
				}
			}
		}
	}

	/**
	 * This renders our metabox on most page/post types.
	 *
	 * @param \WP_Post $post Current post object.
	 *
	 * @since 2.0.0
	 *
	 */
	public function meta_box_display( $post ) {
		if(\StylePress\Core\Permissions::get_instance()->can_edit_post_meta_boxes($post)) {
			include_once STYLEPRESS_PATH . 'metaboxes/post-meta-box.php';
		}
	}

	/**
	 * Saves our metabox details, which is the style for a particular page.
	 *
	 * @param int $post_id The post we're current saving.
	 *
	 * @since 2.0.0
	 *
	 */
	public function meta_box_save( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['stylepress_style_nonce'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['stylepress_style_nonce'], 'stylepress_style_nonce' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['stylepress_style'] ) && is_array( $_POST['stylepress_style'] ) ) { // WPCS: sanitization ok. input var okay.
			$default_styles = [];
			foreach ( $_POST['stylepress_style'] as $page_type ) {
				// sanitise each one.
			}
			update_post_meta( $post_id, 'stylepress_style', sanitize_text_field( $_POST['stylepress_style'] ) ); // WPCS: sanitization ok. input var okay.
		}
	}
}
