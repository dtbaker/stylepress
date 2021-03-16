export default ( btn ) => {
  const $button = jQuery( btn );
  const existingWidth = $button.outerWidth();
  const loadingText = '⡀⡀⡀⡀⡀⡀⡀⡀⡀⡀⠄⠂⠁⠁⠂⠄';
  const animationIndex = [ 0, 1, 2 ];
  const elementTypeSelector = $button.is( 'input' ) || $button.is( 'button' ) ? 'val' : 'text';
  const existing_text = $button[elementTypeSelector]();
  let completed = false;

  $button[elementTypeSelector]( loadingText );
  $button.css( 'width', existingWidth );

  // $button.attr('disabled',true);


  // animate the text indent
  const moo = () => {
    if ( completed ) {
      return;
    }
    let currentText = '';

    // increase each index up to the loading length
    for ( let i = 0; i < animationIndex.length; i++ ) {
      animationIndex[i] = animationIndex[i] + 1;
      if ( animationIndex[i] >= loadingText.length ) {
        animationIndex[i] = 0;
      }
      currentText += loadingText.charAt( animationIndex[i]);
    }
    $button[elementTypeSelector]( currentText );
    setTimeout( moo, 60 );
  };

  moo();

  return {
    done: function() {
      completed = true;
      $button[elementTypeSelector]( existing_text );
      $button.attr( 'disabled', false );
    }
  };
};
