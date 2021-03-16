import { Controller } from 'stimulus';

export default class extends Controller {
  static values = {
    styleSlug: String
  }
  static targets = [ 'styleSelector' ]

  connect() {
    const wizardManager = this.application.controllers.find( controller => 'wizard' === controller.context.identifier );
    wizardManager.registerStepRequirement( this.checkStep.bind( this ) );
  }

  checkStep() {
    return new Promise( ( resolve, reject ) => {
      if ( ! this.styleSlugValue ) {
        reject( 'Please select a style' );
      }
      resolve({
        args: {
          remote_style_slug: this.styleSlugValue
        }
      });
    });
  }

  setStyle( e ) {
    this.styleSlugValue = e.currentTarget.getAttribute( 'data-style' );
    for ( const style of this.styleSelectorTargets ) {
      if ( style.getAttribute( 'data-style' ) === this.styleSlugValue ) {
        style.classList.add( 'stylepress-setup-wizard__style--current' );
      } else {
        style.classList.remove( 'stylepress-setup-wizard__style--current' );
      }
    }
  }
}
