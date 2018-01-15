
<div id="stylepress-header">
	<a href="https://stylepress.org" target="_blank" class="stylepress-logo"><img src="<?php echo esc_url( DTBAKER_ELEMENTOR_URI . 'assets/img/logo-stylepress-sml.png' );?>"></a>
	<div class="icons">
		<a href="https://stylepress.org" target="_blank"><?php _e('StylePress by dtbaker', 'stylepress'); ?></a>
		<a href="https://stylepress.org" target="_blank">v<?php echo DTBAKER_ELEMENTOR_VERSION;?></a>
		<a href="https://twitter.com/WPStylePress" target="_blank"><i class="fa fa-twitter"></i></a>
		<a href="https://github.com/dtbaker/stylepress" target="_blank"><i class="fa fa-github"></i></a>
        <br/><br/>
        <em><?php _e('StylePress is BETA - there are bugs!', 'stylepress'); ?></em><br/>
        <em><a href="https://github.com/dtbaker/stylepress/issues" target="_blank"><?php _x('Report issues and feedback on github', 'stylepress'); ?></a></em>
	</div>
	<?php /*switch($_GET['page']) {
		case 'dtbaker-stylepress': ?>
			<div class="buttons">
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=dtbaker_style' ) ); ?>" class="button"><?php _e('Import', 'stylepress'); ?></a>
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=dtbaker_style' ) ); ?>"
				   class="button button-primary"><?php _e('Create New Style', 'stylepress'); ?></a>
			</div>
			<?php
			break;
	}*/
	?>
</div>