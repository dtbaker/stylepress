import $ from 'jquery';

class PostGrid {
  constructor() {
  }
  pageLoaded = () => {
    console.log('Post Grid Loaded')
  };
}

export let post_grid = new PostGrid();
