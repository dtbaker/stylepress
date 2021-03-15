import { Controller } from 'stimulus';


const dtbaker_loading_button = (btn) => {
  var $button = jQuery(btn);
  var existing_width = $button.outerWidth();
  var loading_text = '⡀⡀⡀⡀⡀⡀⡀⡀⡀⡀⠄⠂⠁⠁⠂⠄';
  var completed = false;

  $button.css('width',existing_width);
  var _modifier = $button.is('input') || $button.is('button') ? 'val' : 'text';
  var existing_text = $button[_modifier]();
  $button[_modifier](loading_text);
  // $button.attr('disabled',true);

  var anim_index = [0,1,2];

  // animate the text indent
  function moo() {
    if (completed)return;
    var current_text = '';
    // increase each index up to the loading length
    for(var i = 0; i < anim_index.length; i++){
      anim_index[i] = anim_index[i]+1;
      if(anim_index[i] >= loading_text.length)anim_index[i] = 0;
      current_text += loading_text.charAt(anim_index[i]);
    }
    $button[_modifier](current_text);
    setTimeout(function(){ moo();},60);
  }

  moo();

  return {
    done: function(){
      completed = true;
      $button[_modifier](existing_text);
      $button.attr('disabled',false);
    }
  }
}

export default class extends Controller {
  static values = {
    step: String,
    nextUrl: String,
    prevUrl: String,
    ajaxEndpoint: String,
    ajaxNonce: String
  }

  static targets = ['errorMessage','nextButton','backButton']

  connect(){
    this.stepRequirements = []
    this.setupNavButtons()
  }

  setErrorMessage (message){
    this.errorMessageTarget.innerText = message
  }

  clearErrorMessage (){
    this.errorMessageTarget.innerText = ''
  }

  registerStepRequirement(callback) {
    this.stepRequirements.push(callback)
  }

  setupNavButtons() {
    const nextStepUrl = this.nextUrlValue
    const prevStepUrl = this.prevUrlValue
    this.nextButtonTarget.addEventListener('click', (e) => {
      e.preventDefault()
      this.clearErrorMessage()
      const loadingButton = dtbaker_loading_button(this.nextButtonTarget)
      const requirementPromises = this.stepRequirements.map(callback => {
        return callback()
      })
      Promise.all(requirementPromises)
        .then((results) => {
          let nextStepUrlWithQueryParams = nextStepUrl
          results.forEach(result => {
            if(result.args){
              for (const [key, value] of Object.entries(result.args)) {
                nextStepUrlWithQueryParams = `${nextStepUrlWithQueryParams}&${key}=${value}`
              }
            }
          })
          window.location.href = nextStepUrlWithQueryParams
        })
        .catch((err) => {
          this.setErrorMessage(err.toString())
          loadingButton.done()
        })
      return false
    });
    this.backButtonTarget.addEventListener('click', function(e){
      e.preventDefault()
      dtbaker_loading_button(this.backButtonTarget);
      window.location.href = prevStepUrl
      return false
    });
  }
}
