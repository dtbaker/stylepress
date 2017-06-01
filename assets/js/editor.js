/**
 * Frontend Elementor Tweaks
 *
 * @package dtbaker-elementor
 */

( function($) {

    $( window ).on( 'elementor:init', function(){

		/*elementor.hooks.addFilter( 'panel/elements/regionViews', function( regionViews ) {

            var regions = elementor.getRegions();

            regionViews.dtbakerpage = {
                region: regionViews.global.region,
                view: Marionette.ItemView.extend({
                        template: '#tmpl-elementor-panel-dtbakerpage',

                        id: 'elementor-panel-dtbakerpage',

                        initialize: function () {
                            setTimeout(function () {
                                elementor.getPanelView().getCurrentPageView().search.reset();
                            }, 100);
                        },

                        onDestroy: function () {
                            elementor.getPanelView().getCurrentPageView().showView('search');
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
		 } );*/



        //elementor.config.pro_library_url = 'https://elementor.com/pro/?ref=1164&campaign=prolib';
        console.log && console.log('Welcome to StylePress');
    } );

	$('body').on('change','select[data-setting="dynamic_field_value"]',function(){
		$('#dtbaker-dynamic-code').text( $(this).val() ? '{{' + $(this).val() + '}}' : '');
	});

    var $templatewrap = $( '#tmpl-elementor-panel-categories' );
    if( $templatewrap.length ) {
        var $template = $('<div>' + $templatewrap.html() + '</div>');
        $template.find('a[href^="https://go.elementor.com"]').each(function(){
            //$(this).attr('href', 'https://elementor.com/pro/?ref=1164&campaign=jslink');
        });
        $templatewrap.html($template.html());
    }

    var $templatewrap = $( '#tmpl-elementor-panel-global' );
    if( $templatewrap.length ) {
        var $template = $('<div>' + $templatewrap.html() + '</div>');
        $template.find('a[href^="https://go.elementor.com"]').each(function(){
            //$(this).attr('href', 'https://elementor.com/pro/?ref=1164&campaign=jslinkglobal');
        });
        $templatewrap.html($template.html());
    }


} )(jQuery);
