<?php

namespace Elementor;

defined( 'STYLEPRESS_PATH' ) || exit;

class Stylepress_Post_Grid extends Widget_Base {
	/**
	 * Get Widgets name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'stylepress_post_grid';
	}

	/**
	 * Get widgets title
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Post Grid', 'stylepress' );
	}

	protected $_has_template_content = false;
	public function is_reload_preview_required() {
		return false;
	}

	/**
	 * Get the current icon for display on frontend.
	 * The extra 'stylepress-widget' class is styled differently in frontend.css
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'stylepress-elementor-widget';
	}

	/**
	 * Get available categories for this widget. Which is our own category for page builder options.
	 *
	 * @return array
	 */
	public function get_categories() {
		return [ 'stylepress' ];
	}

	/**
	 * We always show this item in the panel.
	 *
	 * @return bool
	 */
	public function show_in_panel() {
		return true;
	}

	/**
	 * This registers our controls for the widget. Currently there are none but we may add options down the track.
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_stylepress_post_grid',
			[
				'label' => __( 'Post Grid', 'stylepress' ),
			]
		);

		$this->add_control(
			'refer_wp_org',
			[
				'raw'     => __( 'For more detail about following filters please refer <a href="https://codex.wordpress.org/Template_Tags/get_posts" target="_blank">here</a>', 'stylepress' ),
				'type'    => Controls_Manager::RAW_HTML,
				'classes' => 'elementor-descriptor',
			]
		);

		$post_types = get_post_types( [
			'public'   => 'true',
			'_builtin' => false,
		], 'names', 'and' );
		$post_types = array( 'post' => 'post' ) + $post_types;

		$this->add_control(
			'post_type',
			[
				'label'   => esc_html__( 'Select post type', 'stylepress' ),
				'type'    => Controls_Manager::SELECT2,
				'options' => $post_types,
			]
		);

		$this->add_control(
			'taxonomy_type',
			[
				'label'     => __( 'Select Taxonomy', 'stylepress' ),
				'type'      => Controls_Manager::SELECT2,
				'options'   => '',
				'condition' => [
					'post_type!' => '',
				],
			]
		);

		$this->add_control(
			'terms',
			[
				'label'       => __( 'Select Terms (usually categories/tags)', 'stylepress' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => '',
				'multiple'    => true,
				'condition'   => [
					'taxonomy_type!' => '',
				],
			]
		);

		$this->add_control(
			'cat_exclude',
			[
				'label'       => __( 'Include / Exclude Category ID', 'stylepress' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'description' => __( ' To include a category use the Category ID directly (e.g. 1,2,3). To exclude category add a minus sign before the Category ID (e.g. -1,-44,-3343)', 'stylepress' ),
			]
		);


		$this->end_controls_section();


		$this->start_controls_section(
			'section_content2',
			[
				'label' => esc_html__( 'Pagination & Ordering', 'stylepress' ),   //section name for controler view
			]
		);

		$this->add_control(
			'posts',
			[
				'label'       => esc_html__( 'Post Per Page', 'stylepress' ),
				'description' => esc_html__( 'Give -1 for all post & No Pagination', 'stylepress' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => - 1,
			]
		);

		$this->add_control(
			'pagination_yes',
			[
				'label'     => esc_html__( 'Pagination Enabled', 'stylepress' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					1 => 'Yes',
					2 => 'No'
				],
				'default'   => 1,
				'condition' => [
					'posts!' => - 1,
				]
			]
		);
		$this->add_control(
			'offset',
			[
				'label'   => esc_html__( 'Post Offset', 'stylepress' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => '0'
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Order By', 'stylepress' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'ID'            => 'Post Id',
					'author'        => 'Post Author',
					'title'         => 'Title',
					'date'          => 'Date',
					'modified'      => 'Last Modified Date',
					'parent'        => 'Parent Id',
					'rand'          => 'Random',
					'comment_count' => 'Comment Count',
					'menu_order'    => 'Menu Order',
				],
				'default' => 'date',

			]
		);

		$this->add_control(
			'order',
			[
				'label'   => esc_html__( 'Post Order', 'stylepress' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'asc'  => 'Ascending',
					'desc' => 'Descending'
				],
				'default' => 'desc',

			]
		);

		$this->add_control(
			'sticky_ignore',
			[
				'label'   => esc_html__( 'Sticky Condition', 'stylepress' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'1' => 'Remove Sticky',
					'0' => 'Keep Sticky'
				],

				'default' => '1',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content3',
			[
				'label' => esc_html__( 'Post Style & Image Settings', 'stylepress' ),   //section name for controler view
			]
		);


		$this->add_control(
			'display_type',
			[
				'label'   => esc_html__( 'Choose your desired style', 'stylepress' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'grid'            => 'Grid Layout',
					'list'            => 'List Layout',
					'first-post-grid' => '1st Full Post then Grid',
					'first-post-list' => '1st Full Post then List',
					'minimal'         => 'Minimal Grid'
				],
				'default' => 'grid'
			]
		);

		$this->add_control(
			'posts_per_row',
			[
				'label'     => esc_html__( 'Posts Per Row', 'stylepress' ),
				'type'      => Controls_Manager::SELECT,
				'condition' => [
					'display_type' => [ 'grid', 'minimal' ],
				],
				'options'   => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'6' => '6',
				],
				'default'   => '2',
			]
		);


		$this->add_control(
			'filter_thumbnail',
			[
				'label'   => esc_html__( 'Image Condition', 'stylepress' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					0            => esc_html__( 'Show All', 'stylepress' ),
					'EXISTS'     => esc_html__( 'With Image', 'stylepress' ),
					'NOT EXISTS' => esc_html__( 'Without Image', 'stylepress' ),
				],
				'default' => 0,

			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image', // Actually its `image_size`.
				'default'   => 'large',
				'exclude'   => [ 'custom' ],
				'condition' => [
					'filter_thumbnail!' => 'NOT EXISTS',
				],
			]
		);
		$this->add_control(
			'image_style',
			[
				'label'     => esc_html__( 'Featured Image Style', 'stylepress' ),
				'type'      => Controls_Manager::SELECT2,
				'options'   => [
					'standard'   => 'Standard',
					'top-left'   => 'left top rounded',
					'top-bottom' => 'left bottom rounded'
				],
				'default'   => '1',
				'condition' => [
					'filter_thumbnail!' => 'NOT EXISTS',
				],
			]
		);


		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_grid',
			[
				'label' => esc_html__( 'Style', 'stylepress' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_text_transform',
			[
				'label'     => esc_html__( 'Title Text Transform', 'stylepress' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''           => esc_html__( 'None', 'stylepress' ),
					'uppercase'  => esc_html__( 'UPPERCASE', 'stylepress' ),
					'lowercase'  => esc_html__( 'lowercase', 'stylepress' ),
					'capitalize' => esc_html__( 'Capitalize', 'stylepress' ),
				],
				'selectors' => [
					'{{WRAPPER}} .entry-title' => 'text-transform: {{VALUE}};',   //the selector used above in add_control
				],
			]
		);

		$this->add_responsive_control(
			'title_font_size',
			[
				'label'      => esc_html__( 'Title Size', 'stylepress' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .entry-title' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_color_hover',
			[
				'label'     => esc_html__( 'Title Hover Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_color',
			[
				'label'     => esc_html__( 'Meta Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-meta a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_hover_color',
			[
				'label'     => esc_html__( 'Meta Hover Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-meta a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_color_i',
			[
				'label'     => esc_html__( 'Meta Icon Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'excerpt_text_transform',
			[
				'label'     => esc_html__( 'Excerpt Transform', 'stylepress' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''           => esc_html__( 'None', 'stylepress' ),
					'uppercase'  => esc_html__( 'UPPERCASE', 'stylepress' ),
					'lowercase'  => esc_html__( 'lowercase', 'stylepress' ),
					'capitalize' => esc_html__( 'Capitalize', 'stylepress' ),
				],
				'selectors' => [
					'{{WRAPPER}} .blog-excerpt p' => 'text-transform: {{VALUE}};',   //the selector used above in add_control
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_font_size',
			[
				'label'      => esc_html__( 'Excerpt Size', 'stylepress' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .blog-excerpt p' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'exceprt_color',
			[
				'label'     => esc_html__( 'Excerpt Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .blog-excerpt p' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'te_align',
			[
				'label'     => __( 'Text Alignment', 'stylepress' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => __( 'Left', 'stylepress' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'stylepress' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'stylepress' ),
						'icon'  => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'stylepress' ),
						'icon'  => 'fa fa-align-justify',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .blog-excerpt p' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_align',
			[
				'label'     => __( 'Pagination Alignment', 'stylepress' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => __( 'Left', 'stylepress' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'stylepress' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'stylepress' ),
						'icon'  => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'stylepress' ),
						'icon'  => 'fa fa-align-justify',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid-nav' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'pagi_font_size',
			[
				'label'      => esc_html__( 'Pagination Size', 'stylepress' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .stylepress-grid-nav' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render our custom menu onto the page.
	 */
	protected function render() {

		$settings = $this->get_settings();
		if ( ! empty( $settings['taxonomy_type'] ) ) {
			$terms = get_terms( array(
				'taxonomy'   => $settings['taxonomy_type'],
				'hide_empty' => true,
			) );
			foreach ( $terms as $term ) {
				$term_id[] = $term->term_id;
			}
		}
		if ( ! empty( $settings['terms'] ) ) {
			$category = implode( ", ", $settings['terms'] );
		} elseif ( ! empty( $settings['taxonomy_type'] ) ) {
			$category = implode( ", ", $term_id );
		} else {
			$category = '';
		}

		if ( ! empty( $settings['taxonomy_type'] ) ) {
			$tax_query = array(
				array(
					'taxonomy' => $settings['taxonomy_type'],
					'field'    => 'term_id',
					'terms'    => explode( ',', $category ),
				),
			);
		} else {
			$tax_query = '';
		}
		if ( ! empty( $settings['filter_thumbnail'] ) ) {
			$stylepress_image_condition = array(
				'meta_query' => array(
					array(
						'key'     => '_thumbnail_id',
						'compare' => $settings['filter_thumbnail'],
					)
				)
			);
		} else {
			$stylepress_image_condition = '';
		}


		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) { // if is static front page
			$paged = get_query_var( 'page' );
		} else {
			$paged = 1;
		}


