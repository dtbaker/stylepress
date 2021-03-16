import { modal } from './modal';

class Error {
  constructor() {}

  pageLoaded = () => {}

  displayError = ( title, message, debug, reactivate, tryAgain ) => {
    if ( false !== reactivate ) {
      reactivate = true;
    }
    modal.closeModal();
    modal.openModal({
      title,
      message,
      debug,
      tryAgain,
      reactivate
    });
  }
}

export const error = new Error();
