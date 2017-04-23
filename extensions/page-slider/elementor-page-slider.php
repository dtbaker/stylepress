<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class StylePress_Widget_Page_Carousel extends Widget_Base {

	public function get_name() {
		return 'stylepress-page-carousel';
	}

	public function get_title() {
		return esc_html__( 'Page Slider', 'elementor' );
	}

	/**
	 * Get the current icon for display on frontend.
	 * The extra 'dtbaker-elementor-widget' class is styled differently in frontend.css
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'dtbaker-stylepress-elementor-widget';
	}


	/**
	 * Get available categories for this widget. Which is our own category for page builder options.
	 *
	 * @return array
	 */
	public function get_categories() {
		return [ 'dtbaker-elementor' ];
	}

	/**
	 * We always show this item in the panel.
	 *
	 * @return bool
	 */
	public function show_in_panel() {
		return true;
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_page_carousel',
			[
				'label' => esc_html__( 'Page Slider', 'elementor' ),
			]
		);

		$page_select = array(
			'' => esc_html__( ' - choose - ' ),
		);
		$pages       = get_pages( array( 'parent' => 0, 'post_status' => 'publish,private' ) );
		function add_select_children( $page_select, $pages, $level = 0 ) {
			foreach ( $pages as $page ) {
				// we use the 'p.' prefix because elementor sorts by ID.
				$page_select[ 'p.' . $page->ID ] = ( $level ? str_pad( ' ', $level + 1, '-', STR_PAD_RIGHT ) : '' ) . $page->post_title;
				$sub_pages                       = get_pages( array(
					'parent'      => $page->ID,
					'post_status' => 'publish,private',
				) );
				if ( $sub_pages ) {
					$page_select = add_select_children( $page_select, $sub_pages, $level + 1 );
				}
			}

			return $page_select;
		}

		$page_select = add_select_children( $page_select, $pages, 0 );

		$this->add_control(
			'page_list',
			[
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'default'     => [
					[
						'text' => esc_html__( 'Page #1', 'elementor' ),
						'page' => '',
					],
					[
						'text' => esc_html__( 'Page #2', 'elementor' ),
						'page' => '',
					],
					[
						'text' => esc_html__( 'Page #3', 'elementor' ),
						'page' => '',
					],
				],
				//'section'     => 'section_page_carousel',
				'fields'      => [
					[
						'name'        => 'page',
						'label'       => esc_html__( 'Choose Page', 'elementor' ),
						'type'        => Controls_Manager::SELECT,
						'label_block' => true,
						'options'     => $page_select,
					],
					[
						'name'        => 'id',
						'label'       => esc_html__( 'Choose ID (optional)', 'elementor' ),
						'type'        => Controls_Manager::TEXT,
						'label_block' => true,
					],
					[
						'name'        => 'title',
						'label'       => esc_html__( 'Choose Title (optional)', 'elementor' ),
						'type'        => Controls_Manager::TEXT,
						'label_block' => true,
					],
					[
						'name'        => 'link',
						'label'       => esc_html__( 'Slide Link (optional)', 'elementor' ),
						'type'        => Controls_Manager::URL,
                        'placeholder' => 'http://your-link.com',
						'label_block' => true,
                        'default' => [
                            'url' => '',
                        ],
					],
				],
				'title_field' => false,
			]
		);

		$slides_to_show = range( 1, 10 );
		$slides_to_show = array_combine( $slides_to_show, $slides_to_show );

		$this->add_control(
			'slides_to_show',
			[
				'label'   => esc_html__( 'Slides to Show', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				//'section' => 'section_page_carousel',
				'options' => $slides_to_show,
			]
		);

		$this->add_control(
			'slides_to_scroll',
			[
				'label'     => esc_html__( 'Slides to Scroll', 'elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '2',
				//'section'   => 'section_page_carousel',
				'options'   => $slides_to_show,
				'condition' => [
					'slides_to_show!' => '1',
				],
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => esc_html__( 'Navigation', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'both',
				//'section' => 'section_page_carousel',
				'options' => [
					'both'   => esc_html__( 'Arrows and Dots', 'elementor' ),
					'arrows' => esc_html__( 'Arrows', 'elementor' ),
					'dots'   => esc_html__( 'Dots', 'elementor' ),
					//'icons'   => esc_html__( 'Icons', 'elementor' ),
					'none'   => esc_html__( 'None', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'slider_type',
			[
				'label'   => esc_html__( 'Slider Type', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'normal',
				//'section' => 'section_page_carousel',
				'options' => [
					'normal'   => esc_html__( 'Normal', 'elementor' ),
					'icons' => esc_html__( 'With Icons', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label'   => esc_html__( 'View', 'elementor' ),
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'traditional',
				//'section' => 'section_page_carousel',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_options',
			[
				'label' => __( 'Additional Options', 'elementor' ),
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'   => esc_html__( 'Pause on Hover', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'yes',
				//'section' => 'section_additional_options',
				'options' => [
					'yes' => esc_html__( 'Yes', 'elementor' ),
					'no'  => esc_html__( 'No', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'yes',
				//'section' => 'section_additional_options',
				'options' => [
					'yes' => esc_html__( 'Yes', 'elementor' ),
					'no'  => esc_html__( 'No', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'   => esc_html__( 'Autoplay Speed', 'elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5000,
				//'section' => 'section_additional_options',
			]
		);

		$this->add_control(
			'infinite',
			[
				'label'   => esc_html__( 'Infinite Loop', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'yes',
				//'section' => 'section_additional_options',
				'options' => [
					'yes' => esc_html__( 'Yes', 'elementor' ),
					'no'  => esc_html__( 'No', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'effect',
			[
				'label'     => esc_html__( 'Effect', 'elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'slide',
				//'section'   => 'section_additional_options',
				'options'   => [
					'slide' => esc_html__( 'Slide', 'elementor' ),
					'fade'  => esc_html__( 'Fade', 'elementor' ),
				],
				'condition' => [
					'slides_to_show' => '1',
				],
			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => esc_html__( 'Animation Speed', 'elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
				//'section' => 'section_additional_options',
			]
		);

		$this->add_control(
			'direction',
			[
				'label'   => esc_html__( 'Direction', 'elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ltr',
				//'section' => 'section_additional_options',
				'options' => [
					'ltr' => esc_html__( 'Left', 'elementor' ),
					'rtl' => esc_html__( 'Right', 'elementor' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label' => __( 'Navigation', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation' => [ 'arrows', 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'heading_style_arrows',
			[
				'label' => __( 'Arrows', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_position',
			[
				'label' => __( 'Arrows Position', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'inside',
				'options' => [
					'inside' => __( 'Inside', 'elementor' ),
					'outside' => __( 'Outside', 'elementor' ),
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => __( 'Arrows Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-page-carousel-wrapper .slick-slider .slick-prev:before, {{WRAPPER}} .elementor-page-carousel-wrapper .slick-slider .slick-next:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label' => __( 'Arrows Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-page-carousel-wrapper .slick-slider .slick-prev:before, {{WRAPPER}} .elementor-page-carousel-wrapper .slick-slider .slick-next:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'heading_style_dots',
			[
				'label' => __( 'Dots', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_position',
			[
				'label' => __( 'Dots Position', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'outside',
				'options' => [
					'outside' => __( 'Outside', 'elementor' ),
					'inside' => __( 'Inside', 'elementor' ),
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_size',
			[
				'label' => __( 'Dots Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-page-carousel-wrapper .elementor-page-carousel .slick-dots li button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label' => __( 'Dots Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-page-carousel-wrapper .elementor-page-carousel .slick-dots li button:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_page',
			[
				'label' => __( 'Page', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'page_spacing',
			[
				'label' => __( 'Spacing', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Default', 'elementor' ),
					'custom' => __( 'Custom', 'elementor' ),
				],
				'default' => '',
				'condition' => [
					'slides_to_show!' => '1',
				],
			]
		);

		$this->add_control(
			'page_spacing_custom',
			[
				'label' => __( 'Page Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 20,
				],
				'show_label' => false,
				'selectors' => [
					'{{WRAPPER}} .slick-list' => 'margin-left: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .slick-slide .slick-slide-inner' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'page_spacing' => 'custom',
					'slides_to_show!' => '1',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'page_border',
				'selector' => '{{WRAPPER}} .elementor-page-carousel-wrapper .elementor-page-carousel .slick-slide-page',
			]
		);

		$this->add_control(
			'page_border_radius',
			[
				'label' => __( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-page-carousel-wrapper .elementor-page-carousel .slick-slide-page' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$instance = $this->get_settings();

		if ( empty( $instance['page_list'] ) ) {
			return;
		}

		$slides = [];
		foreach ( $instance['page_list'] as $page_list_id => $page ) {

			$post_id = (int) str_replace( 'p.', '', $page['page'] );
			if ( ! $post_id ) {
				continue;
			}



			$page_html = '';

			$inserted_post = get_post( $post_id );
			if ( $inserted_post ) {

				if ( Plugin::instance()->editor->is_edit_mode() ) {
					$page_html = $inserted_post->post_title;
					$slides[]  = '<div>' . $page_html . '</div>';
				} else {

					$old_global_post = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : false;
					setup_postdata( $GLOBALS['post'] =& $inserted_post );

					$inserted_post->post_content = apply_filters( 'the_content', $inserted_post->post_content );
					$page_html                   = do_shortcode( $inserted_post->post_content );
					$block_id = substr(md5(serialize($instance).$page_list_id.$page_html . $post_id),0,6);

					$elementor = Plugin::instance();

					// copy this from dtbaker-insert-page:
					$page_html = $elementor->frontend->apply_builder_in_content( $page_html );

					// maybe do a set_option get_option hack to prevent fatal errors on elementor attempts?

                    ob_start();
                    if ( class_exists( 'Post_CSS_File' ) ) {
                        $css_file = new Post_CSS_File( $post_id );
                        echo '<style type="text/css">' . $css_file->get_css() . '</style>';
                    }
                    $elementor_css = ob_get_clean();
                    if ( ! isset( $GLOBALS['insert_page_extra_footer_css'] ) ) {
                        $GLOBALS['insert_page_extra_footer_css'] = array();
                    }
                    $GLOBALS['insert_page_extra_footer_css'][] = str_replace( 'elementor-frontend-stylesheet', 'elementor-frontend-stylesheet-' . $block_id, $elementor_css );
					// stop duplicate ID w3c warnings.
					foreach ( array( 'elementor', 'elementor-inner', 'elementor-section-wrap' ) as $id_to_replace ) {
						$page_html = str_replace( 'id="' . $id_to_replace . '"', 'id="' . esc_attr( $block_id ) . '-' . $id_to_replace . '"', $page_html );
					}
					// end copy this


					setup_postdata( $GLOBALS['post'] =& $old_global_post );
					$page_html = apply_filters( 'elementor/widgets/page_carousel/slide', $page_html, $instance );
					$slides[]  = '<div data-slide-id="'.(int)$page_list_id.'" data-post-id="'.(int)$post_id.'" data-icon="'. esc_attr(get_post_meta($post_id,'icon',true)) .'" data-link="'. esc_attr($page['link']['url']) .'"><div class="slick-slide-inner">' . $page_html . '</div></div>';
				}
			}
		}

		if ( empty( $slides ) ) {
			return;
		}

		$is_slideshow = '1' === $instance['slides_to_show'];
		$is_rtl       = ( 'rtl' === $instance['direction'] );
		$direction    = $is_rtl ? 'rtl' : 'ltr';
		$show_dots    = ( in_array( $instance['navigation'], [ 'dots', 'both' ] ) );
		$show_arrows  = ( in_array( $instance['navigation'], [ 'arrows', 'both' ] ) );

		$slick_options = [
			'slidesToShow'  => absint( $instance['slides_to_show'] ),
			'autoplaySpeed' => absint( $instance['autoplay_speed'] ),
			'autoplay'      => ( 'yes' === $instance['autoplay'] ),
			'infinite'      => ( 'yes' === $instance['infinite'] ),
			'pauseOnHover'  => ( 'yes' === $instance['pause_on_hover'] ),
			'speed'         => absint( $instance['speed'] ),
			'arrows'        => $show_arrows,
			'dots'          => $show_dots,
			'rtl'           => $is_rtl,
            'dtbaker_type'  => ! empty( $instance['dtbaker_slider_type'] ) ? $instance['dtbaker_slider_type'] : '',
		];

		$carousel_classes = [ 'elementor-page-carousel' ];

		if ( $show_arrows ) {
			$carousel_classes[] = 'slick-arrows-' . $instance['arrows_position'];
		}

		if ( $show_dots ) {
			$carousel_classes[] = 'slick-dots-' . $instance['dots_position'];
		}

		if ( ! $is_slideshow ) {
			$slick_options['slidesToScroll'] = absint( $instance['slides_to_scroll'] );
		} else {
			$slick_options['fade'] = ( 'fade' === $instance['effect'] );
		}

		$slick_options = apply_filters( 'elementor/widgets/page_carousel/slick_options', $slick_options, $instance );
		if ( Plugin::instance()->editor->is_edit_mode() ) {
			esc_html_e( 'Page Slider Here:' );
			echo implode( '', $slides );
		}else {
			if ( ! empty( $instance['dtbaker_slider_type'] ) && 'icons' === $instance['dtbaker_slider_type'] ) {
				$carousel_classes [] = 'inner-content-width';
			}

			?>
            <div class="elementor-page-carousel-wrapper elementor-slick-slider"
                 dir="<?php echo esc_attr( $direction ); ?>">
                <div class="<?php echo esc_attr( implode( ' ', $carousel_classes ) ); ?>"
                     data-slider_options='<?php echo wp_json_encode( $slick_options ); ?>'>
					<?php echo implode( '', $slides ); ?>
                </div>
            </div>
			<?php
			if ( ! empty( $instance['dtbaker_slider_type'] ) && 'icons' === $instance['dtbaker_slider_type'] ) {
				$icons = array();
				foreach ( $instance['page_list'] as $page_list_id => $page ) {
					$post_id = (int) str_replace( 'p.', '', $page['page'] );
					if ( ! $post_id ) {
						continue;
					}
					$inserted_post = get_post( $post_id );
					if ( $inserted_post ) {
						// create an icon. with the title.
						$icons[] = '<div data-slide-id="' . (int) $page_list_id . '" data-post-id="' . (int) $post_id . '" data-icon="' . esc_attr( get_post_meta( $post_id, 'icon', true ) ) . '" data-link="' . esc_attr( $page['link']['url'] ) . '"> <div class="slider-icon"> </div> <h4>' . esc_html( $page['title'] ) . '</h4> </div>';

					}
				}
				?>
                <div class="elementor-page-carousel-icons" dir="<?php echo esc_attr( $direction ); ?>">
                    <div class="page-carousel-icon-wrapper inner-content-width">
                        <div class="elementor-dtbaker-page-slider-icons">
							<?php echo implode( '', $icons ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
		}
	}

	protected function content_template() {
		?>
		<div class="elementor-dtbaker-page-slider">
			The Page Slider Will Appear Here
		</div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new StylePress_Widget_Page_Carousel() );