<?php


/**
 * Class dtbaker_Widget_Google_Map and dtbaker_Shortcode_Google_Map
 * Easily create a Google Map on any WordPress post/page (with an insert map button).
 * Easily create a Google Map in any Widget Area.
 * Author: dtbaker@gmail.com
 * Copyright 2014
 */


defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;


add_action( 'wp_enqueue_scripts', function(){


	// ?libraries=places
	wp_register_script('googlemaps', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr(get_option('google_maps_api_key','AIzaSyBsnYWO4SSibatp0SjsU9D2aZ6urI-_cJ8')) . '&sensor=false', false, '3');

	wp_register_style( 'stylepress-google-map', DTBAKER_ELEMENTOR_URI . 'extensions/google-maps/google-maps.css' );

	if( isset($_GET['elementor']) || isset($_GET['elementor-preview'])) { //\Elementor\Plugin::$instance->editor->is_edit_mode()){
		wp_enqueue_script('googlemaps');
		wp_enqueue_style('stylepress-google-map');
	}

} );


$widget_file   = DTBAKER_ELEMENTOR_PATH . 'extensions/google-maps/widget.google-map.php';
//$template_file = locate_template( $widget_file );
//if ( $template_file && is_readable( $template_file ) ) {
require_once $widget_file;


add_action( 'customize_register', 'stylepress_register_google_maps_customize_control' );

function stylepress_register_google_maps_customize_control() {
	class stylepress_Google_Maps_Custom_Text_Control extends WP_Customize_Control {
		public $type = 'google_maps_customtext';
		public $extra = ''; // we add this for the extra description

		public function render_content() {
			?>
			<p>Go to <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Google Maps API Page</a> and scroll down to the "Get a Key" button. Click this and follow the prompts. More details and step by step instructions are available in the theme documentation.</p>
			<?php
		}
	}
}

class stylepress_dtbaker_Widget_Google_Map extends WP_Widget{
	/** constructor */
	function __construct() {
		$widget_ops = array(
			'description' => __('Use this to display a Google Map in a Widget Area.', 'plugin-textdomain-here')
		);
		parent::__construct(false, __('Google Map', 'plugin-textdomain-here'), $widget_ops );
	}
	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		extract( $args );
		$title = isset($instance['title']) ? $instance['title'] : '';
		echo $before_widget;
		echo $title ? ($before_title . $title . $after_title) : '';
		// fire our shortcode below to generate map output.
		$shortcode = dtbaker_Shortcode_Google_Map::get_instance();
		echo $shortcode->dtbaker_shortcode_gmap($instance, isset($instance['innercontent']) ? $instance['innercontent'] : '');
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		return array_merge($old_instance, $new_instance);
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr(isset($instance['title']) ? $instance['title'] : '');
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'plugin-textdomain-here'); ?>
				<input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title);?>">
			</label></p>
		<?php
		// pull the same fields in from our mce popup below:
		$shortcode = dtbaker_Shortcode_Google_Map::get_instance();
		foreach($shortcode->fields as $field){
			?>
			<p><label for="<?php echo $this->get_field_id($field['name']); ?>"><?php echo $field['label']; ?>
					<?php switch($field['mce_type']){
						case 'listbox':
							$current_val = isset($instance[$field['name']])?$instance[$field['name']]:$field['default'];
							?>
							<select name="<?php echo $this->get_field_name($field['name']); ?>">
								<?php foreach($field['values'] as $key=>$val){ ?>
									<option value="<?php echo esc_attr($key);?>"<?php echo $current_val == $key ? ' selected':'';?>><?php echo $val;?></option>
								<?php } ?>
							</select>
							<?php
							break;
						case 'textbox':
							?>
							<input type="text" name="<?php echo $this->get_field_name($field['name']); ?>" value="<?php echo esc_attr(isset($instance[$field['name']])?$instance[$field['name']]:$field['default']);?>">
							<?php
							break;
					} ?>
				</label></p>
			<?php
		}
	}
}


class stylepress_dtbaker_Shortcode_Google_Map{
	private static $instance = null;
	public static function get_instance() {
		if ( ! self::$instance )
			self::$instance = new self;
		return self::$instance;
	}