		$args = array(
			'post_type'             => $settings['post_type'],
			'meta_query'            => $stylepress_image_condition,
			'cat'                   => $settings['cat_exclude'],
			'post_status'           => 'publish',
			'posts_per_page'        => $settings['posts'],
			'paged'                 => $paged,
			'tax_query'             => $tax_query,
			'orderby'               => $settings['orderby'],
			'order'                 => $settings['order'],   //ASC / DESC
			'ignore_sticky_posts'   => $settings['sticky_ignore'],
			'stylepress_grid_query' => 'yes',
			'stylepress_set_offset' => $settings['offset'],
		);

		$grid_query = new \WP_Query( $args );

		$count = 0;
		?>

		<div class="content-area stylepress-grid">
			<div class="site-main <?php echo esc_html( $settings['display_type'] . ' ' . $settings['image_style'] ); ?>">
				<div class="row">
					<?php
					if ( $grid_query->have_posts() ) :

						/* Start the Loop */
						while ( $grid_query->have_posts() ) : $grid_query->the_post();  // Start of posts loop found posts
							$count ++;
							\StylePress\Templates::get_template_part( 'content', $settings['display_type'], 'extensions/post-grid/', [
								'count'      => $count,
								'col_no'     => 2,
								'col_width'  => 12,
								'post_count' => '',
								'image_size' => 'full',
							] );
						endwhile; // End of posts loop found posts

