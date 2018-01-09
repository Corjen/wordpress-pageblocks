<?php

namespace Pageblocks;

class Helpers {

  var $blocks = array();

  public function __construct ( $blocks ) {
    $this->blocks = $blocks;
  }

  public function getOpeningPageBlockElement( $count = 0, $type = '', $collapsed, $title = '' ) {
    $uniqid = uniqid();
    $collapsedClass = $collapsed ? 'page-block--collapsed' : '';
    $html = '<div id="' . $uniqid . '" class="page-block ' . $collapsedClass . '">';
    $html .= $this->_getPageBlockHeader( $count, $type, $uniqid, $title );
    return $html;
  }

  private function _getPageBlockHeader( $count, $type, $uniqid, $title ) {
    $thumbnail = isset( $this->blocks[$type]['thumbnail'] ) ? $this->blocks[$type]['thumbnail'] : '';

    $html = '<header class="page-block-header js-toggle-pageblock-collapse" data-target="#' . $uniqid . '">' .
     '<div class="page-block-header-content u-flex u-flex-v-center">' .
     '<img class="page-block-header__thumbnail" src="' . $thumbnail . '"/>' .
     '<div class="page-block-header__title"><b>' . $this->blocks[$type]['title'] . '</b> ' . $title . '</div>' .
     '</div>' .
     '<div class="page-block-utils u-flex">' .
        '<a class="page-block-utils__delete js-delete-pageblock" data-target="#' . $uniqid . '">verwijderen</a>' .
        '<a class="page-block-utils__collapse" data-target="#' . $uniqid . '"><span class="dashicons dashicons-arrow-right icon"></span></a>' .
     '</div>' .
     '</header>' .
     '<div class="page-block__content">' .
     '<input type="hidden" name="pageblocks[' . $count . '][type]" value="' . $type . '">';
    return $html;
  }
  public function getClosingPageBlockElement() {
    $html = '</div></div>';
    return $html;
  }


  public function getOpeningPostboxElement( $title = '', $closed = false, $fullWidth = false ) {
    $class = $closed ? 'closed' : '';
    $style = $fullWidth ? 'width: 100%' : '';
    return '
      <div class="postbox ' . $class . '" id="boxid" style="' . $style . '">
        <button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">Toggle panel: ' . $title . '</span><span class="toggle-indicator" aria-hidden="false"></span></button>
        <h3>' . $title . '</h3>
        <div class="inside">';
   }

   /**
    * Return the closing tag of a collapsable postbox element
    * @return string        HTML output
    */
   public function getClosingPostboxElement() {
     return '</div></div>';
   }

   public function getFieldName( $count, $name ) {
     return 'pageblocks[' . $count . ']' . $name;
   }
}

?>
