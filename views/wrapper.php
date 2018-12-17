<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

?>

<div class="stylepress__wrapper">
	<div class="stylepress__header">
		<?php echo $this->header; ?>
	</div>
	<div class="stylepress__content">
		<?php echo $this->render_template( 'notices/advertisement.php' ); ?>
		<div class="stylepress__content-inner">
			<?php echo $this->content; ?>
		</div>
	</div>
</div>
