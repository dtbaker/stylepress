<?php
/**
 * Page Templates rendered at the bottom of an Elementor page
 *
 * @package dtbaker-elementor
 */

defined( 'DTBAKER_ELEMENTOR_PATH' ) || exit;

?>
<script type="text/template" id="tmpl-elementor-panel-dtbakerpage">
	<div class="dtbaker-elementor-page-style-item">

		StylePress options<br/> coming soon.


	</div>
</script>

<style type="text/css">
	.dtbaker-stylepress-elementor-widget{
		background: url(<?php echo esc_url( DTBAKER_ELEMENTOR_URI .'assets/img/widget-logo.png' );?>) no-repeat center;
        background-size: contain;
        padding: 26px;
        font-size: 20px;
        line-height: 46px;
	}
    .dtbaker-elementor-page-style-item{
        text-align: center;
        padding: 30px;
    }
#elementor-controls [class*="elementor-control-stylepress"]{
    border:4px solid #3d99d4;

}
</style>