	public function init(){
		// comment this 'add_action' out to disable shortcode backend mce view feature
		add_action( 'admin_init', array( $this, 'init_plugin' ), 20 );
		add_shortcode('stylepress_google_map', array($this,'dtbaker_shortcode_gmap'));
		add_action('widgets_init', create_function('', 'return register_widget("stylepress_dtbaker_Widget_Google_Map");'));

		add_action( 'customize_register', array( $this, 'customize_register' ), 30 );

	}



	public function customize_register( $wp_customize ){


		$wp_customize->add_section('dtbaker_google_map', array(
			'title'    => __('Google Map'),
			'description' => '',
			'priority' => 120,
		));
		$wp_customize->add_setting( 'google_maps_api_key', array(
			'default'    => '',
			'capability' => 'edit_theme_options',
			'type'       => 'theme_mod',

		) );
		$wp_customize->add_control( 'google_maps_api_key', array(
			'settings' => 'google_maps_api_key',
			'label'    => 'Google Maps API Key',
			'section'  => 'dtbaker_google_map',
			'type'     => 'text',
		) );

		if(class_exists('stylepress_Google_Maps_Custom_Text_Control')) {
			$wp_customize->add_setting('google_maps_api_key_description', array(
					'default' => '',
					'type' => 'google_maps_customtext',
					'capability' => 'edit_theme_options',
					'transport' => 'refresh',
				)
			);

			$wp_customize->add_control( new stylepress_Google_Maps_Custom_Text_Control( $wp_customize, 'google_maps_customtext', array(
					'section' => 'dtbaker_google_map',
					'settings' => 'google_maps_api_key_description',
					'extra'   => 'Here is my extra description text ...'
				) )
			);
		}

	}

