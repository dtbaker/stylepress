<?php

namespace StylePress\Wizard;

defined( 'STYLEPRESS_VERSION' ) || exit;

?>
<h1><?php esc_html_e( 'Import Style' ); ?></h1>
<p><?php esc_html_e( 'Importing the site style to your StylePress library...', 'stylepress'); ?></p>
<div
	data-controller="import"
	data-import-style-slug-value="<?php echo esc_attr($remote_style_slug);?>"
	data-import-style-data-value="<?php echo esc_attr(json_encode($remote_style_data, JSON_HEX_APOS));?>"
	data-import-ajax-endpoint-value="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ) ;?>"
	data-import-ajax-nonce-value="<?php echo wp_create_nonce('stylepress-import-process') ;?>"
>
	<?php
	if($is_already_installed){
		?>
		<p><?php esc_html_e( 'Styles successfully imported, please continue below.', 'stylepress'); ?></p>
		<?php
	}else{
		?>
		<div data-import-target="import-progress"></div>
		<?php
	}
	?>
</div>
