/**
 * Frontend Elementor Tweaks
 *
 * @package stylepress
 */

(function ($) {

  $(window).on('elementor:init', function () {

    /*elementor.hooks.addFilter( 'panel/elements/regionViews', function( regionViews ) {

            var regions = elementor.getRegions();

            regionViews.stylepresspage = {
                region: regionViews.global.region,
                view: Marionette.ItemView.extend({
                        template: '#tmpl-elementor-panel-stylepresspage',

                        id: 'elementor-panel-stylepresspage',

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
     $template.find( '.elementor-panel-navigation' ).append( '<div id="elementor-panel-elements-navigation-stylepresspage" class="elementor-panel-navigation-tab" data-view="stylepresspage">Style</div>' );
     $templatewrap.html( $template.html() );
     elementor.on( 'elementor:init', function(){
     var $tabs = $( '#elementor-panel-elements-navigation' );
     } );*/


    //elementor.config.pro_library_url = 'https://elementor.com/pro/?ref=1164&campaign=prolib';
    console.log && console.log('Welcome to StylePress');
  });

  $('body').on('change', 'select[data-setting="dynamic_field_value"]', function () {
    $('#stylepress-dynamic-code').text($(this).val() ? '{{' + $(this).val() + '}}' : '');
  });

  var $templatewrap = $('#tmpl-elementor-panel-categories');
  if ($templatewrap.length) {
    var $template = $('<div>' + $templatewrap.html() + '</div>');
    $template.find('a[href^="https://go.elementor.com"]').each(function () {
      //$(this).attr('href', 'https://elementor.com/pro/?ref=1164&campaign=jslink');
    });
    $templatewrap.html($template.html());
  }

  var $templatewrap = $('#tmpl-elementor-panel-global');
  if ($templatewrap.length) {
    var $template = $('<div>' + $templatewrap.html() + '</div>');
    $template.find('a[href^="https://go.elementor.com"]').each(function () {
      //$(this).attr('href', 'https://elementor.com/pro/?ref=1164&campaign=jslinkglobal');
    });
    $templatewrap.html($template.html());
  }


})(jQuery);
