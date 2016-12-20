<?php

namespace Pageblocks;

class Display {

  var $blocks = array();
  var $postID;

  public function __construct ( $blocks, $postID ) {
    $this->blocks = $blocks;
    $this->postID = $postID;
  }

  public function display() {
    $pageblocks = get_post_meta( $this->postID, 'pageblocks', true );
    if ( ! $pageblocks ) return;

    foreach ( $pageblocks as $key => $pageblock ) {
      $type = $pageblock['type'];
      call_user_func_array(
        array( $this->blocks[$type]['class'], 'display' ),
        array( 'content' => $pageblock )
      );
    }
  }
}

?>