	public function init_plugin() {
	}
	// front end shortcode displaying:
	public function dtbaker_shortcode_gmap($atts=array(), $innercontent='', $code='') {
//		if(!isset($atts['address']))return;
		static $map_id=0;
		$map_id++;
		$defaults = array();
		foreach($this->fields as $field){
			$defaults[$field['name']] = $field['default'];
		}
		extract(shortcode_atts($defaults, $atts));
		ob_start();
		$template_file = locate_template('google_map.php');

		wp_enqueue_script('googlemaps');
		wp_enqueue_style('stylepress-google-map');

		if(!$template_file) {
			?>
			<div id="googlemap<?php echo (int)$map_id; ?>" class="googlemap" style="height:<?php echo (int)$height; ?>px;"></div>
			<div class="clear"></div>
			<?php if ( !empty($enlarge_button) ) { ?>
				<div class="stylepress_map_buttons">
					<a href="http://maps.google.com/maps?q=<?php echo urlencode( esc_html($address) ); ?>" class="dtbaker_button"
					   target="_blank"><?php _e( 'Enlarge Map', 'plugin-textdomain-here'); ?></a>
					<a href="https://maps.google.com?daddr=<?php echo urlencode( esc_html($address) ); ?>" class="dtbaker_button"
					   target="_blank"><?php _e( 'Get Directions', 'plugin-textdomain-here'); ?></a>
				</div>
			<?php } ?>
			<?php if ( $map_id == 1 ) {

			} ?>
			<script type="text/javascript">
                (function ($) {
                    var geocoder;
                    var map;
                    var query = "<?php echo esc_js($address,'"');?>";
                    function initialize() {
                        if(typeof google == 'undefined')return;
                        geocoder = new google.maps.Geocoder();
                        var myOptions = {
                            zoom: <?php echo (int)$zoom;?>,
                            scrollwheel: false,
                            styles: <?php echo $this->get_map_styles('js', $atts);?>,
                            disableDefaultUI: true,
							/*controls: {
							 map_type: {
							 type: ['roadmap', 'satellite', 'hybrid'],
							 position: 'top_right',
							 style: 'dropdown_menu'
							 },
							 overview: {opened: false},
							 pan: false,
							 rotate: false,
							 scale: false,
							 street_view: {position: 'top_right'},
							 zoom: {
							 position: 'top_left',
							 style: 'large'
							 }
							 },*/
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        };
                        map = new google.maps.Map(document.getElementById("googlemap<?php echo (int)$map_id;?>"), myOptions);
                        codeAddress();
                    }

                    function codeAddress() {
                        var address = query;
                        geocoder.geocode({'address': address}, function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                var marker = new google.maps.Marker({
                                    map: map,
                                    position: results[0].geometry.location
                                });
								<?php if(strlen($innercontent)){ ?>
                                var infowindow = new google.maps.InfoWindow({
                                    content: unescape("<?php echo str_replace('+',' ',(preg_replace('/\s+/',' ',addcslashes( nl2br( $innercontent ),'"'))));?>")
                                });
                                google.maps.event.addListener(marker, 'click', function () {
                                    infowindow.open(map, marker);
                                });
                                infowindow.open(map, marker);
								<?php } ?>
                                map.setCenter(marker.getPosition());
                                setTimeout(function () {
                                    map.panBy(0, -50);
                                }, 10);
                            } else {
                                alert("Geocode was not successful for the following reason: " + status);
                            }
                        });
                    }

                    $(function () {
                        initialize();
                    });
                }(jQuery));
			</script>
			<?php
		}else{
			include($template_file);
		}
		return preg_replace("#\s+#", ' ', ob_get_clean());
	}

	// copied from dtbaker location plugin
	public static function get_map_styles($type, $atts = array()){

		if(!empty($atts['mapstyle'])){
			return html_entity_decode($atts['mapstyle']);
		}
		$styles = array(
			array(
				'featureType' => 'landscape',
				'stylers' => array(
					'saturation' => -100,
					'lightness' => 60,
				),
			),
			array(
				'featureType' => 'road.local',
				'stylers' => array(
					'saturation' => -100,
					'lightness' => 40,
					'visibility' => 'on',
				),
			),
			array(
				'featureType' => 'transit',
				'stylers' => array(
					'saturation' => -100,
					'visibility' => 'simplified',
				),
			),
			array(
				'featureType' => 'water',
				'stylers' => array(
					'lightness' => 30,
					'visibility' => 'on',
				),
			),
			array(
				'featureType' => 'road.highway',
				'elementType' => 'geometry.fill',
				'stylers' => array(
					'color' => '#ef8c25',
					'lightness' => 40,
				),
			),
			array(
				'featureType' => 'road.highway',
				'elementType' => 'geometry.stroke',
				'stylers' => array(
					'visibility' => 'off',
				),
			),
			array(
				'featureType' => 'poi.park',
				'elementType' => 'geometry.fill',
				'stylers' => array(
					'color' => '#b6c54c',
					'lightness' => 40,
					'saturation' => -40,
				),
			),
		);
		$styles = apply_filters('dtbaker_google_map_styles',$styles);
		switch($type){
			case 'js':
				$js_array = array();
				foreach($styles as $style){
					$style_js = $style;
					unset($style_js['stylers']);
					if(isset($style['stylers'])){
						$style_js['stylers'] = array();
						foreach($style['stylers'] as $s => $v){
							$style_js['stylers'][] = (object)array($s => $v);
						}
					}
					$js_array[] = $style_js;
				}
				return json_encode($js_array);
			case 'img':
				// style=feature:featureArgument|element:elementArgument|rule1:rule1Argument|rule2:rule2Argument
				$img_string = '';
				foreach($styles as $style){
					$img_string .= '&style=feature:'.$style['featureType'];
					if(isset($style['elementType'])){
						$img_string .= '|element:'.$style['elementType'];
					}
					if(isset($style['stylers'])){
						foreach($style['stylers'] as $s => $v){
							$img_string .= '|' . $s .':'.$v;
						}
					}
				}
				return $img_string;
				break;
		}
		return '';
	}


	public $fields = array(
		array(
			'name' => 'address',
			'mce_type' => 'textbox',
			'label' => 'Address',
			'default' => 'Sydney, Australia',
		),
		array(
			'name' => 'height',
			'mce_type' => 'textbox',
			'label' => 'Height',
			'default' => '400',
		),
		array(
			'name' => 'zoom',
			'mce_type' => 'textbox',
			'label' => 'Map Zoom (1-20)',
			'default' => '15',
		),
		array(
			'name' => 'enlarge_button',
			'mce_type' => 'listbox',
			'label' => 'Enlarge Button',
			'default' => '1',
			'values' => array(
				1 => 'Yes',
				0 => 'No',
			),
		),
		array(
			'name' => 'innercontent',
			'mce_type' => 'textbox',
			'label' => 'Popup',
			'default' => '',
		),
		array(
			'name' => 'mapstyle',
			'mce_type' => 'textbox',
			'label' => 'Style',
			'default' => '',
		),
	);
}

stylepress_dtbaker_Shortcode_Google_Map::get_instance()->init();


