/**
 * Frontend Elementor Tweaks
 *
 * @package dtbaker-elementor
 */


( function($) {

    function do_email_subscribe(){
        var $parent = $(this).parent();
        var elm = $parent.data('elm');
        var post = $parent.data('post');
        var email = $parent.find('.stylepress-subscribe-email').val();
        $parent.find('.stylepress-email-status').text('');
        $parent.addClass('submitting');

        $.post(stylepress_email.ajax_url, {
            'action': 'stylepress_email_sub',
            'email': email,
            'post': post,
            'elm': elm
        }, function(response) {

            $parent.removeClass('submitting').addClass('submitted');
            if( response && response.success){
                $parent.addClass('success');
            }else{
                $parent.addClass('failure');
            }
            if( response && response.data){
                $parent.find('.stylepress-email-status').text(response.data);
            }
        }, 'json');
    }


    $(function() {
        $('.stylepress-subscribe-send').click(do_email_subscribe);
        $('.stylepress-subscribe-email').on('keyup', function (event) {
            if (event.keyCode == 13) {
                do_email_subscribe.call(this);
            }
        });
    });

} )(jQuery);
