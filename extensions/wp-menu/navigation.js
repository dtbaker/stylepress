/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens
 */
(function ( $ ) {
    $.fn.GenerateMobileMenu = function( options ) {
        // Set the default settings
        var settings = $.extend({
            menu: '.stylepress-main-navigation'
        }, options );

        // Bail if our menu doesn't exist
        if ( ! $( settings.menu ).length ) {
            return;
        }

        // Open the mobile menu
        $( this ).on( 'click', function( e ) {
            e.preventDefault();
            $( this ).closest( settings.menu ).toggleClass( 'toggled' );
            $( this ).closest( settings.menu ).attr( 'aria-expanded', $( this ).closest( settings.menu ).attr( 'aria-expanded' ) === 'true' ? 'false' : 'true' );
            $( this ).toggleClass( 'toggled' );
            $( this ).children( 'i' ).toggleClass( 'fa-bars' ).toggleClass( 'fa-close' );
            $( this ).attr( 'aria-expanded', $( this ).attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
            return false;
        });
    };


    $( document ).ready( function() {

        initDropDownMenu();
        // Initiate our mobile menu
        $( '.stylepress-main-navigation .stylepress-menu-toggle' ).GenerateMobileMenu();

        // Build the mobile button that displays the dropdown menu
        $( document ).on( 'click', 'nav .dropdown-menu-toggle', function( e ) {
            e.preventDefault();
            var _this = $( this );
            var mobile = $( '.stylepress-menu-toggle' );
            var slideout = $( '.slideout-navigation' );

            if ( mobile.is( ':visible' ) || 'visible' == slideout.css( 'visibility' ) ) {
                _this.closest( 'li' ).toggleClass( 'sfHover' );
                _this.parent().next( '.children, .sub-menu' ).toggleClass( 'toggled-on' );
                _this.attr( 'aria-expanded', $( this ).attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
            }
            return false;
        });

        // Display the dropdown on click if the item URL doesn't go anywhere
        $( document ).on( 'click', '.main-nav .menu-item-has-children > a', function( e ) {
            var _this = $( this );
            var mobile = $( '.stylepress-menu-toggle' );
            var slideout = $( '.slideout-navigation' );
            var url = _this.attr( 'href' );
            if ( '#' == url || '' == url ) {
                if ( mobile.is( ':visible' ) || 'visible' == slideout.css( 'visibility' ) ) {
                    e.preventDefault();
                    _this.closest( 'li' ).toggleClass( 'sfHover' );
                    _this.next( '.children, .sub-menu' ).toggleClass( 'toggled-on' );
                    _this.attr( 'aria-expanded', $( this ).attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
                    return false;
                }
            }
        });
    });


    var menu_dropdown_height_set = false;




    function initDropDownMenu(){
        "use strict";

        var menu_items = $('.drop_down > ul > li');
        console.log(menu_items);

        menu_items.each( function(i) {
            if ($(menu_items[i]).find('.second').length > 0) {
                if($(menu_items[i]).hasClass('wide')){
                    var dropdown = $(this).find('.inner > ul');
                    var dropdownPadding = parseInt(dropdown.css('padding-left').slice(0, -2)) + parseInt(dropdown.css('padding-right').slice(0, -2));

                    if(!$(this).hasClass('left_position') && !$(this).hasClass('right_position')){
                        $(this).find('.second').css('left',0);
                    }

                    var tallest = 0;
                    $(this).find('.second > .inner > ul > li').each(function() {
                        var thisHeight = $(this).height();
                        if(thisHeight > tallest) {
                            tallest = thisHeight;
                        }
                    });

                    $(this).find('.second > .inner > ul > li').height(tallest);

                    var row_number;
                    if($(this).find('.second > .inner > ul > li').length > 4){
                        row_number = 4;
                    }else{
                        row_number = $(this).find('.second > .inner > ul > li').length;
                    }

                    var width = row_number*($(this).find('.second > .inner > ul > li').outerWidth());
                    $(this).find('.second > .inner > ul').width(width);

                    if(!$(this).hasClass('wide_background')){
                        if(!$(this).hasClass('left_position') && !$(this).hasClass('right_position')){
                            var left_position = ($(window).width() - 2 * ($(window).width()-$(this).find('.second').offset().left))/2 + (width+dropdownPadding)/2;
                            $(this).find('.second').css('left',-left_position);
                        }
                    } else{
                        if(!$(this).hasClass('left_position') && !$(this).hasClass('right_position')){
                            var left_position = $(this).find('.second').offset().left;
                            $(this).find('.second').css('left',-left_position);
                            $(this).find('.second').css('width',$(window).width());
                        }
                    }
                }

                if(!menu_dropdown_height_set){
                    $(menu_items[i]).data('original_height', $(menu_items[i]).find('.second').height() + 'px');
                    $(menu_items[i]).find('.second').height(0);
                }

                if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                    $(menu_items[i]).on("touchstart mouseenter",function(){
                        $(menu_items[i]).find('.second').css({'height': $(menu_items[i]).data('original_height'), 'overflow': 'visible', 'visibility': 'visible', 'opacity': '1'});
                    }).on("mouseleave", function(){
                        $(menu_items[i]).find('.second').css({'height': '0px','overflow': 'hidden', 'visivility': 'hidden', 'opacity': '0'});
                    });

                }else{
                    var config = {
                        interval: 0,
                        over: function(){
                            setTimeout(function() {
                                $(menu_items[i]).find('.second').addClass('drop_down_start');
                                $(menu_items[i]).find('.second').stop().css({'height': $(menu_items[i]).data('original_height')});
                            }, 150);
                        },
                        timeout: 150,
                        out: function(){
                            $(menu_items[i]).find('.second').stop().css({'height': '0px'});
                            $(menu_items[i]).find('.second').removeClass('drop_down_start');
                        }
                    };
                    $(menu_items[i]).hoverIntent(config);
                }
            }
        });
        $('.drop_down ul li.wide ul li a, .drop_down ul li.narrow ul li a').on('click',function(){
            var $this = $(this);

            if(!$this.next('ul').length && ($this.attr('href') !== "http://#") && ($this.attr('href') !== "#") && !$this.hasClass('no_link')) {
                setTimeout(function() {
                    $this.mouseleave();
                }, 500);
            }
        });

        menu_dropdown_height_set = true;
    }



}( jQuery ));

