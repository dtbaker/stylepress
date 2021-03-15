import { Controller } from 'stimulus';

export default class extends Controller {
  static values = {
    styleSlug: String,
    styleHash: String
  }
  connect(){
    console.log('connected', this.styleSlugValue)
  }
}
