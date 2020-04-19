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

	public $current_step = 'introduction';
	public $tgmpa_instance;
	public $tgmpa_menu_slug = 'tgmpa-install-plugins';
	public $tgmpa_url = 'themes.php?page=tgmpa-install-plugins';

	public function __construct() {

		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'wp_ajax_envato_setup_plugins', array( $this, 'ajax_plugins' ) );
		add_action( 'wp_ajax_envato_setup_content', array( $this, 'ajax_content' ) );

		if ( isset( $_GET['step'] ) ) {
			$steps = $this->get_steps();
			if ( isset( $steps[ $_GET['step'] ] ) ) {
				$this->current_step = $_GET['step'];
			}
		}
	}

	public function admin_head(){
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
		$this->content = $this->render_template(
			'admin/wizard.php', [
			]
		);
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
		$steps['default_plugins'] = array(
			'name'    => esc_html__( 'Plugins' ),
			'view'    => array( $this, 'envato_setup_default_plugins' ),
			'handler' => '',
		);
		$steps['style']           = array(
			'name'    => esc_html__( 'Style' ),
			'view'    => array( $this, 'envato_setup_color_style' ),
			'handler' => array( $this, 'envato_setup_color_style_save' ),
		);
		$steps['default_content'] = array(
			'name'    => esc_html__( 'Content' ),
			'view'    => array( $this, 'envato_setup_default_content' ),
			'handler' => '',
		);
		$steps['design']          = array(
			'name'    => esc_html__( 'Logo' ),
			'view'    => array( $this, 'envato_setup_logo_design' ),
			'handler' => array( $this, 'envato_setup_logo_design_save' ),
		);
		$steps['help_support']    = array(
			'name'    => esc_html__( 'Support' ),
			'view'    => array( $this, 'envato_setup_help_support' ),
			'handler' => '',
		);
		$steps['next_steps']      = array(
			'name'    => esc_html__( 'Ready!' ),
			'view'    => array( $this, 'envato_setup_ready' ),
			'handler' => '',
		);

		return $steps;

	}

	public function step_output() {
		$steps = $this->get_steps();
		if ( $this->current_step && isset( $steps[ $this->current_step ] ) ) {
			call_user_func( $steps[ $this->current_step ]['view'] );
		}
	}


	public function get_step_link( $step ) {
		return add_query_arg( 'step', $step, admin_url( 'admin.php?page=' . STYLEPRESS_SLUG ) );
	}

	public function get_next_step_link() {
		$keys = array_keys( $this->get_steps() );

		return $this->get_step_link($keys[ array_search( $this->current_step, $keys ) + 1 ]);
	}

	/**
	 * Introduction step
	 */
	public function envato_setup_introduction() {

		if ( isset( $_REQUEST['export'] ) ) {

			include( 'envato-setup-export.php' );

		} else if ( get_option( 'envato_setup_complete', false ) ) {
			?>
			<h1><?php printf( esc_html__( 'Welcome to the setup wizard for %s.' ), wp_get_theme() ); ?></h1>
			<p><?php esc_html_e( 'It looks like you have already run the setup wizard. Below are some options: ' ); ?></p>
			<ul>
				<li>
					<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
					   class="button-primary button button-next button-large"><?php esc_html_e( 'Run Setup Wizard Again' ); ?></a>
				</li>
			</ul>
			<p class="envato-setup-actions step">
				<a
					href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '' ) ); ?>"
					class="button button-large"><?php esc_html_e( 'Cancel' ); ?></a>
			</p>
			<?php
		} else {
			?>
			<h1><?php printf( esc_html__( 'Welcome to the setup wizard for %s.' ), wp_get_theme() ); ?></h1>
			<p><?php printf( esc_html__( 'Thank you for choosing the %s theme from ThemeForest. This quick setup wizard will help you configure your new website. This wizard will install the required WordPress plugins, default content, logo and tell you a little about Help &amp; Support options. It should only take 5 minutes.' ), wp_get_theme() ); ?></p>
			<p class="envato-setup-actions step">
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
				   class="button-primary button button-large button-next"><?php esc_html_e( 'Let\'s Go!' ); ?></a>
				<a
					href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '' ) ); ?>"
					class="button button-large"><?php esc_html_e( 'Not right now' ); ?></a>
			</p>
			<?php
		}
	}

	private function get_plugins() {
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

		tgmpa_load_bulk_installer();
		// install plugins with TGM.
		if ( ! class_exists( 'TGM_Plugin_Activation' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
			die( 'Failed to find TGM' );
		}
		$url     = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'envato-setup' );
		$plugins = $this->get_plugins();

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

		?>
		<h1><?php esc_html_e( 'Default Plugins' ); ?></h1>
		<form method="post">

			<?php
			$plugins = $this->get_plugins();
			if ( count( $plugins['all'] ) ) {
				?>
				<p><?php esc_html_e( 'Your website needs a few essential plugins. The following plugins will be installed or updated:' ); ?></p>
				<ul class="envato-wizard-plugins">
					<?php foreach ( $plugins['all'] as $slug => $plugin ) { ?>
						<li data-slug="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $plugin['name'] ); ?>
							<span>
    								<?php
								    $keys = array();
								    if ( isset( $plugins['install'][ $slug ] ) ) {
									    $keys[] = 'Installation';
								    }
								    if ( isset( $plugins['update'][ $slug ] ) ) {
									    $keys[] = 'Update';
								    }
								    if ( isset( $plugins['activate'][ $slug ] ) ) {
									    $keys[] = 'Activation';
								    }
								    echo implode( ' and ', $keys ) . ' required';
								    ?>
    							</span>
							<div class="spinner"></div>
						</li>
					<?php } ?>
				</ul>
				<?php
			} else {
				echo '<p><strong>' . esc_html_e( 'Good news! All plugins are already installed and up to date. Please continue.' ) . '</strong></p>';
			} ?>

			<p><?php esc_html_e( 'You can add and remove plugins later on from within WordPress.' ); ?></p>

			<p class="envato-setup-actions step">
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
				   class="button-primary button button-large button-next"
				   data-callback="install_plugins"><?php esc_html_e( 'Continue' ); ?></a>
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
				   class="button button-large button-next"><?php esc_html_e( 'Skip this step' ); ?></a>
				<?php wp_nonce_field( 'envato-setup' ); ?>
			</p>
		</form>
		<?php
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



	public function envato_setup_color_style() {
		?>
		<h1><?php esc_html_e( 'Site Style' ); ?></h1>
		<form method="post">
			<p><?php esc_html_e( 'Please choose your site style below. You can import additional styles later on from settings.' ); ?></p>

			<div class="theme-presets">
				<?php
				$current_style = get_theme_mod( 'dtbwp_site_style', false );
				foreach ( Styles::get_instance()->get_all_styles() as $style_name => $style_data ) {
					?>
					<div class="theme-preset <?php echo $style_name === $current_style ? 'current' : ''; ?>">
						<a href="#" data-style="<?php echo esc_attr( $style_name ); ?>">
							<img src="<?php echo esc_url($style_data['thumbnail_url']);?>" alt="<?php echo esc_attr($style_data['title']);?>" />
							<br/>
							<?php echo esc_html($style_data['title']);?>
						</a>
					</div>
				<?php } ?>
			</div>

			<input type="hidden" name="new_style" id="new_style" value="" />

			<p class="envato-setup-actions step">
				<input type="submit" class="button-primary button button-large button-next"
				       value="<?php esc_attr_e( 'Continue' ); ?>" name="save_step"/>
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
				   class="button button-large button-next"><?php esc_html_e( 'Skip this step' ); ?></a>
				<?php wp_nonce_field( 'envato-setup' ); ?>
			</p>
		</form>
		<?php
	}

	/**
	 * Save logo & design options
	 */
	public function envato_setup_color_style_save() {
		check_admin_referer( 'envato-setup' );

		$new_style = isset( $_POST['new_style'] ) ? $_POST['new_style'] : false;
		if ( $new_style ) {
			set_theme_mod( 'dtbwp_site_style', $new_style );
		}

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

}

