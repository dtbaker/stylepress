/**
 * Frontend Elementor Tweaks
 *
 * @package dtbaker-elementor
 */

( function($) {

	elementor.hooks.addFilter( 'panel/elements/regionViews', function( regionViews ) {

		var regions = elementor.getRegions();

		regionViews.dtbakerpage = {
			region: regionViews.global.region,
			view: Marionette.ItemView.extend({
				template: '#tmpl-elementor-panel-dtbakerpage',

				id: 'elementor-panel-dtbakerpage',

				initialize: function () {
					setTimeout( function(){
						elementor.getPanelView().getCurrentPageView().search.reset();
					}, 100);
				},

				onDestroy: function () {
					elementor.getPanelView().getCurrentPageView().showView( 'search' );
				}
				}
			)
		};

		return regionViews;
	} );

	var $templatewrap = $( '#tmpl-elementor-panel-elements' );
	var $template = $( '<div>' + $templatewrap.html() + '</div>' );
	$template.find( '.elementor-panel-navigation' ).append( '<div id="elementor-panel-elements-navigation-dtbakerpage" class="elementor-panel-navigation-tab" data-view="dtbakerpage">Style</div>' );
	$templatewrap.html( $template.html() );
	elementor.on( 'elementor:init', function(){
		var $tabs = $( '#elementor-panel-elements-navigation' );
		// $tabs.append('<div id="elementor-panel-elements-navigation-dtbakerpage" class="elementor-panel-navigation-tab" data-view="dtbakerpage">Page</div>');
		/*var foo = Marionette.CompositeView.extend( {
            template: '#tmpl-elementor-panel-dtbaker-page',
                    id: 'elementor-panel-dtbaker-page',
                    initialize: function() {
                this.listenTo( elementor.channels.panelElements, 'filter:change', this.onPanelElementsFilterChange );
            }
        } );*/
	} );

	$('body').on('change','select[data-setting="dynamic_field_value"]',function(){
		$('#dtbaker-dynamic-code').text( $(this).val() ? '{{' + $(this).val() + '}}' : '');
	});
} )(jQuery);
