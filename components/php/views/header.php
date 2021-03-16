<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

?>
<div class="stylepress__header">
	<a href="https://stylepress.org" target="_blank" class="stylepress__logo">
		<img alt="StylePress" src="<?php echo esc_url( STYLEPRESS_URI . 'src/images/logo-stylepress-sml.png' ); ?>">
	</a>
	<div class="stylepress__about">
		<div class="stylepress__links">
			<a href="https://stylepress.org" target="_blank" class="stylepress__link">StylePress by dtbaker</a>
			<a href="https://stylepress.org" target="_blank" class="stylepress__link">v<?php echo STYLEPRESS_VERSION; ?></a>
			<a href="https://twitter.com/WPStylePress" target="_blank" class="stylepress__link">
				<img alt="Twitter" src="<?php echo esc_url( STYLEPRESS_URI . 'src/images/t-icon.png' ); ?>">
			</a>
			<a href="https://github.com/dtbaker/stylepress" target="_blank" class="stylepress__link">
				<img alt="Github" src="<?php echo esc_url( STYLEPRESS_URI . 'src/images/github.png' ); ?>">
			</a>
		</div>
		<em>StylePress is in BETA</em><br/>
		<em>
			<a href="https://github.com/dtbaker/stylepress/issues" target="_blank" class="stylepress__link">
				Report issues and feedback on github
			</a>
		</em>
	</div>
</div>
