<?php
/**
 * Our Wizard class.
 *
 * @package stylepress
 */

namespace StylePress\Wizard;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Wizard
 */
class Wizard extends \StylePress\Core\Base {
	const PAGE_SLUG = STYLEPRESS_SLUG . '-wizard';

	public $current_step = 'introduction';

	public function __construct() {
		if(!empty($_GET['step'])) {
			$steps = $this->get_steps();
			if ( isset( $steps[ $_GET['step'] ] ) ) {
				$this->current_step = $_GET['step'];
			}
			Import::get_instance();
			Style::get_instance();
		}
	}

	public function add_top_level_menu(){
		if(\StylePress\Core\Permissions::get_instance()->can_run_setup_wizard()) {
			add_menu_page(
				__( 'StylePress', 'stylepress' ),
				__( 'StylePress', 'stylepress' ),
				'manage_options',
				self::PAGE_SLUG,
				array(
					$this,
					'wizard_page_callback',
				),
				STYLEPRESS_URI . 'src/images/icon.png'
			);
			// hack to remove default submenu
			$page = add_submenu_page(
				self::PAGE_SLUG,
				__( 'Setup Wizard', 'stylepress' ),
				__( 'Setup Wizard', 'stylepress' ),
				'manage_options',
				self::PAGE_SLUG,
				array(
					$this,
					'wizard_page_callback'
				)
			);
			add_action( 'admin_print_styles-' . $page, array( $this, 'wizard_ui_page_assets' ) );

			return self::PAGE_SLUG;
		}
	}

	public function wizard_ui_page_assets () {
		wp_enqueue_style( 'stylepress-wizard', STYLEPRESS_URI . 'build/assets/wizard.css', false, STYLEPRESS_VERSION );
		wp_enqueue_script( 'stylepress-wizard', STYLEPRESS_URI . 'build/assets/wizard.js', [], STYLEPRESS_VERSION );

		// Add contextual help contents
		ob_start();
		?>
		<h3>Getting Started</h3>
		<ol>
			<li>Test</li>
			<li>Test</li>
			<li>Test</li>
			<li>Test</li>
		</ol>
		<?php
		$help_customize = ob_get_clean();

		get_current_screen()->add_help_tab( array(
			'id'      => 'stylepress-help',
			'title'   => __( 'Getting Started', 'stylepress' ),
			'content' => $help_customize,
		) );
	}


	/**
	 * This is our callback for rendering our custom menu page.
	 * This page shows all our site styles and currently selected defaults.
	 *
	 * @since 2.0.0
	 */
	public function wizard_page_callback() {
		$output_steps = $this->get_steps();
		$step_keys    = array_keys( $output_steps );
		include_once __DIR__ .'/views/wrapper.php';
	}


	public function get_steps() {
		static $steps = array();
		if(count($steps)){
			// get_steps() can be expensive so be sure to memoize the result
			return $steps;
		}
		$steps['introduction']    = array(
			'name'    => esc_html__( 'Introduction' ),
			'view'    => array( Introduction::get_instance(), 'view' ),
			'handler' => '',
		);
		$steps['style']           = array(
			'name'    => esc_html__( 'Style' ),
			'view'    => array( Style::get_instance(), 'view' ),
			'handler' => '',
		);
		$steps['import']           = array(
			'name'    => esc_html__( 'Import' ),
			'view'    => array( Import::get_instance(), 'view' ),
			'handler' => '',
		);
		$steps['plugins'] = array(
			'name'    => esc_html__( 'Plugins' ),
			'view'    => array( Plugins::get_instance(), 'view' ),
			'handler' => '',
		);
		$steps['layout'] = array(
			'name'    => esc_html__( 'Layout' ),
			'view'    => array( Layout::get_instance(), 'view' ),
			'handler' => '',
		);
		$steps['content'] = array(
			'name'    => esc_html__( 'Content' ),
			'view'    => array( Content::get_instance(), 'view' ),
			'handler' => '',
		);
		$steps['complete'] = array(
			'name'    => esc_html__( 'Complete' ),
			'view'    => array( Complete::get_instance(), 'view' ),
			'handler' => '',
		);

		$steps = apply_filters( 'stylepress_setup_wizard_steps', $steps, $this );

		return $steps;
	}

	public function step_output() {
		$steps = $this->get_steps();
		if ( $this->current_step && isset( $steps[ $this->current_step ] ) ) {
			call_user_func( $steps[ $this->current_step ]['view'] );
		}
	}

	public function get_step_link( $step ) {
		return add_query_arg( 'step', $step, admin_url( 'admin.php?page=' . self::PAGE_SLUG) );
	}

	public function get_next_step_link() {
		$keys = array_keys( $this->get_steps() );
		$link = admin_url( '' );
		$next_step = array_search( $this->current_step, $keys ) + 1;
		if(isset($keys[ $next_step ])) {
			$link = $this->get_step_link( $keys[ $next_step ] );
		}
		return $link;
	}

	public function get_prev_step_link() {
		$keys = array_keys( $this->get_steps() );
		$link = admin_url( '' );
		$prev_step = array_search( $this->current_step, $keys ) - 1;
		if(isset($keys[ $prev_step ])) {
			$link = $this->get_step_link( $keys[ $prev_step ] );
		}
		return $link;
	}
}

