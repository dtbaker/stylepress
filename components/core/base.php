<?php
/**
 * StylePress: Base class
 *
 * Provides some core helper methods for a base class that we can extend.
 *
 * @package stylepress
 * @since 2.0.0
 */

namespace StylePress\Core;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * StylePress plugin.
 *
 * The main plugin handler class is responsible for initializing StylePress. The
 * class registers and all the components required to run the plugin.
 *
 * @since 2.0.0
 */
class Base {
	/**
	 * Holds the plugin instance.
	 *
	 * @since 2.0.0
	 * @access protected
	 * @static
	 *
	 * @var Base
	 */
	private static $instances = [];

	/**
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'stylepress' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'stylepress' ), '1.0.0' );
	}

	/**
	 * Sets up a single instance of the plugin.
	 *
	 * @return static An instance of the class.
	 * @since 2.0.0
	 * @access public
	 * @static
	 *
	 */
	public static function get_instance() {
		$module = get_called_class();
		if ( ! isset( self::$instances[ $module ] ) ) {
			self::$instances[ $module ] = new $module();
		}

		return self::$instances[ $module ];
	}
}
