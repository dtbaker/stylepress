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