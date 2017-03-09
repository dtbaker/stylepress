<?php


if( $post->post_parent ){


}else{ ?>

<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
	<input type="hidden" name="action" value="stylepress_export" />
	<input type="hidden" name="post_id" value="<?php echo (int)$post->ID;?>" />
	<?php wp_nonce_field( 'stylepress_export_data', 'stylepress_export_data' ); ?>
	<button type="submit" name="save" class="button">Export StylePress</button>
</form>

<?php }