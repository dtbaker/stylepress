/* global window, document */
if (!window._babelPolyfill) {
  require('babel-polyfill');
}
import $ from 'jquery';

import {post_grid_backend} from './../../../extensions/post-grid/js/post-grid-backend';

class StylePressBackend {
  constructor() {
  };

  pageLoaded = () => {
    post_grid_backend.pageLoaded();
  };
}

$(function () {
  const stylepressBackend = new StylePressBackend();
  stylepressBackend.pageLoaded();
});