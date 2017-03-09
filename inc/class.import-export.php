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

		if(!$post_id)return;
		$post_data = get_post($post_id);
		$final_export_data = array();

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
				$attachment_id = (int) get_post_meta( $post_data->ID, '_thumbnail_id', true );
				if ( $attachment_id ) {
					$media_to_export[ $attachment_id ] = true;
				}

				if ( $post_data->ID == 2 ) {
					//print_r($meta);
				}

				$final_export_data[ $post_type ][] = array(
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

				$final_export_data[ $post_type ][] = array(
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
			$final_export_data['easy_google_font'] = array();
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

}

