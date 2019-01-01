import $ from 'jquery';

class PostGrid {
  constructor() {
  }
  pageLoaded = () => {
    console.log('Loaded2')
  };
}

export let post_grid = new PostGrid();
