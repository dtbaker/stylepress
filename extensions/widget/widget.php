<?php


defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

class dtbaker_elementor_template_widget extends WP_Widget {
	/**
	 * Contact Us constructor
	 */
	function __construct() {
		$widget_ops  = array(
			'classname'   => 'dtbaker_elementor_template_widget',
			'description' => __( 'Display an Elementor template', 'dtbaker_elementor' )
		);
		$control_ops = array( 'id_base' => 'dtbaker_elementor_template_widget' );
		parent::__construct( 'dtbaker_elementor_template_widget', __( 'Elementor Template', 'dtbaker_elementor' ), $widget_ops, $control_ops );
	}


	/**
	 * Widget frontend output
	 *
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		if ( ! empty( $instance['template_id'] ) && 'publish' === get_post_status( $instance['template_id'] ) ) {
			$this->sidebar_id = $args['id'];

			add_filter( 'elementor/frontend/builder_content_data', [ $this, 'filter_content_data' ] );

			echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $instance['template_id'] );

			remove_filter( 'elementor/frontend/builder_content_data', [ $this, 'filter_content_data' ] );

			unset( $this->sidebar_id );
		}

		echo $args['after_widget'];
	}

	/**
	 * Avoid nesting a sidebar within a template that will appear in the sidebar itself
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function filter_content_data( $data ) {
		if ( ! empty( $data ) ) {
			$data = \Elementor\Plugin::instance()->db->iterate_data( $data, function ( $element ) {
				if ( 'widget' === $element['elType'] && 'sidebar' === $element['widgetType'] && $this->sidebar_id === $element['settings']['sidebar'] ) {
					$element['settings']['sidebar'] = null;
				}

				return $element;
			} );
		}

		return $data;
	}

	/**
	 * @param array $instance
	 *
	 * @return void
	 */
	public function form( $instance ) {
		$default = [
			'title'       => '',
			'template_id' => '',
		];

		$instance = array_merge( $default, $instance );

		$source    = \Elementor\Plugin::$instance->templates_manager->get_source( 'local' );
		$templates = $source->get_items();

		if ( ! $templates ) {
			return;
		}
		?>
		<p>
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title', 'elementor-pro' ); ?>
				:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
			       value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>

		<p>
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'template_id' ) ); ?>"><?php esc_attr_e( 'Choose Template', 'elementor-pro' ); ?>
				:</label>
			<select class="widefat elementor-widget-template-select"
			        id="<?php echo esc_attr( $this->get_field_id( 'template_id' ) ); ?>"
			        name="<?php echo esc_attr( $this->get_field_name( 'template_id' ) ); ?>">
				<option value="">— <?php _e( 'Select', 'elementor-pro' ); ?> —</option>
				<?php
				foreach ( $templates as $template ) :
					$selected = selected( $template['template_id'], $instance['template_id'] );
					?>
					<option value="<?php echo $template['template_id']; ?>" <?php echo $selected; ?>
					        data-type="<?php echo esc_attr( $template['type'] ); ?>">
						<?php echo $template['title']; ?> (<?php echo $template['type']; ?>)
					</option>
				<?php endforeach; ?>
			</select>
			<?php
			$style = ' style="display:none"';

			$template_type = get_post_meta( $instance['template_id'], '_elementor_template_type', true );

			// 'widget' is editable only from an Elementor page
			if ( 'page' === $template_type ) {
				$style = '';
			}
			?>
			<a target="_blank" class="elementor-edit-template"<?php echo $style; ?>
			   href="<?php echo esc_url( add_query_arg( 'elementor', '', get_permalink( $instance['template_id'] ) ) ); ?>">
				<i class="fa fa-pencil"></i> <?php echo __( 'Edit Template', 'elementor-pro' ); ?>
			</a>
		</p>
		<?php
	}

	/**
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                = [];
		$instance['title']       = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['template_id'] = $new_instance['template_id'];

		return $instance;
	}

}
