import $ from 'jquery';

class PostGridBackend {
  constructor() {
  }

  elementorLoaded = () => {
    elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
        if ( 'stylepress_post_grid' === model.attributes.widgetType && panel && 'undefined' !== typeof panel.$el ) {

          // defaults.
          if ( 'undefined' !== typeof model.attributes.settings.attributes.taxonomy_type ) {
            panel.$el.find( '[data-setting="taxonomy_type"]' ).empty();
            panel.$el.find( '[data-setting="taxonomy_type"]' ).append( '<option value="' + model.attributes.settings.attributes.taxonomy_type + '">' + model.attributes.settings.attributes.taxonomy_type + '</option>' );
          }

          if ( 'undefined' !== typeof model.attributes.settings.attributes.terms ) {
            panel.$el.find( '[data-setting="terms"]' ).empty();
            for ( var i = 0; i < model.attributes.settings.attributes.terms.length; i++ ) {
              panel.$el.find( '[data-setting="terms"]' ).append( '<option value="' + model.attributes.settings.attributes.terms[i] + '">' + model.attributes.settings.attributes.terms[i] + '</option>' );
            }
            panel.$el.find( '[data-setting="terms"]' ).val( model.attributes.settings.attributes.terms );
          }

          panel.$el.off( 'change.stylepress' )
            .on( 'change.stylepress', function() {
              model.renderRemoteServer();
              return true;
            })
            .on( 'change.stylepress', '[data-setting="post_type"]', function() {
              var $dropdown = panel.$el.find( '[data-setting="taxonomy_type"]' );
              var oldVal = $dropdown.val();
              $dropdown.empty();
              var post_type = $( '[data-setting="post_type"]' ).val() || [];
              var data = {
                action: 'stylepress_grid_ajax_tax',
                admin_nonce: stylepress_admin.admin_nonce,
                post_type: post_type
              };
              $.post( stylepress_admin.ajaxurl, data, function( taxonomy_names ) {
                if ( taxonomy_names && 'undefined' !== typeof taxonomy_names.data ) {
                  $.each( taxonomy_names.data, function() {
                    if ( 'post_format' === this.name ) {
                      return;
                    }
                    $dropdown.append( '<option value="' + this.name + '">' + this.name + '</option>' );
                  });
                  $dropdown.val( oldVal );

                  //$dropdown[0].selectedIndex = -1;
                }
                $dropdown.trigger( 'change.stylepress' );
              });
              return true;
            }).on( 'change.stylepress', '[data-setting="taxonomy_type"]', function() {
            var $dropdown = panel.$el.find( '[data-setting="terms"]' );
            var oldVal = $dropdown.val();
            $dropdown.empty();

            //$('[data-setting="terms"]')[0].options.length = 0;
            var taxonomy_type = $( '[data-setting="taxonomy_type"]' ).val();
            var data = {
              action: 'stylepress_grid_ajax_terms',
              admin_nonce: stylepress_admin.admin_nonce,
              taxonomy_type: taxonomy_type
            };
            $.post( stylepress_admin.ajaxurl, data, function( terms ) {
              if ( terms && 'undefined' !== typeof terms.data ) {
                $.each( terms.data, function() {
                  $dropdown.append( '<option value="' + this.id + '">' + this.name + '</option>' );
                });
                $dropdown.val( oldVal );

                //$dropdown[0].selectedIndex = -1;
              }
            });
            return true;
          });
          panel.$el.find( '[data-setting="post_type"]' ).trigger( 'change.stylepress' );
        }
      }
    );

  };

  backendLoaded = () => {

  };
}

export let post_grid_backend = new PostGridBackend();
