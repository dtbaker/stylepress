<?php
/**
 * Our Backend class.
 *
 * This handles our main admin page
 *
 * @package stylepress
 */

namespace StylePress\Logging;

defined( 'STYLEPRESS_VERSION' ) || exit;

/**
 * All the magic happens here.
 *
 * Class Backend
 */
class Debug extends \StylePress\Core\Base {
	public function debug_message( $message ) {
		if ( STYLEPRESS_DEBUG_OUTPUT && is_user_logged_in() ) {
			echo '<div class="stylepress-debug">';
			echo '<span>StylePress:</span> &nbsp; ';
			echo $message;
			echo "</div>";
		}
	}
}