						if ( $settings['pagination_yes'] == 1 ) :  //Start of pagination condition
							$big = 999999999; // need an unlikely integer
							$totalpages = $grid_query->max_num_pages;
							$current = max( 1, $paged );
							$paginate_args = array(
								'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
								'format'    => '?paged=%#%',
								'current'   => $current,
								'total'     => $totalpages,
								'show_all'  => false,
								'end_size'  => 1,
								'mid_size'  => 3,
								'prev_next' => true,
								'prev_text' => esc_html__( '« Previous' ),
								'next_text' => esc_html__( 'Next »' ),
								'type'      => 'plain',
								'add_args'  => false,
							);

							$pagination = paginate_links( $paginate_args ); ?>
							<div class="col-md-12">
								<nav class='pagination wp-caption stylepress-grid-nav'>
									<?php echo $pagination; ?>
								</nav>
							</div>
						<?php endif; //end of pagination condition
						?>


					<?php else :   //if no posts found
						\StylePress\Templates::get_template_part( 'content', 'none', 'extensions/post-grid/' );
					endif; //end of post loop ?>

				</div><!-- #main -->
			</div><!-- #primary -->
		</div>

		<?php
		wp_reset_postdata();
	}

	public function render_plain_content() {}

}

Plugin::instance()->widgets_manager->register_widget_type( new Stylepress_Post_Grid() );