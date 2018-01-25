/**
 * Manage pageblock sort & delete
 */

/* global jQuery */

jQuery(document).ready(function($) {
  var pageblockContainer = $('.js-pageblock-container')

  /**
   * Delete a page block
   */
  $(document).on('click', '.js-delete-pageblock', function() {
    var target = $(this).data('target')
    var confirm = window.confirm('Are you sure you want to delete this block?')
    if (confirm) {
      $(target).remove()
      updateIndexes()
    }
  })

  pageblockContainer.sortable({
    axis: 'y',
    handle: '.page-block-header',
    helper: 'clone',
    placeholder: 'sortable-placeholder',
    stop: function(event, ui) {
      updateIndexes()
      reInitEditors()
    }
  })

  function updateIndexes() {
    var idList = pageblockContainer.sortable('toArray')
    $.each(idList, function(index, id) {
      var self = $('#' + id)
      // Find children
      var input = self.find('input, textarea, select')
      // Loop inputs and change index
      input.each(function(i, el) {
        var oldName = $(this).attr('name')
        if (oldName !== undefined) {
          $(this).attr('name', oldName.replace(/\[(.+?)\]/, '[' + index + ']'))
        }
      })
    })
  }

  function reInitEditors() {
    pageblockContainer.find('.js-editor').each(function(i, editor) {
      wp.editor.remove(editor.id)
      wp.editor.initialize(editor.id, {
        tinymce: true
      })
    })
  }
})
