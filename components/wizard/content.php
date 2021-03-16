<?php
/**
 * Our Elementor integration class.
 *
 * @package stylepress
 */

namespace StylePress\Wizard;

use StylePress\Core\Base;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Plugin
 */
class Content extends Base {


	public function is_possible_upgrade() {
		$posts = get_posts();
		$pages = get_pages();
		if ( count( $posts ) > 1 || count( $pages ) > 3 ) {
			return true;
		}

		return false;
	}

	public function content_default_get() {

		$content = array();

		// find out what content is in our default json file.
		$available_content = array(); // $this->get_json( 'default.json' );
		foreach ( $available_content as $post_type => $post_data ) {
			if ( count( $post_data ) ) {
				$first           = current( $post_data );
				$post_type_title = ! empty( $first['type_title'] ) ? $first['type_title'] : ucwords( $post_type ) . 's';
				if ( $post_type_title == 'Navigation Menu Items' ) {
					$post_type_title = 'Navigation';
				}
				$content[ $post_type ] = array(
					'title'            => $post_type_title,
					'description'      => sprintf( esc_html__( 'This will create default %s as seen in the demo.' ), $post_type_title ),
					'pending'          => esc_html__( 'Pending.' ),
					'installing'       => esc_html__( 'Installing.' ),
					'success'          => esc_html__( 'Success.' ),
					'install_callback' => array( $this, '_content_install_type' ),
					'checked'          => $this->is_possible_upgrade() ? 0 : 1,
					// dont check if already have content installed.
				);
			}
		}

		$content['settings'] = array(
			'title'            => esc_html__( 'Settings' ),
			'description'      => esc_html__( 'Configure default settings.' ),
			'pending'          => esc_html__( 'Pending.' ),
			'installing'       => esc_html__( 'Installing Default Settings.' ),
			'success'          => esc_html__( 'Success.' ),
			'install_callback' => array( $this, '_content_install_settings' ),
			'checked'          => $this->is_possible_upgrade() ? 0 : 1,
			// dont check if already have content installed.
		);

		$content = apply_filters( 'stylepress_setup_wizard_content', $content, $this );

		return $content;

	}


	public function view() {
		include __DIR__ . '/views/content.php';
	}

}
