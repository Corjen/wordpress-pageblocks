<?php
/**
 * Pageblocks class
 *
 * @package Pageblocks
 * @since 0.0.1
 */
namespace Pageblocks;

use Cuisine\Wrappers\Metabox;
use Cuisine\Wrappers\Field;

use \WP_Screen;

class Pageblocks {
  public static $blocks = array();

  public static $helpers;

  public function __construct ( $blocks = array(), $postTypes = array( 'page' ), $templates = array() ) {
    self::$blocks = $blocks;

    if ( is_admin() ) {
      self::$helpers = new Helpers( $blocks );
      // Register ajax & save post hook
      add_action( 'wp_ajax_get_page_block', array( $this, 'getPageBlockAjaxCallback' ) );
      add_action( 'save_post', array( $this, 'savePost' ) );

      // Return if post ID is not set
      if ( ! isset( $_GET['post'] ) ) {
        return;
      }

      // Else, define metabox
      Metabox::make( 'Pageblocks', $postTypes, array(
        'priority' => 'high'
      ))->set('\\Pageblocks\Pageblocks::metabox');

      // Return if current template is not in accepted templates
      if ( ! empty( $templates ) ) {
        $template_file = get_post_meta( $_GET['post'], '_wp_page_template', TRUE );
        if ( ! in_array( $template_file, $templates ) ) {
          return;
        }
      }

      // Enqueue assets
      add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAssets' ) );
    }
  }

  public static function display () {

    print_r( self::$blocks );
  }

  /**
   * Enqueue assets
   */
  public function enqueueAssets () {
    global $pagenow;
    if ( $pagenow === 'post.php' ) {
      wp_enqueue_editor();
      wp_enqueue_media();
      wp_enqueue_script( 'pageblocks-collapse', PB_PLUGIN_URL . '/assets/js/pageblocks-collapse.js' );
      wp_enqueue_script( 'pageblocks-select', PB_PLUGIN_URL . '/assets/js/pageblocks-select.js' );
      wp_enqueue_script( 'pageblocks-sort-delete', PB_PLUGIN_URL . '/assets/js/pageblocks-sort-delete.js' );

      wp_enqueue_style( 'pageblocks', PB_PLUGIN_URL . '/assets/css/pageblocks.css' );
      wp_enqueue_style( 'admin', PB_PLUGIN_URL . '/assets/css/admin.css' );
    }
  }

  /**
   * Display the metabox
   * @param  object $post current post
   * @return string       html
   */
  public static function metabox ( $post ) {
    $pageblocks = get_post_meta( $post->ID, 'pageblocks', true );
    ?>
    <header class="pageblock-container-header">
      <?php echo self::getSelectPageBlock(); ?>
    </header>
    <section class="pageblock-container js-pageblock-container">
      <?php if ( ! empty( $pageblocks ) ) : ?>
        <?php echo self::getExisitingPageBlocks( $pageblocks ); ?>
      <?php endif; ?>
    </section>
    <?php
  }

  /**
  * Get a dropdown with pageblocks
  * @return string html
  */
  public static function getSelectPageBlock() {
    $html = '<select name="pageblock" class="js-select-pageblock">';
    $html .= '<option value="">Select pageblock</option>';
    foreach ( self::$blocks as $key => $value ) {
      $html .= '<option value="' . $key . '">' . $value['title'] . '</option>';
    }
    $html .= '</select>';

    return $html;
  }

  /**
   * Get existing pageblocks
   * @param  array $pageblocks Current pageblocks
   * @return string html
   */
  public static function getExisitingPageBlocks( $pageblocks ) {
    foreach ( $pageblocks as $key => $pageblock ) {
      $type = $pageblock['type'];
      call_user_func_array(
        array( self::$blocks[$type]['class'], 'admin' ),
        array(
          'count' => $key,
          'type' => $type,
          'content' => $pageblock,
          'collapsed' => true,
          'helpers' => self::$helpers,
        ) );
    }
  }

  /**
   * Ajax callback
   */
  public function getPageBlockAjaxCallback() {
    $helpers = new Helpers( self::$blocks );
    if ( ! isset( $_GET['type'] ) ) {
      die( 'Type not set' );
    } else {
      $type = $_GET['type'];
      $count = $_GET['count'];
      call_user_func_array(
        array( self::$blocks[$type]['class'], 'admin' ),
        array(
          'count' => $count,
          'type' => $type,
          'content' => array(),
          'collapsed' => false,
          'helpers' => self::$helpers,
      ) );
      die();
    }
  }

  public function savePost( $post_id ) {
    /**
     * TODO: Nonce validation
     */
     global $pagenow;
     if ( isset( $_POST['pageblocks'] ) ) {
       update_post_meta( $post_id, 'pageblocks', $_POST['pageblocks'] );
     } if ( ! isset( $_POST['pageblocks'] ) && $pagenow === 'post.php' ) {
       delete_post_meta( $post_id, 'pageblocks' );
     }
  }
}


?>
