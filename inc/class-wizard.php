<?php
/**
 * Our Wizard class.
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Wizard
 */
class Wizard extends Base {

	const PAGE_SLUG = 'stylepress-wizard';

	public $current_step = 'introduction';
	public $tgmpa_instance;
	public $tgmpa_menu_slug = 'tgmpa-install-plugins';
	public $tgmpa_url = 'themes.php?page=tgmpa-install-plugins';

	public function __construct() {

		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp_ajax_envato_setup_plugins', array( $this, 'ajax_plugins' ) );
		add_action( 'wp_ajax_envato_setup_content', array( $this, 'ajax_content' ) );

	}

	public function admin_init() {

		if ( isset( $_GET['step'] ) ) {
			$steps = $this->get_steps();
			if ( isset( $steps[ $_GET['step'] ] ) ) {
				$this->current_step = $_GET['step'];
				if ( ! empty( $_REQUEST['save_step'] ) ) {
					if ( isset( $steps[ $this->current_step ]['handler'] ) ) {
						call_user_func( $steps[ $this->current_step ]['handler'] );
					}
				}
			}
		}
	}

	public function admin_head() {
		?>
		<script type="text/javascript">
      var stylepress_wizard = <?php echo json_encode(
				array(
					'tgm_plugin_nonce' => array(
						'update'  => wp_create_nonce( 'tgmpa-update' ),
						'install' => wp_create_nonce( 'tgmpa-install' ),
					),
					'tgm_bulk_url'     => admin_url( $this->tgmpa_url ),
					'ajaxurl'          => admin_url( 'admin-ajax.php' ),
					'wpnonce'          => wp_create_nonce( 'envato_setup_nonce' ),
					'verify_text'      => esc_html__( '...verifying' ),
				)
			);?>;
		</script>
		<?php
	}

	/**
	 * This is our callback for rendering our custom menu page.
	 * This page shows all our site styles and currently selected defaults.
	 *
	 * @since 2.0.0
	 */
	public function setup_page_callback() {

		$this->content = $this->render_template( 'admin/wizard.php' );
		$this->header  = $this->render_template( 'admin/header.php' );
		echo $this->render_template( 'wrapper.php' );
	}


