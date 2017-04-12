<?php
/**
 * Our DtbakerElementorManager class.
 * This handles all our hooks and stuff.
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

/**
 * All the magic happens here.
 *
 * Class DtbakerElementorManager
 */
class DtbakerDynamicField {

	/**
	 * Stores our instance that can (and is) accessed from various places.
	 *
	 * @var DtbakerElementorManager null
	 *
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Grab a static instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return DtbakerElementorManager
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function get_replace_fields(){
		$fields = array();
		$fields['permalink'] = 'Post Permalink';
		$fields['post_title'] = 'Post Title';
		$fields['page_title'] = 'Page Title';
		$fields['excerpt'] = 'Post Excerpt';
		// grab a list of all available custom keys.

		if(function_exists('WC')){
			$fields['woocommerce_price'] = 'Product Price';
			$fields['woocommerce_addtocart'] = 'Add To Cart URL';
		}
		foreach($this->get_custom_keys() as $key => $description){

		}
		return $fields;
	}

	public function get_custom_keys(){
		return array();
	}


	public function page_id(){
		if(!empty($GLOBALS['stylepress_post_for_dynamic_fields']) && is_object($GLOBALS['stylepress_post_for_dynamic_fields']) && !empty($GLOBALS['stylepress_post_for_dynamic_fields']->ID)){
			return (int)$GLOBALS['stylepress_post_for_dynamic_fields']->ID;
		}else if(!empty($GLOBALS['stylepress_post_for_dynamic_fields']) && (int)$GLOBALS['stylepress_post_for_dynamic_fields']){
			return (int)$GLOBALS['stylepress_post_for_dynamic_fields'];
		}
		return get_queried_object_id();
	}
	public function get_field($field){
		if(is_callable( array($this,$field))){
			return $this->$field();
		}
		$current_page = $this->page_id();
		if(strpos($field, 'woocommerce') !== false && function_exists('wc_get_product')){
			$_product = wc_get_product( $current_page );
			switch($field){
				case 'woocommerce_price':
					return wc_price($_product->get_price());
				case 'woocommerce_addtocart':
					return $_product->add_to_cart_url();
			}
		}
		// else, search for custom post type.
		if($current_page){
			return get_post_meta($current_page, $field, true);
		}
	}
	public function page_title(){
		return get_the_title( $this->page_id() );
	}
	public function excerpt(){
		return get_the_excerpt( $this->page_id() );
	}
	public function post_title(){
		return get_the_title( $this->page_id() );
	}
	public function product_title(){
		return get_the_title( $this->page_id() );
	}
	public function permalink(){
		return get_the_permalink( $this->page_id() );
	}
	public function post_thumbnail(){
		return get_the_post_thumbnail_url( $this->page_id() );
	}
	public function post_thumbnail_id(){
		return get_post_thumbnail_id( $this->page_id() );
	}
	public function search_query(){
		return esc_html( !empty($_GET['s']) ? $_GET['s'] : '' );
	}


}

