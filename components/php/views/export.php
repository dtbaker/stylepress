<?php
/**
 * Admin page showing all available Elementor Styles
 *
 * @package stylepress
 */

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

?>

<div class="stylepress__main">
	<h1><?php echo esc_html__( 'Export Done', 'text_domain' ); ?>:</h1>
	<pre><?php echo esc_textarea(var_export($export_result,true));?></pre>
</div>


