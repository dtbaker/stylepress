<?php

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


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



// add custom menu fields to menu
add_filter( 'wp_setup_nav_menu_item', 'stylepress_add_custom_nav_fields' );

// save menu custom fields
add_action( 'wp_update_nav_menu_item', 'stylepress_update_custom_nav_fields', 10, 3 );

// edit menu walker
add_filter( 'wp_edit_nav_menu_walker', 'stylepress_edit_walker', 10, 2 );


function stylepress_add_custom_nav_fields( $menu_item ) {
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

	$custom_fields = array('slideout');
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
