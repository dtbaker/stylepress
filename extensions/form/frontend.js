function stylepress_datepicker(){
    if(jQuery.fn.datepicker) {
        jQuery(".stylepress-datepicker").removeAttr('id').datepicker({
            //comment the beforeShow handler if you want to see the ugly overlay
            beforeShow: function() {
                setTimeout(function(){
                    jQuery('.ui-datepicker').css('z-index', 999999);
                }, 0);
            }
        });
    }
}
jQuery(document).ready(function($) {
    stylepress_datepicker();
});