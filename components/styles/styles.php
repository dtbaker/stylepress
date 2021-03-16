<?php
/**
 * Our Backend class.
 *
 * This handles our main admin page
 *
 * @package stylepress
 */

namespace StylePress\Styles;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Backend
 */
class Styles extends \StylePress\Core\Base {

	const PAGE_SLUG = STYLEPRESS_SLUG . '-styles';

	public function add_submenu($top_level_slug){
		$page = add_submenu_page(
			$top_level_slug,
			__( 'Styles', 'stylepress' ),
			__( 'Styles', 'stylepress' ),
			'manage_options',
			self::PAGE_SLUG,
			array(
				$this,
				'styles_page_callback'
			)
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'admin_page_assets' ) );
	}

	public function admin_page_assets() {
		wp_enqueue_style( 'stylepress-styles', STYLEPRESS_URI . 'build/assets/styles.css', false, STYLEPRESS_VERSION );
		wp_enqueue_script( 'stylepress-styles', STYLEPRESS_URI . 'build/assets/styles.js', [], STYLEPRESS_VERSION );
	}
	/**
	 * This is our callback for rendering our custom menu page.
	 * This page shows all our site styles and currently selected defaults.
	 *
	 * @since 2.0.0
	 */
	public function styles_page_callback() {
		if ( isset( $_GET['style_id'] ) ) {
			include_once __DIR__ . '/views/single_style.php';
		} else if ( isset( $_GET['remote_style_slug'] ) ) {
			include_once __DIR__ . '/views/remote_style_preview.php';
		} else {
			include_once __DIR__ . '/views/styles_overview.php';
		}
	}

	public function stylepress_new_style() {
		// Check if our nonce is set.
		if ( ! isset( $_POST['stylepress_new_style'] ) ) { // WPCS: input var okay.
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['stylepress_new_style'], 'stylepress_new_style' ) ) { // WPCS: sanitization ok. input var okay.
			return;
		}

		$new_style_name   = stripslashes( sanitize_text_field( trim( $_POST['new_style_name'] ) ) );
		$new_category     = sanitize_text_field( trim( $_POST['new_style_category'] ) );
		$new_style_parent = (int) $_POST['new_style_parent'];

		if ( ! $new_style_name ) {
			wp_die( 'Please go back and enter a new style name' );
		}

		if ( ! $new_category ) {
			wp_die( 'No category found' );
		}

		$post_id = wp_insert_post( [
			'post_type'   => Cpt::CPT,
			'post_status' => 'publish',
			'post_title'  => $new_style_name,
			'post_parent' => $new_style_parent,
		], true );

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			wp_die( 'Failed to create new style' );
		}

		wp_set_object_terms( $post_id, $new_category, STYLEPRESS_SLUG . '-cat', false );

		if ( $new_category === 'theme_styles' ) {
			// hack to allow Elementor Theme Style editor:
			update_post_meta( $post_id, '_elementor_template_type', 'kit' );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=' . self::STYLES_PAGE_SLUG . ( $new_style_parent ? '&style_id=' . $new_style_parent : '' ) . '&saved#cat-' . $new_category ) );
		exit;

	}
}

