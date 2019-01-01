/* global window, document */
if (!window._babelPolyfill) {
  require('babel-polyfill');
}
import $ from 'jquery';

import {post_grid} from './../../../extensions/post-grid/js/post-grid';

class StylePress {
  constructor() {
  };

  pageLoaded = () => {
    post_grid.pageLoaded();
  };
}

$(function () {
  const stylepress = new StylePress();
  stylepress.pageLoaded();
});