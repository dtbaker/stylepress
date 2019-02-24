/* global window, document */
if (!window._babelPolyfill) {
  require('babel-polyfill');
}
import $ from 'jquery';

import {post_grid_backend} from './../../../extensions/post-grid/js/post-grid-backend';

class StylePressBackend {
  constructor() {
  };

  backendLoaded = () => {
    post_grid_backend.backendLoaded();
  };

  elementorLoaded = () => {
    console.log && console.log('Welcome to StylePress');

    // Adjust default values
    for (var widget in elementor.config.widgets) {
      if (elementor.config.widgets.hasOwnProperty(widget)) {
        if(typeof elementor.config.widgets[widget].controls.stylepress_default_css !== 'undefined'){
          // This widget has some default styles available, time to clear the default options.
          //console.log && console.log(widget + '!!');
          for (var control in elementor.config.widgets[widget].controls) {
            if (elementor.config.widgets[widget].controls.hasOwnProperty(control) && control[0] !== '_') {
              if(typeof elementor.config.widgets[widget].controls[control].default !== 'undefined'){
                // This control has a "size" option (e.g. divider size). Set it to null so our default styles can be applied.
                if(typeof elementor.config.widgets[widget].controls[control].default === 'object' && typeof elementor.config.widgets[widget].controls[control].default.size !== 'undefined'){
                  elementor.config.widgets[widget].controls[control].default.size = null;
                }
              }
            }
          }
        }
      }
    }

    post_grid_backend.elementorLoaded();
    $('body').on('change', 'select[data-setting="dynamic_field_value"]', function () {
      $('#stylepress-dynamic-code').text($(this).val() ? '{{' + $(this).val() + '}}' : '');
    });
  };

}

const stylepressBackend = new StylePressBackend();
$(function () {
  stylepressBackend.backendLoaded();
});
$(window).on('elementor:init', () => {
  stylepressBackend.elementorLoaded();
});