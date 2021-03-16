import { Controller } from 'stimulus';
import loadingButton from 'lib/loading_button';

export default class extends Controller {
  static values = {
    step: String,
    nextUrl: String,
    prevUrl: String,
    ajaxEndpoint: String,
    ajaxNonce: String
  }

  static targets = [ 'errorMessage', 'nextButton', 'backButton' ]

  connect() {
    this.stepRequirements = [];
    this.setupNavButtons();
  }

  setErrorMessage( message ) {
    this.errorMessageTarget.innerText = message;
  }

  clearErrorMessage() {
    this.errorMessageTarget.innerText = '';
  }

  registerStepRequirement( callback ) {
    this.stepRequirements.push( callback );
  }

  setupNavButtons() {
    const nextStepUrl = this.nextUrlValue;
    const prevStepUrl = this.prevUrlValue;
    this.nextButtonTarget.addEventListener( 'click', ( e ) => {
      e.preventDefault();
      this.clearErrorMessage();
      const button = loadingButton( this.nextButtonTarget );
      const requirementPromises = this.stepRequirements.map( callback => {
        return callback();
      });
      Promise.all( requirementPromises )
        .then( ( results ) => {
          let nextStepUrlWithQueryParams = nextStepUrl;
          results.forEach( result => {
            if ( result.args ) {
              for ( const [ key, value ] of Object.entries( result.args ) ) {
                nextStepUrlWithQueryParams = `${nextStepUrlWithQueryParams}&${key}=${value}`;
              }
            }
          });
          window.location.href = nextStepUrlWithQueryParams;
        })
        .catch( ( err ) => {
          this.setErrorMessage( err.toString() );
          button.done();
        });
      return false;
    });
    this.backButtonTarget.addEventListener( 'click', function( e ) {
      e.preventDefault();
      loadingButton( this.backButtonTarget );
      window.location.href = prevStepUrl;
      return false;
    });
  }
}
