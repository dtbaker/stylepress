<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

?>
<h1><?php esc_html_e( 'Default Content' ); ?></h1>
<form method="post">
	<?php if ( $this->is_possible_upgrade() ) { ?>
		<p><?php esc_html_e( 'It looks like you already have content installed on this website. If you would like to install the default demo content as well you can select it below.' ); ?></p>
	<?php } else { ?>
		<p><?php printf( esc_html__( 'It\'s time to insert some default content for your new WordPress website. Choose what you would like inserted below and click Continue. It is recommended to leave everything selected. Once inserted, this content can be managed from the WordPress admin dashboard. ' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=page' ) ) . '" target="_blank">', '</a>' ); ?></p>
	<?php } ?>
	<table class="stylepress-pages">
		<thead>
		<tr>
			<td class="check"></td>
			<th class="item"><?php esc_html_e( 'Item' ); ?></th>
			<th class="description"><?php esc_html_e( 'Description' ); ?></th>
			<th class="status"><?php esc_html_e( 'Status' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $this->content_default_get() as $slug => $default ) { ?>
			<tr class="stylepress_default_content" data-content="<?php echo esc_attr( $slug ); ?>">
				<td>
					<input type="checkbox" name="default_content[<?php echo esc_attr( $slug ); ?>]"
					       class="stylepress_default_content"
					       id="default_content_<?php echo esc_attr( $slug ); ?>"
					       value="1" <?php echo ( ! isset( $default['checked'] ) || $default['checked'] ) ? ' checked' : ''; ?>>
				</td>
				<td><label
						for="default_content_<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $default['title'] ); ?></label>
				</td>
				<td class="description"><?php echo esc_html( $default['description'] ); ?></td>
				<td class="status"><span><?php echo esc_html( $default['pending'] ); ?></span>
					<div class="spinner"></div>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<p class="stylepress-actions step">
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
		   class="button-primary button button-large button-next"
		   data-callback="install_content"><?php esc_html_e( 'Continue' ); ?></a>
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
		   class="button button-large button-next"><?php esc_html_e( 'Skip this step' ); ?></a>
		<?php wp_nonce_field( 'stylepress' ); ?>
	</p>
</form>
