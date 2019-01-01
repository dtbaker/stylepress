import $ from 'jquery';

class PostGridBackend {
  constructor() {
  }

  elementorLoaded = () => {
    elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
        if (panel && typeof panel.$el !== 'undefined') {
          panel.$el.on('change', '[data-setting="post_type"]', function () {
            $('[data-setting="taxonomy_type"]').empty();
            var post_type = $('[data-setting="post_type"]').val() || [];
            var data = {
              action: 'stylepress_grid_ajax_tax',
              admin_nonce: stylepress_admin.admin_nonce,
              post_type: post_type
            };
            $.post(stylepress_admin.ajaxurl, data, function (taxonomy_names) {
              if (taxonomy_names && typeof taxonomy_names.data !== 'undefined') {
                $.each(taxonomy_names.data, function () {
                  if (this.name === 'post_format') {
                    return;
                  }
                  $('[data-setting="taxonomy_type"]').append('<option value="' + this.name + '">' + this.name + '</option>');
                });
                $('[data-setting="taxonomy_type"]')[0].selectedIndex = -1;
              }
            });
            return true;
          }).on('change', '[data-setting="taxonomy_type"]', function () {
            $('[data-setting="terms"]')[0].options.length = 0;
            var taxonomy_type = $('[data-setting="taxonomy_type"]').val();
            var data = {
              action: 'stylepress_grid_ajax_terms',
              admin_nonce: stylepress_admin.admin_nonce,
              taxonomy_type: taxonomy_type
            };
            $.post(stylepress_admin.ajaxurl, data, function (terms) {
              if (terms && typeof terms.data !== 'undefined') {
                $.each(terms.data, function () {
                  $('[data-setting="terms"]').append('<option value="' + this.id + '">' + this.name + '</option>');
                });
                $('[data-setting="terms"]')[0].selectedIndex = -1;
              }
            });
            return true;
          });
        }
      }
    )

  };

  backendLoaded = () => {

  };
}

export let post_grid_backend = new PostGridBackend();
