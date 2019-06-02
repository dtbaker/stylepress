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
				'label' => __( 'Grid Query', 'stylepress' ),
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

		$this->add_control(
			'filter_thumbnail',
			[
				'label'   => esc_html__( 'Include / Exclude Images', 'stylepress' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					0            => esc_html__( 'Show All Posts', 'stylepress' ),
					'EXISTS'     => esc_html__( 'Only Posts With Images', 'stylepress' ),
					'NOT EXISTS' => esc_html__( 'Only Posts Without Images', 'stylepress' ),
				],
				'default' => 0,

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
			'posts_per_page',
			[
				'label'       => esc_html__( 'Post Per Page', 'stylepress' ),
				'description' => esc_html__( 'Give -1 for all post', 'stylepress' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 20,
			]
		);

		$this->add_control(
			'pagination_yes',
			[
				'label'        => __( 'Enable Pagination', 'stylepress' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
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
			'section_grid_meta',
			[
				'label' => esc_html__( 'Meta Information', 'stylepress' ),   //section name for controler view
			]
		);

		$this->add_control(
			'meta_show_thumbnail',
			[
				'label'        => __( 'Show Thumbnail', 'stylepress' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'meta_show_title',
			[
				'label'        => __( 'Show Title', 'stylepress' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'meta_show_date',
			[
				'label'        => __( 'Show Date', 'stylepress' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'meta_show_author',
			[
				'label'        => __( 'Show Author', 'stylepress' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'meta_show_category',
			[
				'label'        => __( 'Show Category', 'stylepress' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'meta_show_tags',
			[
				'label'        => __( 'Show Tags', 'stylepress' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'meta_show_excerpt',
			[
				'label'        => __( 'Show Excerpt', 'stylepress' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'meta_exceprt_length',
			[
				'label'     => __( 'Excerpt Length', 'stylepress' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => '10',
				'condition' => [
					'meta_show_excerpt' => 'yes',
				],
			]
		);


		$this->add_control(
			'meta_show_comments',
			[
				'label'        => __( 'Show Comments', 'stylepress' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'meta_show_readmore',
			[
				'label'        => __( 'Read More Button', 'stylepress' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_post_layout_options',
			[
				'label' => esc_html__( 'Grid', 'stylepress' ),   //section name for controler view
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'display_type',
			[
				'label'   => esc_html__( 'Post layout style', 'stylepress' ),
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

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Card', 'stylepress' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'background_color',
			[
				'label'     => esc_html__( 'Background Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__item' => 'background-color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'card_row_gap',
			[
				'label'      => esc_html__( 'Row Gap', 'stylepress' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'size_units' => [ '%', 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .stylepress-grid__item' => 'margin: 0 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_inner_padding',
			[
				'label'      => esc_html__( 'Card Padding', 'stylepress' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'size_units' => [ '%', 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .stylepress-grid__item' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_width',
			[
				'label'      => esc_html__( 'Card Width', 'stylepress' ),
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
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'size_units' => [ '%', 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .stylepress-grid__item' => 'flex: 0 1 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'card_border',
				'selector' => '{{WRAPPER}} .stylepress-grid__item',
				'separator' => 'before',
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .stylepress-grid__item',
			]
		);



		$this->end_controls_section();


		$this->start_controls_section(
			'section_post_image_options',
			[
				'label' => esc_html__( 'Thumbnail', 'stylepress' ),   //section name for controler view
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image', // Actually its `image_size`.
				'default'   => 'large',
				//'exclude'   => [ 'custom' ],
			]
		);


		$this->add_control(
			'thumbnail_height',
			[
				'label' => __( 'Max Height', 'stylepress' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '200',
					'unit' => 'px',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 700,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__item-thumb' => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_margin',
			[
				'label'           => __( 'Image Gap', 'stylepress' ),
				'type'            => Controls_Manager::DIMENSIONS,
				'size_units'      => [ 'px', 'em', '%' ],
				'selectors'       => [
					"{{WRAPPER}} .stylepress-grid__item-image" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'image_style' => 'category-over',
				],
			]
		);

		$this->add_control(
			'image_style',
			[
				'label'     => esc_html__( 'Featured Image Style', 'stylepress' ),
				'type'      => Controls_Manager::SELECT2,
				'options'   => [
					'category-over' => 'Category Text Overlay',
					'standard'   => 'Standard',
					'top-left'   => 'Left top rounded',
					'top-bottom' => 'Left bottom rounded',
				],
				'default'   => 'category-over',
				'separator' => 'before',
			]
		);

		// stylepress-grid__item-thumb-overlay


		$this->add_responsive_control(
			'category_over_align',
			[
				'label'     => __( 'Category Alignment', 'stylepress' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => __( 'Left', 'stylepress' ),
						'icon'  => 'fa fa-align-left',
					],
					'right'   => [
						'title' => __( 'Right', 'stylepress' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__item-thumb-overlay' => '{{VALUE}}: 0;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'category_over_typography',
				'label'          => __( 'Category Typography', 'stylepress' ),
				'selector'       => '{{WRAPPER}} .stylepress-grid__item-thumb-overlay',
				'scheme'         => Scheme_Typography::TYPOGRAPHY_3,
				'fields_options' => [],
				'condition' => [
					'image_style' => 'category-over',
				],
			]
		);

		$this->add_control(
			'category_over_typography_color',
			[
				'label'     => __( 'Category Text Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__item-thumb-overlay' => 'color: {{VALUE}};',
				],
				'condition' => [
					'image_style' => 'category-over',
				],
			]
		);

		$this->add_control(
			'category_over_typography_bg',
			[
				'label'     => __( 'Category Background Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__item-thumb-overlay' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'image_style' => 'category-over',
				],
			]
		);

		$this->add_responsive_control(
			'category_over_typography_padding',
			[
				'label'           => __( 'Category Padding', 'stylepress' ),
				'type'            => Controls_Manager::DIMENSIONS,
				'size_units'      => [ 'px', 'em', '%' ],
				'selectors'       => [
					"{{WRAPPER}} .stylepress-grid__item-thumb-overlay" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'image_style' => 'category-over',
				],
			]
		);

		$this->add_responsive_control(
			'category_over_typography_margin',
			[
				'label'           => __( 'Category Margin', 'stylepress' ),
				'type'            => Controls_Manager::DIMENSIONS,
				'size_units'      => [ 'px', 'em', '%' ],
				'selectors'       => [
					"{{WRAPPER}} .stylepress-grid__item-thumb-overlay" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'image_style' => 'category-over',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_grid',
			[
				'label' => esc_html__( 'Content', 'stylepress' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_responsive_control(
			'stylepress_blog_text_align',
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
					'{{WRAPPER}} .stylepress-grid__item-excerpt p, {{WRAPPER}} .stylepress-grid__item-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'title_text_typo',
				'label'          => __( 'Title Text', 'stylepress' ),
				'selector'       => '{{WRAPPER}} .stylepress-grid__item-title',
				'scheme'         => Scheme_Typography::TYPOGRAPHY_3,
				'fields_options' => [],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__item-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_color_hover',
			[
				'label'     => esc_html__( 'Title Hover Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__item-title a:hover, {{WRAPPER}} .stylepress-grid__item-title a:active, {{WRAPPER}} .stylepress-grid__item-title a:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'           => __( 'Title Spacing', 'stylepress' ),
				'type'            => Controls_Manager::DIMENSIONS,
				'size_units'      => [ 'px', 'em', '%' ],
				'selectors'       => [
					"{{WRAPPER}} .stylepress-grid__item-title" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'excerpt_text_typo',
				'label'          => __( 'Excerpt Text', 'stylepress' ),
				'selector'       => '{{WRAPPER}} .stylepress-grid__item-excerpt p',
				'scheme'         => Scheme_Typography::TYPOGRAPHY_3,
				'fields_options' => [],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'stylepress_blog_excerpt_color',
			[
				'label'     => esc_html__( 'Excerpt Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__item-excerpt p' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'excerpt_spacing',
			[
				'label'           => __( 'Excerpt Spacing', 'stylepress' ),
				'type'            => Controls_Manager::DIMENSIONS,
				'size_units'      => [ 'px', 'em', '%' ],
				'selectors'       => [
					"{{WRAPPER}} .stylepress-grid__item-excerpt" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'meta_text_typo',
				'label'          => __( 'Meta Text', 'stylepress' ),
				'selector'       => '{{WRAPPER}} .stylepress-grid__item-meta',
				'scheme'         => Scheme_Typography::TYPOGRAPHY_3,
				'fields_options' => [],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'meta_color',
			[
				'label'     => esc_html__( 'Meta Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__item-meta, {{WRAPPER}} .stylepress-grid__item-meta a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_hover_color',
			[
				'label'     => esc_html__( 'Meta Hover Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__item-meta a:hover, {{WRAPPER}} .stylepress-grid__item-meta a:active, {{WRAPPER}} .stylepress-grid__item-meta a:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'meta_spacing',
			[
				'label'           => __( 'Meta Spacing', 'stylepress' ),
				'type'            => Controls_Manager::DIMENSIONS,
				'size_units'      => [ 'px', 'em', '%' ],
				'selectors'       => [
					"{{WRAPPER}} .stylepress-grid__item-meta" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_blog_decoration',
			[
				'label' => __( 'Decoration', 'stylepress' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'decoration_image',
			[
				'label' => __( 'Decoration Image', 'stylepress' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
			]
		);

		$this->add_responsive_control(
			'decoration_width',
			[
				'label' => __( 'Width', 'stylepress' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '50',
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__item-decoration-image' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'decoration_align',
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
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__item-decoration' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_pagination',
			[
				'label' => esc_html__( 'Pagination', 'stylepress' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .stylepress-grid__pagination' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'pagination_typo',
				'label'          => __( 'Pagination Text', 'stylepress' ),
				'selector'       => '{{WRAPPER}} .stylepress-grid-nav',
				'scheme'         => Scheme_Typography::TYPOGRAPHY_3,
				'fields_options' => [],
			]
		);

		$this->start_controls_tabs( 'tabs_pagination_style' );

		$this->start_controls_tab(
			'tab_pagination_normal',
			[
				'label' => __( 'Normal', 'stylepress' ),
			]
		);
		/////////////////////////

		$this->add_control(
			'pagination_text_normal',
			[
				'label'     => esc_html__( 'Text Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'default' => '#4a4a4a',
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__pagination span, {{WRAPPER}} .stylepress-grid__pagination a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_background_normal',
			[
				'label'     => esc_html__( 'Background Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'default' => '#f7f7f7',
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__pagination span, {{WRAPPER}} .stylepress-grid__pagination a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_border_normal',
			[
				'label'     => esc_html__( 'Border Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'default' => '#d7d8d8',
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__pagination a, {{WRAPPER}} .stylepress-grid__pagination span' => 'border-color: {{VALUE}}; box-shadow: 0px 1px 0px {{VALUE}}; ',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_hover',
			[
				'label' => __( 'Hover', 'stylepress' ),
			]
		);
		/////////////////////////

		$this->add_control(
			'pagination_text_hover',
			[
				'label'     => esc_html__( 'Text Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__pagination a:hover, {{WRAPPER}} .stylepress-grid__pagination a:active, {{WRAPPER}} .stylepress-grid__pagination a:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_background_hover',
			[
				'label'     => esc_html__( 'Background Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__pagination a:hover, {{WRAPPER}} .stylepress-grid__pagination a:active, {{WRAPPER}} .stylepress-grid__pagination a:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_border_hover',
			[
				'label'     => esc_html__( 'Border Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__pagination a:hover, {{WRAPPER}} .stylepress-grid__pagination a:active, {{WRAPPER}} .stylepress-grid__pagination a:focus' => 'border-color: {{VALUE}}; box-shadow: 0px 1px 0px {{VALUE}}; ',
				],
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_current',
			[
				'label' => __( 'Current', 'stylepress' ),
			]
		);
		/////////////////////////

		$this->add_control(
			'pagination_text_current',
			[
				'label'     => esc_html__( 'Text Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__pagination span.current' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_background_current',
			[
				'label'     => esc_html__( 'Background Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'default' => '#0073af',
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__pagination span.current' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_border_current',
			[
				'label'     => esc_html__( 'Border Color', 'stylepress' ),
				'type'      => Controls_Manager::COLOR,
				'default' => '#03537d',
				'selectors' => [
					'{{WRAPPER}} .stylepress-grid__pagination span.current' => 'border-color: {{VALUE}}; box-shadow: 0px 1px 0px {{VALUE}}; ',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/****************************************************
		 **************** BUTTON ***************************
		 ****************************************************/

		$this->start_controls_section(
			'read_more_section',
			[
				'label' => __( 'Button', 'stylepress' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'read_more_text',
			[
				'label' => __( 'Text', 'stylepress' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Read More', 'stylepress' ),
				'placeholder' => __( 'Read More', 'stylepress' ),
			]
		);

		$this->add_responsive_control(
			'read_more_align',
			[
				'label' => __( 'Alignment', 'stylepress' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'stylepress' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'stylepress' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'stylepress' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'stylepress' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'default' => '',
			]
		);

		$this->add_control(
			'read_more_size',
			[
				'label' => __( 'Size', 'stylepress' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => [
					'xs' => __( 'Extra Small', 'stylepress' ),
					'sm' => __( 'Small', 'stylepress' ),
					'md' => __( 'Medium', 'stylepress' ),
					'lg' => __( 'Large', 'stylepress' ),
					'xl' => __( 'Extra Large', 'stylepress' ),
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'read_more_icon',
			[
				'label' => __( 'Icon', 'stylepress' ),
				'type' => Controls_Manager::ICON,
				'label_block' => true,
				'default' => '',
			]
		);

		$this->add_control(
			'read_more_icon_align',
			[
				'label' => __( 'Icon Position', 'stylepress' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => __( 'Before', 'stylepress' ),
					'right' => __( 'After', 'stylepress' ),
				],
				'condition' => [
					'read_more_icon!' => '',
				],
			]
		);

		$this->add_control(
			'read_more_icon_indent',
			[
				'label' => __( 'Icon Spacing', 'stylepress' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'read_more_icon!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'read_more_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button',
			]
		);

		$this->start_controls_tabs( 'read_more_tabs_button_style' );

		$this->start_controls_tab(
			'read_more_tab_button_normal',
			[
				'label' => __( 'Normal', 'stylepress' ),
			]
		);

		$this->add_control(
			'read_more_button_text_color',
			[
				'label' => __( 'Text Color', 'stylepress' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_button_background_color',
			[
				'label' => __( 'Background Color', 'stylepress' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_4,
				],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'read_more_tab_button_hover',
			[
				'label' => __( 'Hover', 'stylepress' ),
			]
		);

		$this->add_control(
			'read_more_hover_color',
			[
				'label' => __( 'Text Color', 'stylepress' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_button_background_hover_color',
			[
				'label' => __( 'Background Color', 'stylepress' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_button_hover_border_color',
			[
				'label' => __( 'Border Color', 'stylepress' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'read_more_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_hover_animation',
			[
				'label' => __( 'Hover Animation', 'stylepress' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'read_more_border',
				'selector' => '{{WRAPPER}} .elementor-button',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'read_more_border_radius',
			[
				'label' => __( 'Border Radius', 'stylepress' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'read_more_button_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-button',
			]
		);

		$this->add_responsive_control(
			'read_more_text_padding',
			[
				'label' => __( 'Padding', 'stylepress' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'read_more_text_margin',
			[
				'label' => __( 'Margin', 'stylepress' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render our custom menu onto the page.
	 */
	protected function render() {


		$GLOBALS['stylepress_render']['has_done_inner_content'] = true;

		$settings = $this->get_settings_for_display();
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
			'posts_per_page'        => $settings['posts_per_page'],
			'paged'                 => $paged,
			'tax_query'             => $tax_query,
			'orderby'               => $settings['orderby'],
			'order'                 => $settings['order'],   //ASC / DESC
			'ignore_sticky_posts'   => $settings['sticky_ignore'],
			'stylepress_grid_query' => 'yes',
			'stylepress_set_offset' => $settings['offset'],
		);

		$grid_query = new \WP_Query( $args );

		add_filter( 'excerpt_length', function($length) use ( $settings){
			return (int)$settings['meta_exceprt_length'];
		}, 999 );

		add_filter( 'excerpt_more', function($more){
			// Kill the built in exceprt link for our own custom one that always displays.
			return '';
		} );

		$count = 0;
		?>

		<div class="stylepress-grid
		stylepress-grid--<?php echo esc_attr( $settings['display_type'] ); ?>
		stylepress-grid--image-<?php echo esc_attr( $settings['image_style'] ); ?>
			">

			<?php
			if ( $grid_query->have_posts() ) :
				/* Start the Loop */
				?>
				<div class="stylepress-grid__content">
					<?php
					while ( $grid_query->have_posts() ) : $grid_query->the_post();  // Start of posts loop found posts
						$count ++;
						$this_settings = $settings;
						$this_settings['count'] = $count;
						$this_settings['post_count'] = $grid_query->found_posts;
						\StylePress\Templates::get_template_part( 'content', $this_settings['display_type'], 'extensions/post-grid/', $this_settings );
					endwhile; // End of posts loop found posts
					?>
				</div>
				<?php

				if ( $settings['pagination_yes'] == 'yes' ) :  //Start of pagination condition
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
					<div class="stylepress-grid__pagination">
						<nav class="stylepress-grid__pagination-nav">
							<?php echo $pagination; ?>
						</nav>
					</div>
				<?php endif; //end of pagination condition
				?>


			<?php else :   //if no posts found
				?>
				<div class="stylepress-grid__content">
					<?php
					\StylePress\Templates::get_template_part( 'content', 'none', 'extensions/post-grid/' );
					?>
				</div>
			<?php
			endif; //end of post loop ?>

		</div>

		<?php
		wp_reset_postdata();
	}

	public function render_plain_content() {
	}

}

Plugin::instance()->widgets_manager->register_widget_type( new Stylepress_Post_Grid() );