<?php

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

class DtbakerElementorShortcode {

	const SHORTCODE = 'elementor-template';

	public function __construct() {
		$this->add_actions();
	}

	public function admin_columns_headers( $defaults ) {
		$defaults['shortcode'] = __( 'Shortcode', 'elementor-pro' );

		return $defaults;
	}

	public function admin_columns_content( $column_name, $post_id ) {
		if ( 'shortcode' === $column_name ) {
			// %s = shortcode, %d = post_id
			$shortcode = esc_attr( sprintf( '[%s id="%d"]', self::SHORTCODE, $post_id ) );
			printf( '<input class="elementor-shortcode-input" type="text" readonly onfocus="this.select()" value="%s" />', $shortcode );
		}
	}

	public function shortcode( $attributes = [] ) {
		if ( empty( $attributes['id'] ) ) {
			return '';
		}

		$include_css = false;

		if ( isset( $attributes['css'] ) && 'false' !== $attributes['css'] ) {
			$include_css = (bool) $attributes['css'];
		}

		return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $attributes['id'], $include_css );
	}

	private function add_actions() {
		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			if ( is_admin() ) {
				add_action( 'manage_elementor_library_posts_columns', [ $this, 'admin_columns_headers' ] );
				add_action( 'manage_elementor_library_posts_custom_column', [ $this, 'admin_columns_content' ], 10, 2 );
			}

			add_shortcode( self::SHORTCODE, [ $this, 'shortcode' ] );
		}
	}
}

$shortcode = new DtbakerElementorShortcode();
