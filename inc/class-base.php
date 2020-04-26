<?php
/**
 * StylePress: Base class
 *
 * Provides some core helper methods for a base class that we can extend.
 *
 * @package stylepress
 * @since 2.0.0
 */

namespace StylePress;

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

	const PAGE_SLUG = STYLEPRESS_SLUG;

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

	public $content = '';
	public $header = '';


	public function get_url() {
		return admin_url( 'admin.php?page=' . self::PAGE_SLUG );
	}

	/**
	 * Render a template
	 *
	 * @param string $default_template_path The path to the template, relative to the plugin's `views` folder
	 *
	 * @return string
	 */
	protected function render_template( $default_template_path, $variables = [] ) {
		do_action( 'stylepress_render_template_pre', $default_template_path, $this );
		$template_path = STYLEPRESS_PATH . 'views/' . $default_template_path;
		$template_path = apply_filters( 'stylepress_template_path', $template_path );
		if ( is_file( $template_path ) ) {
			ob_start();
			extract( $variables );
			require $template_path;
			$template_content = apply_filters( 'stylepress_template_content', ob_get_clean(), $default_template_path, $template_path, $this );
		} else {
			$template_content = '';
		}
		do_action( 'stylepress_render_template_post', $default_template_path, $this, $template_path, $template_content );

		return $template_content;
	}

	/**
	 * Checks current memory limit and sets a new one if required.
	 *
	 * Used during importing to ensure we don't run out of memory on large imports.
	 *
	 * @access public
	 * @since 2.0.0
	 */
	public function check_memory_limit() {
		$memory_limit = ini_get( 'memory_limit' );
		if ( $memory_limit != - 1 ) {
			$last = $memory_limit[ strlen( $memory_limit ) - 1 ];
			$val  = rtrim( $memory_limit, $last );
			switch ( strtolower( $last ) ) {
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}
			if ( $val < ( 1024 * 1024 * 1024 ) ) {
				@ini_set( 'memory_limit', '512M' );
			}
		}
	}

}
