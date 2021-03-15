import { Controller } from 'stimulus';

export default class extends Controller {
  static values = {
    styleSlug: String
  }
  static targets = ['styleSelector']

  connect(){
    console.log(this.styleSelectorTargets)
    const wizardManager = this.application.controllers.find(controller => controller.context.identifier === 'wizard-manager')
    wizardManager.registerStepRequirement(this.checkStep.bind(this))
  }

  async checkStep () {
    if(!this.styleSlugValue){
      throw new Error("Please select a style")
    }
    return {
      args: {
        remote_style_slug: this.styleSlugValue
      }
    }
  }

  setStyle(e){
    this.styleSlugValue = e.currentTarget.getAttribute('data-style')
    for(const style of this.styleSelectorTargets){
      if(style.getAttribute('data-style') === this.styleSlugValue){
        style.classList.add('stylepress-setup-wizard__style--current')
      }else{
        style.classList.remove('stylepress-setup-wizard__style--current')
      }
    }
  }
}
