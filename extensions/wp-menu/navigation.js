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

    var menuactiontimeouts = 140;
    var current_slider_parent_node;
    var $current_slider_slideout_node;
    var is_inside, is_inside_slideout;
    function close_nav_slideouts(){
        $('.stylepress-nav-slideout').removeClass('visible');
        if(current_slider_parent_node){
            current_slider_parent_node.removeClass('currently-active');
        }
        current_slider_parent_node = null;
        if($current_slider_slideout_node) {
            $current_slider_slideout_node.off('mouseenter.stylepress').off('mouseleave.stylepress');
        }
        $current_slider_slideout_node = null;
    }
    var check_timeout;
    function check_if_we_should_close_nav(){
        if(check_timeout){
            clearTimeout(check_timeout);
        }
        check_timeout = setTimeout(function(){
            if(!is_inside){
                close_nav_slideouts();
            }
        }, menuactiontimeouts);
    }

    var check_slideout_timeout;
    function check_if_we_should_close_slideout(){
        if(check_slideout_timeout){
            clearTimeout(check_slideout_timeout);
        }
        check_slideout_timeout = setTimeout(function(){
            if(!is_inside_slideout){
                close_nav_slideouts();
            }else{
                setTimeout(check_if_we_should_close_slideout, menuactiontimeouts);
            }
        }, menuactiontimeouts);
    }

    $( document ).ready( function() {

        setTimeout( function(){
            $('.stylepress-nav-menu').each(function(){
                $(this).find('.stylepress-nav-menuitems').each(function(){
                    $(this).find('.stylepress-nav-slideouts').css('top',$(this).find('.stylepress-main-navigation').height() + 'px');
                });
                var bar_height = $(this).height();
                if($(this).hasClass('fixed')){
                    $(this).parent().find('.stylepress-nav-menu-placeholder').first().css('height', bar_height + 'px');
                }
            });
            $('body').addClass('stylepress-navigation-loaded');
        }, 500 );
        // Initiate the desktop hover element
        $('.stylepress-nav-menu').on('mouseenter',function(){
            is_inside = true;
        }).on('mouseleave',function(){
            is_inside = false;
            check_if_we_should_close_nav();
        });
        $('.stylepress_menu li.menu-item').on('mouseenter',function(){
            var $t = $(this);
            if( $t.hasClass('stylepress_has_navslide')) {
                is_inside_slideout = true;
                if( current_slider_parent_node != $t ) {
                    close_nav_slideouts();
                    var slideoutid = $t.data('stylepressslideout');
                    if (slideoutid) {
                        $t.addClass('currently-active');
                        current_slider_parent_node = $t;
                        $current_slider_slideout_node = $('.stylepress-nav-slideout[data-id="' + slideoutid + '"]');
                        $current_slider_slideout_node.addClass('visible');
                        $current_slider_slideout_node.on('mouseenter.stylepress',function(){
                            is_inside_slideout = true;
                        }).on('mouseleave.stylepress',function(){
                            is_inside_slideout = false;
                            check_if_we_should_close_slideout();
                        });
                    }
                }
            }else{
                is_inside_slideout = false;
                check_if_we_should_close_slideout();

            }
        }).on('touchstart', function (e) {

            /// todo:

            'use strict'; //satisfy code inspectors
            var link = $(this); //preselect the link
            if (link.hasClass('already-touched')) {
                return true;
            } else {
                link.addClass('already-touched');
                $('a.taphover').not(this).removeClass('hover');
                e.preventDefault();
                return false; //extra, and to make sure the function has consistent return points
            }
        });


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




}( jQuery ));

