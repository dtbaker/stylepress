<?php

class stylepress_walker_nav_menu extends Walker_Nav_Menu {

	// add classes to ul sub-menus
	function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
		$id_field = $this->db_fields['id'];
		if ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
		}

		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	public static $current_element = false;

	function start_lvl( &$output, $depth = 0, $args = array() ) {

//		$output .= "\n\n\n <!-- start level $depth : ".self::$current_element."  --> \n\n\n";
		if ( $depth == 0 ) {
			// our first drop down after the parent <a>
			// we save our opening <ul> for the child entry.
			if ( self::$current_element ) {
//				$slideout = get_post_meta( self::$current_element, '_menu_item_slideout', true );
//				if ( (int) $slideout > 0 ) {
//					$output .= '<div class="stylepress-second-level"><div class="inner">';
//					$output .= \Elementor\Plugin::instance()->frontend->get_builder_content( $slideout, false );
//					$output .= '</div></div>';
//				}
			}
		}
		// then start normal non-mobile menu:
		$output .= "\n<ul>\n";
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= "\n</ul>\n";
		$output .= "\n\n\n <!-- end level $depth  --> \n\n\n";
	}

	// add main/sub classes to li's and links
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		global $stylepress_options_proya;


//		$output .= "\n\n\n <!-- start ITEM $item->ID --> \n\n\n";
		self::$current_element = $item->ID;
		$sub = "";

		$button = '';
		if ( $depth == 0 && $args->has_children ) :
			$sub    = ' has_kids';
			$button = '<span role="button" class="dropdown-menu-toggle" aria-expanded="false"></span>';
		endif;
		if ( $depth == 1 && $args->has_children ) :
			$sub = 'is_kid';
		endif;

		// passed classes
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;


		$data_attr = '';
		if($item->ID){
			$display_type = get_post_meta( $item->ID, '_menu_item_displaytype', true );
			if($display_type){
				switch($display_type){
					case STYLEPRESS_MENU_DISPLAY_MEGA:
						$classes[] = 'stylepress_megamenu';
						break;
					case STYLEPRESS_MENU_DISPLAY_SLIDEOUT:
						$slideout = get_post_meta( $item->ID, '_menu_item_slideout', true );
						if ( (int) $slideout > 0 ) {
							$data_attr .= ' data-stylepressslideout="' . (int)$slideout . '"';
							$GLOBALS['stylepress_nav_slideouts'][(int)$slideout] = true;
							$classes[] = 'stylepress_has_navslide';
						}
						break;
				}
			}
		}

		$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

		$args->link_before = empty( $args->link_before ) ? '' : $args->link_before;
		$args->link_after  = empty( $args->link_after ) ? '' : $args->link_after;

		$item_output = sprintf(
			'<li class="%s"%s><a href="%s">%s%s%s%s</a>',
			$class_names,
			$data_attr,
			esc_attr( $item->url ),
			$args->link_before,
			apply_filters( 'the_title', $item->title, $item->ID ),
			$args->link_after,
			$button
		);


		// build html
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

}