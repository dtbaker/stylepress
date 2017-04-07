

( function($) {

    $( window ).on( 'elementor:init', function() {
        elementor.hooks.addFilter( 'elementor_pro/forms/content_template/field/stylepress-datepicker', function( html, item, i, settings ) {
            var placeholder = 'placeholder="Choose Date"';
            // if ( item.placeholder ) {
            //     placeholder = 'placeholder="' + _.escape( item.placeholder ) + '"';
            // }
            var itemClasses = _.escape( item.css_classes );

            var required = '';
            if ( item.required ) {
                required = 'required';
            }

            var inputField = '<input size="1" type="text" class="elementor-field stylepress-datepicker elementor-field-textual elementor-size-' + settings.input_size + ' ' + itemClasses + '" name="form_field_' + i + '" id="form_field_' + i + '" ' + required + ' ' + placeholder + ' >';
            return inputField;

        } );
        elementor.hooks.addFilter( 'elementor_pro/forms/content_template/field/stylepress-description', function( html, item, i, settings ) {

            var itemClasses = _.escape( item.css_classes );

            var inputField = '<div class="elementor-field stylepress-description elementor-size-' + settings.input_size + ' ' + itemClasses + '" id="form_field_' + i + '"> (description </div>';
            return inputField;

        } );

    });

} )(jQuery);