<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

update_option( 'stylepress_setup_wizard_complete', time() );
?>
<a href="https://twitter.com/share" class="twitter-share-button"
   data-url="http://themeforest.net/user/dtbaker/portfolio?ref=dtbaker"
   data-text="<?php echo esc_attr( 'I just installed the ' . wp_get_theme() . ' #WordPress theme from #ThemeForest' ); ?>"
   data-via="EnvatoMarket" data-size="large">Tweet</a>
<script>!function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (!d.getElementById(id)) {
      js = d.createElement(s);
      js.id = id;
      js.src = "//platform.twitter.com/widgets.js";
      fjs.parentNode.insertBefore(js, fjs);
    }
  }(document, "script", "twitter-wjs");</script>

<h1><?php esc_html_e( 'Your Website is Ready!' ); ?></h1>

<p>Congratulations! The theme has been activated and your website is ready. Login to your WordPress
	dashboard to make changes and modify any of the default content to suit your needs.</p>
<p>Please come back and <a href="http://themeforest.net/downloads" target="_blank">leave a 5-star rating</a>
	if you are happy with this theme. <br/>Follow <a href="https://twitter.com/dtbaker" target="_blank">@dtbaker</a>
	on Twitter to see updates. Thanks! </p>

<div class="stylepress-next-steps">
	<div class="stylepress-next-steps-first">
		<h2><?php esc_html_e( 'Next Steps' ); ?></h2>
		<ul>
			<li class="setup-product"><a class="button button-primary button-large"
			                             href="https://twitter.com/dtbaker"
			                             target="_blank"><?php esc_html_e( 'Follow @dtbaker on Twitter' ); ?></a>
			</li>
			<li class="setup-product"><a class="button button-next button-large"
			                             href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'View your new website!' ); ?></a>
			</li>
		</ul>
	</div>
	<div class="stylepress-next-steps-last">
		<h2><?php esc_html_e( 'More Resources' ); ?></h2>
		<ul>
			<li class="documentation"><a href="http://dtbaker.net/envato/documentation/"
			                             target="_blank"><?php esc_html_e( 'Read the Theme Documentation' ); ?></a>
			</li>
			<li class="howto"><a href="https://wordpress.org/support/"
			                     target="_blank"><?php esc_html_e( 'Learn how to use WordPress' ); ?></a>
			</li>
			<li class="rating"><a href="http://themeforest.net/downloads"
			                      target="_blank"><?php esc_html_e( 'Leave an Item Rating' ); ?></a></li>
			<li class="support"><a href="http://dtbaker.net/envato/"
			                       target="_blank"><?php esc_html_e( 'Get Help and Support' ); ?></a></li>
		</ul>
	</div>
</div>
