<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;


$new_style_slug = isset( $_GET['remote_style'] ) ? $_GET['remote_style'] : false;
if ( ! $new_style_slug ) {
	wp_die( 'Invalid remote style, please go back and try again' );
}
$verified_request_to_import = wp_verify_nonce( $_GET['remote_style_hash'], 'install style ' . $new_style_slug );
$remote_style_data    = Remote_Styles::get_instance()->get_remote_style_data( $new_style_slug );
if ( ! $remote_style_data ) {
	wp_die( 'Invalid remote style data, please go back and try again' );
}
$is_already_installed    = Remote_Styles::get_instance()->is_remote_style_imported( $new_style_slug );

?>
<h1><?php esc_html_e( 'Import Style' ); ?></h1>
<form method="post">
	<p><?php esc_html_e( 'Importing the site style to your StylePress library...', 'stylepress'); ?></p>

	<?php
	if($is_already_installed){
		?>
		<p><?php esc_html_e( 'Styles successfully imported, please continue below.', 'stylepress'); ?></p>
		<?php
	}else if($verified_request_to_import){
		?>
		Importing
		<div
			data-controller="wizard-import"
			class="stylepress__import-progress js-stylepress-import"
			data-wizard-import-style-slug-value="<?php echo esc_attr($new_style_slug);?>"
			data-wizard-import-style-hash-value="<?php echo esc_attr($_GET['remote_style_hash']);?>"
		></div>
		<?php
	}else{
		?>

		<?php
	}
	?>

	<p class="stylepress-actions step">
		<input type="submit" class="button-primary button button-large button-next"
		       value="<?php esc_attr_e( 'Continue' ); ?>" name="save_step"/>
		<?php wp_nonce_field( 'stylepress' ); ?>
	</p>
</form>
