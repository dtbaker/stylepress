/**
 * Frontend Elementor Tweaks
 *
 * @package stylepress
 */

(function ($) {

  $(window).on('elementor:init', function () {
    console.log && console.log('Welcome to StylePress');
  });

  $('body').on('change', 'select[data-setting="dynamic_field_value"]', function () {
    $('#stylepress-dynamic-code').text($(this).val() ? '{{' + $(this).val() + '}}' : '');
  });

})(jQuery);
