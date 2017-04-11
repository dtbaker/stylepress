<?php

class stylepress_walker_nav_menu extends Walker_Nav_Menu {

	// add classes to ul sub-menus
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output )
	{
		$id_field = $this->db_fields['id'];
		if ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
		}
		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {

		$indent = str_repeat("\t", $depth);
		if($depth == 0){
			$out_div = '<div class="second"><div class="inner">';
		}else{
			$out_div = '';
		}

		// build html
		$output .= "\n" . $indent . $out_div  .'<ul>' . "\n";
	}
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);

		if($depth == 0){
			$out_div_close = '</div></div>';
		}else{
			$out_div_close = '';
		}

		$output .= "$indent</ul>". $out_div_close ."\n";
	}

	// add main/sub classes to li's and links
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		global $stylepress_options_proya;
		$sub = "";
		$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
		if($depth==0 && $args->has_children) :
			$sub = ' has_sub';
		endif;
		if($depth==1 && $args->has_children) :
			$sub = 'sub';
		endif;

		// passed classes
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

		$wide_background = '';
		if(isset($stylepress_options_proya['enable_wide_manu_background']) && $stylepress_options_proya['enable_wide_manu_background'] == "yes" ){
			$wide_background = 'true';
		}

		//menu type class
		$menu_type = "";
		if($depth==0){
			if($item->type_menu == "wide"){
				$menu_type = " wide";

				if($wide_background == 'true'){
					$menu_type = " wide wide_background";
				}
			}elseif($item->type_menu == "wide_icons"){
				$menu_type = " wide icons";
				if($wide_background == 'true'){
					$menu_type = " wide icons wide_background";
				}
			}else{
				$menu_type = " narrow";
			}
		}

		//wide menu position class
		$wide_menu_position = "";
		if($depth==0){
			if($item->wide_position == "right"){
				$wide_menu_position = " right_position";
			}elseif($item->wide_position == "left"){
				$wide_menu_position = " left_position";
			}else{
				$wide_menu_position = "";
			}
		}

		//custom widget area in popup class
		$show_widget_area_in_popup = "";
		$show_in_popup = false;
		if($depth > 0 && $item->show_widget_area_in_popup == "show_widget_area_in_popup"){
			$show_widget_area_in_popup = ' show_widget_area_in_popup';
			$show_in_popup = true;
		}

		$anchor = '';
		if($item->anchor != ""){
			$anchor = '#'.esc_attr($item->anchor);
			$class_names .= ' anchor-item';
		}

		$active = "";
		// depth dependent classes
		if ($item->anchor == "" && (($item->current && $depth == 0) ||  ($item->current_item_ancestor && $depth == 0))):
			$active = 'active';
		endif;

		// build html
		$output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $class_names . ' ' . $active . $sub . $menu_type . $wide_menu_position . $show_widget_area_in_popup .'">';



		$current_a = "";
		// link attributes
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ' href="'   . esc_attr( $item->url        ) .$anchor.'"';
		if (($item->current && $depth == 0) ||  ($item->current_item_ancestor && $depth == 0) ):
			$current_a .= ' current ';
		endif;

		$no_link_class = '';
		if($item->nolink != '') {
			$no_link_class = ' no_link';
		}

		$attributes .= ' class="'.$current_a.$no_link_class.'"';
		$item_output = $args->before;
		if($show_in_popup){
			$item_output .= '<div class="popup_wrapper">';
		}
		if($item->hide == ""){
			if($item->nolink == ""){
				$item_output .= '<a'. $attributes .'>';
			} else{
				$item_output .= '<a'. $attributes .' style="cursor: default;" onclick="JavaScript: return false;">';
			}

			$icon = !empty($item->icon) && $item->icon !== 'null' ? $item->icon : 'blank';

			$angle_icon   = '';
			if($depth == 1) {
				$angle_icon = 'fa-angle-right';
			}

			$icon_pack = 'font_awesome';

			if(empty($this->icon_pack)) {
				$item->icon_pack = $icon_pack;
			}

			if($item->icon_pack == 'font_awesome') {
				$icon .= ' fa';
			}

			$item_output .= '<i class="menu_icon '.$icon.'"></i>';
			$item_output .= '<span>'.apply_filters( 'the_title', $item->title, $item->ID );

			if($depth==0 && isset($stylepress_options_proya['menu_underline_dash']) && $stylepress_options_proya['menu_underline_dash'] == "yes"){
				$item_output .= '<span class="underline_dash"></span>';
			}

			$item_output .= '</span><span class="plus"></span>';

			//append arrow for dropdown
			if($args->has_children && $angle_icon != "") {
				$item_output .= '<i class="q_menu_arrow fa '.$angle_icon.'"></i>';
			}

			$item_output .= '</a>';
		}

		if($item->sidebar != "" && $depth > 0){
			ob_start();
			dynamic_sidebar($item->sidebar);
			$sidebar_content = ob_get_contents();
			ob_end_clean();
			$item_output .= $sidebar_content;
		}


		if($show_in_popup){
			$item_output .= '</div>';
		}

		$item_output .= $args->after;

		// build html
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

/* Custom WP_NAV_MENU function for mobile navigation */

class stylepress_type2_walker_nav_menu extends Walker_Nav_Menu {

