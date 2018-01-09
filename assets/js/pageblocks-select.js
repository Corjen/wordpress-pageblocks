/**
 * Watch page blocks select
 */

/* global jQuery, ajaxurl, cuisineInitMediaFields, wp */
jQuery(document).ready(function($) {
  var container = $('.js-pageblock-container')

  $(document).on('change', '.js-select-pageblock', function() {
    var val = $(this).val()
    var count = $('.page-block').length

    $(this).val('')

    if (val !== '') {
      $.ajax({
        url: ajaxurl,
        data: {
          action: 'get_page_block',
          type: val,
          count: count
        }
      })
        .success(function(response) {
          container.append(response)
          cuisineInitMediaFields()
          container.find('.js-editor').each(function(i, editor) {
            wp.editor.initialize(editor.id)
          })
        })
        .error(function(error) {
          container.append(error.responseText)
          console.error(error)
        })
    }
  })
})
