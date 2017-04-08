<?php
namespace Elementor;

use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;
use ElementorPro\Modules\PanelPostsControl\Controls\Group_Control_Posts;
use ElementorPro\Modules\PanelPostsControl\Module;
use Elementor\Controls_Manager;

/**
 * Class Posts
 */
class Stylepress_Loop extends Widget_Base {

	/**
	 * @var \WP_Query
	 */
	private $_query = null;

	protected $_has_template_content = false;

	public function get_name() {
		return 'stylepress-loop';
	}

	public function get_title() {
		return __( 'StylePress Loop', 'stylepress' );
	}

	public function get_icon() {
		return 'dtbaker-stylepress-elementor-widget';
	}

	public function get_categories() {
		return [ 'dtbaker-elementor' ];
	}

	public function get_script_depends() {
		return [ 'imagesloaded' ];
	}


	public function get_query() {
		return $this->_query;
	}

	protected function _register_controls() {
		$this->register_query_section_controls();
	}

	private function register_query_section_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'stylepress' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		// choose stylepress component layout to render in each column.
		$components = \DtbakerElementorManager::get_instance()->get_all_page_components();

		$this->add_control(
			'stylepress_layout',
			[
				'label' => __( 'Choose Layout', 'stylepress' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => $components,
			]
		);


		$this->add_responsive_control(
			'columns',
			[
				'label' => __( 'Columns', 'stylepress' ),
				'type' => Controls_Manager::SELECT,
				'desktop_default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => __( 'Posts Per Page', 'stylepress' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 3,
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'stylepress' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_group_control(
			Group_Control_Posts::get_type(),
			[
				'name' => 'posts',
				'label' => __( 'Posts', 'stylepress' ),
			]
		);

		$this->add_control(
			'advanced',
			[
				'label' => __( 'Advanced', 'stylepress' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __( 'Order By', 'stylepress' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'post_date',
				'options' => [
					'post_date' => __( 'Date', 'stylepress' ),
					'post_title' => __( 'Title', 'stylepress' ),
					'menu_order' => __( 'Menu Order', 'stylepress' ),
					'rand' => __( 'Random', 'stylepress' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __( 'Order', 'stylepress' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc' => __( 'ASC', 'stylepress' ),
					'desc' => __( 'DESC', 'stylepress' ),
				],
			]
		);

		$this->add_control(
			'offset',
			[
				'label' => __( 'Offset', 'stylepress' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0,
				'condition' => [
					'posts_post_type!' => 'by_id',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'filter_bar',
			[
				'label' => __( 'Filter Bar', 'stylepress' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_filter_bar',
			[
				'label' => __( 'Show', 'stylepress' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'stylepress' ),
				'label_on' => __( 'On', 'stylepress' ),
			]
		);

		$this->add_control(
			'taxonomy',
			[
				'label' => __( 'Taxonomy', 'stylepress' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => [],
				'options' => $this->get_taxonomies(),
				'condition' => [
					'show_filter_bar' => 'yes',
					'posts_post_type!' => 'by_id',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_layout',
			[
				'label' => __( 'Items', 'stylepress' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_gap',
			[
				'label' => __( 'Item Gap', 'stylepress' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-loop' => 'margin: 0 -{{SIZE}}px',
					'(desktop){{WRAPPER}} .stylepress-loop-item' => 'width: calc( 100% / {{columns.SIZE}} ); border: {{SIZE}}px solid transparent',
					'(tablet){{WRAPPER}} .stylepress-loop-item' => 'width: calc( 100% / {{columns_tablet.SIZE}} ); border: {{SIZE}}px solid transparent',
					'(mobile){{WRAPPER}} .stylepress-loop-item' => 'width: calc( 100% / {{columns_mobile.SIZE}} ); border: {{SIZE}}px solid transparent',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'stylepress' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .stylepress-loop-item__img, {{WRAPPER}} .stylepress-loop-item__overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_overlay',
			[
				'label' => __( 'Item Overlay', 'stylepress' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color_background',
			[
				'label' => __( 'Background Color', 'stylepress' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_4,
				],
				'selectors' => [
					'{{WRAPPER}} a .stylepress-loop-item__overlay' => 'background-color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_filter',
			[
				'label' => __( 'Filter Bar', 'stylepress' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_filter_bar' => 'yes',
				],
			]
		);

		$this->add_control(
			'color_filter',
			[
				'label' => __( 'Color', 'stylepress' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-loop__filter' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'color_filter_active',
			[
				'label' => __( 'Active Color', 'stylepress' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-loop__filter.elementor-active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_filter',
				'label' => __( 'Typography', 'stylepress' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .stylepress-loop__filter',
			]
		);

		$this->add_control(
			'filter_item_spacing',
			[
				'label' => __( 'Space Between', 'stylepress' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-loop__filter:not(:last-child)' => 'margin-right: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .stylepress-loop__filter:not(:first-child)' => 'margin-left: calc({{SIZE}}{{UNIT}}/2)',
				],
			]
		);

		$this->add_control(
			'filter_spacing',
			[
				'label' => __( 'Spacing', 'stylepress' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .stylepress-loop__filters' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function get_taxonomies() {
		$taxonomies = get_taxonomies( [ 'show_in_nav_menus' => true ], 'objects' );

		$options = [ '' => '' ];

		foreach ( $taxonomies as $taxonomy ) {
			$options[ $taxonomy->name ] = $taxonomy->label;
		}

		return $options;
	}

	protected function get_posts_tags() {
		$taxonomy = $this->get_settings( 'taxonomy' );

		foreach ( $this->_query->posts as $post ) {
			if ( ! $taxonomy ) {
				$post->tags = [];

				continue;
			}

			$tags = wp_get_post_terms( $post->ID, $taxonomy );

			$tags_slugs = [];

			foreach ( $tags as $tag ) {
				$tags_slugs[ $tag->term_id ] = $tag;
			}

			$post->tags = $tags_slugs;
		}
	}

	public function query_posts() {
		$query_args = Module::get_query_args( 'posts', $this->get_settings() );

		$query_args['posts_per_page'] = $this->get_settings( 'posts_per_page' );

		$this->_query = new \WP_Query( $query_args );
	}

	public $layout_template = false;

	public function render() {
		$this->query_posts();

		$wp_query = $this->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		wp_enqueue_style('stylepress-loop');


		$settings = $this->get_settings();
		if(!empty($settings['stylepress_layout'])) {

		    $this->layout_template = $settings['stylepress_layout'];

			$this->get_posts_tags();

			$this->render_loop_header();

			while ( $wp_query->have_posts() ) {
				$wp_query->the_post();

				$this->render_post();
			}
		}

		$this->render_loop_footer();

		wp_reset_postdata();
	}

	protected function render_thumbnail() {
		$settings = $this->get_settings();

		$settings['thumbnail_size'] = [
			'id' => get_post_thumbnail_id(),
		];

		$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail_size' );
		?>
        <div class="stylepress-loop-item__img elementor-post__thumbnail">
			<?php echo $thumbnail_html ?>
        </div>
		<?php
	}

	protected function get_portfolio_js_options() {
		$settings = $this->get_settings();

		$options = [
			'itemGap' => $settings['item_gap']['size'],
			'columns' => $settings['columns'],
			'columns_tablet' => $settings['columns_tablet'],
			'columns_mobile' => $settings['columns_mobile'],
		];

		return $options;
	}

	protected function render_filter_menu() {
		$taxonomy = $this->get_settings( 'taxonomy' );

		if ( ! $taxonomy ) {
			return;
		}

		$terms = [];

		foreach ( $this->_query->posts as $post ) {
			$terms += $post->tags;
		}

		if ( empty( $terms ) ) {
			return;
		}
		?>
        <ul class="stylepress-loop__filters">
            <li class="stylepress-loop__filter elementor-active" data-filter="__all"><?php echo __( 'All', 'stylepress' ); ?></li>
			<?php foreach ( $terms as $term ) { ?>
                <li class="stylepress-loop__filter" data-filter="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></li>
			<?php } ?>
        </ul>
		<?php
	}

	protected function render_title() {

		$tag = $this->get_settings( 'title_tag' );
		?>
        <<?php echo $tag ?> class="stylepress-loop-item__title">
		<?php the_title() ?>
        </<?php echo $tag ?>>
		<?php
	}

	protected function render_categories_names() {
		global $post;

		if ( ! $post->tags ) {
			return;
		}

		$separator = '<span class="stylepress-loop-item__tags__separator"></span>';

		$tags_array = [];

		foreach ( $post->tags as $tag ) {
			$tags_array[] = '<span class="stylepress-loop-item__tags__tag">' . $tag->name . '</span>';
		}

		?>
        <div class="stylepress-loop-item__tags">
			<?php echo implode( $separator, $tags_array ); ?>
        </div>
		<?php
	}

	protected function render_post_header() {
		global $post;

		$classes = [];

		foreach ( $post->tags as $tag ) {
			$classes[] = 'elementor-filter-' . $tag->term_id;
		}
		?>
        <article class="stylepress-loop-item <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php
	}

	protected function render_post_footer() {
		?>
        </article>
		<?php
	}

	protected function render_overlay_header() {
		?>
        <div class="stylepress-loop-item__overlay">
		<?php
	}

	protected function render_overlay_footer() {
		?>
        </div>
		<?php
	}

	protected function render_loop_header() {
		if ( $this->get_settings( 'show_filter_bar' ) ) {
			$this->render_filter_menu();
		}
		?>
        <div class="stylepress-loop elementor-posts-container" data-stylepress-options="<?php echo esc_attr( wp_json_encode( $this->get_portfolio_js_options() ) ); ?>">
		<?php
	}

	protected function render_loop_footer() {
		?>
        </div>
		<?php
	}

	protected function render_post() {

		$this->render_post_header();


		// allow developers to overwrite the default output with their own
		if($result = apply_filters('stylepress_loop_item','',$this)){
		    echo $result;
        }else{
            // render with our builder:

			    global $post;
				$GLOBALS['stylepress_post_for_dynamic_fields']                    = $post;
				$GLOBALS['stylepress_template_turtles'][ $this->layout_template ] = $this->layout_template;

			    echo Plugin::instance()->frontend->get_builder_content( $this->layout_template, true );

//				$content = Plugin::instance()->frontend->get_builder_content_for_display( $this->layout_template );

        }


		$this->render_post_footer();
	}
}


Plugin::instance()->widgets_manager->register_widget_type( new Stylepress_Loop() );