	public function get_steps() {

		$steps                    = array();
		$steps['introduction']    = array(
			'name'    => esc_html__( 'Introduction' ),
			'view'    => array( $this, 'envato_setup_introduction' ),
			'handler' => '',
		);
		$steps['style']           = array(
			'name'    => esc_html__( 'Style' ),
			'view'    => array( $this, 'envato_setup_color_style' ),
			'handler' => array( $this, 'envato_setup_color_style_save' ),
		);
		$steps['default_plugins'] = array(
			'name'    => esc_html__( 'Plugins' ),
			'view'    => array( $this, 'envato_setup_default_plugins' ),
			'handler' => '',
		);
		$steps['default_content'] = array(
			'name'    => esc_html__( 'Content' ),
			'view'    => array( $this, 'envato_setup_default_content' ),
			'handler' => '',
		);
		//		$steps['design']          = array(
		//			'name'    => esc_html__( 'Logo' ),
		//			'view'    => array( $this, 'envato_setup_logo_design' ),
		//			'handler' => array( $this, 'envato_setup_logo_design_save' ),
		//		);
		$steps['help_support'] = array(
			'name'    => esc_html__( 'Support' ),
			'view'    => array( $this, 'envato_setup_help_support' ),
			'handler' => '',
		);
		$steps['next_steps']   = array(
			'name'    => esc_html__( 'Ready!' ),
			'view'    => array( $this, 'envato_setup_ready' ),
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
		return add_query_arg( 'step', $step );
	}

	public function get_next_step_link() {
		$keys = array_keys( $this->get_steps() );

		return $this->get_step_link( $keys[ array_search( $this->current_step, $keys ) + 1 ] );
	}

	/**
	 * Introduction step
	 */
	public function envato_setup_introduction() {
		include STYLEPRESS_PATH . 'views/admin/wizard/welcome.php';
	}

	private function get_style_data() {
		$current_style = Remote_Styles::get_instance()->get_current_site_style();
		if ( $current_style ) {
			return Remote_Styles::get_instance()->get_style( $current_style );
		}

		return false;
	}

	private function get_plugins() {

		$current_style_data = $this->get_style_data();

		$instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		$plugins  = array(
			'all'      => array(), // Meaning: all plugins which still have open actions.
			'install'  => array(),
			'update'   => array(),
			'activate' => array(),
		);

		foreach ( $instance->plugins as $slug => $plugin ) {
			if ( $instance->is_plugin_active( $slug ) && false === $instance->does_plugin_have_update( $slug ) ) {
				// No need to display plugins if they are installed, up-to-date and active.
				continue;
			} else {
				$plugins['all'][ $slug ] = $plugin;

				if ( ! $instance->is_plugin_installed( $slug ) ) {
					$plugins['install'][ $slug ] = $plugin;
				} else {
					if ( false !== $instance->does_plugin_have_update( $slug ) ) {
						$plugins['update'][ $slug ] = $plugin;
					}

					if ( $instance->can_plugin_activate( $slug ) ) {
						$plugins['activate'][ $slug ] = $plugin;
					}
				}
			}
		}

		return $plugins;
	}


	public function envato_setup_default_plugins() {

		$url     = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'envato-setup' );
		$plugins = $this->get_plugins();

		tgmpa_load_bulk_installer();
		// install plugins with TGM.
		if ( ! class_exists( 'TGM_Plugin_Activation' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
			die( 'Failed to find TGM' );
		}

		// copied from TGM

		$method = ''; // Leave blank so WP_Filesystem can populate it as necessary.
		$fields = array_keys( $_POST ); // Extra fields to pass to WP_Filesystem.

		if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
			return true; // Stop the normal page form from displaying, credential request form will be shown.
		}

		// Now we have some credentials, setup WP_Filesystem.
		if ( ! WP_Filesystem( $creds ) ) {
			// Our credentials were no good, ask the user for them again.
			request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );

			return true;
		}

		/* If we arrive here, we have the filesystem */

