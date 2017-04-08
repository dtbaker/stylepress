/**
 * Frontend CSS Editor
 *
 * @package dtbaker-elementor
 */


( function($) {

    function async(u, c) {
        var d = document, t = 'script',
            o = d.createElement(t),
            s = d.getElementsByTagName(t)[0];
        o.src = '//' + u;
        if (c) { o.addEventListener('load', function (e) { c(null, e); }, false); }
        s.parentNode.insertBefore(o, s);
    }

    var css_editor_active = false;

    var $editor_holder,$showhide,$savebutton,ace_editor;

    function save_css(){

    }

    function ace_loaded_show_editor(){

        if(!$editor_holder){
            $editor_holder = $('<div id="stylepress_csser"></div>');
            $showhide = $('<a href="#" class="stylepress-csser-showhide">Show/Hide</a>');
            $showhide.click(function(e){
                e.preventDefault();
                toggle_css_editor();
                return false;
            });
            $savebutton = $('<a href="#" class="stylepress-csser-save">Save CSS</a>');
            $savebutton.click(function(e){
                e.preventDefault();
                save_css();
                return false;
            });
            $editor_holder.append('<div class="stylepress-css-info"></div>');
            var $buttons = $('<div class="stylepress-css-buttons"></div>');
            $buttons.append($showhide);
            $buttons.append($savebutton);
            $editor_holder.append($buttons);
            $editor_holder.append('<pre id="stylepress_css" class="stylepress-css-box">/* loading */</pre>');
            $('#wpadminbar').after($editor_holder);
        }

        $('html').addClass('stylepress-csser');


        if(!ace_editor) {
            // load in the css content from ajax, then init the editor
            $.post(stylepress_css.ajaxurl,{
                nonce: stylepress_css.nonce,
                action: 'stylepress_get_css',
                style_id: stylepress_css.style_id,
                post_id: stylepress_css.post_id
            },function(response){
                if(response && response.success && response.data){
                    $('#stylepress_css').text(response.data.css);
                    ace_editor = ace.edit("stylepress_css");
                    ace_editor.setTheme("ace/theme/monokai");
                    ace_editor.getSession().setMode("ace/mode/css");
                    ace_editor.getSession().on('change', function(e) {
                        // e.type, etc
                    });
                }else{
                    $('#stylepress_css').text('Failed to load css');
                }
            });
        }
        css_editor_active = true;

    }


    function toggle_css_editor(){

        if(!css_editor_active){
            // show it
            if(typeof ace !== 'undefined'){
                ace_loaded_show_editor();
            }else{
                async('cdn.jsdelivr.net/ace/1.2.6/noconflict/ace.js', function() {
                    ace_loaded_show_editor();
                });
            }
        }else{
            // hide it
            $('html').removeClass('stylepress-csser');
            css_editor_active = false;
        }
    }



    $('body').on('click','#wp-admin-bar-stylepress_navc a',function(e){
        e.preventDefault();
        toggle_css_editor();
        return false;
    });

} )(jQuery);
