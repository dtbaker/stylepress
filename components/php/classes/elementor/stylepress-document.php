<?php

namespace Elementor\Modules\Library\Documents;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Elementor section library document.
 *
 * Elementor section library document handler class is responsible for
 * handling a document of a section type.
 *
 * @since 2.0.0
 */
class Stylepress_Document extends Library_Document {

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['support_kit'] = true;
		$properties['cpt']         = [
			\StylePress\Styles::CPT
		];

		return $properties;
	}

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @return string Document name.
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'stylepress';
	}

	/**
	 * Get document title.
	 *
	 * Retrieve the document title.
	 *
	 * @return string Document title.
	 * @since 2.0.0
	 * @access public
	 * @static
	 *
	 */
	public static function get_title() {
		return __( 'StylePress', 'stylepress' );
	}
}
