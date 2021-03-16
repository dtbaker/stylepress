<?php
/**
 * Our Elementor integration class.
 *
 * @package stylepress
 */

namespace StylePress\Wizard;

use StylePress\Core\Base;
use StylePress\Core\Permissions;
use StylePress\Remote_Styles;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Plugin
 */
class Ajax extends Base {

	public function __construct(){
		add_action( 'wp_ajax_stylepress_setup_plugins', array( $this, 'ajax_plugins' ) );
		add_action( 'wp_ajax_stylepress_setup_content', array( $this, 'ajax_content' ) );
		add_action( 'wp_ajax_stylepress_import_remote_style', array( $this, 'import_remote_style' ) );
	}


	public function import_remote_style() {
		if ( ! check_ajax_referer( 'stylepress-wizard-import-process', 'nonce' ) || !Permissions::get_instance()->can_run_setup_wizard()) {
			wp_send_json_error( esc_html__( 'Failed to import remote style', 'stylepress' ) );
		}

		$remote_style_slug = isset( $_GET['remote_style_slug'] ) ? $_GET['remote_style_slug'] : false;
		if ( ! $remote_style_slug ) {
			wp_send_json_error( esc_html__('Invalid remote style, please go back and try again', 'stylepress' ) );
		}
		$remote_style_data    = Remote_Styles::get_instance()->get_remote_style_data( $remote_style_slug );
		if ( ! $remote_style_data ) {
			wp_send_json_error( esc_html__('Invalid remote style data, please go back and try again' , 'stylepress' ));
		}
		$is_already_installed    = Remote_Styles::get_instance()->is_remote_style_imported( $remote_style_slug );

		wp_send_json_success('Imported successfully');
	}


}
