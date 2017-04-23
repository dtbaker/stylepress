/**
 * Payment pay
 *
 * @package dtbaker-elementor
 */


( function($) {

    var $dialog = false;
    var current_modal = {};
    var $current_slidein;


    function close_slideins(){
        $(document).off('keyup.stylepressmodal');
        $('body').removeClass('showing_side_menu');
        $('#stylepressslideinstyles').remove();
        $current_slidein = null;
    }
    function show_slidein(slideinid){
        $slidein = $('.stylepress_slide_in_menu[data-id="' + slideinid + '"]');
        if($slidein.length) {

            // console.log($current_slidein);
            // console.log($slidein);
            // console.log($current_slidein == $slidein);
            if($current_slidein && $current_slidein[0] == $slidein[0]){
                close_slideins();
                return;
            }
            close_slideins();
            $current_slidein = $slidein;

            $(document).on('keyup.stylepressmodal',function(e) {
                if (e.keyCode == 27) { // escape key maps to keycode `27`
                    close_slideins();
                }
            });
            var size = $slidein.outerWidth();
            // var size = parseInt($slidein.data('size'));
            // if(!size)size = 400;
            // $('head').append('<style type="text/css" id="stylepressslideinstyles">body.showing_side_menu > *:not(.stylepress_slide_in_menu):not(#wpadminbar){transform: translateX(-' + size + 'px); }</style>');
            // account for sidebar padding
            // size += 60;
            $('head').append('<style type="text/css" id="stylepressslideinstyles">body > #site-offcanvas-wrap{left: -' + size + 'px; }</style>');
            $('.stylepress_slide_in_menu').removeClass('shown');
            $slidein.addClass('shown');
            $('body').addClass('showing_side_menu');
        }else{
            alert('Slide in failure');
        }
    }


    function open_popup(){

        if(typeof current_modal.display != 'undefined' && current_modal.display == 1){
            // we're showing a slide in
            show_slidein(current_modal.id);
            return;

        }
        // todo: push/pop active dialogs to support multiple.
        if($dialog){
            $dialog.dialog('close');
        }
        $dialog = $('#stylepress-modal-pop-' + current_modal.id);

        var width = '500px';
        if(typeof current_modal.settings != 'undefined' && typeof current_modal.settings.modal_width != 'undefined'){
            width = current_modal.settings.modal_width;
        }else if(typeof current_modal.width != 'undefined'){
            width = current_modal.width;
        }
        var px = parseInt(width);
        if(px > window.innerWidth){
            width =  ( window.innerWidth - 20 );
            width = width + "px";
        }

        $dialog.dialog({
            title: false,
            dialogClass: 'wp-dialog stylepress-modal', //stylepress-modal-loading
            autoOpen: true,
            draggable: true,
            autoResize:true,
            width: width,
            modal: true,
            resizable: false,
            closeOnEscape: true,
            position: {
                my: "center",
                at: "center",
                of: window
            },
            open: function () {
                // close dialog by clicking the overlay behind it
                $dialog.find('.ui-widget-overlay').bind('click', function(){
                    $dialog.dialog('close');
                });
                var $wrapper = $dialog.parents('.stylepress-modal');
                var currentTop = parseInt($wrapper.css('top'));
                if(currentTop <= 130){
                    $wrapper.css('top','130px');
                }

                // stylepress_datepicker();

                /*$.post(stylepress_modal.ajax_url, {
                    'action': 'stylepress_modal_pop',
                    'modal': current_modal
                }, function(response) {

                    $wrapper = $dialog.parents('.stylepress-modal');
                    $wrapper.removeClass('stylepress-modal-loading');
                    $dialog.find('.stylepress-modal-inner').html(response);

                    $dialog.dialog("option", "position", {
                        my: "center",
                        at: "center",
                        of: window
                    });

                }, 'html');*/

            },
            create: function () {
                // style fix for WordPress admin
                $dialog.find('.ui-dialog-titlebar-close').addClass('ui-button');
            }
        });
    }


    $('body').on('click','.elementor-widget-stylepress_modal_button, .elementor-widget-button, .elementor-widget-icon-box', function(e){

        var data = $(this).data('stylepressmodal');
        if(data) {
            var postdata = {
                id: data.id, //$(this).data('id'),
                settings: data
            };
            e.preventDefault();
            current_modal = postdata;
            open_popup();
            return false;
        }
    });
    $('body').on('click','a[data-stylepressmodal]', function(e){
        var postdata = $(this).data('stylepressmodal');
        e.preventDefault();
        current_modal = postdata;
        open_popup();
        return false;
    });

    $('body').on('click','.ui-widget-overlay',function(){
        if($dialog){
            $dialog.dialog('close');
        }
    });

    $('body').on('click','.close_sidebar',function(e){
        e.preventDefault();
        close_slideins();
        return false;
    });


} )(jQuery);