	// add classes to ul sub-menus
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output )
	{
		$id_field = $this->db_fields['id'];
		if ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
		}
		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {

		$indent = str_repeat("\t", $depth);

		// build html
		$output .= "\n" . $indent  .'<ul class="sub_menu">' . "\n";
	}
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);

		$output .= "$indent</ul>" ."\n";
	}

	// add main/sub classes to li's and links
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		global $stylepress_options_proya;
		$sub = "";
		$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
		if($depth >=0 && $args->has_children) :
			$sub = ' has_sub';
		endif;

		// passed classes
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

		$anchor = '';
		if($item->anchor != ""){
			$anchor = '#'.esc_attr($item->anchor);
		}

		$active = "";
		// depth dependent classes
		if ($item->anchor == "" && (($item->current && $depth == 0) ||  ($item->current_item_ancestor && $depth == 0))):
			$active = 'active';
		endif;

		// build html
		$output .= $indent . '<li id="mobile-menu-item-'. $item->ID . '" class="' . $class_names . ' ' . $active . $sub .'">';

		$current_a = "";
		// link attributes
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ' href="'   . esc_attr( $item->url        ) .$anchor.'"';
		if (($item->current && $depth == 0) ||  ($item->current_item_ancestor && $depth == 0) ):
			$current_a .= ' current ';
		endif;

		$attributes .= ' class="'. $current_a . '"';
		$item_output = $args->before;
		if($item->hide == ""){
			if($item->nolink == ""){
				$item_output .= '<a'. $attributes .'>';
			}else{
				$item_output .= '<h3>';
			}
			$item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
			$item_output .= $args->link_after;
			if($item->nolink == ""){
				$item_output .= '</a><span class="mobile_arrow"><i class="fa fa-angle-right"></i><i class="fa fa-angle-down"></i></span>';
			}else{
				$item_output .= '</h3><span class="mobile_arrow"><i class="fa fa-angle-right"></i><i class="fa fa-angle-down"></i></span>';
			}
		}
		$item_output .= $args->after;

		// build html
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

}

/* Custom WP_NAV_MENU function for popup navigation */

class stylepress_type3_walker_nav_menu extends Walker_Nav_Menu {

	// add classes to ul sub-menus
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ){
		$id_field = $this->db_fields['id'];
		if ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
		}
		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);

		// build html
		$output .= "\n" . $indent  .'<ul class="sub_menu">' . "\n";
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);

		$output .= "$indent</ul>" ."\n";
	}

	// add main/sub classes to li's and links
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		global $stylepress_options_theme16;
		$sub = "";
		$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
		if($depth >=0 && $args->has_children) :
			$sub = ' has_sub';
		endif;


		// passed classes
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

		$anchor = '';
		if($item->anchor != ""){
			$anchor = '#'.esc_attr($item->anchor);
		}

		$active = "";
		// depth dependent classes
		if ($item->anchor == "" && (($item->current && $depth == 0) ||  ($item->current_item_ancestor && $depth == 0))):
			$active = 'active';
		endif;

		// build html
		$output .= $indent . '<li id="popup-menu-item-'. $item->ID . '" class="' . $class_names . ' ' . $active . $sub .'">';

		$current_a = "";
		// link attributes
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ' href="'   . esc_attr( $item->url        ) .$anchor.'"';
		if (($item->current && $depth == 0) ||  ($item->current_item_ancestor && $depth == 0) ):
			$current_a .= ' current ';
		endif;

		$attributes .= ' class="'. $current_a . '"';
		$item_output = $args->before;
		if($item->hide == ""){
			if($item->nolink == ""){
				$item_output .= '<a'. $attributes .'>';
			}else{
				$item_output .= '<h6>';
			}
			$item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
			$item_output .= $args->link_after;
			if($item->nolink == ""){
				$item_output .= '</a>';
			}else{
				$item_output .= '</h6>';
			}
		}
		$item_output .= $args->after;

		// build html
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

}


/* Custom WP_NAV_MENU function for left/right navigation */

class stylepress_type4_walker_nav_menu extends Walker_Nav_Menu {

	// add classes to ul sub-menus
	function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
		$id_field = $this->db_fields['id'];
		if ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
		}

		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {

		$indent = str_repeat( "\t", $depth );

		// build html
		$output .= "\n" . $indent . '<ul class="sub_menu">' . "\n";
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );

		$output .= "$indent</ul>" . "\n";
	}

	// add main/sub classes to li's and links
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		global $stylepress_options_proya;
		$sub    = "";
		$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
		if ( $depth >= 0 && $args->has_children ) :
			$sub = ' has_sub';
		endif;

		// passed classes
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

		$anchor = '';
		if ( $item->anchor != "" ) {
			$anchor = '#' . esc_attr( $item->anchor );
		}

		$active = "";
		// depth dependent classes
		if ( $item->anchor == "" && ( ( $item->current && $depth == 0 ) || ( $item->current_item_ancestor && $depth == 0 ) ) ):
			$active = 'active';
		endif;

		// build html
		$output .= $indent . '<li id="mobile-menu-item-' . $item->ID . '" class="' . $class_names . ' ' . $active . $sub . '">';

		$current_a = "";
		// link attributes
		$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
		$attributes .= ' href="' . esc_attr( $item->url ) . $anchor . '"';
		if ( ( $item->current && $depth == 0 ) || ( $item->current_item_ancestor && $depth == 0 ) ):
			$current_a .= ' current ';
		endif;

		$attributes  .= ' class="' . $current_a . '"';
		$item_output = $args->before;
		if ( $item->hide == "" ) {
			if ( $item->nolink == "" ) {
				$item_output .= '<a' . $attributes . '>';
			} else {
				$item_output .= '<h3>';
			}
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID );
			$item_output .= $args->link_after;
			if ( $item->nolink == "" ) {
				$item_output .= '</a><span class="mobile_arrow"><i class="fa fa-angle-right"></i><i class="fa fa-angle-down"></i></span>';
			} else {
				$item_output .= '</h3><span class="mobile_arrow"><i class="fa fa-angle-right"></i><i class="fa fa-angle-down"></i></span>';
			}
		}
		$item_output .= $args->after;

		// build html
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

}
