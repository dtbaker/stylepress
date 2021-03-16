import { Controller } from 'stimulus';

export default class extends Controller {
  static values = {
    styleSlug: String,
    styleData: Object
  }
  connect() {
    console.log( 'connected', this.styleSlugValue );
    console.log( 'connected2', this.styleDataValue );
  }
}
