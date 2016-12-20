<?php
/*
Plugin Name: Pageblocks
Description: Add custom pageblocks to WordPress
Version: 0.1
Author: Corjen Molll
*/

add_action( 'plugins_loaded', function () {

  define( 'PB_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
  define( 'PB_PLUGIN_DIR', dirname( __FILE__  ) );

  require __DIR__ . '/lib/Pageblocks.php';
  require __DIR__ . '/lib/Helpers.php';
  require __DIR__ . '/lib/Display.php';
} );
