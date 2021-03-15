var EnvatoWizard = (function($){

  var t;

  // callbacks from form button clicks.
  var callbacks = {
    install_plugins: function(btn){
      var plugins = new PluginManager();
      plugins.init(btn);
    },
    install_content: function(btn){
      var content = new ContentManager();
      content.init(btn);
    }
  };

  function window_loaded(){
    // init button clicks:
    $('.button-upload').on( 'click', function(e) {
      e.preventDefault();
      renderMediaUploader();
    });

  }

  function loading_content(){
    // $('.stylepress-content').block({
    //   message: null,
    //   overlayCSS: {
    //     background: '#fff',
    //     opacity: 0.6
    //   }
    // });
  }

  function PluginManager(){

    var complete;
    var items_completed = 0;
    var current_item = '';
    var $current_node;
    var current_item_hash = '';

    function ajax_callback(response){
      if(typeof response == 'object' && typeof response.message != 'undefined'){
        $current_node.find('span').text(response.message);
        if(typeof response.url != 'undefined'){
          // we have an ajax url action to perform.

          if(response.hash === current_item_hash){
            $current_node.find('span').text("failed");
            find_next();
          }else {
            current_item_hash = response.hash;
            jQuery.post(response.url, response, function(response2) {
              process_current();
              $current_node.find('span').text(response.message + stylepress_wizard.verify_text);
            }).fail(ajax_callback);
          }

        }else if(typeof response.done != 'undefined'){
          // finished processing this plugin, move onto next
          find_next();
        }else{
          // error processing this plugin
          find_next();
        }
      }else{
        // error - try again with next plugin
        $current_node.find('span').text("ajax error");
        find_next();
      }
    }
    function process_current(){
      if(current_item){
        // query our ajax handler to get the ajax to send to TGM
        // if we don't get a reply we can assume everything worked and continue onto the next one.
        jQuery.post(stylepress_wizard.ajaxurl, {
          action: 'stylepress_setup_plugins',
          wpnonce: stylepress_wizard.wpnonce,
          slug: current_item
        }, ajax_callback).fail(ajax_callback);
      }
    }
    function find_next(){
      var do_next = false;
      if($current_node){
        if(!$current_node.data('done_item')){
          items_completed++;
          $current_node.data('done_item',1);
        }
        $current_node.find('.spinner').css('visibility','hidden');
      }
      var $li = $('.stylepress-wizard-plugins li');
      $li.each(function(){
        if(current_item === '' || do_next){
          current_item = $(this).data('slug');
          $current_node = $(this);
          process_current();
          do_next = false;
        }else if($(this).data('slug') === current_item){
          do_next = true;
        }
      });
      if(items_completed >= $li.length){
        // finished all plugins!
        complete();
      }
    }

    return {
      init: function(btn){
        $('.stylepress-wizard-plugins').addClass('installing');
        complete = function(){
          loading_content();
          //window.location.href=btn.href;
        };
        find_next();
      }
    }
  }

  function ContentManager(){

    var complete;
    var items_completed = 0;
    var current_item = '';
    var $current_node;
    var current_item_hash = '';

    function ajax_callback(response) {
      if(typeof response == 'object' && typeof response.message != 'undefined'){
        $current_node.find('span').text(response.message);
        if(typeof response.url != 'undefined'){
          // we have an ajax url action to perform.
          if(response.hash === current_item_hash){
            $current_node.find('span').text("failed");
            find_next();
          }else {
            current_item_hash = response.hash;
            jQuery.post(response.url, response, ajax_callback).fail(ajax_callback); // recuurrssionnnnn
          }
        }else if(typeof response.done != 'undefined'){
          // finished processing this plugin, move onto next
          find_next();
        }else{
          // error processing this plugin
          find_next();
        }
      }else{
        // error - try again with next plugin
        $current_node.find('span').text("ajax error");
        find_next();
      }
    }

    function process_current(){
      if(current_item){

        var $check = $current_node.find('input:checkbox');
        if($check.is(':checked')) {
          console.log("Doing 2 "+current_item);
          // process htis one!
          jQuery.post(stylepress_wizard.ajaxurl, {
            action: 'stylepress_setup_content',
            wpnonce: stylepress_wizard.wpnonce,
            content: current_item
          }, ajax_callback).fail(ajax_callback);
        }else{
          $current_node.find('span').text("Skipping");
          setTimeout(find_next,300);
        }
      }
    }
    function find_next(){
      var do_next = false;
      if($current_node){
        if(!$current_node.data('done_item')){
          items_completed++;
          $current_node.data('done_item',1);
        }
        $current_node.find('.spinner').css('visibility','hidden');
      }
      var $items = $('tr.stylepress_default_content');
      var $enabled_items = $('tr.stylepress_default_content input:checked');
      $items.each(function(){
        if (current_item === '' || do_next) {
          current_item = $(this).data('content');
          $current_node = $(this);
          process_current();
          do_next = false;
        } else if ($(this).data('content') === current_item) {
          do_next = true;
        }
      });
      if(items_completed >= $items.length){
        // finished all items!
        complete();
      }
    }

    return {
      init: function(btn){
        $('.stylepress-pages').addClass('installing');
        $('.stylepress-pages').find('input').prop("disabled", true);
        complete = function(){
          loading_content();
          window.location.href=btn.href;
        };
        find_next();
      }
    }
  }

  /**
   * Callback function for the 'click' event of the 'Set Footer Image'
   * anchor in its meta box.
   *
   * Displays the media uploader for selecting an image.
   *
   * @since 0.1.0
   */
  function renderMediaUploader() {
    'use strict';

    var file_frame, attachment;

    if ( undefined !== file_frame ) {
      file_frame.open();
      return;
    }

    file_frame = wp.media.frames.file_frame = wp.media({
      title: 'Upload Logo',//jQuery( this ).data( 'uploader_title' ),
      button: {
        text: 'Select Logo' //jQuery( this ).data( 'uploader_button_text' )
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      attachment = file_frame.state().get('selection').first().toJSON();

      jQuery('.site-logo').attr('src',attachment.url);
      jQuery('#new_logo_id').val(attachment.id);
      // Do something with attachment.id and/or attachment.url here
    });
    // Now display the actual file_frame
    file_frame.open();

  }

  return {
    init: function(){
      t = this;
      $(window_loaded);
    },
    callback: function(func){
      console.log(func);
      console.log(this);
    }
  }

})(jQuery);


EnvatoWizard.init();
