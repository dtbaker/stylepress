<?php
/**
 * Our Export class.
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Export
 */
class Export extends Base {

	const PAGE_SLUG = 'stylepress-export';

	public function __construct() {

	}

	private function do_the_export() {

		$return = [];
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );

		// Export Path(s)
		$stylefolder                   = basename( Plugin::get_instance()->get_current_site_style() );
		$export_path                   = trailingslashit( WP_CONTENT_DIR ) . 'stylepress-export/' . basename( $stylefolder );
		$export_images_path            = trailingslashit( $export_path ) . 'images';
		$export_content_path           = trailingslashit( $export_path ) . 'content';
		$return['current_style']       = $stylefolder;
		$return['export_path']         = $export_path;
		$return['export_images_path']  = $export_images_path;
		$return['export_content_path'] = $export_content_path;

		// if directory didn't exist, let's create it
		if ( ! is_dir( $export_content_path ) ) {
			wp_mkdir_p( $export_content_path );
		}
		if ( ! is_dir( $export_images_path ) ) {
			wp_mkdir_p( $export_images_path );
		}

		$default_content      = array();
		$post_types_to_export = array( 'attachment', 'wpcf7_contact_form', 'post', 'page' );
		foreach ( get_post_types() as $post_type ) {
			if ( ! in_array( $post_type, $post_types_to_export ) ) { // which post types to ignore.
				$post_types_to_export[] = $post_type;
			}
		}
		$categories = get_categories( array( 'type' => '' ) );
		$taxonomies = get_taxonomies();
		foreach ( $post_types_to_export as $post_type ) {
			if ( in_array( $post_type, array( 'revision', 'event', 'event-recurring' ) ) ) {
				continue;
			} // post types to ignore.
			$args                = array( 'post_type' => $post_type, 'posts_per_page' => - 1 );
			$args['post_status'] = array( 'publish', 'private', 'inherit' );
			$post_datas          = get_posts( $args );
			if ( ! isset( $default_content[ $post_type ] ) ) {
				$default_content[ $post_type ] = array();
			}
			$object = get_post_type_object( $post_type );
			if ( $object && ! empty( $object->labels->singular_name ) ) {
				$type_title = $object->labels->name;
			} else {
				$type_title = ucwords( $post_type ) . 's';
			}

			foreach ( $post_datas as $post_data ) {
				$meta = get_post_meta( $post_data->ID, '', true );
				if ( $post_data->ID == 65 ) {
					//			print_r($meta); exit;
				}
				foreach ( $meta as $meta_key => $meta_val ) {
					if (
						// which keys to nuke all the time
						in_array( $meta_key, array( '_location_id' ) )
						||
						(
							// which keys we want to keep all the time, using strpos:
							strpos( $meta_key, 'elementor' ) === false &&
							strpos( $meta_key, 'dtbaker' ) === false &&
							strpos( $meta_key, 'stylepress_' ) === false &&
							strpos( $meta_key, '_slider' ) === false &&
							// which post types we keep all meta values for:
							! in_array( $post_type, array(
								'nav_menu_item',
								'location',
								'event',
								'product',
								'wpcf7_contact_form',
							) ) &&
							// other meta keys we always want to keep:
							! in_array( $meta_key, array(
								'stylepress_post_title_details',
								'stylepress_page_style',
								'sliderlink',
								'slidercolor',
								'_wp_attached_file',
								'_thumbnail_id',
							) )
						)
					) {
						unset( $meta[ $meta_key ] );
					} else {
						$meta[ $meta_key ] = maybe_unserialize( get_post_meta( $post_data->ID, $meta_key, true ) );
					}
				}
				// copy stock images into the images/stock/ folder for theme import.
				if ( $post_type === 'attachment' ) {
					$file = get_attached_file( $post_data->ID );
					if ( is_file( $file ) ) {
						if ( filesize( $file ) > 1500000 ) {
							$image = wp_get_image_editor( $file );
							if ( ! is_wp_error( $image ) ) {
								list( $width, $height, $type, $attr ) = getimagesize( $file );
								$image->resize( min( $width, 1200 ), null, false );
								$image->save( $file );
							}
						}
						$post_data->guid = wp_get_attachment_url( $post_data->ID );
						if ( is_dir( $export_images_path ) ) {
							copy( $file, trailingslashit( $export_images_path ) . basename( $file ) );
						}
					}
					// fix for incorrect GUID when renaming files with the rename plugin, causes import to bust.

				}
				$terms = array();
				foreach ( $taxonomies as $taxonomy ) {
					$terms[ $taxonomy ] = wp_get_post_terms( $post_data->ID, $taxonomy, array( 'fields' => 'all' ) );
					if ( $terms[ $taxonomy ] ) {
						foreach ( $terms[ $taxonomy ] as $tax_id => $tax ) {
							if ( ! empty( $tax->term_id ) ) {
								$terms[ $taxonomy ][ $tax_id ]->meta = get_term_meta( $tax->term_id );
								if ( ! empty( $terms[ $taxonomy ][ $tax_id ]->meta ) ) {
									foreach ( $terms[ $taxonomy ][ $tax_id ]->meta as $key => $val ) {
										if ( is_array( $val ) && count( $val ) == 1 && isset( $val[0] ) ) {
											$terms[ $taxonomy ][ $tax_id ]->meta[ $key ] = $val[0];
										}
									}
								}
							}
						}
					}
				}
				$default_content[ $post_type ][] = array(
					'type_title'     => $type_title,
					'post_id'        => $post_data->ID,
					'post_title'     => $post_data->post_title,
					'post_status'    => $post_data->post_status,
					'post_name'      => $post_data->post_name,
					'post_content'   => $post_data->post_content,
					'post_excerpt'   => $post_data->post_excerpt,
					'post_parent'    => $post_data->post_parent,
					'menu_order'     => $post_data->menu_order,
					'post_date'      => $post_data->post_date,
					'post_date_gmt'  => $post_data->post_date_gmt,
					'guid'           => $post_data->guid,
					'post_mime_type' => $post_data->post_mime_type,
					'meta'           => $meta,
					'terms'          => $terms,
					//                          'other' => $post_data,
				);
			}
		}
		// put certain content at very end.
		$nav = isset( $default_content['nav_menu_item'] ) ? $default_content['nav_menu_item'] : array();
		if ( $nav ) {
			unset( $default_content['nav_menu_item'] );
			$default_content['nav_menu_item'] = $nav;
		}
		//              print_r($default_content);
		//              exit;
		// find the ID of our menu names so we can import them into default menu locations and also the widget positions below.
		$menus    = get_terms( 'nav_menu' );
		$menu_ids = array();
		foreach ( $menus as $menu ) {
			if ( $menu->name == 'Main Menu' ) {
				$menu_ids['primary'] = $menu->term_id;
			} else if ( $menu->name == 'Quick Links' ) {
				$menu_ids['footer_quick'] = $menu->term_id;
			}
		}

