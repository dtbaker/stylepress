<?php
/**
 * WordPress Nav Menu Widget
 *
 * @package dtbaker-elementor
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}


/**
 * Creates our custom Elementor widget
 *
 * Class Widget_Dtbaker_WP_Menu
 *
 * @package Elementor
 */
class Widget_Dtbaker_WP_Menu extends Widget_Base {


	/**
	 * Get Widgets name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'dtbaker_wp_menu';
	}

	/**
	 * Get widgets title
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'WordPress Menu', 'stylepress' );
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

	/**
	 * This registers our controls for the widget. Currently there are none but we may add options down the track.
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_dtbaker_wp_menu',
			[
				'label' => __( 'WordPress Menu', 'stylepress' ),
			]
		);

		$this->add_control(
			'desc',
			[
				'label' => sprintf( __( 'Choose the WordPress menu to output below. To change menu items please go to the <a href="%s" target="_blank">WordPress Menu Editor</a> page.', 'stylepress' ), admin_url( 'nav-menus.php' ) ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);

		if ( false && ! function_exists( 'max_mega_menu_is_enabled' ) ) {

			$this->add_control(
				'megamenu',
				[
					// Translators: %s is the URL for MegaMenu plugin
					'label' => sprintf( __( 'We recommend installing the <a href="%s" target="_blank">Max Mega Menu</a> plugin to get an awesome menu layout.', 'stylepress' ), 'https://wordpress.org/plugins/megamenu/' ),
					'type' => Controls_Manager::RAW_HTML,
				]
			);

		}

		$menu_select = array(
			'' => esc_html__( ' - choose - ', 'stylepress' ),
		);

		if ( function_exists( 'max_mega_menu_is_enabled' ) ) {
			$menus = get_registered_nav_menus();
			foreach ( $menus as $location => $description ) {
				$menu_select[ $location ] = $description;
			}
		}
		// we also show a list of users menues.
		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu ){
		    $menu_select[$menu->term_id] = $menu->name;
        }


		$this->add_control(
			'menu_location',
			[
				'label'   => esc_html__( 'Choose Menu', 'stylepress' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $menu_select,
			]
		);

		/*
        if(function_exists('max_mega_menu_is_enabled') && class_exists('Mega_Menu_Style_Manager')){

            $style_manager = new \Mega_Menu_Style_Manager();
            $themes = $style_manager->get_themes();

            $menu_styles = array(
                '' => esc_html__( 'Default' ),
            );
            foreach($themes as $theme_id => $theme){
                $menu_styles[$theme_id] = $theme['title'];
            }

            $this->add_control(
                'menu_style',
                [
                    'label' => __( 'Menu Menu Style', 'elementor' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => '',
                    'options' => $menu_styles,
                ]
            );

        }*/

		$this->end_controls_section();

		$this->start_controls_section(
			'section_stylepress_menu_style',
			[
				'label' => __( 'Menu Style', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'menu_align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'prefix_class' => 'elementor-align-',
				'selectors' => [
					'{{WRAPPER}} .stylepress-main-navigation' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_background',
			[
				'label' => __( 'Background', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f8f8f8',
				'selectors' => [
					'{{WRAPPER}} .stylepress-main-navigation, {{WRAPPER}} .stylepress-main-navigation .stylepress-inside-navigation ul ul' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'menu_background_hover',
			[
				'label' => __( 'Background (hover)', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#eaeaea',
				'selectors' => [
					'{{WRAPPER}} .stylepress-main-navigation .stylepress-inside-navigation ul li:hover a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'font_color',
			[
				'label' => __( 'Font Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .stylepress-main-navigation .stylepress-menu-toggle, {{WRAPPER}} .stylepress-main-navigation .stylepress-inside-navigation ul li a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'font_color_hover',
			[
				'label' => __( 'Font Color (Hover)', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .stylepress-main-navigation .stylepress-inside-navigation ul li a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();


		do_action( 'dtbaker_wp_menu_elementor_controls', $this );

	}

	/**
	 * Render our custom menu onto the page.
	 */
	protected function render() {
		$settings = $this->get_settings();

		if ( ! empty( $settings['menu_location'] ) ) {

			/*
            if(function_exists('max_mega_menu_is_enabled') && !empty($settings['menu_style'])) {

                // $menu_styles
                add_filter('option_megamenu_settings', function ($value, $option) use ($settings) {

                    if($value && !empty($value[$settings['menu_location']])){
                        $value[$settings['menu_location']]['theme'] = $settings['menu_style'];
                    }

                    return $value;
                }, 10, 2);
            }*/

			// if the menu is a "location" then we

			if ( function_exists('max_mega_menu_is_enabled') && max_mega_menu_is_enabled($settings['menu_location']) ){
				wp_nav_menu( array( 'theme_location' => $settings['menu_location'] ) );
            }else{
			    ob_start();
			    ?>
                <nav itemtype="http://schema.org/SiteNavigationElement" itemscope="itemscope" class="stylepress-main-navigation">
                    <button class="stylepress-menu-toggle" aria-controls="<?php echo $this->get_id();?>-menu" aria-expanded="false">
                        <span class="stylepress-mobile-menu"><?php esc_html_e('Menu','stylepress');?></span>
                    </button>
                    <div id="<?php echo $this->get_id();?>-menu" class="stylepress-inside-navigation">
						<?php

                        if(is_numeric($settings['menu_location'])){
	                        $nav_menu = wp_get_nav_menu_object( $settings['menu_location'] );
	                        if ( $nav_menu ){
		                        wp_nav_menu( array(
			                        'menu'        => $nav_menu,
			                        'fallback_cb' => '',
			                        'container'       => 'div',
			                        'container_class' => 'main-nav',
			                        'container_id'    => 'primary-menu',
			                        'menu_class'      => '',
			                        'items_wrap'      => '<ul id="%1$s" class="%2$s ' . '">%3$s</ul>',
		                        ) );
                            }else{
	                            echo "Menu Configuration Issue";
                            }
                        }else {
	                        wp_nav_menu(
		                        array(
			                        'theme_location'  => $settings['menu_location'],
			                        'container'       => 'div',
			                        'container_class' => 'main-nav',
			                        'container_id'    => 'primary-menu',
			                        'menu_class'      => '',
			                        'items_wrap'      => '<ul id="%1$s" class="%2$s ' . '">%3$s</ul>'
		                        )
	                        );
                        }
						?>
                    </div><!-- .inside-navigation -->
                </nav><!-- #site-navigation -->
                <?php
                echo apply_filters('stylepress_menu_output', ob_get_clean(), $settings['menu_location'], $settings );
            }

		} else {
			$this->content_template();
		}

	}

	/**
	 * This is outputted while rending the page.
	 */
	protected function content_template() {
		?>
		<div class="dtbaker-wp-menu-content-area">
		WordPress Menu Will Appear Here
		</div>
		<?php
	}

}

Plugin::instance()->widgets_manager->register_widget_type( new Widget_Dtbaker_WP_Menu() );