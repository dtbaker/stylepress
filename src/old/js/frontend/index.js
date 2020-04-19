import "core-js/stable";
import "regenerator-runtime/runtime";

import $ from 'jquery';

import '../../scss/frontend.scss'

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