<?php
/**
 * Our DtbakerElementorImportExport class.
 * Handles importing/exporting our custom designs.
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

/**
 * Handles importing/exporting our custom designs.
 *
 * Class DtbakerElementorManager
 */
class DtbakerElementorImportExport {

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


	public function export_data($post_id){

		if(!$post_id)return false;
		$post_data = get_post($post_id);
		$final_export_data = array(
			'slug' => $post_data->post_name,
			'version' => '',
			'cost' => '',
			'styles' => array(),
			'easy_google_font' => array(),
			'options' => array(),
		);

		// Export code copied from dtbaker's theme setup wizard shindig.
		if($post_data && $post_data->post_type == 'dtbaker_style' && !$post_data->post_parent) {

			// cool, we have out post parent ready to export.
			$post_type       = 'dtbaker_style';
			$media_to_export = array();
			// export child style data.
			$args        = array(
				'post_type'      => $post_type,
				'posts_per_page' => - 1,
				'post_parent'    => $post_data->ID,
				'post_status'    => 'publish'
			);
			$export_posts = array();
			$export_posts[] = get_post($post_id);
			$children = get_posts( $args );
			if($children) {
				$export_posts = array_merge( $export_posts, $children );
			}

			foreach ( $export_posts as $export_post) {

				$post_data = get_post($export_post->ID);

				$meta = get_post_meta( $post_data->ID, '', true );
				foreach ( $meta as $meta_key => $meta_val ) {
					if (
						// which keys to nuke all the time
						in_array( $meta_key, array( '_location_id', 'stylepress_dev' ) )
						||
						(
							// which keys we want to keep all the time, using strpos:
							strpos( $meta_key, 'elementor' ) === false &&
							strpos( $meta_key, 'stylepress' ) === false &&
							strpos( $meta_key, 'dtbaker' ) === false &&
							// other meta keys we always want to keep:
							! in_array( $meta_key, array(
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

				// we have to strip some values from meta export.
				$strip_keys = array('mailchimp_api_key','mailchimp_list_id');
				if(!empty($meta['_elementor_data'])){
					$elementor_meta = @json_decode($meta['_elementor_data'],true);
					if($elementor_meta){
						array_walk_recursive( $elementor_meta, function( &$val, $key ) use ($strip_keys){
							if( in_array($key, $strip_keys)){
								$val = '';
							}
						});
						$meta['_elementor_data'] = wp_json_encode($elementor_meta);
					}
				}


				$attachment_id = (int) get_post_meta( $post_data->ID, '_thumbnail_id', true );
				if ( $attachment_id ) {
					$media_to_export[ $attachment_id ] = true;
				}

				if ( $post_data->ID == 2 ) {
					//print_r($meta);
				}

				$final_export_data['styles'][ $post_type ][] = array(
					'post_id'        => $post_data->ID,
					'post_title'     => $post_data->post_title,
					'post_type'     => $post_data->post_type,
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
				);

				// todo: parse elementor elements metadata to find image ids and export those as well.

			}

			$post_type = 'attachment';
			foreach($media_to_export as $media_id => $tf){

				$post_data = get_post($media_id);

				$meta = get_post_meta( $post_data->ID, '', true );
				foreach ( $meta as $meta_key => $meta_val ) {
					if (
						// which keys to nuke all the time
						in_array( $meta_key, array( '_location_id' ) )
						||
						(
							// which keys we want to keep all the time, using strpos:
							strpos( $meta_key, 'elementor' ) === false &&
							strpos( $meta_key, 'stylepress' ) === false &&
							strpos( $meta_key, 'dtbaker' ) === false &&
							// other meta keys we always want to keep:
							! in_array( $meta_key, array(
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

				$final_export_data['styles'][ $post_type ][] = array(
					'post_id'        => $post_data->ID,
					'post_title'     => $post_data->post_title,
					'post_type'     => $post_data->post_type,
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
				);
			}

			// also need to export the Google Font settings for this style.
			$all_options     = get_option( 'tt_font_theme_options', array() );
			foreach($all_options as $key=>$val){
				if(preg_match('#^'. (int)$post_id .'[a-z]#', $key )){
					$new_key = str_replace( (int)$post_id, '', $key );
					$final_export_data['easy_google_font'][$new_key] = $val;
				}
			}

		}

		return $final_export_data;
	}


	public function import_data($style){

		if( $style && !empty($style['slug'])){
			if(get_transient('import_style_'.$style['slug'])){
				//return false; // doing duplicates.
			}
			// hacky way to stop people clicking twice.
			set_transient('import_style_'.$style['slug'], time(), 60 );


			if(!empty($style['styles'])){
				if(!empty($style['styles']['attachment'])){

					// import attachments first before the actual content that will use them.
					foreach($style['styles']['attachment'] as $data) {
						$this->_process_post_data( 'attachment', $data );
					}
					unset($style['styles']['attachment']);
				}

				foreach($style['styles'] as $post_type => $post_data){
					foreach($post_data as $data) {
						$this->_process_post_data( $post_type, $data );
					}

				}
			}
			if(!empty($style['easy_google_font'])){
			}
			if(!empty($style['options'])){
			}

			$this->_handle_post_orphans();

		}

		return false;

	}

	private function _handle_post_orphans() {
		$orphans = $this->_post_orphans();
		foreach ( $orphans as $original_post_id => $original_post_parent_id ) {
			if ( $original_post_parent_id ) {
				if ( $this->_imported_post_id( $original_post_id ) && $this->_imported_post_id( $original_post_parent_id ) ) {
					$post_data                = array();
					$post_data['ID']          = $this->_imported_post_id( $original_post_id );
					$post_data['post_parent'] = $this->_imported_post_id( $original_post_parent_id );
					wp_update_post( $post_data );
					$this->_post_orphans( $original_post_id, 0 ); // ignore future
				}
			}
		}
	}

	private function _imported_post_id( $original_id = false, $new_id = false ) {
		if ( is_array( $original_id ) || is_object( $original_id ) ) {
			return false;
		}
		$post_ids = get_transient( 'stylepressimportpostids' );
		if ( ! is_array( $post_ids ) ) {
			$post_ids = array();
		}
		if ( $new_id ) {
			if ( ! isset( $post_ids[ $original_id ] ) ) {
//				$this->log( 'Insert old ID ' . $original_id . ' as new ID: ' . $new_id );
			} else if ( $post_ids[ $original_id ] != $new_id ) {
//				$this->error( 'Replacement OLD ID ' . $original_id . ' overwritten by new ID: ' . $new_id );
			}
			$post_ids[ $original_id ] = $new_id;
			set_transient( 'stylepressimportpostids', $post_ids, 60 * 60 );
		} else if ( $original_id && isset( $post_ids[ $original_id ] ) ) {
			return $post_ids[ $original_id ];
		} else if ( $original_id === false ) {
			return $post_ids;
		}

		return false;
	}


	public $logs = array();

	public function log( $message ) {
		$this->logs[] = $message;
	}

	public $errors = array();

	public function error( $message ) {
		$this->logs[] = 'ERROR!!!! ' . $message;
	}


	private function _post_orphans( $original_id = false, $missing_parent_id = false ) {
		$post_ids = get_transient( 'stylepresspostorphans' );
		if ( ! is_array( $post_ids ) ) {
			$post_ids = array();
		}
		if ( $missing_parent_id ) {
			$post_ids[ $original_id ] = $missing_parent_id;
			set_transient( 'stylepresspostorphans', $post_ids, 60 * 60 );
		} else if ( $original_id && isset( $post_ids[ $original_id ] ) ) {
			return $post_ids[ $original_id ];
		} else if ( $original_id === false ) {
			return $post_ids;
		}

		return false;
	}


	private function _process_post_data( $post_type, $post_data, $delayed = 0, $debug = false ) {

		$this->log( " Processing $post_type " . $post_data['post_id'] );
		$original_post_data = $post_data;

		if ( $debug ) {
			echo "HERE\n";
		}
		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}
		if ( ! $debug && $this->_imported_post_id( $post_data['post_id'] ) ) {
			return true; // already done :)
		}
		/*if ( 'nav_menu_item' == $post_type ) {
			$this->process_menu_item( $post );
			continue;
		}*/

		if ( empty( $post_data['post_title'] ) && empty( $post_data['post_name'] ) ) {
			// this is menu items
			$post_data['post_name'] = $post_data['post_id'];
		}

		$post_data['post_type'] = $post_type;

		$post_parent = (int) $post_data['post_parent'];
		if ( $post_parent ) {
			// if we already know the parent, map it to the new local ID
			if ( $this->_imported_post_id( $post_parent ) ) {
				$post_data['post_parent'] = $this->_imported_post_id( $post_parent );
				// otherwise record the parent for later
			}else{
				$this->_post_orphans( intval( $post_data['post_id'] ), $post_parent );
				$post_data['post_parent'] = 0;//
			}
		}


		switch ( $post_type ) {
			case 'attachment':
				// import media via url
				if ( ! empty( $post_data['guid'] ) ) {

					// check if this has already been imported.
					$old_guid = $post_data['guid'];
					if ( $this->_imported_post_id( $old_guid ) ) {
						return true; // alrady done;
					}
					$remote_url = $post_data['guid'];

					$post_data['upload_date'] = date( 'Y/m', strtotime( $post_data['post_date_gmt'] ) );
					if ( isset( $post_data['meta'] ) ) {
						foreach ( $post_data['meta'] as $key => $meta ) {
							if ( $key == '_wp_attached_file' ) {
								foreach ( (array) $meta as $meta_val ) {
									if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta_val, $matches ) ) {
										$post_data['upload_date'] = $matches[0];
									}
								}
							}
						}
					}

					$upload = $this->_fetch_remote_file( $remote_url, $post_data );

					if ( ! is_array( $upload ) || is_wp_error( $upload ) ) {
						// todo: error
						return false;
					}

					if ( $info = wp_check_filetype( $upload['file'] ) ) {
						$post['post_mime_type'] = $info['type'];
					} else {
						return false;
					}

					$post_data['guid'] = $upload['url'];

					// as per wp-admin/includes/upload.php
					$post_id = wp_insert_attachment( $post_data, $upload['file'] );
					if ( $post_id ) {

						if ( ! empty( $post_data['meta'] ) ) {
							foreach ( $post_data['meta'] as $meta_key => $meta_val ) {
								if ( $meta_key != '_wp_attached_file' && ! empty( $meta_val ) ) {
									update_post_meta( $post_id, $meta_key, $meta_val );
								}
							}
						}

						wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

						// remap resized image URLs, works by stripping the extension and remapping the URL stub.
						if ( preg_match( '!^image/!', $info['type'] ) ) {
							$parts = pathinfo( $remote_url );
							$name  = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

							$parts_new = pathinfo( $upload['url'] );
							$name_new  = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

							$this->_imported_post_id( $parts['dirname'] . '/' . $name, $parts_new['dirname'] . '/' . $name_new );
						}
						$this->_imported_post_id( $post_data['post_id'], $post_id );
						//$this->_imported_post_id( $old_guid, $post_id );
					}
				}
				break;
			default:
				// work out if we have to delay this post insertion


				if ( ! empty( $post_data['meta'] ) && is_array( $post_data['meta'] ) ) {

					// replace any elementor post data:

					// fix for double json encoded stuff:
					foreach ( $post_data['meta'] as $meta_key => $meta_val ) {
						if ( is_string( $meta_val ) && strlen( $meta_val ) && $meta_val[0] == '[' ) {
							$test_json = @json_decode( $meta_val, true );
							if ( is_array( $test_json ) ) {
								$post_data['meta'][ $meta_key ] = $test_json;
							}
						}
					}

					array_walk_recursive( $post_data['meta'], array( $this, '_elementor_id_import' ) );


				}

				$post_data['post_content'] = $this->_parse_gallery_shortcode_content( $post_data['post_content'] );

				// we have to fix up all the visual composer inserted image ids
				$replace_post_id_keys = array(
					'parallax_image',
					'dtbwp_row_image_top',
					'dtbwp_row_image_bottom',
					'image',
					'item', // vc grid
					'post_id',
				);
				foreach ( $replace_post_id_keys as $replace_key ) {
					if ( preg_match_all( '# ' . $replace_key . '="(\d+)"#', $post_data['post_content'], $matches ) ) {
						foreach ( $matches[0] as $match_id => $string ) {
							$new_id = $this->_imported_post_id( $matches[1][ $match_id ] );
							if ( $new_id ) {
								$post_data['post_content'] = str_replace( $string, ' ' . $replace_key . '="' . $new_id . '"', $post_data['post_content'] );
							} else {
								$this->error( 'Unable to find POST replacement for ' . $replace_key . '="' . $matches[1][ $match_id ] . '" in content.' );
								if ( $delayed ) {
									// already delayed, unable to find this meta value, insert it anyway.

								} else {

									$this->error( 'Adding ' . $post_data['post_id'] . ' to delay listing.' );
									//                                      echo "Delaying post id ".$post_data['post_id']."... \n\n";
									//$this->_delay_post_process( $post_type, $original_post_data );

									return false;
								}
							}
						}
					}
				}

				$post_id = wp_insert_post( $post_data, true );
				if ( ! is_wp_error( $post_id ) ) {
					$this->_imported_post_id( $post_data['post_id'], $post_id );
					// add/update post meta
					if ( ! empty( $post_data['meta'] ) ) {
						foreach ( $post_data['meta'] as $meta_key => $meta_val ) {

							// if the post has a featured image, take note of this in case of remap
							if ( '_thumbnail_id' == $meta_key ) {
								/// find this inserted id and use that instead.
								$inserted_id = $this->_imported_post_id( intval( $meta_val ) );
								if ( $inserted_id ) {
									$meta_val = $inserted_id;
								}
							}
							//                                  echo "Post meta $meta_key was $meta_val \n\n";

							update_post_meta( $post_id, $meta_key, $meta_val );

						}
					}

					if ( !empty($post_data['meta']['_elementor_data']) || !!empty($post_data['meta']['_elementor_css']) ) {
						$this->elementor_post( $post_id );
					}
				}

				break;
		}

		return true;
	}


	private function _fetch_remote_file( $url, $post ) {
		// extract the file name and extension from the url
		$file_name  = basename( $url );
		$local_file = trailingslashit( get_template_directory() ) . 'images/stock/' . $file_name;
		$upload     = false;
		if ( is_file( $local_file ) && filesize( $local_file ) > 0 ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			WP_Filesystem();
			global $wp_filesystem;
			$file_data = $wp_filesystem->get_contents( $local_file );
			$upload    = wp_upload_bits( $file_name, 0, $file_data, $post['upload_date'] );
			if ( $upload['error'] ) {
				return new WP_Error( 'upload_dir_error', $upload['error'] );
			}
		}

		if ( ! $upload || $upload['error'] ) {
			// get placeholder file in the upload dir with a unique, sanitized filename
			$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
			if ( $upload['error'] ) {
				return new WP_Error( 'upload_dir_error', $upload['error'] );
			}

			// fetch the remote url and write it to the placeholder file
			//$headers = wp_get_http( $url, $upload['file'] );

			$max_size = (int) apply_filters( 'import_attachment_size_limit', 0 );

			// we check if this file is uploaded locally in the source folder.
			$response = wp_remote_get( $url );
			if ( is_array( $response ) && ! empty( $response['body'] ) && $response['response']['code'] == '200' ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				$headers = $response['headers'];
				WP_Filesystem();
				global $wp_filesystem;
				$wp_filesystem->put_contents( $upload['file'], $response['body'] );
				//
			} else {
				// required to download file failed.
				@unlink( $upload['file'] );

				return new WP_Error( 'import_file_error', esc_html__( 'Remote server did not respond' ) );
			}

			$filesize = filesize( $upload['file'] );

			if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
				@unlink( $upload['file'] );

				return new WP_Error( 'import_file_error', esc_html__( 'Remote file is incorrect size' ) );
			}

			if ( 0 == $filesize ) {
				@unlink( $upload['file'] );

				return new WP_Error( 'import_file_error', esc_html__( 'Zero size file downloaded' ) );
			}

			if ( ! empty( $max_size ) && $filesize > $max_size ) {
				@unlink( $upload['file'] );

				return new WP_Error( 'import_file_error', sprintf( esc_html__( 'Remote file is too large, limit is %s' ), size_format( $max_size ) ) );
			}
		}

		// keep track of the old and new urls so we can substitute them later
		$this->_imported_post_id( $url, $upload['url'] );
		$this->_imported_post_id( $post['guid'], $upload['url'] );
		// keep track of the destination if the remote url is redirected somewhere else
		if ( isset( $headers['x-final-location'] ) && $headers['x-final-location'] != $url ) {
			$this->_imported_post_id( $headers['x-final-location'], $upload['url'] );
		}

		return $upload;
	}


	public function elementor_post( $post_id = false ) {

		// regenrate the CSS for this Elementor post
		if( class_exists( 'Elementor\Post_CSS_File' ) ) {
			$post_css = new Elementor\Post_CSS_File($post_id);
			$post_css->update();
		}

	}



	// return the difference in length between two strings
	public function cmpr_strlen( $a, $b ) {
		return strlen( $b ) - strlen( $a );
	}


	private function _parse_gallery_shortcode_content( $content ) {
		// we have to format the post content. rewriting images and gallery stuff
		$replace      = $this->_imported_post_id();
		$urls_replace = array();
		foreach ( $replace as $key => $val ) {
			if ( $key && $val && ! is_numeric( $key ) && ! is_numeric( $val ) ) {
				$urls_replace[ $key ] = $val;
			}
		}
		if ( $urls_replace ) {
			uksort( $urls_replace, array( &$this, 'cmpr_strlen' ) );
			foreach ( $urls_replace as $from_url => $to_url ) {
				$content = str_replace( $from_url, $to_url, $content );
			}
		}
		if ( preg_match_all( '#\[gallery[^\]]*\]#', $content, $matches ) ) {
			foreach ( $matches[0] as $match_id => $string ) {
				if ( preg_match( '#ids="([^"]+)"#', $string, $ids_matches ) ) {
					$ids = explode( ',', $ids_matches[1] );
					foreach ( $ids as $key => $val ) {
						$new_id = $val ? $this->_imported_post_id( $val ) : false;
						if ( ! $new_id ) {
							unset( $ids[ $key ] );
						} else {
							$ids[ $key ] = $new_id;
						}
					}
					$new_ids                   = implode( ',', $ids );
					$content = str_replace( $ids_matches[0], 'ids="' . $new_ids . '"', $content );
				}
			}
		}
		// contact form 7 id fixes.
		if ( preg_match_all( '#\[contact-form-7[^\]]*\]#', $content, $matches ) ) {
			foreach ( $matches[0] as $match_id => $string ) {
				if ( preg_match( '#id="(\d+)"#', $string, $id_match ) ) {
					$new_id = $this->_imported_post_id( $id_match[1] );
					if ( $new_id ) {
						$content = str_replace( $id_match[0], 'id="' . $new_id . '"', $content );
					} else {
						// no imported ID found. remove this entry.
						$content = str_replace( $matches[0], '(insert contact form here)', $content );
					}
				}
			}
		}
		return $content;
	}

	private function _elementor_id_import( &$item, $key ) {
		if ( $key == 'id' && ! empty( $item ) && is_numeric( $item ) ) {
			// check if this has been imported before
			$new_meta_val = $this->_imported_post_id( $item );
			if ( $new_meta_val ) {
				$item = $new_meta_val;
			}
		}
		if ( ( $key == 'page' || $key == 'page_id' ) && ! empty( $item ) ) {

			if ( false !== strpos( $item, 'p.' ) ) {
				$new_id = str_replace( 'p.', '', $item );
				// check if this has been imported before
				$new_meta_val = $this->_imported_post_id( $new_id );
				if ( $new_meta_val ) {
					$item = 'p.' . $new_meta_val;
				}
			} else if ( is_numeric( $item ) ) {
				// check if this has been imported before
				$new_meta_val = $this->_imported_post_id( $item );
				if ( $new_meta_val ) {
					$item = $new_meta_val;
				}
			}
		}
		if ( $key == 'post_id' && ! empty( $item ) && is_numeric( $item ) ) {
			// check if this has been imported before
			$new_meta_val = $this->_imported_post_id( $item );
			if ( $new_meta_val ) {
				$item = $new_meta_val;
			}
		}
		if ( $key == 'url' && ! empty( $item ) && (strstr( $item, 'ocalhost' ) || strstr( $item, 'dev.dtbaker' )) ) {
			// check if this has been imported before
			$new_meta_val = $this->_imported_post_id( $item );
			if ( $new_meta_val ) {
				$item = $new_meta_val;
			}
		}
		if ( ($key == 'shortcode' || $key == 'editor') && ! empty( $item ) ) {
			// we have to fix the [contact-form-7 id=133] shortcode issue.
			$item = $this->_parse_gallery_shortcode_content( $item );

		}
	}

}

