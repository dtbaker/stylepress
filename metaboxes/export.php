<?php


if( $post->post_parent ){


}else{ ?>

	<a href="<?php print wp_nonce_url(admin_url('admin.php?action=stylepress_export&post_id=' . (int)$post->ID), 'stylepress_export_data', 'stylepress_export_data');?>" class="button" id="elementor-export-option">Export StylePress</a>

<?php }