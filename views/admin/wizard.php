<?php


namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

$wizard       = Wizard::get_instance();
$output_steps = $wizard->get_steps();
$step_keys    = array_keys( $output_steps );
?>
<div class="stylepress-setup-wizard">
	<ol class="stylepress-setup-wizard__steps">
		<?php foreach ( $output_steps as $step_key => $step ) {
			if ( $step_key === 'introduction' ) {
				continue;
			}
			?>
			<li class="<?php
			$show_link = false;
			if ( $step_key === $wizard->current_step ) {
				echo 'active';
			} elseif ( array_search( $wizard->current_step, $step_keys ) > array_search( $step_key, $step_keys ) ) {
				echo 'done';
				$show_link = true;
			}
			?>"><?php
				if ( $show_link ) {
					?>
					<a
						href="<?php echo esc_url( $wizard->get_step_link( $step_key ) ); ?>"><?php echo esc_html( $step['name'] ); ?></a>
					<?php
				} else {
					echo esc_html( $step['name'] );
				}
				?></li>
		<?php } ?>
	</ol>

	<div class="stylepress-setup-wizard__content">
		<?php
		$wizard->step_output();
		?>
	</div>
</div>
