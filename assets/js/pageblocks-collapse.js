/**
 * Toggle page block collapse
 */

/* global jQuery */
jQuery(document).ready(function ($) {
  $(document).on('click', '.js-toggle-pageblock-collapse', function () {
    var target = $(this).data('target')
    $(target).toggleClass('page-block--collapsed')
  })
})