		// choose which custom options to load into defaults
		$all_options = wp_load_alloptions();
		//print_r($all_options);exit;
		foreach ( $all_options as $name => $value ) {
			if ( stristr( $name, 'user_roles' ) ) {
				continue;
			}
			if ( stristr( $name, 'stylepress' ) ) {
				$my_options[ $name ] = maybe_unserialize( $value );
			}
			if ( stristr( $name, 'elementor' ) ) {
				$my_options[ $name ] = maybe_unserialize( $value );
			}
			if ( stristr( $name, '_widget_area_manager' ) ) {
				$my_options[ $name ] = $value;
			}
			if ( stristr( $name, 'wam_' ) ) {
				$my_options[ $name ] = $value;
			}
			//if ( stristr( $name, 'dbem_' ) !== false ) { $my_options[ $name ] = $value; }
			//                  if ( stristr( $name, 'woo' ) !== false ) { $my_options[ $name ] = $value; }
			if ( stristr( $name, 'stylepress_featured_images' ) !== false ) {
				$my_options[ $name ] = $value;
			}
			if ( 'theme_mods_stylepress' === $name ) {
				$my_options[ $name ]            = maybe_unserialize( $value );
				$my_options[ $name . '-child' ] = maybe_unserialize( $value );
				unset( $my_options[ $name ]['nav_menu_locations'] );
			}
		}
		//		$my_options['dbem_credits']                        = 0;
		//		$my_options['woocommerce_cart_redirect_after_add'] = 'yes';
		//		$my_options['woocommerce_enable_ajax_add_to_cart'] = 'no';
		//$my_options['travel_settings']                     = array( 'api_key' => 'AIzaSyBsnYWO4SSibatp0SjsU9D2aZ6urI-_cJ8' );
		//$my_options['tt-font-google-api-key']              = 'AIzaSyBsnYWO4SSibatp0SjsU9D2aZ6urI-_cJ8';

		if ( is_dir( $export_content_path ) ) {

			file_put_contents( trailingslashit( $export_content_path ) . 'default.json', json_encode( $default_content ) );
			file_put_contents( trailingslashit( $export_content_path ) . 'menu.json', json_encode( $menu_ids ) );
			file_put_contents( trailingslashit( $export_content_path ) . 'options.json', json_encode( $my_options ) );
		}

		return $return;
	}

	public function export_page_callback() {

		$export_result = $this->do_the_export();
		$this->content = $this->render_template( 'admin/export.php', [
			'export_result' => $export_result,
		] );
		$this->header  = $this->render_template( 'admin/header.php' );
		echo $this->render_template( 'wrapper.php' );
	}


}

