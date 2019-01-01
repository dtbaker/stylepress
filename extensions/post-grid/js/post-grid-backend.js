import $ from 'jquery';

class PostGridBackend {
  constructor() {
  }
  pageLoaded = () => {
    console.log('Loaded back')
  };
}

export let post_grid_backend = new PostGridBackend();