		include STYLEPRESS_PATH . 'views/admin/wizard/plugins.php';
	}


	public function ajax_plugins() {
		if ( ! check_ajax_referer( 'envato_setup_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
			wp_send_json_error( array( 'error' => 1, 'message' => esc_html__( 'No Slug Found' ) ) );
		}
		$json = array();
		// send back some json we use to hit up TGM
		$plugins = $this->get_plugins();
		// what are we doing with this plugin?
		foreach ( $plugins['activate'] as $slug => $plugin ) {
			if ( $_POST['slug'] == $slug ) {
				$json = array(
					'url'           => admin_url( $this->tgmpa_url ),
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa_menu_slug,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-activate',
					'action2'       => - 1,
					'message'       => esc_html__( 'Activating Plugin' ),
				);
				break;
			}
		}
		foreach ( $plugins['update'] as $slug => $plugin ) {
			if ( $_POST['slug'] == $slug ) {
				$json = array(
					'url'           => admin_url( $this->tgmpa_url ),
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa_menu_slug,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-update',
					'action2'       => - 1,
					'message'       => esc_html__( 'Updating Plugin' ),
				);
				break;
			}
		}
		foreach ( $plugins['install'] as $slug => $plugin ) {
			if ( $_POST['slug'] == $slug ) {
				$json = array(
					'url'           => admin_url( $this->tgmpa_url ),
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa_menu_slug,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-install',
					'action2'       => - 1,
					'message'       => esc_html__( 'Installing Plugin' ),
				);
				break;
			}
		}

		if ( $json ) {
			$json['hash'] = md5( serialize( $json ) ); // used for checking if duplicates happen, move to next plugin
			wp_send_json( $json );
		} else {
			wp_send_json( array( 'done' => 1, 'message' => esc_html__( 'Success' ) ) );
		}
		exit;

	}

	public function envato_setup_default_content() {
		include STYLEPRESS_PATH . 'views/admin/wizard/content.php';
	}

	public function envato_setup_color_style() {
		include STYLEPRESS_PATH . 'views/admin/wizard/style.php';
	}

	public function envato_setup_help_support() {
		include STYLEPRESS_PATH . 'views/admin/wizard/support.php';
	}

	public function envato_setup_ready() {
		include STYLEPRESS_PATH . 'views/admin/wizard/ready.php';
	}

	/**
	 * Save logo & design options
	 */
	public function envato_setup_color_style_save() {
		check_admin_referer( 'envato-setup' );

		$new_style = isset( $_POST['new_style'] ) ? $_POST['new_style'] : false;
		if ( $new_style ) {
			Remote_Styles::get_instance()->set_current_site_style( $new_style );
			wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		} else {
			wp_safe_redirect( esc_url_raw( $this->get_step_link( 'style' ) ) );
		}
		exit;
	}


	private function get_json( $file ) {

		$current_style_data     = $this->get_style_data();
		$json_content_directory = __DIR__ . '/content/something/';
		$file_name              = $json_content_directory . basename( $file );
		if ( is_file( $file_name ) ) {
			return json_decode( file_get_contents( $file_name ), true );
		}

		return array();
	}


	public function content_default_get() {

		$content = array();

		// find out what content is in our default json file.
		$available_content = $this->get_json( 'default.json' );
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

	public function is_possible_upgrade() {
		$posts = get_posts();
		$pages = get_pages();
		if ( count( $posts ) > 1 || count( $pages ) > 3 ) {
			return true;
		}

		return false;
	}

	public function ajax_content() {
		$content = $this->content_default_get();
		if ( ! check_ajax_referer( 'envato_setup_nonce', 'wpnonce' ) || empty( $_POST['content'] ) && isset( $content[ $_POST['content'] ] ) ) {
			wp_send_json_error( array( 'error' => 1, 'message' => esc_html__( 'No content Found' ) ) );
		}

		$json         = false;
		$this_content = $content[ $_POST['content'] ];

		if ( isset( $_POST['proceed'] ) ) {
			// install the content!

			$this->log( ' -!! STARTING SECTION for ' . $_POST['content'] );

			// init delayed posts from transient.
			$this->delay_posts = get_transient( 'delayed_posts' );
			if ( ! is_array( $this->delay_posts ) ) {
				$this->delay_posts = array();
			}

			if ( ! empty( $this_content['install_callback'] ) ) {
				if ( $result = call_user_func( $this_content['install_callback'] ) ) {

					$this->log( ' -- FINISH. Writing ' . count( $this->delay_posts, COUNT_RECURSIVE ) . ' delayed posts to transient ' );
					set_transient( 'delayed_posts', $this->delay_posts, 60 * 60 * 24 );

					if ( is_array( $result ) && isset( $result['retry'] ) ) {
						// we split the stuff up again.
						$json = array(
							'url'         => admin_url( 'admin-ajax.php' ),
							'action'      => 'envato_setup_content',
							'proceed'     => 'true',
							'retry'       => time(),
							'retry_count' => $result['retry_count'],
							'content'     => $_POST['content'],
							'_wpnonce'    => wp_create_nonce( 'envato_setup_nonce' ),
							'message'     => $this_content['installing'],
							'logs'        => $this->logs,
							'errors'      => $this->errors,
						);
					} else {
						$json = array(
							'done'    => 1,
							'message' => $this_content['success'],
							'debug'   => $result,
							'logs'    => $this->logs,
							'errors'  => $this->errors,
						);
					}
				}
			}
		} else {

			$json = array(
				'url'      => admin_url( 'admin-ajax.php' ),
				'action'   => 'envato_setup_content',
				'proceed'  => 'true',
				'content'  => $_POST['content'],
				'_wpnonce' => wp_create_nonce( 'envato_setup_nonce' ),
				'message'  => $this_content['installing'],
				'logs'     => $this->logs,
				'errors'   => $this->errors,
			);
		}

		if ( $json ) {
			$json['hash'] = md5( serialize( $json ) ); // used for checking if duplicates happen, move to next plugin
			wp_send_json( $json );
		} else {
			wp_send_json( array(
				'error'   => 1,
				'message' => esc_html__( 'Error' ),
				'logs'    => $this->logs,
				'errors'  => $this->errors,
			) );
		}

		exit;

	}

}

