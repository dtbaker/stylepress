"use strict";

window.console = window.console || {
        log: function () {
        }, debug: function () {
        }
    };

var dtbakerPageSlider = {
    init: function ($) {
        if ($().slick) {

            var $carousel = $('.elementor-page-carousel');
            if ($carousel.length) {

                $carousel.each(function () {
                    var savedOptions = $(this).data('slider_options'),
                        tabletSlides = 1 === savedOptions.slidesToShow ? 1 : 2,
                        defaultOptions = {
                            responsive: [
                                {
                                    breakpoint: 767,
                                    settings: {
                                        slidesToShow: tabletSlides,
                                        slidesToScroll: tabletSlides
                                    }
                                },
                                {
                                    breakpoint: 480,
                                    settings: {
                                        slidesToShow: 1,
                                        slidesToScroll: 1
                                    }
                                }
                            ]
                        },
                        slickOptions = $.extend({}, defaultOptions, savedOptions);
                    $(this).slick(slickOptions);
                    if( typeof savedOptions.dtbaker_type !== 'undefined'  && 'icons' === savedOptions.dtbaker_type){
                        $('.elementor-page-carousel-icons [data-slide-id]').first().addClass('current');
                        var $thisslick = $(this);
                        var current_slide = 0;
                        (function(){
                            var $clickslick = $thisslick;
                            $('.elementor-page-carousel-icons [data-slide-id]').click(function(){
                                //var current_slide = parseInt($clickslick.slick('slickCurrentSlide'));
                                var next_slide = parseInt($(this).index());
                                if(current_slide === next_slide){
                                    // user has clicked twice on the icon button
                                    // redirect
                                    var url = $(this).data('link');
                                    if( url && '#' !== url ){
                                        window.location.href=url;
                                        return;
                                    }
                                }
                                $clickslick.slick('slickGoTo', next_slide );
                            });
                        }());
                        $(this).on('beforeChange', function(events, slick, currentSlide, nextSlide){
                            // grab the next html id of the slide.
                            var next_id = $(slick['$slides'][nextSlide]).data('slide-id');
                            $('.elementor-page-carousel-icons .current').removeClass('current');
                            $('.elementor-page-carousel-icons [data-slide-id="' + next_id + '"]').addClass('current');
                        });
                        $(this).on('afterChange', function(events, slick, currentSlide, nextSlide){
                            current_slide = currentSlide;
                        });
                    }

                });
            }
        }
    }

};

jQuery(function ($) {
    dtbakerPageSlider.init($);
});
