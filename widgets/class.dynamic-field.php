<?php
/**
 * Our DtbakerElementorManager class.
 * This handles all our hooks and stuff.
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

/**
 * All the magic happens here.
 *
 * Class DtbakerElementorManager
 */
class DtbakerDynamicField {

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



	public function page_title(){
		return get_the_title();
	}
	public function product_title(){
		return get_the_title();
	}


}

