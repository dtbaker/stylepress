<?php
/**
 * Our Settings class.
 *
 * This handles storing our site wide settings.
 *
 * @package stylepress
 */

namespace StylePress\Core;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Settings
 */
class Settings extends Base {

	const OPTION_KEY = STYLEPRESS_SLUG . '-options';

	public function get( $key = false ) {
		$settings = get_option( self::OPTION_KEY, [] );
		if ( ! $settings || ! is_array( $settings ) ) {
			$settings = [];
		}
		if ( $key !== false ) {
			return apply_filters( 'stylepress_setting', isset( $settings[ $key ] ) ? $settings[ $key ] : false, $key );
		}

		return apply_filters( 'stylepress_settings', $settings );
	}

	public function set( $key, $value ) {
		$settings         = $this->get();
		$settings[ $key ] = $value;
		update_option( self::OPTION_KEY, $settings );
	}

	/**
	 * Returns a list of all our configuraable page types.
	 *
	 * @since 2.0.0
	 *
	 */
	public function get_all_page_types() {
		$defaults = array(
			'_global'    => 'Global Default',
			'front_page' => 'Front/Home Page',
			'page'       => 'Single Page',
			'post'       => 'Single Blog Post',
			'category'   => 'Category',
			'search'     => 'Search Results',
			'404'        => '404 Page',
			'archive'    => 'Archive Pages',
			'tag'        => 'Tag Pages',
			'attachment' => 'Image Attachment',
		);

		if ( function_exists( 'WC' ) ) {
			// add our own woocommerce entries.
			$defaults['products']         = 'WooCommerce Shop';
			$defaults['product']          = 'WooCommerce Product';
			$defaults['product_category'] = 'WooCommerce Category';
		}

		$post_types = get_post_types( array( 'public' => true ) );
		foreach ( $post_types as $post_type ) {
			if ( ! in_array( $post_type, array( \StylePress\Styles\Cpt::CPT, 'elementor_library', 'attachment' ), true ) ) {
				if ( ! isset( $defaults[ $post_type ] ) ) {
					$data                   = get_post_type_object( $post_type );
					$defaults[ $post_type ] = $data->labels->singular_name;
				}
			}
		}

		return apply_filters( 'stylepress_page_types', $defaults );
	}

}

