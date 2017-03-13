/**
 * Payment pay
 *
 * @package dtbaker-elementor
 */


( function($) {

    var stripe_pub_key = 'pk_live_sHzlXhBGv3ySRTVbsCGkxcgd';
    var loading_stripe = false;
    var stripe_load_done = false;
    var purchasing_style = {};
    var $dialog = false;
    var stripe = false, card = false;

    function bind_stripe_elements(){

        stripe = Stripe(stripe_pub_key);
        var elements = stripe.elements();

        var style = {
            base: {
                color: '#32325d',
                lineHeight: '24px',
                fontFamily: 'Helvetica Neue',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };


        card = elements.create('card', {style: style});
        card.mount('.stripe-card-element');

        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

    }

    function stylepress_stripe_has_loaded(){

        loading_stripe = false;
        stripe_load_done = true;

        bind_stripe_elements();





    }

    function open_popup(){
        if($('#stylepress-payment-pop').length){
            $('#stylepress-payment-pop').remove();
        }
        $dialog = $('<div id="stylepress-payment-pop"></div>');
        $dialog.html( $('#tmpl-stylepress-payment-popup').html());

        $dialog.dialog({
            title: 'Purchase',
            dialogClass: 'wp-dialog',
            autoOpen: true,
            draggable: true,
            width: 'auto',
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
                var slider4 = new Slider(document.getElementById('stylepress-card-amount'), {
                    isOneWay: true,
                    start: parseInt( purchasing_style.stylecost ),
                    min: 1,
                    max: 40
                });
                slider4.subscribe('moving', function(data) {
                    document.getElementById('stylepress-amount-update').innerHTML = '$' + Math.round(data.right);
                    $('.stylepress-card-amount-value').val(Math.round(data.right));
                });
                document.getElementById('stylepress-amount-update').innerHTML = '$' + Math.round(slider4.getInfo().right);
                $('.stylepress-card-amount-value').val(parseInt( purchasing_style.stylecost ));

                var $btn = $dialog.find('.stylepress-final-purchase-button');
                $btn.text('Purchase & Install Style: ' + purchasing_style.stylename);
                $btn.click(function(){
                    $dialog.find('.stylepress-payment-popup').addClass('processing');
                    stripe.createToken(card).then(function(result) {
                        if (!result){
                            $dialog.find('.stylepress-payment-popup').removeClass('processing');
                            alert('Failed to process stripe');
                        }else if(result.error) {
                            // Inform the user if there was an error
                            $dialog.find('.stylepress-payment-popup').removeClass('processing');
                            var errorElement = document.getElementById('card-errors');
                            errorElement.textContent = result.error.message;
                        } else {
                            // Send the token to our stylepress server for processing.
                            var postdata = stylepress_payment || {};
                            postdata.token = result.token.id;
                            postdata.purchase = purchasing_style.styleslug;
                            postdata.name = $dialog.find('input[name="name"]').val();
                            postdata.email = $dialog.find('input[name="email"]').val();
                            postdata.amount = $dialog.find('input[name="amount"]').val();
                            $.ajax({
                                type: 'POST',
                                url: 'https://styleserver.stylepress.org/wp-admin/admin-ajax.php?action=stylepress_payment',
                                crossDomain: true,
                                data: postdata,
                                dataType: 'json',
                                success: function(responseData, textStatus, jqXHR) {
                                    if(responseData && responseData.success && responseData.data){
                                        // success! redirect to install page.

                                        // post our purchase token to the local wp install, we send this along with install requests to verify payment.
                                        $.post(ajaxurl, {
                                            'action': 'stylepress_purchase_complete',
                                            'payment': stylepress_payment,
                                            'server': responseData.data
                                        }, function (response) {

                                            if(!response.success){
                                                alert("failed to record local payment. Please contact dtbaker.");
                                            }

                                            window.location.href=purchasing_style.redirect;

                                        }, 'json').fail(function() {
                                            alert("Error saving local payment. Contact dtbaker.");
                                        });

                                    }else{
                                        $dialog.find('.stylepress-payment-popup').removeClass('processing');
                                        alert('Payment failed. ' + (typeof responseData.message != 'undefined' ? responseData.message : ''));
                                    }
                                },
                                error: function (responseData, textStatus, errorThrown) {
                                    $dialog.find('.stylepress-payment-popup').removeClass('processing');
                                    alert('Payment processing failed.');
                                }
                            });
                        }
                    });
                });


            },
            create: function () {
                // style fix for WordPress admin
                $dialog.find('.ui-dialog-titlebar-close').addClass('ui-button');
            }
        });
    }

    function stylepress_load_stripe(){
        if(loading_stripe){
            return;
        }
        if(stripe_load_done){
            stylepress_stripe_has_loaded();
        }
        loading_stripe = true;
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://js.stripe.com/v3/';
        document.head.appendChild(script);
        script.onload = stylepress_stripe_has_loaded;
    }

    $(function() {
        $('.button-stylepress-pay').click(function(e){
            e.preventDefault();

            $(this).addClass('paying');

            purchasing_style = $(this).data();
            purchasing_style.redirect = $(this).attr('href');

            open_popup();

            stylepress_load_stripe();

            return false;
        });
    });

} )(jQuery);

