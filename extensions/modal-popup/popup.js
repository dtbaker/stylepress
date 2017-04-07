/**
 * Payment pay
 *
 * @package dtbaker-elementor
 */


( function($) {

    var $dialog = false;
    var current_modal = {};

    function open_popup(){

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


    $(function() {
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

    });

} )(jQuery);

