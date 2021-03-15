import { Controller } from 'stimulus';


const dtbaker_loading_button = (btn) => {
  var $button = jQuery(btn);
  if($button.data('done-loading') === 'yes')return false;
  var existing_text = $button.text();
  var existing_width = $button.outerWidth();
  var loading_text = '⡀⡀⡀⡀⡀⡀⡀⡀⡀⡀⠄⠂⠁⠁⠂⠄';
  var completed = false;

  $button.css('width',existing_width);
  var _modifier = $button.is('input') || $button.is('button') ? 'val' : 'text';
  $button[_modifier](loading_text);
  // $button.attr('disabled',true);
  $button.data('done-loading','yes');

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

  static targets = ['nextButton','backButton']

  connect(){
    this.setupNavButtons()
  }

  setupNavButtons() {
    const nextStepUrl = this.nextUrlValue
    const prevStepUrl = this.prevUrlValue
    this.nextButtonTarget.addEventListener('click', function(e){
      dtbaker_loading_button(this);
      window.location.href = nextStepUrl
    });
    this.backButtonTarget.addEventListener('click', function(e){
      e.preventDefault()
      dtbaker_loading_button(this);
      window.location.href = prevStepUrl
      return false
    });
  }
}
