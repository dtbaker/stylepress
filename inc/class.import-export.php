<?php
/**
 * Our DtbakerElementorImportExport class.
 * Handles importing/exporting our custom designs.
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

/**
 * Handles importing/exporting our custom designs.
 *
 * Class DtbakerElementorManager
 */
class DtbakerElementorImportExport {

	/**
	 * Stores our instance that can (and is) accessed from various places.
	 *
	 * @var DtbakerElementorManager null
	 *
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Grab a static instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return DtbakerElementorManager
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}



}

