<?php

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

define('STYLEPRESS_MENU_DISPLAY_MEGA',1);
define('STYLEPRESS_MENU_DISPLAY_SLIDEOUT',2);

add_action( 'wp_enqueue_scripts', function(){
	wp_enqueue_style( 'stylepress-nav-menu', DTBAKER_ELEMENTOR_URI . 'extensions/wp-menu/menu.css', false );
	wp_enqueue_script( 'stylepress-nav-menu', DTBAKER_ELEMENTOR_URI . 'extensions/wp-menu/navigation.js', array('jquery') );
} );


add_action('admin_enqueue_scripts', function($hook){
	if($hook == 'nav-menus.php') {
		wp_enqueue_script( 'stylepress-nav-menu-edit', DTBAKER_ELEMENTOR_URI . 'extensions/wp-menu/admin-nav-menu.js' );
		wp_enqueue_style( 'stylepress-nav-menu-edit', DTBAKER_ELEMENTOR_URI . 'extensions/wp-menu/admin-nav-menu.css' );
	}
});

require_once DTBAKER_ELEMENTOR_PATH . 'extensions/wp-menu/widget.wp-menu.php';


require_once DTBAKER_ELEMENTOR_PATH . 'extensions/wp-menu/walker-edit-page.php';
require_once DTBAKER_ELEMENTOR_PATH . 'extensions/wp-menu/walker-display.php';
function stylepress_edit_walker($walker,$menu_id) {
	return 'Walker_Nav_Menu_Edit_StylePress';
}

// change our walker for all menu types if we detect a megamenu or stylepress template dropdown
add_filter( 'wp_nav_menu_args', function( $args ){

    $menu = false;
    if(empty($args['theme_location']) && !empty($args['menu']) && !empty($args['menu']->term_id)){
        $menu = $args['menu'];
    }else if(!empty($args['theme_location'])) {
	    $menu = get_term(get_nav_menu_locations()[$args['theme_location']], 'nav_menu');
    }
    if($menu && is_object($menu) && !empty($menu->term_id)) {
        $items = wp_get_nav_menu_items( $menu->term_id );
        $has_stylepress = false;
        if($items){
            foreach($items as $item){
                if(!empty($item->displaytype)){
                    switch($item->displaytype){
                        case STYLEPRESS_MENU_DISPLAY_SLIDEOUT:
                            if(!empty($item->slideout)){
                                $has_stylepress = true;
                            }
                            break;
                        case STYLEPRESS_MENU_DISPLAY_MEGA:
                            $has_stylepress = true;
                            break;
                    }
                }
            }
        }
        if($has_stylepress){
            $GLOBALS['stylepress_nav_slideouts'] = array();
            $args['walker'] = new \stylepress_walker_nav_menu();
            $args['do_stylepress'] = true;
            $args['container'] = 'div';
            $args['container_class'] .= ' main-nav stylepress_menu';
            $args['items_wrap'] = '<ul id="%1$s" class="%2$s ' . '">%3$s</ul>';
				$args['menu_class'] =  str_replace('sf-menu','',$args['menu_class']);
            add_filter('wp_nav_menu', function( $nav_menu, $args ){
                if(!empty($args->do_stylepress) && !empty($GLOBALS['stylepress_nav_slideouts'])){
                    ob_start();
                    ?>
                    <div class="stylepress-nav-slideouts">
                        <?php
                        foreach($GLOBALS['stylepress_nav_slideouts'] as $template_id => $tf){
                            ?>
                            <div class="stylepress-nav-slideout" data-id="<?php echo $template_id;?>">
                                <?php
                                echo \Elementor\Plugin::instance()->frontend->get_builder_content( $template_id, false );
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    $nav_menu .= ob_get_clean();
                }
                return $nav_menu;
            }, 10, 2);


        }
	}
	return $args;
}, 9999);



// add custom menu fields to menu
add_filter( 'wp_setup_nav_menu_item', 'stylepress_add_custom_nav_fields' );

// save menu custom fields
add_action( 'wp_update_nav_menu_item', 'stylepress_update_custom_nav_fields', 10, 3 );

// edit menu walker
add_filter( 'wp_edit_nav_menu_walker', 'stylepress_edit_walker', 10, 2 );


function stylepress_add_custom_nav_fields( $menu_item ) {
	$menu_item->displaytype = get_post_meta( $menu_item->ID, '_menu_item_displaytype', true );
	$menu_item->slideout = get_post_meta( $menu_item->ID, '_menu_item_slideout', true );

	return $menu_item;

}

/**
 * Save menu custom fields
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
function stylepress_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {

	// todo: do we need to nonce this?

	$custom_fields = array('slideout','displaytype');
	if(isset($_POST['menu-item-db-id']) && is_array($_POST['menu-item-db-id'])){
		foreach($_POST['menu-item-db-id'] as $key => $db_id){
			foreach ($custom_fields as $custom_field) {
				$post_field = 'menu-item-'.$custom_field;
				if($db_id && isset($_POST[$post_field]) && is_array($_POST[$post_field]) && isset($_POST[$post_field][$key])){
					// we've got one ready to save.
					update_post_meta( $db_id, '_menu_item_'.$custom_field, $_POST[$post_field][$key] );
				}
			}
		}
	}

}
